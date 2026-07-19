<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DemoImageGenerator
{
    public function generate(string $relativePath, int $width, int $height, string $title, array $colors): string
    {
        if (\function_exists('imagewebp')) {
            return $this->generateWebp($relativePath, $width, $height, $title, $colors);
        }

        $svgPath = preg_replace('/\.webp$/', '.svg', $relativePath);

        return $this->generateSvg($svgPath, $width, $height, $title, $colors);
    }

    public function regenerate(string $relativePath, int $width, int $height, string $title, array $colors): string
    {
        $svgPath = preg_replace('/\.webp$/', '.svg', $relativePath);

        foreach ([$relativePath, $svgPath] as $file) {
            if (Storage::disk('public')->exists($file)) {
                Storage::disk('public')->delete($file);
            }
        }

        return $this->generate($relativePath, $width, $height, $title, $colors);
    }

    public function ensurePlaceholder(string $relativePath, int $width, int $height, string $title, array $colors): string
    {
        $svgPath = preg_replace('/\.webp$/', '.svg', $relativePath);

        if (Storage::disk('public')->exists($relativePath) || Storage::disk('public')->exists($svgPath)) {
            return Storage::disk('public')->exists($relativePath) ? $relativePath : $svgPath;
        }

        return $this->generate($relativePath, $width, $height, $title, $colors);
    }

    private function generateWebp(string $relativePath, int $width, int $height, string $title, array $colors): string
    {
        if (! \function_exists('imagewebp')) {
            throw new RuntimeException('GD с поддержкой WebP недоступен.');
        }

        $absolutePath = Storage::disk('public')->path($relativePath);
        $directory = dirname($absolutePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $image = \imagecreatetruecolor($width, $height);
        $background = \imagecolorallocate($image, 246, 247, 251);
        \imagefilledrectangle($image, 0, 0, $width, $height, $background);

        $this->drawProductMockup($image, $width, $height, $title, $colors);

        if (! \imagewebp($image, $absolutePath, 86)) {
            \imagedestroy($image);
            throw new RuntimeException("Не удалось сохранить {$relativePath}");
        }

        \imagedestroy($image);

        return $relativePath;
    }

    private function generateSvg(string $relativePath, int $width, int $height, string $title, array $colors): string
    {
        $absolutePath = Storage::disk('public')->path($relativePath);
        $directory = dirname($absolutePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $accent = $this->rgbString($colors[0]);
        $accentSoft = $this->rgbaString($colors[0], 0.14);
        $isWide = $width > $height * 1.4;
        $label = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $shapes = $isWide
            ? $this->wideBannerShapes($width, $height, $accent, $accentSoft, $title)
            : $this->productShapes($width, $height, $accent, $accentSoft, $title);

        $shadowCx = (int) ($width / 2);
        $shadowCy = (int) ($height * 0.88);
        $shadowRx = (int) ($width * 0.34);
        $shadowRy = max(8, (int) ($height * 0.06));
        $badgeCx = (int) ($width * 0.88);
        $badgeCy = (int) ($height * 0.12);

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}" role="img" aria-label="{$label}">
  <rect width="100%" height="100%" fill="#F6F7FB"/>
  <ellipse cx="{$shadowCx}" cy="{$shadowCy}" rx="{$shadowRx}" ry="{$shadowRy}" fill="rgba(23,32,51,0.06)"/>
  {$shapes}
  <circle cx="{$badgeCx}" cy="{$badgeCy}" r="16" fill="rgba(18,200,117,0.12)"/>
  <path d="M{$badgeCx} {$badgeCy} v7 M{$badgeCx} {$badgeCy} h7" stroke="#12C875" stroke-width="2" stroke-linecap="round"/>
</svg>
SVG;

        file_put_contents($absolutePath, $svg);

        return $relativePath;
    }

    private function productShapes(int $width, int $height, string $accent, string $accentSoft, string $title): string
    {
        $variant = crc32($title) % 3;
        $cx = (int) ($width / 2);
        $cy = (int) ($height / 2);

        return match ($variant) {
            0 => $this->svgBottle($cx, $cy, $accent, $accentSoft),
            1 => $this->svgBox($cx, $cy, $accent, $accentSoft),
            default => $this->svgTube($cx, $cy, $accent, $accentSoft),
        };
    }

    private function wideBannerShapes(int $width, int $height, string $accent, string $accentSoft, string $title): string
    {
        $y = (int) ($height / 2);
        $step = (int) ($width / 4);

        return implode("\n  ", [
            $this->svgBox($step, $y, $accent, $accentSoft, 0.85),
            $this->svgBottle($step * 2, $y, $accent, $accentSoft, 0.9),
            $this->svgTube($step * 3, $y, $accent, $accentSoft, 0.85),
        ]);
    }

    private function svgBox(int $cx, int $cy, string $accent, string $accentSoft, float $scale = 1): string
    {
        $w = (int) (118 * $scale);
        $h = (int) (148 * $scale);
        $x = $cx - (int) ($w / 2);
        $y = $cy - (int) ($h / 2);
        $line1Y = $y + 42;
        $line2Y = $y + 68;
        $line3Y = $y + 86;

        return <<<SVG
<g>
  <rect x="{$x}" y="{$y}" width="{$w}" height="{$h}" rx="12" fill="#FFFFFF" stroke="#E3E7EF"/>
  <rect x="{$x}" y="{$y}" width="{$w}" height="28" rx="12" fill="{$accentSoft}"/>
  <rect x="{$x}" y="{$y}" width="{$w}" height="28" fill="{$accent}" opacity="0.85"/>
  <rect x="{$x}" y="{$line1Y}" width="{$w}" height="18" rx="4" fill="#EEF1F6"/>
  <rect x="{$x}" y="{$line2Y}" width="{$w}" height="10" rx="4" fill="#EEF1F6"/>
  <rect x="{$x}" y="{$line3Y}" width="{$w}" height="10" rx="4" fill="#EEF1F6"/>
</g>
SVG;
    }

    private function svgBottle(int $cx, int $cy, string $accent, string $accentSoft, float $scale = 1): string
    {
        $bodyW = (int) (72 * $scale);
        $bodyH = (int) (132 * $scale);
        $x = $cx - (int) ($bodyW / 2);
        $y = $cy - (int) ($bodyH / 2) + 8;
        $capW = (int) (42 * $scale);
        $capX = $cx - (int) ($capW / 2);
        $capY = $y - (int) (24 * $scale);

        $labelY = $y + 28;
        $line1Y = $y + 78;
        $line2Y = $y + 92;
        $lineW = $bodyW - 28;
        $lineX = $x + 14;

        return <<<SVG
<g>
  <rect x="{$capX}" y="{$capY}" width="{$capW}" height="22" rx="6" fill="{$accent}"/>
  <rect x="{$x}" y="{$y}" width="{$bodyW}" height="{$bodyH}" rx="24" fill="#FFFFFF" stroke="#E3E7EF"/>
  <rect x="{$x}" y="{$labelY}" width="{$bodyW}" height="34" rx="18" fill="{$accentSoft}"/>
  <rect x="{$x}" y="{$labelY}" width="{$bodyW}" height="34" rx="18" fill="{$accent}" opacity="0.78"/>
  <rect x="{$lineX}" y="{$line1Y}" width="{$lineW}" height="8" rx="4" fill="#EEF1F6"/>
  <rect x="{$lineX}" y="{$line2Y}" width="{$lineW}" height="8" rx="4" fill="#EEF1F6"/>
</g>
SVG;
    }

    private function svgTube(int $cx, int $cy, string $accent, string $accentSoft, float $scale = 1): string
    {
        $w = (int) (54 * $scale);
        $h = (int) (148 * $scale);
        $x = $cx - (int) ($w / 2);
        $y = $cy - (int) ($h / 2);

        $lineX = $x + 10;
        $lineW = $w - 20;
        $line1Y = $y + 52;
        $line2Y = $y + 66;
        $line3Y = $y + 80;

        return <<<SVG
<g>
  <rect x="{$x}" y="{$y}" width="{$w}" height="{$h}" rx="18" fill="#FFFFFF" stroke="#E3E7EF"/>
  <rect x="{$x}" y="{$y}" width="{$w}" height="36" rx="18" fill="{$accentSoft}"/>
  <rect x="{$x}" y="{$y}" width="{$w}" height="36" rx="18" fill="{$accent}" opacity="0.82"/>
  <rect x="{$lineX}" y="{$line1Y}" width="{$lineW}" height="8" rx="4" fill="#EEF1F6"/>
  <rect x="{$lineX}" y="{$line2Y}" width="{$lineW}" height="8" rx="4" fill="#EEF1F6"/>
  <rect x="{$lineX}" y="{$line3Y}" width="{$lineW}" height="8" rx="4" fill="#EEF1F6"/>
</g>
SVG;
    }

    private function drawProductMockup($image, int $width, int $height, string $title, array $colors): void
    {
        $white = \imagecolorallocate($image, 255, 255, 255);
        $border = \imagecolorallocate($image, 227, 231, 239);
        $accent = \imagecolorallocate($image, $colors[0][0], $colors[0][1], $colors[0][2]);
        $cx = (int) ($width / 2);
        $cy = (int) ($height / 2);
        $boxW = (int) min($width * 0.42, 160);
        $boxH = (int) min($height * 0.52, 190);
        $x = $cx - (int) ($boxW / 2);
        $y = $cy - (int) ($boxH / 2);

        \imagefilledrectangle($image, $x, $y, $x + $boxW, $y + $boxH, $white);
        \imagerectangle($image, $x, $y, $x + $boxW, $y + $boxH, $border);
        \imagefilledrectangle($image, $x, $y, $x + $boxW, $y + 28, $accent);
    }

    private function rgbString(array $rgb): string
    {
        return sprintf('rgb(%d,%d,%d)', $rgb[0], $rgb[1], $rgb[2]);
    }

    private function rgbaString(array $rgb, float $alpha): string
    {
        return sprintf('rgba(%d,%d,%d,%.2f)', $rgb[0], $rgb[1], $rgb[2], $alpha);
    }

    private function shortTitle(string $title): string
    {
        if (function_exists('mb_strlen') && mb_strlen($title) > 42) {
            return rtrim(mb_substr($title, 0, 39)).'...';
        }

        if (strlen($title) > 42) {
            return rtrim(substr($title, 0, 39)).'...';
        }

        return $title;
    }

    private function blend(array $start, array $end, float $ratio): array
    {
        return [
            (int) round($start[0] + ($end[0] - $start[0]) * $ratio),
            (int) round($start[1] + ($end[1] - $start[1]) * $ratio),
            (int) round($start[2] + ($end[2] - $start[2]) * $ratio),
        ];
    }
}
