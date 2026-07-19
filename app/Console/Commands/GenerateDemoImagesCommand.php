<?php

namespace App\Console\Commands;

use App\Models\Banner;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\DemoImageGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateDemoImagesCommand extends Command
{
    protected $signature = 'salomat:generate-images {--force : Regenerate all existing images}';

    protected $description = 'Generate demo images for products and banners';

    public function handle(DemoImageGenerator $generator): int
    {
        if (! Storage::disk('public')->exists('products')) {
            Storage::disk('public')->makeDirectory('products');
        }

        if (! Storage::disk('public')->exists('banners')) {
            Storage::disk('public')->makeDirectory('banners');
        }

        $generate = $this->option('force')
            ? fn (string $path, int $width, int $height, string $title, array $colors) => $generator->regenerate($path, $width, $height, $title, $colors)
            : fn (string $path, int $width, int $height, string $title, array $colors) => $generator->ensurePlaceholder($path, $width, $height, $title, $colors);

        $generate(
            'placeholders/product.webp',
            400,
            400,
            'Sabth',
            [[91, 61, 245], [18, 200, 117]],
        );

        $productColors = [
            [[91, 61, 245], [123, 97, 247]],
            [[18, 200, 117], [56, 176, 116]],
            [[74, 144, 226], [91, 61, 245]],
            [[245, 166, 35], [18, 200, 117]],
        ];

        Product::query()->each(function (Product $product, int $index) use ($generate, $productColors) {
            $jpgPath = 'products/'.$product->slug.'.jpg';

            if (Storage::disk('public')->exists($jpgPath)) {
                if ($product->main_image !== $jpgPath) {
                    $product->update(['main_image' => $jpgPath]);
                }

                return;
            }

            $path = $product->main_image ?: 'products/'.$product->slug.'.webp';
            $colors = $productColors[$index % count($productColors)];

            $generatedPath = $generate($path, 400, 400, $product->name, $colors);

            if ($product->main_image !== $generatedPath) {
                $product->update(['main_image' => $generatedPath]);
            }
        });

        ProductImage::query()
            ->with('product:id,name,slug')
            ->orderBy('product_id')
            ->orderBy('sort_order')
            ->each(function (ProductImage $image, int $index) use ($generate, $productColors) {
                if (Storage::disk('public')->exists($image->image)) {
                    return;
                }

                $title = $image->product?->name ?? 'Salomat';
                $colors = $productColors[$index % count($productColors)];

                $generate($image->image, 400, 400, $title, $colors);
            });

        $bannerSizes = [
            'home_left' => [320, 420],
            'home_slider' => [920, 320],
            'home_right' => [320, 420],
            'promo' => [360, 220],
            'home_promo' => [360, 220],
        ];

        Banner::query()->each(function (Banner $banner, int $index) use ($generate, $bannerSizes) {
            if (! $banner->image || Storage::disk('public')->exists($banner->image)) {
                return;
            }

            [$width, $height] = $bannerSizes[$banner->position] ?? [920, 320];
            $colors = [
                [[74, 144, 226], [91, 61, 245]],
                [[245, 166, 35], [214, 69, 69]],
                [[91, 61, 245], [18, 200, 117]],
                [[18, 200, 117], [74, 144, 226]],
            ][$index % 4];

            $imagePath = $generate(
                $banner->image,
                $width,
                $height,
                $banner->title,
                $colors,
            );

            if ($banner->image !== $imagePath) {
                $banner->update(['image' => $imagePath]);
            }
        });

        $this->info($this->option('force')
            ? 'Demo images regenerated successfully.'
            : 'Demo images generated successfully.');

        return self::SUCCESS;
    }
}
