<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Hapus semua user
        // DB::table('users')->delete();

        // // Buat user admin
        // User::create([
        //     'name' => 'Admin',
        //     'email' => 'admin@example.com',
        //     'password' => bcrypt('password'), // Ganti password sebelum produksi
        //     'role' => 'admin',
        // ]);

        // // Buat user kasir
        // User::create([
        //     'name' => 'Kasir',
        //     'email' => 'kasir@example.com',
        //     'password' => bcrypt('password'),
        //     'role' => 'kasir',
        // ]);

        // Jalankan seeder lainnya (kalau perlu)
        $this->call([
            RoleAndPermissionSeeder::class,
            KasirSeeder::class,
            SampleProductsAndTablesSeeder::class,
            // tambahkan seeder lain kalau ada
        ]);
    }
}
