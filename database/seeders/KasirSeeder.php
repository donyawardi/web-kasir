<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class KasirSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'kasir@example.com'],
            [
                'name' => 'Kasir',
                'password' => bcrypt('kasir123'),
                'email_verified_at' => now(),
            ]
        );

        if (! $user->hasRole('kasir')) {
            $user->assignRole('kasir');
        }
    }
}
