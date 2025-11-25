<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table = 'wallet';

    protected $fillable = [
        'user_id',
        'order_id',
        'type',
        'amount',
        'payment_method',
        'payment_id',
        'payment_status',
    ];

   // app/Models/Wallet.php
        public function user()
        {
            return $this->belongsTo(UserDetail::class, 'user_id', 'id');
        }
}
