<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class CategoryImageService
{
    public const MAX_SIZE_KB = 5120;

    public const MAX_SIZE = 5242880;

    public const MAX_WIDTH = 800;

    public const WEBP_QUALITY = 82;

    public const JPEG_QUALITY = 85;

    public function store(UploadedFile $file): string
    {
        $this->validateFile($file);

        if (! extension_loaded('gd')) {
            return $this->storeRaw($file);
        }

        $useWebp = function_exists('imagewebp');
        $extension = $useWebp ? 'webp' : 'jpg';
        $filename = Str::uuid()->toString().'.'.$extension;
        $relativePath = 'categories/'.$filename;
        $absolutePath = Storage::disk('public')->path($relativePath);

        $this->ensureDirectory($absolutePath);

        if ($useWebp) {
            $this->saveAsWebp($file, $absolutePath);
        } else {
            $this->saveAsJpeg($file, $absolutePath);
        }

        return $relativePath;
    }

    public function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function storeRaw(UploadedFile $file): string
    {
        $extension = match ($file->getMimeType()) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        $relativePath = 'categories/'.Str::uuid()->toString().'.'.$extension;

        Storage::disk('public')->putFileAs('categories', $file, basename($relativePath));

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

    private function saveAsWebp(UploadedFile $file, string $destination): void
    {
        $source = $this->loadImage($file);
        $source = $this->resizeIfNeeded($source);

        if ($file->getMimeType() === 'image/png') {
            imagepalettetotruecolor($source);
            imagealphablending($source, true);
            imagesavealpha($source, true);
        }

        if (! imagewebp($source, $destination, self::WEBP_QUALITY)) {
            imagedestroy($source);
            throw new RuntimeException('Не удалось сохранить изображение.');
        }

        imagedestroy($source);
    }

    private function saveAsJpeg(UploadedFile $file, string $destination): void
    {
        $source = $this->loadImage($file);
        $source = $this->resizeIfNeeded($source);

        if (! imagejpeg($source, $destination, self::JPEG_QUALITY)) {
            imagedestroy($source);
            throw new RuntimeException('Не удалось сохранить изображение.');
        }

        imagedestroy($source);
    }

    private function loadImage(UploadedFile $file)
    {
        $contents = file_get_contents($file->getRealPath());

        if ($contents === false) {
            throw new RuntimeException('Не удалось прочитать загруженный файл.');
        }

        $source = @imagecreatefromstring($contents);

        if ($source === false) {
            throw new RuntimeException('Не удалось обработать изображение.');
        }

        return $source;
    }

    private function resizeIfNeeded($source)
    {
        $width = imagesx($source);
        $height = imagesy($source);

        if ($width <= self::MAX_WIDTH) {
            return $source;
        }

        $newHeight = (int) round($height * (self::MAX_WIDTH / $width));
        $resized = imagecreatetruecolor(self::MAX_WIDTH, $newHeight);
        $white = imagecolorallocate($resized, 255, 255, 255);
        imagefilledrectangle($resized, 0, 0, self::MAX_WIDTH, $newHeight, $white);
        imagecopyresampled($resized, $source, 0, 0, 0, 0, self::MAX_WIDTH, $newHeight, $width, $height);
        imagedestroy($source);

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
