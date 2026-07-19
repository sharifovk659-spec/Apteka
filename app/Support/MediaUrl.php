<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class MediaUrl
{
    public static function fromStorage(?string $path, string $fallback): string
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return self::publicPath($path);
        }

        if ($path) {
            foreach (['webp', 'svg', 'jpg', 'jpeg', 'png'] as $extension) {
                $alternate = preg_replace('/\.[^.]+$/', ".{$extension}", $path);

                if ($alternate && Storage::disk('public')->exists($alternate)) {
                    return self::publicPath($alternate);
                }
            }
        }

        return asset($fallback);
    }

    private static function publicPath(string $path): string
    {
        return '/storage/'.ltrim($path, '/');
    }
}
