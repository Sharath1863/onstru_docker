<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Posts extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'file_type',
        'category',
        'category_id',
        'file',
        'caption',
        'location',
        'sense',
        'value',
        'like_cnt',
        'com_cnt',
        'status',
        'created_by',
    ];

    protected $casts = [
        'file' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'created_by');
    }

    // Single like by a specific user
    public function likedByAuth()
    {
        return $this->hasOne(PostLike::class, 'post_id')
            ->where('user_id', Auth::id());
    }

    public function post_save()
    {
        return $this->hasOne(Save_post::class, 'post_id')
            ->where('user_id', Auth::id());
    }

    public function post_report()
    {
        return $this->hasOne(Report::class, 'f_id')
            ->where('type', 'post')
            ->where('user_id', Auth::id());
    }
}
