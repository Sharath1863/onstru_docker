<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Click extends Model
{
    protected $table = 'clicks';
    
    protected $fillable = [
        'category',
        'category_id',
        'boost_id',
        'status',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'created_by');
    }
}
