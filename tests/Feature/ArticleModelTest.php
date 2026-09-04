<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_article_with_required_fields(): void
    {
        $category = Category::create([
            'name' => 'Politik',
            'slug' => 'politik',
        ]);

        $article = Article::create([
            'category_id' => $category->id,
            'title' => 'Berita Terkini',
            'slug' => 'berita-terkini',
            'excerpt' => 'Ringkasan berita politik',
            'content' => 'Konten lengkap berita politik hari ini.',
        ]);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'category_id' => $category->id,
            'title' => 'Berita Terkini',
            'slug' => 'berita-terkini',
            'status' => 'draft',
        ]);
        $this->assertNull($article->published_at);
        $this->assertNull($article->thumbnail);
    }

    public function test_article_belongs_to_category(): void
    {
        $category = Category::create([
            'name' => 'Ekonomi',
            'slug' => 'ekonomi',
        ]);

        $article = Article::create([
            'category_id' => $category->id,
            'title' => 'Pasar Saham Naik',
            'slug' => 'pasar-saham-naik',
            'excerpt' => 'Ringkasan pasar saham',
            'content' => 'Detail indeks harga saham gabungan hari ini.',
        ]);

        $this->assertInstanceOf(Category::class, $article->category);
        $this->assertEquals($category->id, $article->category->id);
    }

    public function test_article_slug_must_be_unique(): void
    {
        $category = Category::create([
            'name' => 'Olahraga',
            'slug' => 'olahraga',
        ]);

        Article::create([
            'category_id' => $category->id,
            'title' => 'Pertandingan Sepakbola',
            'slug' => 'pertandingan-sepakbola',
            'excerpt' => 'Hasil skor',
            'content' => 'Detail pertandingan kemarin.',
        ]);

        $this->expectException(QueryException::class);

        Article::create([
            'category_id' => $category->id,
            'title' => 'Pertandingan Sepakbola Lain',
            'slug' => 'pertandingan-sepakbola',
            'excerpt' => 'Hasil skor lain',
            'content' => 'Detail pertandingan lain.',
        ]);
    }

    public function test_published_at_is_cast_to_datetime(): void
    {
        $category = Category::create([
            'name' => 'Gaya Hidup',
            'slug' => 'gaya-hidup',
        ]);

        $now = now();
        $article = Article::create([
            'category_id' => $category->id,
            'title' => 'Tips Sehat',
            'slug' => 'tips-sehat',
            'excerpt' => 'Tips hidup sehat',
            'content' => 'Detail gaya hidup sehat...',
            'status' => 'published',
            'published_at' => $now,
        ]);

        $this->assertInstanceOf(Carbon::class, $article->published_at);
        $this->assertEquals($now->toDateTimeString(), $article->published_at->toDateTimeString());
    }

    public function test_deleting_category_cascades_articles(): void
    {
        $category = Category::create([
            'name' => 'Otomotif',
            'slug' => 'otomotif',
        ]);

        $article = Article::create([
            'category_id' => $category->id,
            'title' => 'Mobil Listrik Baru',
            'slug' => 'mobil-listrik-baru',
            'excerpt' => 'Peluncuran mobil listrik baru',
            'content' => 'Spesifikasi lengkap mobil...',
        ]);

        $category->delete();

        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }
}
