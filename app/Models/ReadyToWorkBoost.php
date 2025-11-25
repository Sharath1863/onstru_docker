<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadyToWorkBoost extends Model
{
    protected $table = 'readytowork_boost';

    protected $fillable = [
        'user_id',
        'days',
        'amount',
        'click',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'user_id');
    }
}
