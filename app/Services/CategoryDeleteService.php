<?php

namespace App\Services;

use App\Models\Category;
use RuntimeException;

class CategoryDeleteService
{
    public function __construct(
        private readonly CategoryImageService $imageService,
    ) {}

    public function canDelete(Category $category): bool
    {
        return $this->deleteReason($category) === null;
    }

    public function deleteReason(Category $category): ?string
    {
        if ($category->children()->exists()) {
            $count = $category->children()->count();

            return "У категории есть подкатегории ({$count}). Сначала удалите или переместите их.";
        }

        if ($category->products()->exists()) {
            $count = $category->products()->count();

            return "В категории есть товары ({$count}). Переместите товары в другую категорию перед удалением.";
        }

        return null;
    }

    public function delete(Category $category): void
    {
        if ($reason = $this->deleteReason($category)) {
            throw new RuntimeException($reason);
        }

        $this->imageService->delete($category->image);
        $category->delete();
    }
}
