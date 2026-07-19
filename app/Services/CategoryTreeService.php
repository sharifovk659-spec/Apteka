<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CategoryTreeService
{
    private ?Collection $grouped = null;

    /** @var array<int, int>|null */
    private ?array $publishedProductCounts = null;

    public function allGroupedByParent(): Collection
    {
        if ($this->grouped === null) {
            $categories = Category::query()
                ->select(['id', 'parent_id', 'name', 'slug', 'sort_order', 'is_active'])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            $this->grouped = $categories->groupBy(fn (Category $category) => $category->parent_id ?? 0);
        }

        return $this->grouped;
    }

    public function resetCache(): void
    {
        $this->grouped = null;
        $this->publishedProductCounts = null;
    }

    public function optionsForSelect(?int $excludeId = null, bool $activeOnly = false): array
    {
        return $this->flattenForSelect(null, 0, $excludeId, $activeOnly);
    }

    public function flattenForSelect(?int $parentId = null, int $depth = 0, ?int $excludeId = null, bool $activeOnly = false): array
    {
        $groupKey = $parentId ?? 0;
        $children = $this->allGroupedByParent()->get($groupKey, collect());

        if ($activeOnly) {
            $children = $children->where('is_active', true);
        }

        $options = [];

        foreach ($children as $category) {
            if ($excludeId !== null && $category->id === $excludeId) {
                continue;
            }

            $prefix = $depth > 0 ? str_repeat('—', $depth).' ' : '';
            $options[] = [
                'id' => $category->id,
                'name' => $category->name,
                'label' => $prefix.$category->name,
                'depth' => $depth,
            ];

            $options = array_merge(
                $options,
                $this->flattenForSelect($category->id, $depth + 1, $excludeId, $activeOnly)
            );
        }

        return $options;
    }

    public function descendantIds(int $categoryId): array
    {
        $ids = [];
        $queue = [$categoryId];

        while ($queue !== []) {
            $currentId = array_shift($queue);
            $children = $this->allGroupedByParent()->get($currentId, collect());

            foreach ($children as $child) {
                $ids[] = $child->id;
                $queue[] = $child->id;
            }
        }

        return $ids;
    }

    public function isValidParent(?int $parentId, ?int $categoryId = null): bool
    {
        if ($parentId === null) {
            return true;
        }

        if ($categoryId !== null && $parentId === $categoryId) {
            return false;
        }

        if (! Category::query()->whereKey($parentId)->exists()) {
            return false;
        }

        if ($categoryId !== null && in_array($parentId, $this->descendantIds($categoryId), true)) {
            return false;
        }

        return true;
    }

    public function depth(Category $category): int
    {
        $depth = 0;
        $parentId = $category->parent_id;

        while ($parentId !== null) {
            $depth++;
            $parentId = Category::query()->whereKey($parentId)->value('parent_id');
        }

        return $depth;
    }

    public function flattenForAdminTable(): Collection
    {
        $rows = collect($this->flattenForSelect(null, 0));

        return Category::query()
            ->with(['parent:id,name'])
            ->withCount('products')
            ->withCount(['children'])
            ->whereIn('id', $rows->pluck('id'))
            ->get()
            ->sortBy(function (Category $category) use ($rows) {
                return $rows->search(fn (array $row) => $row['id'] === $category->id);
            })
            ->values()
            ->each(function (Category $category) use ($rows) {
                $row = $rows->firstWhere('id', $category->id);
                $category->setAttribute('tree_depth', $row['depth'] ?? 0);
            });
    }

    public function rootsWithChildren(bool $activeOnly = true): Collection
    {
        $query = Category::query()
            ->select(['id', 'name', 'slug', 'parent_id', 'sort_order', 'is_active', 'icon', 'image'])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query
            ->with($this->nestedChildrenRelation($activeOnly))
            ->get();
    }

    public function homeCategories(): Collection
    {
        return $this->rootsWithChildren()
            ->each(function (Category $category) {
                $category->setAttribute(
                    'products_count',
                    $this->productCountIncludingDescendants($category->id)
                );
            });
    }

    public function homeSubcategories(): Collection
    {
        $flat = collect($this->flattenForSelect(null, 0, null, true))
            ->filter(fn (array $row) => $row['depth'] > 0);

        $categories = Category::query()
            ->select(['id', 'name', 'slug'])
            ->whereIn('id', $flat->pluck('id'))
            ->get()
            ->keyBy('id');

        return $flat
            ->map(function (array $row) use ($categories) {
                $category = $categories->get($row['id']);

                if (! $category) {
                    return null;
                }

                $category->setAttribute('tree_depth', $row['depth']);
                $category->setAttribute(
                    'products_count',
                    $this->productCountIncludingDescendants($category->id)
                );

                return $category;
            })
            ->filter()
            ->values();
    }

    private function publishedCountsByCategory(): array
    {
        if ($this->publishedProductCounts === null) {
            $this->publishedProductCounts = Product::query()
                ->where('is_active', true)
                ->where('status', 'published')
                ->selectRaw('category_id, COUNT(*) as aggregate')
                ->groupBy('category_id')
                ->pluck('aggregate', 'category_id')
                ->map(fn ($count) => (int) $count)
                ->all();
        }

        return $this->publishedProductCounts;
    }

    public function productCountIncludingDescendants(int $categoryId): int
    {
        $counts = $this->publishedCountsByCategory();
        $ids = array_merge([$categoryId], $this->descendantIds($categoryId));
        $total = 0;

        foreach ($ids as $id) {
            $total += $counts[$id] ?? 0;
        }

        return $total;
    }

    private function nestedChildrenRelation(bool $activeOnly): array
    {
        return [
            'children' => function ($query) use ($activeOnly) {
                $query->select(['id', 'name', 'slug', 'parent_id', 'sort_order', 'is_active', 'icon', 'image'])
                    ->orderBy('sort_order')
                    ->orderBy('name');

                if ($activeOnly) {
                    $query->where('is_active', true);
                }

                $query->with($this->nestedChildrenRelation($activeOnly));
            },
        ];
    }

    public function applyCategoryFilter(Builder $query, Category $category): void
    {
        $categoryIds = array_merge([$category->id], $this->descendantIds($category->id));

        $query->whereIn('category_id', $categoryIds);
    }

    public function catalogFilterCategories(): Collection
    {
        $flat = collect($this->flattenForSelect(null, 0, null, true));
        $categories = Category::query()->active()->get()->keyBy('id');

        return $flat
            ->map(function (array $row) use ($categories) {
                $category = $categories->get($row['id']);

                if (! $category) {
                    return null;
                }

                $category->setAttribute('tree_depth', $row['depth']);
                $category->setAttribute('tree_label', $row['label']);
                $category->setAttribute(
                    'products_count',
                    $this->productCountIncludingDescendants($category->id)
                );

                return $category;
            })
            ->filter()
            ->values();
    }
}
