<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Storage;

/**
 * Mengelola gambar inline yang disematkan di dalam konten artikel
 * (diupload lewat rich text editor).
 *
 * Tanggung jawab:
 * - Menambahkan atribut srcset responsive pada <img> di konten.
 * - Mengekstrak path gambar inline dari konten.
 * - Membersihkan file gambar yang sudah tidak dipakai lagi.
 */
class ArticleContentImages
{
    public function __construct(protected ThumbnailGenerator $thumbnails) {}

    /**
     * Tambahkan srcset responsive ke setiap <img> yang mengarah ke storage lokal.
     * Gambar eksternal (http/https selain host sendiri) dibiarkan apa adanya.
     */
    public function addResponsiveAttributes(string $html): string
    {
        return preg_replace_callback(
            '/<img\b[^>]*>/i',
            fn (array $matches) => $this->processImageTag($matches[0]),
            $html
        );
    }

    /**
     * Ekstrak path relatif (di disk public) dari semua <img> lokal di konten.
     *
     * @return array<int, string>
     */
    public function extractPaths(?string $html): array
    {
        if (! $html) {
            return [];
        }

        preg_match_all('/<img\b[^>]*\bsrc=["\']([^"\']+)["\']/i', $html, $matches);

        $paths = [];
        foreach ($matches[1] as $src) {
            $path = $this->srcToStoragePath($src);
            if ($path) {
                $paths[] = $path;
            }
        }

        return array_values(array_unique($paths));
    }

    /**
     * Hapus file gambar inline yang tidak lagi dirujuk oleh konten manapun.
     * Dipakai saat artikel dihapus atau konten diperbarui.
     *
     * @param  array<int, string>  $keepPaths  Path yang masih dipakai dan harus dipertahankan
     */
    public function deleteOrphans(array $previousPaths, array $keepPaths): void
    {
        $orphans = array_diff($previousPaths, $keepPaths);

        foreach ($orphans as $path) {
            $this->thumbnails->delete($path);
        }
    }

    /**
     * Ambil path gambar yang dipakai artikel lain (untuk menghindari
     * penghapusan gambar yang dipakai bersama).
     *
     * @return array<int, string>
     */
    public function pathsUsedByOtherArticles(int $excludeArticleId): array
    {
        $paths = [];

        Article::where('id', '!=', $excludeArticleId)
            ->whereNotNull('content')
            ->pluck('content')
            ->each(function ($content) use (&$paths) {
                foreach ($this->extractPaths($content) as $path) {
                    $paths[$path] = true;
                }
            });

        return array_keys($paths);
    }

    /**
     * Proses satu tag <img>: suntikkan srcset & sizes jika gambar lokal.
     */
    protected function processImageTag(string $tag): string
    {
        if (! preg_match('/\bsrc=["\']([^"\']+)["\']/i', $tag, $srcMatch)) {
            return $tag;
        }

        $path = $this->srcToStoragePath($srcMatch[1]);

        if (! $path) {
            return $tag;
        }

        $srcset = $this->thumbnails->srcset($path);

        if ($srcset === '') {
            return $tag;
        }

        // Jangan menimpa srcset yang sudah ada
        if (str_contains($tag, 'srcset=')) {
            return $tag;
        }

        $sizes = 'sizes="(min-width: 1024px) 896px, 100vw"';
        $srcsetAttr = 'srcset="'.e($srcset).'"';

        return rtrim($tag, '>').' '.$srcsetAttr.' '.$sizes.'>';
    }

    /**
     * Konversi URL src gambar menjadi path relatif di disk public.
     * Mengembalikan null jika bukan gambar lokal.
     */
    protected function srcToStoragePath(string $src): ?string
    {
        // Abaikan data URI dan URL eksternal
        if (str_starts_with($src, 'data:')) {
            return null;
        }

        $path = parse_url($src, PHP_URL_PATH) ?? '';

        // Cocokkan pola /storage/... baik relatif maupun absolute
        if (str_contains($path, '/storage/')) {
            $relative = ltrim(substr($path, strpos($path, '/storage/') + 9), '/');

            // Hanya proses gambar yang memang ada di disk public
            return Storage::disk('public')->exists($relative) ? $relative : null;
        }

        return null;
    }
}
