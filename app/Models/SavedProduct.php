<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedProduct extends Model
{
    protected $table = 'saved_product';

    protected $fillable = [
        'product_id',
        'c_by',
        'status',
    ];
}
