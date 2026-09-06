<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    public function test_homepage_can_sort_published_articles_from_oldest_to_newest(): void
    {
        $oldArticle = Article::create([
            'category_id' => $this->categoryTech->id,
            'title' => 'Berita Paling Lama',
            'slug' => 'berita-paling-lama',
            'excerpt' => 'Berita lama.',
            'content' => 'Isi berita lama.',
            'status' => 'published',
            'published_at' => now()->subDays(3),
        ]);

        $newArticle = Article::create([
            'category_id' => $this->categoryTech->id,
            'title' => 'Berita Paling Baru',
            'slug' => 'berita-paling-baru',
            'excerpt' => 'Berita baru.',
            'content' => 'Isi berita baru.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/?sort=oldest');

        $response->assertStatus(200);
        $response->assertViewHas('sort', 'oldest');
        $response->assertViewHas('featuredArticle', $oldArticle);
        $response->assertSeeInOrder(['Berita Paling Lama', 'Berita Paling Baru']);
        $response->assertDontSee('sort=latest');
    }

    public function test_homepage_rejects_an_invalid_news_sort(): void
    {
        $response = $this->get('/?sort=invalid');

        $response->assertStatus(302);
        $response->assertSessionHasErrors('sort');
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

    public function test_homepage_passes_featured_secondary_and_more_articles(): void
    {
        $category = Category::firstOrCreate(['slug' => 'berita-umum'], ['name' => 'Berita Umum']);

        // Create 7 published articles
        for ($i = 1; $i <= 7; $i++) {
            Article::create([
                'category_id' => $category->id,
                'title' => "Berita Ke-{$i}",
                'slug' => "berita-ke-{$i}",
                'excerpt' => "Ringkasan berita {$i}",
                'content' => "Isi lengkap berita {$i}",
                'author_name' => "Penulis {$i}",
                'status' => 'published',
                'published_at' => now()->subMinutes($i * 10),
            ]);
        }

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewHas('featuredArticle');
        $response->assertViewHas('secondaryArticles');
        $response->assertViewHas('moreArticles');
    }

    public function test_main_layout_renders_haliminews_brand_search_and_guest_avatar(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('HalimiNews');
        $response->assertSee(route('news.search'));
        $response->assertSee('name="q"', false);
        $response->assertSee('https://x.com');
        $response->assertSee('https://facebook.com');
        $response->assertSee(route('login'));
        $response->assertSee('title="Login Admin"', false);
        $response->assertSee('Built with Laravel &amp; Tailwind CSS', false);
    }

    public function test_main_layout_renders_admin_dashboard_avatar_when_authenticated(): void
    {
        $user = User::create([
            'name' => 'Editor HalimiNews',
            'email' => 'editor@haliminews.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee(route('admin.dashboard'));
        $response->assertSee('Dashboard Admin (Editor HalimiNews)');
    }

    public function test_homepage_redesign_renders_category_nav_hero_and_more_articles(): void
    {
        $category = Category::firstOrCreate(['slug' => 'teknologi'], ['name' => 'Teknologi']);

        // Create 6 published articles
        for ($i = 1; $i <= 6; $i++) {
            Article::create([
                'category_id' => $category->id,
                'title' => "Artikel Berita {$i}",
                'slug' => "artikel-berita-{$i}",
                'excerpt' => "Ringkasan konten artikel berita {$i}",
                'content' => "Konten lengkap artikel berita {$i}",
                'author_name' => "Penulis Hebat {$i}",
                'status' => 'published',
                'published_at' => now()->subHours($i),
            ]);
        }

        $response = $this->get('/');

        $response->assertStatus(200);

        // 1. Category Navigation Bar
        $response->assertSee('Home');
        $response->assertSee('Semua Berita');
        $response->assertSee(route('news.index'));
        $response->assertSee($category->name);
        $response->assertSee(route('news.category', $category->slug));

        // 2. Hero Section: Featured Article (Article 1)
        $response->assertSee('Artikel Berita 1');
        $response->assertSee('Ringkasan konten artikel berita 1');
        $response->assertSee('Penulis Hebat 1');
        $response->assertSee('read more &rarr;', false);

        // 3. Hero Section: Secondary Articles (Articles 2 - 5)
        $response->assertSee('Artikel Berita 2');
        $response->assertSee('Artikel Berita 3');
        $response->assertSee('Artikel Berita 4');
        $response->assertSee('Artikel Berita 5');

        // 4. Additional News Section ($moreArticles, Article 6)
        $response->assertSee('Berita Terkini Lainnya');
        $response->assertSee('Lihat Semua Berita');
        $response->assertSee('Artikel Berita 6');

        // 5. No trending author section
        $response->assertDontSee('Trending Author');
        $response->assertDontSee('trending author');
    }

    public function test_homepage_empty_state_rendered_when_no_published_articles(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Belum Ada Berita yang Diterbitkan');
        $response->assertSee('Login Admin untuk Menambah Berita');
    }
}
