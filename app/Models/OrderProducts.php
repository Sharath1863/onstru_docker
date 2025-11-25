<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProducts extends Model
{
    protected $table = 'order_products';

    protected $fillable = [
        'order_id',
        'vendor_order',
        'product_id',
        'base_price',
        'cashback',
        'shipping',
        'tax',
        'margin',
        'quantity',
        'bal_qty',
        'tracking',
        'status',
        'settlement_status',
        'accepted_at',
        'shipped_at',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo(UserDetail::class, 'vendor_id', 'id');
        // return $this->product ? $this->product->vendor() : null;
    }

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id', 'order_id');
    }
}
