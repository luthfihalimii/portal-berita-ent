<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleCrudTest extends TestCase
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

    public function test_guest_cannot_access_article_crud(): void
    {
        $this->get('/admin/articles')->assertRedirect('/login');
        $this->get('/admin/articles/create')->assertRedirect('/login');
        $this->post('/admin/articles', ['title' => 'Judul Berita'])->assertRedirect('/login');
    }

    public function test_admin_can_view_article_list(): void
    {
        Article::create([
            'category_id' => $this->category->id,
            'title' => 'Inovasi AI Terbaru',
            'slug' => 'inovasi-ai-terbaru',
            'excerpt' => 'Ringkasan artikel AI',
            'content' => 'Konten lengkap inovasi teknologi AI.',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/articles');

        $response->assertStatus(200);
        $response->assertSee('Inovasi AI Terbaru');
        $response->assertSee('Draft');
    }

    public function test_admin_can_create_draft_article_without_thumbnail(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/articles', [
            'category_id' => $this->category->id,
            'title' => 'Perkembangan Superkomputer',
            'slug' => 'perkembangan-superkomputer',
            'excerpt' => 'Ringkasan superkomputer',
            'content' => 'Detail perkembangan komputasi awan dan superkomputer.',
            'status' => 'draft',
        ]);

        $response->assertRedirect('/admin/articles');
        $this->assertDatabaseHas('articles', [
            'title' => 'Perkembangan Superkomputer',
            'slug' => 'perkembangan-superkomputer',
            'status' => 'draft',
            'thumbnail' => null,
            'published_at' => null,
        ]);
    }

    public function test_admin_can_create_published_article_with_thumbnail(): void
    {
        Storage::fake('public');

        $thumbnail = UploadedFile::fake()->image('news-thumbnail.jpg', 600, 400);

        $response = $this->actingAs($this->admin)->post('/admin/articles', [
            'category_id' => $this->category->id,
            'title' => 'Robotika Masa Depan',
            'slug' => 'robotika-masa-depan',
            'excerpt' => 'Ringkasan robotika',
            'content' => 'Ulasan teknologi robotika cerdas.',
            'status' => 'published',
            'thumbnail' => $thumbnail,
        ]);

        $response->assertRedirect('/admin/articles');

        $article = Article::where('slug', 'robotika-masa-depan')->first();
        $this->assertNotNull($article);
        $this->assertEquals('published', $article->status);
        $this->assertNotNull($article->published_at);
        $this->assertNotNull($article->thumbnail);

        Storage::disk('public')->assertExists($article->thumbnail);
    }

    public function test_article_creation_requires_mandatory_fields(): void
    {
        $response = $this->actingAs($this->admin)
            ->from('/admin/articles/create')
            ->post('/admin/articles', []);

        $response->assertRedirect('/admin/articles/create');
        $response->assertSessionHasErrors(['title', 'excerpt', 'content', 'category_id', 'status']);
    }

    public function test_admin_can_update_article_and_publish(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'title' => 'Draft Awal Berita',
            'slug' => 'draft-awal-berita',
            'excerpt' => 'Ringkasan draft',
            'content' => 'Konten awal.',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/articles/{$article->id}", [
            'category_id' => $this->category->id,
            'title' => 'Judul Berita Telah Diubah',
            'slug' => 'judul-berita-telah-diubah',
            'excerpt' => 'Ringkasan yang diperbarui',
            'content' => 'Konten lengkap yang sudah direvisi.',
            'status' => 'published',
        ]);

        $response->assertRedirect('/admin/articles');
        $article->refresh();

        $this->assertEquals('Judul Berita Telah Diubah', $article->title);
        $this->assertEquals('published', $article->status);
        $this->assertNotNull($article->published_at);
    }

    public function test_admin_can_delete_article_and_file(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('article-image.jpg');
        $path = $file->store('thumbnails', 'public');

        $article = Article::create([
            'category_id' => $this->category->id,
            'title' => 'Artikel Akan Dihapus',
            'slug' => 'artikel-akan-dihapus',
            'excerpt' => 'Ringkasan',
            'content' => 'Konten...',
            'thumbnail' => $path,
            'status' => 'draft',
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($this->admin)->delete("/admin/articles/{$article->id}");

        $response->assertRedirect('/admin/articles');
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_admin_can_create_article_with_author_name(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/articles', [
            'category_id' => $this->category->id,
            'title' => 'Penemuan Eksoplanet Baru',
            'slug' => 'penemuan-eksoplanet-baru',
            'author_name' => 'Mary Frost',
            'excerpt' => 'Para astronom menemukan planet baru.',
            'content' => 'Konten lengkap mengenai eksoplanet baru.',
            'status' => 'published',
        ]);

        $response->assertRedirect('/admin/articles');
        $this->assertDatabaseHas('articles', [
            'title' => 'Penemuan Eksoplanet Baru',
            'author_name' => 'Mary Frost',
        ]);
    }

    public function test_admin_can_update_article_author_name(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'title' => 'Berita Awal',
            'slug' => 'berita-awal',
            'author_name' => 'Penulis Lama',
            'excerpt' => 'Ringkasan',
            'content' => 'Konten...',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/articles/{$article->id}", [
            'category_id' => $this->category->id,
            'title' => 'Berita Awal Diperbarui',
            'slug' => 'berita-awal',
            'author_name' => 'Lucas Ray',
            'excerpt' => 'Ringkasan baru',
            'content' => 'Konten baru...',
            'status' => 'published',
        ]);

        $response->assertRedirect('/admin/articles');
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'author_name' => 'Lucas Ray',
        ]);
    }
}
