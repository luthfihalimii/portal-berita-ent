<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'thumbnail',
        'author_name',
        'status',
        'published_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function getAuthorNameAttribute(?string $value): string
    {
        return filled($value) ? $value : 'Redaksi HalimiNews';
    }

    /**
     * Format waktu publikasi yang ramah pembaca Indonesia.
     * Contoh: "10:30, Hari ini", "Kemarin", "2 jam yang lalu", "05 Jan 2026".
     */
    public function getPublishedForHumansAttribute(): string
    {
        if (! $this->published_at) {
            return '';
        }

        if ($this->published_at->isToday()) {
            return $this->published_at->format('H:i') . ', Hari ini';
        }

        if ($this->published_at->isYesterday()) {
            return 'Kemarin';
        }

        if ($this->published_at->greaterThanOrEqualTo(now()->subDays(7))) {
            return $this->published_at->diffForHumans();
        }

        return $this->published_at->format('d M Y');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
