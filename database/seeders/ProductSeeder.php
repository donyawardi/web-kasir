<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::firstOrCreate([
            'name' => 'Ayam Bakar',
            'price' => 25000
        ]);

        Product::firstOrCreate([
            'name' => 'Nasi Putih',
            'price' => 5000
        ]);

        Product::firstOrCreate([
            'name' => 'Es Teh Manis',
            'price' => 7000
        ]);
    }
}