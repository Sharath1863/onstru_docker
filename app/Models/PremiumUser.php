<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PremiumUser extends Model
{
    protected $table = 'premium_users';
    
    protected $fillable = [
        'user_id',
        'price',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'user_id');
    }
}
