<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicNewsTest extends TestCase
{
    use RefreshDatabase;

    private Category $categoryTech;
    private Category $categorySports;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryTech = Category::create([
            'name' => 'Teknologi',
            'slug' => 'teknologi',
        ]);

        $this->categorySports = Category::create([
            'name' => 'Olahraga',
            'slug' => 'olahraga',
        ]);
    }

    public function test_homepage_shows_published_articles_and_hides_drafts(): void
    {
        Article::create([
            'category_id' => $this->categoryTech->id,
            'title' => 'Peluncuran Smartphone Canggih',
            'slug' => 'peluncuran-smartphone-canggih',
            'excerpt' => 'Smartphone terbaru resmi rilis.',
            'content' => 'Konten lengkap smartphone canggih.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        Article::create([
            'category_id' => $this->categoryTech->id,
            'title' => 'Rahasia Internal Perusahaan',
            'slug' => 'rahasia-internal-perusahaan',
            'excerpt' => 'Ini berita rahasia draft.',
            'content' => 'Konten draft tidak boleh tampil.',
            'status' => 'draft',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Peluncuran Smartphone Canggih');
        $response->assertDontSee('Rahasia Internal Perusahaan');
    }

    public function test_can_view_all_published_news_list(): void
    {
        Article::create([
            'category_id' => $this->categorySports->id,
            'title' => 'Final Kejuaraan Dunia',
            'slug' => 'final-kejuaraan-dunia',
            'excerpt' => 'Pertandingan final seru.',
            'content' => 'Konten olahraga final...',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/berita');

        $response->assertStatus(200);
        $response->assertSee('Final Kejuaraan Dunia');
    }

    public function test_can_view_published_article_detail(): void
    {
        $article = Article::create([
            'category_id' => $this->categoryTech->id,
            'title' => 'Revolusi Kecerdasan Buatan',
            'slug' => 'revolusi-kecerdasan-buatan',
            'excerpt' => 'Perkembangan pesat AI modern.',
            'content' => 'Isi berita lengkap yang sangat mendalam mengenai AI.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get("/berita/{$article->slug}");

        $response->assertStatus(200);
        $response->assertSee('Revolusi Kecerdasan Buatan');
        $response->assertSee('Isi berita lengkap yang sangat mendalam mengenai AI.');
    }

    public function test_cannot_view_draft_article_detail_publicly(): void
    {
        $article = Article::create([
            'category_id' => $this->categoryTech->id,
            'title' => 'Artikel Masih Konsep',
            'slug' => 'artikel-masih-konsep',
            'excerpt' => 'Konsep draft.',
            'content' => 'Draft rahasia.',
            'status' => 'draft',
        ]);

        $response = $this->get("/berita/{$article->slug}");

        $response->assertStatus(404);
    }

    public function test_can_filter_articles_by_category(): void
    {
        Article::create([
            'category_id' => $this->categoryTech->id,
            'title' => 'Laptop Terbaru 2026',
            'slug' => 'laptop-terbaru-2026',
            'excerpt' => 'Laptop baru dirilis.',
            'content' => 'Detail laptop...',
            'status' => 'published',
            'published_at' => now(),
        ]);

        Article::create([
            'category_id' => $this->categorySports->id,
            'title' => 'Juara Balap Mobil',
            'slug' => 'juara-balap-mobil',
            'excerpt' => 'Kemenangan dramatis.',
            'content' => 'Detail balap...',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/berita/kategori/teknologi');

        $response->assertStatus(200);
        $response->assertSee('Laptop Terbaru 2026');
        $response->assertDontSee('Juara Balap Mobil');
    }

    public function test_can_search_published_articles(): void
    {
        Article::create([
            'category_id' => $this->categoryTech->id,
            'title' => 'Eksplorasi Luar Angkasa',
            'slug' => 'eksplorasi-luar-angkasa',
            'excerpt' => 'Misi ke mars.',
            'content' => 'Roket baru meluncur...',
            'status' => 'published',
            'published_at' => now(),
        ]);

        Article::create([
            'category_id' => $this->categoryTech->id,
            'title' => 'Draft Eksplorasi Luar Angkasa',
            'slug' => 'draft-eksplorasi-luar-angkasa',
            'excerpt' => 'Draft eksplorasi.',
            'content' => 'Draft rahasia...',
            'status' => 'draft',
        ]);

        $response = $this->get('/search?q=Angkasa');

        $response->assertStatus(200);
        $response->assertSee('Eksplorasi Luar Angkasa');
        $response->assertDontSee('Draft Eksplorasi Luar Angkasa');
    }
}
