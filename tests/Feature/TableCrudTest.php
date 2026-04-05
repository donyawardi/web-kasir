<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Tests\TestCase;
use App\Models\User;
use App\Models\Table;

class TableCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    }

    public function test_admin_can_create_table_and_qr_is_generated()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.tables.store'), [
            'table_number' => 'meja 99',
            'status' => 'available',
        ]);

        $response->assertRedirect(route('admin.tables.index'));
        $this->assertDatabaseHas('tables', ['table_number' => 'meja 99']);

        $table = Table::where('table_number', 'meja 99')->first();
        $this->assertNotNull($table);
        $this->assertNotEmpty($table->qr_code, 'QR code should be generated and saved');
    }

    public function test_public_can_view_table_qr_page()
    {
        $table = Table::factory()->create(['qr_code' => null]);

        $response = $this->get(route('tables.qr', $table->id));

        $response->assertStatus(200);
        $response->assertSee('Scan QR', 'QR page should be rendered');
    }
}
