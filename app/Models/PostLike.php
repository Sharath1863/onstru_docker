<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    protected $table = 'post_like';

    protected $fillable = [
        'post_id',
        'user_id',
        'status'
    ];
    // PostLike.php
    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'user_id');
    }

    public function post_data()
    {
        return $this->hasOne(Posts::class, 'id', 'post_id');
    }
}
