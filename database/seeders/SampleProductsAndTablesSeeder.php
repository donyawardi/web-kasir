<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Table;

class SampleProductsAndTablesSeeder extends Seeder
{
    public function run(): void
    {
        // sample products
        $products = [
            ['name' => 'Nasi Goreng', 'price' => 20000, 'stock' => 50, 'description' => 'Nasi goreng spesial'],
            ['name' => 'Mie Goreng', 'price' => 18000, 'stock' => 40, 'description' => 'Mie goreng enak'],
            ['name' => 'Es Teh', 'price' => 5000, 'stock' => 100, 'description' => 'Es teh manis']
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(['name' => $p['name']], $p);
        }

        // sample tables
        for ($i = 1; $i <= 6; $i++) {
            Table::firstOrCreate(['table_number' => $i], ['status' => 'available']);
        }
    }
}
