<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_category_with_name_and_slug(): void
    {
        $category = Category::create([
            'name' => 'Teknologi',
            'slug' => 'teknologi',
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Teknologi',
            'slug' => 'teknologi',
        ]);
    }

    public function test_slug_must_be_unique(): void
    {
        Category::create([
            'name' => 'Teknologi',
            'slug' => 'teknologi',
        ]);

        $this->expectException(QueryException::class);

        Category::create([
            'name' => 'Teknologi Lain',
            'slug' => 'teknologi',
        ]);
    }

    public function test_category_has_many_articles(): void
    {
        $category = Category::create([
            'name' => 'Teknologi',
            'slug' => 'teknologi',
        ]);

        $article = Article::create([
            'category_id' => $category->id,
            'title' => 'Judul Artikel',
            'slug' => 'judul-artikel',
            'excerpt' => 'Ringkasan artikel',
            'content' => 'Konten lengkap artikel',
            'status' => 'draft',
        ]);

        $this->assertTrue($category->articles->contains($article));
        $this->assertInstanceOf(Article::class, $category->articles->first());
    }
}
