<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBoost extends Model
{
    protected $table = 'product_boosts';

    protected $fillable = [
        'product_id',
        'type',
        'amount',
        'click',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
