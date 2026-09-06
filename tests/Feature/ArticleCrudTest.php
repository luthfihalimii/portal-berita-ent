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

    public function test_guest_cannot_upload_editor_image(): void
    {
        $image = UploadedFile::fake()->image('photo.jpg');

        $this->post('/admin/articles/upload-image', ['image' => $image])
            ->assertRedirect('/login');
    }

    public function test_admin_can_upload_editor_image(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('editor-photo.jpg', 800, 600);

        $response = $this->actingAs($this->admin)
            ->post('/admin/articles/upload-image', ['image' => $image]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['url']);

        $url = $response->json('url');
        $path = str_replace('/storage/', '', parse_url($url, PHP_URL_PATH));

        Storage::disk('public')->assertExists($path);
    }

    public function test_editor_image_upload_validates_file_type(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->admin)
            ->post('/admin/articles/upload-image', ['image' => $file]);

        $response->assertSessionHasErrors('image');
    }

    public function test_article_content_is_sanitized_from_xss(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/articles', [
            'category_id' => $this->category->id,
            'title' => 'Berita dengan Konten Berbahaya',
            'slug' => 'berita-konten-berbahaya',
            'excerpt' => 'Ringkasan',
            'content' => '<p>Konten normal</p><script>alert("xss")</script><iframe src="http://evil.com"></iframe>',
            'status' => 'published',
        ]);

        $response->assertRedirect('/admin/articles');

        $article = Article::where('slug', 'berita-konten-berbahaya')->first();
        $this->assertNotNull($article);
        $this->assertStringNotContainsString('<script>', $article->content);
        $this->assertStringNotContainsString('<iframe>', $article->content);
        $this->assertStringContainsString('<p>Konten normal</p>', $article->content);
    }

    public function test_article_content_preserves_safe_html_formatting(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/articles', [
            'category_id' => $this->category->id,
            'title' => 'Berita dengan Formatting',
            'slug' => 'berita-dengan-formatting',
            'excerpt' => 'Ringkasan',
            'content' => '<h2>Sub Judul</h2><p><strong>Teks tebal</strong> dan <em>miring</em></p><ul><li>Item 1</li></ul><a href="https://example.com">Link</a>',
            'status' => 'published',
        ]);

        $response->assertRedirect('/admin/articles');

        $article = Article::where('slug', 'berita-dengan-formatting')->first();
        $this->assertNotNull($article);
        $this->assertStringContainsString('<strong>Teks tebal</strong>', $article->content);
        $this->assertStringContainsString('<em>miring</em>', $article->content);
        $this->assertStringContainsString('href="https://example.com"', $article->content);
        $this->assertStringContainsString('<li>Item 1</li>', $article->content);
    }

    public function test_legacy_plain_text_content_is_rendered_with_line_breaks(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'title' => 'Berita Lama',
            'slug' => 'berita-lama',
            'excerpt' => 'Ringkasan',
            'content' => "Paragraf pertama.\nParagraf kedua.",
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->assertStringContainsString('<br', $article->rendered_content);
        $this->assertStringContainsString('Paragraf pertama.', $article->rendered_content);
    }

    public function test_html_content_is_rendered_as_is(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'title' => 'Berita HTML',
            'slug' => 'berita-html',
            'excerpt' => 'Ringkasan',
            'content' => '<p>Paragraf <strong>pertama</strong>.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->assertEquals('<p>Paragraf <strong>pertama</strong>.</p>', $article->rendered_content);
    }
}
