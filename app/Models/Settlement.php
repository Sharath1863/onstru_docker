<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    //
    protected $table = 'settlement';

    protected $fillable = [
        'from_date',
        'to_date',
        'franchise_id',
        'amount', 
    ];
  
}
