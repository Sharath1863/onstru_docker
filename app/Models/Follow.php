<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    protected $table = 'follows';

    protected $fillable = [
        'follower_id',
        'following_id',
    ];

    public function followingUser()
    {
        return $this->belongsTo(UserDetail::class, 'following_id');
    }

    public function followerUser()
    {
        return $this->belongsTo(UserDetail::class, 'follower_id');
    }
}
