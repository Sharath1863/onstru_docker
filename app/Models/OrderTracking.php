<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    protected $table = 'order_tracking';

    protected $fillable = [
        'order_id',
        'product_id',
        'tracking_id',
        'qty',
        'tracking',
        'ord_lat',
        'ord_long',
        'vendor_invoice',
        'otp',
        'status',
        'created_by',
    ];

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id', 'order_id');
    }

    public function OrderProducts()
    {
        return $this->hasMany(OrderProducts::class, 'order_id', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
