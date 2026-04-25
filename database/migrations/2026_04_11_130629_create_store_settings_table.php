<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default: store is open, with operating hours
        DB::table('store_settings')->insert([
            ['key' => 'is_open',     'value' => '1',    'created_at' => now(), 'updated_at' => now()],
            ['key' => 'open_time',   'value' => '08:00','created_at' => now(), 'updated_at' => now()],
            ['key' => 'close_time',  'value' => '22:00','created_at' => now(), 'updated_at' => now()],
            ['key' => 'closed_note', 'value' => 'Toko sedang tutup. Silahkan datang kembali sesuai jam operasional kami.', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
