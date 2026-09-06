<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory;

    /**
     * Kunci cache untuk daftar kategori yang diurutkan berdasarkan nama.
     */
    public const CACHE_KEY = 'categories.all';

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Ambil semua kategori (diurutkan berdasarkan nama) dengan cache.
     * Kategori jarang berubah, jadi aman di-cache untuk mengurangi query
     * berulang di setiap halaman publik.
     *
     * @return Collection<int, static>
     */
    public static function cached()
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return static::orderBy('name')->get();
        });
    }

    /**
     * Hapus cache kategori. Dipanggil otomatis saat kategori berubah.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::clearCache());
        static::deleted(fn () => static::clearCache());
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
