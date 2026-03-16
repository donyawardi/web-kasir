<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'), // ganti dengan password kuat di production
            ]
        );

        // Assign role admin
        $admin->assignRole('admin');

        // Tambahkan kasir kalau perlu
        $kasir = User::firstOrCreate(
            ['email' => 'kasir@example.com'],
            [
                'name' => 'Kasir',
                'password' => Hash::make('password'),
            ]
        );

        $kasir->assignRole('kasir');
    }
}