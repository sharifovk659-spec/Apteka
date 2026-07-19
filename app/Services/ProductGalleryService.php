<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProductGalleryService
{
    public const MAX_IMAGES = 10;

    public function __construct(
        private readonly ProductImageService $imageService,
    ) {}

    /**
     * @param  array<int, UploadedFile>  $files
     */
    public function attachImages(Product $product, array $files): void
    {
        if ($files === []) {
            return;
        }

        $currentCount = $product->images()->count();

        if ($currentCount + count($files) > self::MAX_IMAGES) {
            throw new RuntimeException('Максимум '.self::MAX_IMAGES.' изображений на один товар.');
        }

        $nextSort = (int) $product->images()->max('sort_order');

        foreach ($files as $file) {
            $nextSort++;
            $path = $this->imageService->store($file);

            $product->images()->create([
                'image' => $path,
                'alt_text' => $product->name,
                'sort_order' => $nextSort,
                'is_primary' => false,
            ]);
        }

        $this->ensurePrimary($product);
        $this->syncMainImage($product);
    }

    public function syncGallery(Product $product, array $payload): void
    {
        DB::transaction(function () use ($product, $payload) {
            $deleteIds = collect($payload['delete_image_ids'] ?? [])
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->values();

            if ($deleteIds->isNotEmpty()) {
                $imagesToDelete = $product->images()->whereIn('id', $deleteIds)->get();

                foreach ($imagesToDelete as $image) {
                    $this->imageService->delete($image->image);
                    $image->delete();
                }
            }

            $newFiles = collect($payload['new_images'] ?? [])
                ->filter(fn ($file) => $file instanceof UploadedFile)
                ->values()
                ->all();

            $remainingCount = $product->images()->count();

            if ($remainingCount + count($newFiles) > self::MAX_IMAGES) {
                throw new RuntimeException('Максимум '.self::MAX_IMAGES.' изображений на один товар.');
            }

            $nextSort = (int) $product->images()->max('sort_order');

            foreach ($newFiles as $file) {
                $nextSort++;
                $path = $this->imageService->store($file);

                $product->images()->create([
                    'image' => $path,
                    'alt_text' => $payload['alt_text'] ?? $product->name,
                    'sort_order' => $nextSort,
                    'is_primary' => false,
                ]);
            }

            $order = collect($payload['image_order'] ?? [])
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->values();

            if ($order->isNotEmpty()) {
                foreach ($order as $index => $imageId) {
                    $product->images()->whereKey($imageId)->update(['sort_order' => $index + 1]);
                }
            }

            $primaryId = isset($payload['primary_image_id']) ? (int) $payload['primary_image_id'] : null;

            if ($primaryId) {
                $product->images()->update(['is_primary' => false]);
                $product->images()->whereKey($primaryId)->update(['is_primary' => true]);
            }

            $this->ensurePrimary($product);
            $this->syncMainImage($product);
        });
    }

    public function ensurePrimary(Product $product): void
    {
        $product->load('images');

        if ($product->images->isEmpty()) {
            return;
        }

        if ($product->images->contains(fn (ProductImage $image) => $image->is_primary)) {
            return;
        }

        $first = $product->images->sortBy('sort_order')->first();
        $first?->update(['is_primary' => true]);
    }

    public function syncMainImage(Product $product): void
    {
        $product->load('images');

        $primary = $product->images
            ->sortBy('sort_order')
            ->firstWhere('is_primary', true)
            ?? $product->images->sortBy('sort_order')->first();

        $product->updateQuietly([
            'main_image' => $primary?->image,
        ]);
    }

    public function deleteAllImages(Product $product): void
    {
        $product->loadMissing('images');

        foreach ($product->images as $image) {
            $this->imageService->delete($image->image);
        }

        $this->imageService->delete($product->main_image);
    }
}
