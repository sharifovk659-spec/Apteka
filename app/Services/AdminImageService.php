<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class AdminImageService
{
    public const MAX_SIZE_KB = 5120;

    public const MAX_SIZE = 5242880;

    public const MAX_WIDTH = 1200;

    public function store(UploadedFile $file, string $folder): string
    {
        $this->validateFile($file);
        $folder = trim($folder, '/');

        if (! extension_loaded('gd')) {
            return $this->storeRaw($file, $folder);
        }

        $extension = function_exists('imagewebp') ? 'webp' : 'jpg';
        $relativePath = $folder.'/'.Str::uuid()->toString().'.'.$extension;
        $absolutePath = Storage::disk('public')->path($relativePath);
        $this->ensureDirectory($absolutePath);

        $source = $this->loadImage($file);
        $source = $this->resizeIfNeeded($source);

        if ($file->getMimeType() === 'image/png') {
            \imagepalettetotruecolor($source);
            \imagealphablending($source, true);
            \imagesavealpha($source, true);
        }

        $saved = $extension === 'webp'
            ? \imagewebp($source, $absolutePath, 82)
            : \imagejpeg($source, $absolutePath, 85);

        \imagedestroy($source);

        if (! $saved) {
            throw new RuntimeException('Не удалось сохранить изображение.');
        }

        return $relativePath;
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function storeRaw(UploadedFile $file, string $folder): string
    {
        $extension = match ($file->getMimeType()) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        $relativePath = $folder.'/'.Str::uuid()->toString().'.'.$extension;
        Storage::disk('public')->putFileAs($folder, $file, basename($relativePath));

        return $relativePath;
    }

    private function validateFile(UploadedFile $file): void
    {
        if ($file->getSize() > self::MAX_SIZE) {
            throw new RuntimeException('Размер изображения не должен превышать 5 МБ.');
        }

        if (! in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'], true)) {
            throw new RuntimeException('Допустимые форматы: JPG, PNG, WEBP.');
        }
    }

    private function loadImage(UploadedFile $file)
    {
        $contents = file_get_contents($file->getRealPath());
        $source = @\imagecreatefromstring($contents === false ? '' : $contents);

        if ($source === false) {
            throw new RuntimeException('Не удалось обработать изображение.');
        }

        return $source;
    }

    private function resizeIfNeeded($source)
    {
        $width = \imagesx($source);
        $height = \imagesy($source);

        if ($width <= self::MAX_WIDTH) {
            return $source;
        }

        $newHeight = (int) round($height * (self::MAX_WIDTH / $width));
        $resized = \imagecreatetruecolor(self::MAX_WIDTH, $newHeight);
        \imagecopyresampled($resized, $source, 0, 0, 0, 0, self::MAX_WIDTH, $newHeight, $width, $height);
        \imagedestroy($source);

        return $resized;
    }

    private function ensureDirectory(string $absolutePath): void
    {
        $directory = dirname($absolutePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }
}
