<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
        // pastikan role dan permission ter-seed sebelum test
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    }

    public function test_admin_can_see_product_list()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $product = Product::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertStatus(200)
            ->assertSee($product->name);
    }

    public function test_admin_can_create_product()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'price' => 12345,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function test_admin_can_update_product()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $product = Product::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name' => 'New Name',
            'price' => $product->price,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['name' => 'New Name']);
    }

    public function test_admin_can_delete_product()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
