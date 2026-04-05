<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

class SampleProductsAndTablesSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached permissions
        if (app()->bound(\Spatie\Permission\PermissionRegistrar::class)) {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        }

        // create minimal permissions and roles (idempotent)
        $perms = ['manage products', 'view dashboard'];
        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $kasirRole = Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($perms);

        // create admin and kasir users (idempotent)
        $admin = User::firstOrCreate(
            ['email' => 'admin@pecelayam.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $kasir = User::firstOrCreate(
            ['email' => 'kasir@pecelayam.com'],
            [
                'name' => 'Kasir',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole($adminRole->name);
        $kasir->assignRole($kasirRole->name);

        // sample products
        $products = [
            ['name' => 'Nasi Goreng', 'price' => 20000, 'available' => true, 'description' => 'Nasi goreng spesial'],
            ['name' => 'Mie Goreng', 'price' => 18000, 'available' => true, 'description' => 'Mie goreng enak'],
            ['name' => 'Es Teh', 'price' => 5000, 'available' => true, 'description' => 'Es teh manis']
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(['name' => $p['name']], $p);
        }

        // sample tables (idempotent) and generate QR if missing
        for ($i = 1; $i <= 6; $i++) {
            $tableNumber = (string) $i; // keep simple numeric identifier
            $table = Table::firstOrCreate(['table_number' => $tableNumber], ['status' => 'available']);

            if (empty($table->qr_code)) {
                try {
                    $qrCodeData = route('order.show', ['table' => $table->id]);

                    $renderer = new ImageRenderer(new RendererStyle(256), new SvgImageBackEnd());
                    $writer = new Writer($renderer);
                    $qrCodeSvg = $writer->writeString($qrCodeData);
                    $qrCodeBase64 = base64_encode($qrCodeSvg);

                    $table->qr_code = 'data:image/svg+xml;base64,' . $qrCodeBase64;
                    $table->save();
                } catch (\Exception $e) {
                    // if route() or QR generation fails in certain environments, skip silently
                }
            }
        }
    }
}
