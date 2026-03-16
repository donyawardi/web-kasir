<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@pecelayam.com',
            'password' => Hash::make('password'),
        ]);

        $kasir = User::create([
            'name' => 'Kasir',
            'email' => 'kasir@pecelayam.com',
            'password' => Hash::make('password'),
        ]);

        $admin->assignRole('admin');
        $kasir->assignRole('kasir');
    }
}
