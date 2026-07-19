<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProductDeleteService
{
    public function __construct(
        private readonly ProductGalleryService $galleryService,
    ) {}

    public function canDelete(Product $product): bool
    {
        return ! $product->orderItems()->exists();
    }

    public function deleteReason(Product $product): ?string
    {
        if ($this->canDelete($product)) {
            return null;
        }

        return 'Товар используется в заказах. Удаление невозможно — деактивируйте товар.';
    }

    public function delete(Product $product): void
    {
        if ($reason = $this->deleteReason($product)) {
            throw new RuntimeException($reason);
        }

        DB::transaction(function () use ($product) {
            $this->galleryService->deleteAllImages($product);
            $product->delete();
        });
    }
}
