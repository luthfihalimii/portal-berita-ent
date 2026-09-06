<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Generate responsive image variants (small/medium, WebP) untuk thumbnail artikel.
 * Bergantung pada PHP GD extension.
 */
class ThumbnailGenerator
{
    /**
     * Lebar target per variant (px).
     */
    protected const VARIANTS = [
        'small' => 480,
        'medium' => 960,
    ];

    protected const WEBP_QUALITY = 82;

    /**
     * Simpan file upload asli + buat variant responsive.
     *
     * @return string Path file asli di disk public (misal: thumbnails/abc.jpg)
     */
    public function store(UploadedFile $file): string
    {
        // Simpan file asli seperti biasa
        $path = $file->store('thumbnails', 'public');

        $this->generateVariants($path);

        return $path;
    }

    /**
     * Buat variant small & medium (WebP) dari file yang sudah tersimpan.
     */
    public function generateVariants(string $originalPath): void
    {
        $absolutePath = Storage::disk('public')->path($originalPath);

        if (! file_exists($absolutePath)) {
            return;
        }

        $source = $this->createImageFromFile($absolutePath);

        if (! $source) {
            return;
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        foreach (self::VARIANTS as $name => $targetWidth) {
            // Jangan upscale gambar yang lebih kecil dari target
            if ($sourceWidth <= $targetWidth) {
                continue;
            }

            $targetHeight = (int) round($sourceHeight * ($targetWidth / $sourceWidth));
            $resized = imagescale($source, $targetWidth, $targetHeight);

            if (! $resized) {
                continue;
            }

            $variantPath = $this->variantPath($originalPath, $name);
            $variantAbsolute = Storage::disk('public')->path($variantPath);

            // Pastikan direktori ada
            @mkdir(dirname($variantAbsolute), 0755, true);

            imagewebp($resized, $variantAbsolute, self::WEBP_QUALITY);
            imagedestroy($resized);
        }

        imagedestroy($source);
    }

    /**
     * Hapus file asli + semua variant-nya.
     */
    public function delete(string $originalPath): void
    {
        $disk = Storage::disk('public');

        if ($disk->exists($originalPath)) {
            $disk->delete($originalPath);
        }

        foreach (array_keys(self::VARIANTS) as $name) {
            $variantPath = $this->variantPath($originalPath, $name);
            if ($disk->exists($variantPath)) {
                $disk->delete($variantPath);
            }
        }
    }

    /**
     * Path variant: thumbnails/abc.jpg -> thumbnails/abc-small.webp
     */
    public function variantPath(string $originalPath, string $variant): string
    {
        $info = pathinfo($originalPath);

        return $info['dirname'] . '/' . $info['filename'] . '-' . $variant . '.webp';
    }

    /**
     * Bangun atribut srcset untuk tag <img> berdasarkan variant yang tersedia.
     */
    public function srcset(string $originalPath): string
    {
        $disk = Storage::disk('public');
        $sources = [];

        foreach (self::VARIANTS as $name => $width) {
            $variantPath = $this->variantPath($originalPath, $name);
            if ($disk->exists($variantPath)) {
                $sources[] = asset('storage/' . $variantPath) . ' ' . $width . 'w';
            }
        }

        // Selalu sertakan file asli sebagai fallback terbesar
        $sources[] = asset('storage/' . $originalPath) . ' 1600w';

        return implode(', ', $sources);
    }

    protected function createImageFromFile(string $path): \GdImage|false
    {
        $mime = mime_content_type($path);

        return match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => $this->createFromPng($path),
            'image/webp' => @imagecreatefromwebp($path),
            'image/gif' => @imagecreatefromgif($path),
            default => false,
        };
    }

    /**
     * PNG: pertahankan alpha channel saat convert.
     */
    protected function createFromPng(string $path): \GdImage|false
    {
        $img = @imagecreatefrompng($path);

        if ($img) {
            imagepalettetotruecolor($img);
            imagealphablending($img, true);
            imagesavealpha($img, true);
        }

        return $img;
    }
}
