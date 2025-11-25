<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cashback extends Model
{
    protected $table = 'cashback';

    protected $fillable = [
        'user_id',
        'vendor_id',
        'avail_cb', 
        'status' 
    ];

    public function vendor()
    {
        return $this->belongsTo(UserDetail::class, 'vendor_id');
    }

    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'user_id');
    }
}
