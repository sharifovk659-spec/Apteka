<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogIndexRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\CategoryTreeService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function __construct(
        private readonly CategoryTreeService $categoryTreeService,
    ) {}

    public function index(CatalogIndexRequest $request): View
    {
        $filters = $request->filters();

        $selectedCategory = null;
        if ($filters['category']) {
            $selectedCategory = Category::query()
                ->select(['id', 'name', 'slug', 'parent_id'])
                ->where('slug', $filters['category'])
                ->where('is_active', true)
                ->first();
        }

        $productsQuery = Product::query()
            ->select([
                'id', 'name', 'slug', 'price', 'old_price', 'main_image',
                'manufacturer', 'brand_id', 'category_id', 'stock',
                'requires_prescription', 'created_at',
            ])
            ->where('is_active', true)
            ->where('status', 'published')
            ->withListingRelations();

        $this->applyFilters($productsQuery, $filters, $selectedCategory);
        $this->applySorting($productsQuery, $filters['sort']);

        $products = $productsQuery
            ->paginate(20)
            ->withQueryString();

        $filterCategories = $this->categoryTreeService->catalogFilterCategories();

        $filterBrands = Brand::query()
            ->select(['id', 'name', 'slug'])
            ->where('is_active', true)
            ->whereHas('products', fn (Builder $query) => $query->where('is_active', true)->where('status', 'published'))
            ->orderBy('name')
            ->get();

        $manufacturers = Product::query()
            ->where('is_active', true)
            ->where('status', 'published')
            ->whereNotNull('manufacturer')
            ->where('manufacturer', '!=', '')
            ->distinct()
            ->orderBy('manufacturer')
            ->pluck('manufacturer');

        $dosageForms = Product::query()
            ->where('is_active', true)
            ->where('status', 'published')
            ->whereNotNull('dosage_form')
            ->where('dosage_form', '!=', '')
            ->distinct()
            ->orderBy('dosage_form')
            ->pluck('dosage_form');

        $priceRange = Product::query()
            ->where('is_active', true)
            ->where('status', 'published')
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return view('catalog.index', [
            'products' => $products,
            'filters' => $filters,
            'selectedCategory' => $selectedCategory,
            'filterCategories' => $filterCategories,
            'filterBrands' => $filterBrands,
            'manufacturers' => $manufacturers,
            'dosageForms' => $dosageForms,
            'priceRange' => $priceRange,
            'queryParams' => $request->queryParams(),
        ]);
    }

    private function applyFilters(Builder $query, array $filters, ?Category $selectedCategory): void
    {
        if ($selectedCategory) {
            $this->categoryTreeService->applyCategoryFilter($query, $selectedCategory);
        }

        if ($filters['brand']) {
            $query->whereHas('brand', function (Builder $brandQuery) use ($filters) {
                $brandQuery->where('slug', $filters['brand'])->where('is_active', true);
            });
        }

        if ($filters['manufacturer']) {
            $query->where('manufacturer', $filters['manufacturer']);
        }

        if ($filters['dosage_form']) {
            $query->where('dosage_form', $filters['dosage_form']);
        }

        if ($filters['min_price'] !== null) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if ($filters['max_price'] !== null) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if ($filters['in_stock'] === true) {
            $query->where('stock', '>', 0);
        }

        if ($filters['discount'] === true) {
            $query->whereNotNull('old_price')
                ->whereColumn('old_price', '>', 'price');
        }

        if ($filters['prescription'] !== null) {
            $query->where('requires_prescription', $filters['prescription']);
        }

        if ($filters['search']) {
            $search = $filters['search'];
            $query->where(function (Builder $searchQuery) use ($search) {
                $searchQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('manufacturer', 'like', "%{$search}%")
                    ->orWhereHas('brand', function (Builder $brandQuery) use ($search) {
                        $brandQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }
    }

    private function applySorting(Builder $query, string $sort): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->orderByDesc('created_at'),
            default => $query
                ->orderByDesc('is_bestseller')
                ->orderByDesc('is_featured')
                ->orderBy('name'),
        };
    }
}
