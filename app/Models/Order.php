<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Order attributes
    protected $fillable = [
        'table_id',
        'is_takeaway',
        'status',
        'total_price',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

}
