<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';
    
    protected $fillable = [
        'vendor_id',
        'product_id',
        'quantity',
        'status',
        'c_by',
    ];

    public $timestamps = true;

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function vendor()
    {
        return $this->belongsTo(UserDetail::class, 'vendor_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(UserDetail::class, 'c_by');
    }
    //edited
}
