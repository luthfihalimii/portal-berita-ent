<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoImprovementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->category = Category::create([
            'name' => 'Teknologi',
            'slug' => 'teknologi',
        ]);
    }

    private function createArticle(array $overrides = []): Article
    {
        return Article::create(array_merge([
            'category_id' => $this->category->id,
            'title' => 'Berita Uji',
            'slug' => 'berita-uji',
            'excerpt' => 'Ringkasan berita uji.',
            'content' => '<p>Konten berita uji.</p>',
            'status' => 'published',
            'published_at' => now(),
        ], $overrides));
    }

    // ------------------------------------------------------------------
    // 1. Slug redirect & canonical
    // ------------------------------------------------------------------

    public function test_old_slug_redirects_to_new_slug_with_301(): void
    {
        $article = $this->createArticle(['slug' => 'slug-lama']);

        // Ubah slug via update
        $this->actingAs($this->admin)->put("/admin/articles/{$article->id}", [
            'category_id' => $this->category->id,
            'title' => 'Berita Uji',
            'slug' => 'slug-baru',
            'excerpt' => 'Ringkasan',
            'content' => '<p>Konten.</p>',
            'status' => 'published',
        ]);

        // Akses URL lama harus redirect 301 ke URL baru
        $response = $this->get('/berita/slug-lama');
        $response->assertStatus(301);
        $response->assertRedirect('/berita/slug-baru');
    }

    public function test_slug_redirect_record_is_created_on_slug_change(): void
    {
        $article = $this->createArticle(['slug' => 'slug-awal']);

        $this->actingAs($this->admin)->put("/admin/articles/{$article->id}", [
            'category_id' => $this->category->id,
            'title' => 'Berita Uji',
            'slug' => 'slug-diubah',
            'excerpt' => 'Ringkasan',
            'content' => '<p>Konten.</p>',
            'status' => 'published',
        ]);

        $this->assertDatabaseHas('article_slug_redirects', [
            'article_id' => $article->id,
            'old_slug' => 'slug-awal',
        ]);
    }

    public function test_unknown_slug_returns_404(): void
    {
        $this->get('/berita/slug-tidak-ada')->assertStatus(404);
    }

    public function test_article_page_has_canonical_link(): void
    {
        $this->createArticle(['slug' => 'berita-canonical']);

        $response = $this->get('/berita/berita-canonical');

        $response->assertStatus(200);
        $response->assertSee('rel="canonical"', false);
    }

    // ------------------------------------------------------------------
    // 2. Excerpt auto-generate
    // ------------------------------------------------------------------

    public function test_excerpt_is_auto_generated_when_empty(): void
    {
        $this->actingAs($this->admin)->post('/admin/articles', [
            'category_id' => $this->category->id,
            'title' => 'Berita Tanpa Excerpt',
            'slug' => 'berita-tanpa-excerpt',
            'excerpt' => '',
            'content' => '<p>Ini adalah isi konten berita yang cukup panjang untuk dijadikan excerpt otomatis.</p>',
            'status' => 'published',
        ]);

        $article = Article::where('slug', 'berita-tanpa-excerpt')->first();
        $this->assertNotNull($article);
        $this->assertNotEmpty($article->excerpt);
        $this->assertStringContainsString('Ini adalah isi konten', $article->excerpt);
    }

    public function test_excerpt_auto_generate_strips_html_tags(): void
    {
        $excerpt = Article::generateExcerpt(null, '<p>Konten <strong>tebal</strong> di sini.</p>');

        $this->assertStringNotContainsString('<strong>', $excerpt);
        $this->assertStringContainsString('tebal', $excerpt);
    }

    public function test_excerpt_auto_generate_truncates_long_content(): void
    {
        $longContent = '<p>'.str_repeat('kata ', 100).'</p>';
        $excerpt = Article::generateExcerpt(null, $longContent, 200);

        $this->assertLessThanOrEqual(201, mb_strlen($excerpt)); // 200 + ellipsis
    }

    public function test_manual_excerpt_is_not_overridden(): void
    {
        $excerpt = Article::generateExcerpt('Excerpt manual saya', '<p>Konten apapun.</p>');

        $this->assertSame('Excerpt manual saya', $excerpt);
    }

    // ------------------------------------------------------------------
    // 3. Search improvement
    // ------------------------------------------------------------------

    public function test_search_finds_keyword_in_content(): void
    {
        $this->createArticle([
            'slug' => 'berita-konten-unik',
            'title' => 'Berita Biasa',
            'content' => '<p>Kata unik zimbabwe ada di konten.</p>',
        ]);

        $response = $this->get('/search?q=zimbabwe');

        $response->assertStatus(200);
        $response->assertSee('Berita Biasa');
    }

    public function test_search_prioritizes_title_match_over_content(): void
    {
        $this->createArticle([
            'slug' => 'berita-konten-saja',
            'title' => 'Artikel Kedua',
            'content' => '<p>teknologi di konten.</p>',
            'published_at' => now(),
        ]);

        $this->createArticle([
            'slug' => 'berita-judul',
            'title' => 'teknologi di Judul',
            'content' => '<p>Konten lain.</p>',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/search?q=teknologi');

        $response->assertStatus(200);
        // Artikel dengan keyword di judul harus muncul lebih dulu
        $content = $response->getContent();
        $posTitle = strpos($content, 'teknologi di Judul');
        $posContent = strpos($content, 'Artikel Kedua');
        $this->assertNotFalse($posTitle);
        $this->assertNotFalse($posContent);
        $this->assertLessThan($posContent, $posTitle);
    }

    // ------------------------------------------------------------------
    // 4. Category caching
    // ------------------------------------------------------------------

    public function test_category_cached_returns_all_categories(): void
    {
        Cache::forget(Category::CACHE_KEY);

        $categories = Category::cached();

        $this->assertCount(1, $categories);
        $this->assertTrue(Cache::has(Category::CACHE_KEY));
    }

    public function test_category_cache_is_cleared_on_save(): void
    {
        Category::cached(); // isi cache
        $this->assertTrue(Cache::has(Category::CACHE_KEY));

        Category::create(['name' => 'Olahraga', 'slug' => 'olahraga']);

        $this->assertFalse(Cache::has(Category::CACHE_KEY));
    }

    // ------------------------------------------------------------------
    // 5. HTTP caching / ETag
    // ------------------------------------------------------------------

    public function test_article_page_sends_etag_and_cache_headers(): void
    {
        $this->createArticle(['slug' => 'berita-cache']);

        $response = $this->get('/berita/berita-cache');

        $response->assertStatus(200);
        $this->assertNotNull($response->headers->get('ETag'));
        $this->assertStringContainsString('public', $response->headers->get('Cache-Control'));
    }

    public function test_article_page_returns_304_when_etag_matches(): void
    {
        $this->createArticle(['slug' => 'berita-cache-304']);

        $firstResponse = $this->get('/berita/berita-cache-304');
        $etag = $firstResponse->headers->get('ETag');

        $secondResponse = $this->withHeaders(['If-None-Match' => $etag])
            ->get('/berita/berita-cache-304');

        $secondResponse->assertStatus(304);
    }
}
