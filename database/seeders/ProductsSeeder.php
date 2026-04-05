<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Nasi Goreng Spesial', 'price' => 22000, 'description' => 'Nasi goreng dengan sambal spesial'],
            ['name' => 'Mie Goreng Ayam', 'price' => 18000, 'description' => 'Mie goreng dengan suwiran ayam'],
            ['name' => 'Es Teh Manis', 'price' => 5000, 'description' => 'Es teh manis segar'],
            ['name' => 'Es Jeruk', 'price' => 7000, 'description' => 'Es jeruk perasan'],
            ['name' => 'Ayam Goreng', 'price' => 30000, 'description' => 'Ayam goreng renyah']
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(['name' => $p['name']], $p);
        }
    }
}
