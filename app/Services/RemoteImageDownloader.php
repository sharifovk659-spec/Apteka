<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class RemoteImageDownloader
{
    public const MAX_SIZE = 800;

    public const JPEG_QUALITY = 85;

    public function download(string $url, string $relativePath, ?int $width = null, ?int $height = null): string
    {
        $relativePath = $this->normalizeRelativePath($relativePath);
        $absolutePath = Storage::disk('public')->path($relativePath);
        $directory = dirname($absolutePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $response = Http::timeout(45)
            ->retry(2, 500)
            ->withHeaders([
                'User-Agent' => 'SabthPharmacyDemo/1.0',
                'Accept' => 'image/jpeg,image/png,image/webp,image/*;q=0.8,*/*;q=0.5',
            ])
            ->get($url);

        if (! $response->successful()) {
            throw new RuntimeException("HTTP {$response->status()} for {$url}");
        }

        $body = $response->body();

        if (strlen($body) < 1024) {
            throw new RuntimeException('Downloaded file is too small to be a valid image.');
        }

        if (! \function_exists('imagecreatefromstring')) {
            file_put_contents($absolutePath, $body);

            return $relativePath;
        }

        $source = @\imagecreatefromstring($body);

        if ($source === false) {
            throw new RuntimeException('Downloaded content is not a valid image.');
        }

        $processed = $this->processImage($source, $width, $height);
        \imagedestroy($source);

        if (! \imagejpeg($processed, $absolutePath, self::JPEG_QUALITY)) {
            \imagedestroy($processed);
            throw new RuntimeException("Failed to save {$relativePath}");
        }

        \imagedestroy($processed);

        return $relativePath;
    }

    public function exists(string $relativePath): bool
    {
        $relativePath = $this->normalizeRelativePath($relativePath);

        return Storage::disk('public')->exists($relativePath);
    }

    public function deleteLegacyVariants(string $basename): void
    {
        foreach (['webp', 'svg', 'jpg', 'jpeg', 'png'] as $extension) {
            $path = "products/{$basename}.{$extension}";

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    private function normalizeRelativePath(string $relativePath): string
    {
        $relativePath = ltrim($relativePath, '/');

        if (! str_contains($relativePath, '.')) {
            $relativePath .= '.jpg';
        }

        if (! str_ends_with(strtolower($relativePath), '.jpg')) {
            $relativePath = preg_replace('/\.[^.]+$/', '.jpg', $relativePath) ?? $relativePath.'.jpg';
        }

        return $relativePath;
    }

    private function processImage($source, ?int $targetWidth, ?int $targetHeight)
    {
        $width = \imagesx($source);
        $height = \imagesy($source);

        if ($targetWidth && $targetHeight) {
            return $this->coverCrop($source, $width, $height, $targetWidth, $targetHeight);
        }

        $max = self::MAX_SIZE;

        if ($width <= $max && $height <= $max) {
            return $this->copyImage($source, $width, $height);
        }

        if ($width >= $height) {
            $newWidth = $max;
            $newHeight = (int) round($height * ($max / $width));
        } else {
            $newHeight = $max;
            $newWidth = (int) round($width * ($max / $height));
        }

        return $this->resize($source, $width, $height, $newWidth, $newHeight);
    }

    private function coverCrop($source, int $width, int $height, int $targetWidth, int $targetHeight)
    {
        $scale = max($targetWidth / $width, $targetHeight / $height);
        $scaledWidth = (int) ceil($width * $scale);
        $scaledHeight = (int) ceil($height * $scale);
        $scaled = $this->resize($source, $width, $height, $scaledWidth, $scaledHeight);

        $cropX = (int) max(0, floor(($scaledWidth - $targetWidth) / 2));
        $cropY = (int) max(0, floor(($scaledHeight - $targetHeight) / 2));

        $canvas = \imagecreatetruecolor($targetWidth, $targetHeight);
        $white = \imagecolorallocate($canvas, 255, 255, 255);
        \imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $white);
        \imagecopy(
            $canvas,
            $scaled,
            0,
            0,
            $cropX,
            $cropY,
            $targetWidth,
            $targetHeight,
        );
        \imagedestroy($scaled);

        return $canvas;
    }

    private function resize($source, int $width, int $height, int $newWidth, int $newHeight)
    {
        $canvas = \imagecreatetruecolor($newWidth, $newHeight);
        $white = \imagecolorallocate($canvas, 255, 255, 255);
        \imagefilledrectangle($canvas, 0, 0, $newWidth, $newHeight, $white);
        \imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return $canvas;
    }

    private function copyImage($source, int $width, int $height)
    {
        $canvas = \imagecreatetruecolor($width, $height);
        $white = \imagecolorallocate($canvas, 255, 255, 255);
        \imagefilledrectangle($canvas, 0, 0, $width, $height, $white);
        \imagecopy($canvas, $source, 0, 0, 0, 0, $width, $height);

        return $canvas;
    }
}
