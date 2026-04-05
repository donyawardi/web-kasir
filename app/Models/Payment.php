<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'status', 'qr_code_url', 'payment_method', 'cash_received', 'snap_token', 'transaction_id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
