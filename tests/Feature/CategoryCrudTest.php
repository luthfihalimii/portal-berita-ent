<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_guest_cannot_access_category_crud(): void
    {
        $this->get('/admin/categories')->assertRedirect('/login');
        $this->get('/admin/categories/create')->assertRedirect('/login');
        $this->post('/admin/categories', ['name' => 'Teknologi'])->assertRedirect('/login');
    }

    public function test_admin_can_view_category_list(): void
    {
        Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);

        $response = $this->actingAs($this->admin)->get('/admin/categories');

        $response->assertStatus(200);
        $response->assertSee('Teknologi');
        $response->assertSee('teknologi');
    }

    public function test_admin_can_create_category(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/categories', [
            'name' => 'Kesehatan & Sains',
            'slug' => 'kesehatan-sains',
        ]);

        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', [
            'name' => 'Kesehatan & Sains',
            'slug' => 'kesehatan-sains',
        ]);
    }

    public function test_category_creation_fails_with_duplicate_slug(): void
    {
        Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);

        $response = $this->actingAs($this->admin)
            ->from('/admin/categories/create')
            ->post('/admin/categories', [
                'name' => 'Teknologi Baru',
                'slug' => 'teknologi',
            ]);

        $response->assertRedirect('/admin/categories/create');
        $response->assertSessionHasErrors('slug');
    }

    public function test_admin_can_update_category(): void
    {
        $category = Category::create(['name' => 'Olahraga', 'slug' => 'olahraga']);

        $response = $this->actingAs($this->admin)->put("/admin/categories/{$category->id}", [
            'name' => 'Olahraga Dunia',
            'slug' => 'olahraga-dunia',
        ]);

        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Olahraga Dunia',
            'slug' => 'olahraga-dunia',
        ]);
    }

    public function test_admin_can_delete_category(): void
    {
        $category = Category::create(['name' => 'Otomotif', 'slug' => 'otomotif']);

        $response = $this->actingAs($this->admin)->delete("/admin/categories/{$category->id}");

        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
