<?php

namespace App\Services;

use App\Models\Brand;
use RuntimeException;

class BrandDeleteService
{
    public function __construct(
        private readonly AdminImageService $images,
    ) {}

    public function canDelete(Brand $brand): bool
    {
        return ! $brand->products()->exists();
    }

    public function deleteReason(Brand $brand): ?string
    {
        if ($this->canDelete($brand)) {
            return null;
        }

        $count = $brand->products()->count();

        return "У бренда есть товары ({$count}). Сначала переместите или удалите их.";
    }

    public function delete(Brand $brand): void
    {
        if ($reason = $this->deleteReason($brand)) {
            throw new RuntimeException($reason);
        }

        $this->images->delete($brand->logo);
        $brand->delete();
    }
}
