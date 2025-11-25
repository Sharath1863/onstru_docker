<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseWallet extends Model
{
    //
    protected $table = 'frn_wallet';
    protected $fillable = [
        'user_id',
        'type',
        'payment_id',
        'payment_type',
        'payment_status',
        'amount',
    ];
}
