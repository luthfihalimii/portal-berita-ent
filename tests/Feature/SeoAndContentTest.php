<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Services\ArticleContentImages;
use App\Services\ThumbnailGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeoAndContentTest extends TestCase
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

    private function createPublishedArticle(array $overrides = []): Article
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
    // Sitemap, Robots, Feed
    // ------------------------------------------------------------------

    public function test_sitemap_is_accessible_and_lists_published_articles(): void
    {
        $this->createPublishedArticle();

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('berita/berita-uji', false);
    }

    public function test_sitemap_excludes_draft_articles(): void
    {
        $this->createPublishedArticle(['slug' => 'draft-tidak-muncul', 'status' => 'draft', 'published_at' => null]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertDontSee('draft-tidak-muncul', false);
    }

    public function test_robots_txt_references_sitemap(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $response->assertSee('Sitemap:');
        $response->assertSee('/sitemap.xml', false);
        $response->assertSee('Disallow: /admin', false);
    }

    public function test_rss_feed_lists_published_articles(): void
    {
        $this->createPublishedArticle();

        $response = $this->get('/feed');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8');
        $response->assertSee('<title>Berita Uji</title>', false);
    }

    // ------------------------------------------------------------------
    // Reading time & JSON-LD
    // ------------------------------------------------------------------

    public function test_reading_time_is_at_least_one_minute(): void
    {
        $article = $this->createPublishedArticle(['content' => '<p>Pendek.</p>']);

        $this->assertSame(1, $article->reading_time);
    }

    public function test_reading_time_scales_with_word_count(): void
    {
        $words = implode(' ', array_fill(0, 600, 'kata'));
        $article = $this->createPublishedArticle(['content' => "<p>{$words}</p>"]);

        $this->assertSame(3, $article->reading_time);
    }

    public function test_json_ld_contains_news_article_schema(): void
    {
        $article = $this->createPublishedArticle();

        $jsonLd = $article->json_ld;

        $this->assertSame('NewsArticle', $jsonLd['@type']);
        $this->assertSame('Berita Uji', $jsonLd['headline']);
        $this->assertSame('HalimiNews', $jsonLd['publisher']['name']);
    }

    public function test_article_page_contains_json_ld_script(): void
    {
        $this->createPublishedArticle();

        $response = $this->get('/berita/berita-uji');

        $response->assertStatus(200);
        $response->assertSee('application/ld+json', false);
        $response->assertSee('NewsArticle', false);
    }

    // ------------------------------------------------------------------
    // SEO meta accessor (refactor)
    // ------------------------------------------------------------------

    public function test_seo_meta_accessor_returns_complete_meta(): void
    {
        $article = $this->createPublishedArticle(['slug' => 'berita-seo-meta']);

        $meta = $article->seo_meta;

        $this->assertSame('Berita Uji - HalimiNews', $meta['title']);
        $this->assertSame('Ringkasan berita uji.', $meta['metaDescription']);
        $this->assertSame('article', $meta['ogType']);
        $this->assertSame(route('news.show', 'berita-seo-meta'), $meta['canonicalUrl']);
        $this->assertNull($meta['ogImage']); // tidak ada thumbnail
    }

    public function test_og_image_url_uses_thumbnail_when_present(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('thumbnails/foto.jpg', 'dummy');

        $article = $this->createPublishedArticle(['thumbnail' => 'thumbnails/foto.jpg']);

        $this->assertStringContainsString('thumbnails/foto.jpg', $article->og_image_url);
    }

    public function test_article_page_renders_seo_meta_from_accessor(): void
    {
        $this->createPublishedArticle([
            'slug' => 'berita-seo-render',
            'title' => 'Judul Untuk SEO',
        ]);

        $response = $this->get('/berita/berita-seo-render');

        $response->assertStatus(200);
        $response->assertSee('<title>Judul Untuk SEO - HalimiNews</title>', false);
    }

    // ------------------------------------------------------------------
    // Inline image optimization (responsive srcset)
    // ------------------------------------------------------------------

    public function test_uploaded_editor_image_generates_webp_variants(): void
    {
        Storage::fake('public');

        // Buat gambar besar agar variant ter-generate
        $image = UploadedFile::fake()->image('besar.jpg', 1600, 900);

        $response = $this->actingAs($this->admin)
            ->post('/admin/articles/upload-image', ['image' => $image]);

        $response->assertStatus(200);
        $url = $response->json('url');
        $path = str_replace('/storage/', '', parse_url($url, PHP_URL_PATH));

        Storage::disk('public')->assertExists($path);

        // Variant small & medium harus ada karena gambar 1600px > 960px
        $variantSmall = (new ThumbnailGenerator)->variantPath($path, 'small');
        $variantMedium = (new ThumbnailGenerator)->variantPath($path, 'medium');

        Storage::disk('public')->assertExists($variantSmall);
        Storage::disk('public')->assertExists($variantMedium);
    }

    public function test_content_images_service_extracts_local_paths(): void
    {
        $service = app(ArticleContentImages::class);

        Storage::fake('public');
        Storage::disk('public')->put('article-images/foto.jpg', 'dummy');

        $html = '<p><img src="/storage/article-images/foto.jpg"> dan <img src="https://external.com/img.png"></p>';

        $paths = $service->extractPaths($html);

        $this->assertContains('article-images/foto.jpg', $paths);
        $this->assertNotContains('https://external.com/img.png', $paths);
    }

    public function test_content_images_service_ignores_data_uri(): void
    {
        $service = app(ArticleContentImages::class);

        $html = '<img src="data:image/png;base64,iVBORw0KGgo=">';

        $this->assertSame([], $service->extractPaths($html));
    }

    // ------------------------------------------------------------------
    // Inline image cleanup
    // ------------------------------------------------------------------

    public function test_orphaned_inline_image_is_deleted_when_article_deleted(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('article-images/orphan.jpg', 'dummy');

        $article = $this->createPublishedArticle([
            'content' => '<p><img src="/storage/article-images/orphan.jpg"></p>',
        ]);

        $this->actingAs($this->admin)->delete("/admin/articles/{$article->id}");

        Storage::disk('public')->assertMissing('article-images/orphan.jpg');
    }

    public function test_shared_inline_image_is_kept_when_one_article_deleted(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('article-images/shared.jpg', 'dummy');

        $articleA = $this->createPublishedArticle([
            'slug' => 'artikel-a',
            'content' => '<p><img src="/storage/article-images/shared.jpg"></p>',
        ]);

        $this->createPublishedArticle([
            'slug' => 'artikel-b',
            'content' => '<p><img src="/storage/article-images/shared.jpg"></p>',
        ]);

        $this->actingAs($this->admin)->delete("/admin/articles/{$articleA->id}");

        // Gambar masih dipakai artikel B, jadi tidak boleh dihapus
        Storage::disk('public')->assertExists('article-images/shared.jpg');
    }

    public function test_inline_image_removed_from_content_is_deleted_on_update(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('article-images/dihapus.jpg', 'dummy');

        $article = $this->createPublishedArticle([
            'content' => '<p><img src="/storage/article-images/dihapus.jpg"></p>',
        ]);

        $this->actingAs($this->admin)->put("/admin/articles/{$article->id}", [
            'category_id' => $this->category->id,
            'title' => 'Berita Uji',
            'slug' => 'berita-uji',
            'excerpt' => 'Ringkasan berita uji.',
            'content' => '<p>Konten tanpa gambar sekarang.</p>',
            'status' => 'published',
        ]);

        Storage::disk('public')->assertMissing('article-images/dihapus.jpg');
    }
}
