<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $table = 'badges';

    protected $fillable = [
        'badge',
        'amount',
        'created_by',
        'status',

    ];

    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'created_by');
    }
}
