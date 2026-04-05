<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\DB;

// Clear existing products
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
Product::truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

$menus = [
    // CUMI
    ['name' => 'Cumi goreng tepung', 'price' => 25000, 'category' => 'Cumi'],
    ['name' => 'Cumi saus tiram', 'price' => 25000, 'category' => 'Cumi'],
    ['name' => 'Cumi saus padang', 'price' => 25000, 'category' => 'Cumi'],
    ['name' => 'Cumi mentega', 'price' => 25000, 'category' => 'Cumi'],
    ['name' => 'Cumi saus asam manis', 'price' => 25000, 'category' => 'Cumi'],
    // UDANG
    ['name' => 'Udang goreng tepung', 'price' => 25000, 'category' => 'Udang'],
    ['name' => 'Udang saus tiram', 'price' => 25000, 'category' => 'Udang'],
    ['name' => 'Udang saus padang', 'price' => 25000, 'category' => 'Udang'],
    ['name' => 'Udang mentega', 'price' => 25000, 'category' => 'Udang'],
    ['name' => 'Udang saus asam manis', 'price' => 25000, 'category' => 'Udang'],
    // NASI
    ['name' => 'Nasi biasa', 'price' => 5000, 'category' => 'Nasi'],
    ['name' => 'Nasi uduk', 'price' => 6000, 'category' => 'Nasi'],
    // MENU UTAMA
    ['name' => 'Soto ayam kampung', 'price' => 15000, 'category' => 'Menu Utama'],
    ['name' => 'Pecel lele', 'price' => 10000, 'category' => 'Menu Utama'],
    ['name' => 'Lele bakar', 'price' => 15000, 'category' => 'Menu Utama'],
    ['name' => 'Ayam goreng', 'price' => 16000, 'category' => 'Menu Utama'],
    ['name' => 'Ayam bakar', 'price' => 17000, 'category' => 'Menu Utama'],
    ['name' => 'Bebek goreng', 'price' => 25000, 'category' => 'Menu Utama'],
    ['name' => 'Bebek bakar', 'price' => 26000, 'category' => 'Menu Utama'],
    // AYAM SAUS
    ['name' => 'Ayam saus tiram', 'price' => 17000, 'category' => 'Ayam Saus'],
    ['name' => 'Ayam saus padang', 'price' => 17000, 'category' => 'Ayam Saus'],
    ['name' => 'Ayam saus mentega', 'price' => 17000, 'category' => 'Ayam Saus'],
    ['name' => 'Ayam saus asam manis', 'price' => 17000, 'category' => 'Ayam Saus'],
    // BEBEK SAUS
    ['name' => 'Bebek saus tiram', 'price' => 26000, 'category' => 'Bebek Saus'],
    ['name' => 'Bebek saus padang', 'price' => 26000, 'category' => 'Bebek Saus'],
    ['name' => 'Bebek saus mentega', 'price' => 26000, 'category' => 'Bebek Saus'],
    ['name' => 'Bebek saus asam manis', 'price' => 26000, 'category' => 'Bebek Saus'],
    // SAYUR
    ['name' => 'Cah kangkung polos', 'price' => 10000, 'category' => 'Sayur'],
    ['name' => 'Cah kangkung udang', 'price' => 35000, 'category' => 'Sayur'],
    ['name' => 'Cah kangkung cumi', 'price' => 35000, 'category' => 'Sayur'],
    // MINUMAN
    ['name' => 'Es teh manis', 'price' => 5000, 'category' => 'Minuman'],
    ['name' => 'Es jeruk', 'price' => 5000, 'category' => 'Minuman'],
    ['name' => 'Aqua', 'price' => 4000, 'category' => 'Minuman'],
];

foreach ($menus as $m) {
    Product::create(array_merge($m, ['available' => true]));
}

echo Product::count() . " produk berhasil dimasukkan.\n";
