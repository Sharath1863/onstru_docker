<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Save_post extends Model
{
    protected $table = 'save_post';

    protected $fillable = [
        'post_id',
        'post_type',
        'user_id',
        'message',
        'status',
    ];

    // PostLike.php
    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'user_id');
    }

    // Single like by a specific user
    public function likedByAuth()
    {
        return $this->hasOne(Save_post::class, 'post_id')
            ->where('user_id', Auth::id());
    }

    public function post_data()
    {
        return $this->hasOne(Posts::class, 'id', 'post_id');
    }
}
