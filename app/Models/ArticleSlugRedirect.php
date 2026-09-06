<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Menyimpan slug lama sebuah artikel agar URL lama bisa di-redirect (301)
 * ke slug terbaru, menjaga ranking SEO dan mencegah broken link.
 */
class ArticleSlugRedirect extends Model
{
    protected $fillable = [
        'article_id',
        'old_slug',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
