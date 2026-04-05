<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus semua produk lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $menus = [
            // CUMI
            ['name' => 'Cumi Goreng Tepung',    'category' => 'Cumi',        'price' => 25000],
            ['name' => 'Cumi Saus Tiram',        'category' => 'Cumi',        'price' => 25000],
            ['name' => 'Cumi Saus Padang',       'category' => 'Cumi',        'price' => 25000],
            ['name' => 'Cumi Mentega',           'category' => 'Cumi',        'price' => 25000],
            ['name' => 'Cumi Saus Asam Manis',   'category' => 'Cumi',        'price' => 25000],
            // UDANG
            ['name' => 'Udang Goreng Tepung',    'category' => 'Udang',       'price' => 25000],
            ['name' => 'Udang Saus Tiram',       'category' => 'Udang',       'price' => 25000],
            ['name' => 'Udang Saus Padang',      'category' => 'Udang',       'price' => 25000],
            ['name' => 'Udang Mentega',          'category' => 'Udang',       'price' => 25000],
            ['name' => 'Udang Saus Asam Manis',  'category' => 'Udang',       'price' => 25000],
            // NASI
            ['name' => 'Nasi Biasa',             'category' => 'Nasi',        'price' => 5000],
            ['name' => 'Nasi Uduk',              'category' => 'Nasi',        'price' => 6000],
            // MENU UTAMA
            ['name' => 'Soto Ayam Kampung',      'category' => 'Menu Utama',  'price' => 15000],
            ['name' => 'Pecel Lele',             'category' => 'Menu Utama',  'price' => 10000],
            ['name' => 'Lele Bakar',             'category' => 'Menu Utama',  'price' => 15000],
            ['name' => 'Ayam Goreng',            'category' => 'Menu Utama',  'price' => 16000],
            ['name' => 'Ayam Bakar',             'category' => 'Menu Utama',  'price' => 17000],
            ['name' => 'Bebek Goreng',           'category' => 'Menu Utama',  'price' => 25000],
            ['name' => 'Bebek Bakar',            'category' => 'Menu Utama',  'price' => 26000],
            // AYAM SAUS
            ['name' => 'Ayam Saus Tiram',        'category' => 'Ayam Saus',   'price' => 17000],
            ['name' => 'Ayam Saus Padang',       'category' => 'Ayam Saus',   'price' => 17000],
            ['name' => 'Ayam Saus Mentega',      'category' => 'Ayam Saus',   'price' => 17000],
            ['name' => 'Ayam Saus Asam Manis',   'category' => 'Ayam Saus',   'price' => 17000],
            // BEBEK SAUS
            ['name' => 'Bebek Saus Tiram',       'category' => 'Bebek Saus',  'price' => 26000],
            ['name' => 'Bebek Saus Padang',      'category' => 'Bebek Saus',  'price' => 26000],
            ['name' => 'Bebek Saus Mentega',     'category' => 'Bebek Saus',  'price' => 26000],
            ['name' => 'Bebek Saus Asam Manis',  'category' => 'Bebek Saus',  'price' => 26000],
            // SAYUR
            ['name' => 'Cah Kangkung Polos',     'category' => 'Sayur',       'price' => 10000],
            ['name' => 'Cah Kangkung Udang',     'category' => 'Sayur',       'price' => 35000],
            ['name' => 'Cah Kangkung Cumi',      'category' => 'Sayur',       'price' => 35000],
            // MINUMAN
            ['name' => 'Es Teh Manis',           'category' => 'Minuman',     'price' => 5000],
            ['name' => 'Es Jeruk',               'category' => 'Minuman',     'price' => 5000],
            ['name' => 'Aqua',                   'category' => 'Minuman',     'price' => 5000],
        ];

        foreach ($menus as $menu) {
            Product::create(array_merge($menu, ['available' => true]));
        }
    }
}