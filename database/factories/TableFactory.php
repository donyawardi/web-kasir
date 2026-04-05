<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Table;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Table>
 */
class TableFactory extends Factory
{
    protected $model = Table::class;

    public function definition(): array
    {
        static $counter = 1;

        return [
            'table_number' => 'meja ' . $counter++,
            'status' => 'available',
            'qr_code' => null,
        ];
    }
}
