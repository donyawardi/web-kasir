<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'total_price',
        'payment_status'
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
