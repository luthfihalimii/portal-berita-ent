<?php

namespace App\Models;

use App\Services\ThumbnailGenerator;
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
            return $this->published_at->format('H:i').', Hari ini';
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

    /**
     * Atribut srcset responsive untuk tag <img> thumbnail.
     * Mengembalikan string kosong jika tidak ada thumbnail.
     */
    public function getThumbnailSrcsetAttribute(): string
    {
        if (! $this->thumbnail) {
            return '';
        }

        return app(ThumbnailGenerator::class)->srcset($this->thumbnail);
    }

    /**
     * Konten yang siap dirender ke halaman publik.
     *
     * Konten baru dari rich text editor sudah berupa HTML yang tersanitasi,
     * sehingga bisa dirender langsung. Konten lama yang masih berupa plain
     * text (dibuat sebelum ada rich editor) di-escape lalu diberi line break
     * agar tetap tampil dengan benar.
     */
    public function getRenderedContentAttribute(): string
    {
        $content = $this->content ?? '';

        // Konten lama (plain text) tidak mengandung tag HTML sama sekali
        if ($content === strip_tags($content)) {
            return nl2br(e($content));
        }

        return $content;
    }

    /**
     * Estimasi waktu baca dalam menit (asumsi 200 kata/menit, minimal 1 menit).
     */
    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content ?? ''));

        return max(1, (int) ceil($wordCount / 200));
    }

    /**
     * Structured data JSON-LD (schema.org NewsArticle) untuk SEO.
     *
     * @return array<string, mixed>
     */
    public function getJsonLdAttribute(): array
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $this->title,
            'description' => $this->excerpt,
            'datePublished' => $this->published_at?->toAtomString(),
            'dateModified' => $this->updated_at?->toAtomString(),
            'author' => [
                '@type' => 'Person',
                'name' => $this->author_name,
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'HalimiNews',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('favicon.svg'),
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('news.show', $this->slug),
            ],
        ];

        if ($this->thumbnail) {
            $data['image'] = [asset('storage/'.$this->thumbnail)];
        }

        return $data;
    }
}
