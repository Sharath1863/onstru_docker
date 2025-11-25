<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'review';
    protected $fillable = ['product_id', 'c_by', 'review', 'stars'];

    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'c_by');
    }
    public function product()
    {
        return $this->belongsTo(Products::class, 'Product_id');
    }
}
