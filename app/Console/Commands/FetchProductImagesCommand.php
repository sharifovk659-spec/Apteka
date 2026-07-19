<?php

namespace App\Console\Commands;

use App\Models\Banner;
use App\Models\Product;
use App\Services\RemoteImageDownloader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FetchProductImagesCommand extends Command
{
    protected $signature = 'salomat:fetch-images {--force : Re-download even if JPG already exists}';

    protected $description = 'Download real product and banner images from the internet';

    public function handle(RemoteImageDownloader $downloader): int
    {
        if (! Storage::disk('public')->exists('products')) {
            Storage::disk('public')->makeDirectory('products');
        }

        if (! Storage::disk('public')->exists('banners')) {
            Storage::disk('public')->makeDirectory('banners');
        }

        $products = Product::query()
            ->with(['category:id,slug', 'images'])
            ->orderBy('id')
            ->get();

        $this->info('Downloading product images...');
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($products as $product) {
            $mainPath = 'products/'.$product->slug.'.jpg';

            if (! $this->option('force') && $downloader->exists($mainPath)) {
                if ($product->main_image !== $mainPath) {
                    $product->update(['main_image' => $mainPath]);
                }
                $bar->advance();
                $success++;

                continue;
            }

            try {
                $downloader->deleteLegacyVariants($product->slug);
                $this->downloadFirstMatch(
                    $downloader,
                    $this->urlsForProduct($product, 0),
                    $mainPath,
                    800,
                    800,
                );
                $product->update(['main_image' => $mainPath]);

                foreach ($product->images as $imageIndex => $galleryImage) {
                    $galleryPath = 'products/'.$product->slug.'-'.($imageIndex + 1).'.jpg';
                    $this->downloadFirstMatch(
                        $downloader,
                        $this->urlsForProduct($product, $imageIndex + 1),
                        $galleryPath,
                        800,
                        800,
                    );
                    $galleryImage->update(['image' => $galleryPath]);
                }

                $success++;
            } catch (\Throwable $exception) {
                $failed++;
                $this->newLine();
                $this->warn("  {$product->slug}: {$exception->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('Downloading banner images...');
        $this->fetchBanners($downloader);

        $this->newLine();
        $this->info("Done. Products OK: {$success}, failed: {$failed}.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function urlsForProduct(Product $product, int $offset): array
    {
        $categorySlug = $product->category?->slug ?? 'lekarstvennye-preparaty';
        $tags = config("product-stock-images.tags.{$categorySlug}")
            ?? config('product-stock-images.tags.lekarstvennye-preparaty', 'medicine,pharmacy');

        $lock = abs(crc32($product->slug)) + ($offset * 17);

        return [
            $this->loremFlickrUrl($tags, $lock, 800, 800),
            $this->loremFlickrUrl('medicine,pharmacy,health', $lock + 3, 800, 800),
            $this->picsumUrl("sabth-{$product->slug}-{$offset}", 800, 800),
        ];
    }

    private function fetchBanners(RemoteImageDownloader $downloader): void
    {
        $bannerTags = config('product-stock-images.banner_tags', []);
        $map = [
            ['position' => 'home_left', 'sort_order' => 1, 'key' => 'home_left', 'w' => 640, 'h' => 840, 'lock' => 501],
            ['position' => 'home_right', 'sort_order' => 1, 'key' => 'home_right', 'w' => 640, 'h' => 840, 'lock' => 502],
            ['position' => 'home_slider', 'sort_order' => 1, 'key' => 'home_slider_1', 'w' => 920, 'h' => 320, 'lock' => 601],
            ['position' => 'home_slider', 'sort_order' => 2, 'key' => 'home_slider_2', 'w' => 920, 'h' => 320, 'lock' => 602],
            ['position' => 'home_slider', 'sort_order' => 3, 'key' => 'home_slider_3', 'w' => 920, 'h' => 320, 'lock' => 603],
        ];

        foreach ($map as $item) {
            $tags = $bannerTags[$item['key']] ?? 'pharmacy,medicine';
            $path = 'banners/'.str_replace('_', '-', $item['key']).'.jpg';
            $urls = [
                $this->loremFlickrUrl($tags, $item['lock'], $item['w'], $item['h']),
                $this->loremFlickrUrl('pharmacy,medicine,health', $item['lock'] + 9, $item['w'], $item['h']),
                $this->picsumUrl('sabth-banner-'.$item['key'], $item['w'], $item['h']),
            ];

            try {
                $this->downloadFirstMatch($downloader, $urls, $path, $item['w'], $item['h']);

                Banner::query()
                    ->where('position', $item['position'])
                    ->where('sort_order', $item['sort_order'])
                    ->update(['image' => $path]);

                $this->line("  Banner {$item['key']} saved.");
            } catch (\Throwable $exception) {
                $this->warn("  Banner {$item['key']}: {$exception->getMessage()}");
            }
        }
    }

    private function downloadFirstMatch(
        RemoteImageDownloader $downloader,
        array $urls,
        string $path,
        int $width,
        int $height,
    ): void {
        $errors = [];

        foreach ($urls as $url) {
            try {
                $downloader->download($url, $path, $width, $height);

                return;
            } catch (\Throwable $exception) {
                $errors[] = $exception->getMessage();
            }
        }

        throw new \RuntimeException(implode(' | ', $errors));
    }

    private function loremFlickrUrl(string $tags, int $lock, int $width, int $height): string
    {
        $tags = str_replace(' ', '', $tags);

        return "https://loremflickr.com/{$width}/{$height}/{$tags}/all?lock={$lock}";
    }

    private function picsumUrl(string $seed, int $width, int $height): string
    {
        $seed = preg_replace('/[^a-zA-Z0-9\-_]/', '-', $seed) ?? 'sabth';

        return "https://picsum.photos/seed/{$seed}/{$width}/{$height}";
    }
}
