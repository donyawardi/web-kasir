<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function table()
    {
        return $this->belongsTo(Table::class);
    }

}
