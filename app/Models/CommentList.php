<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentList extends Model
{
    protected $table = 'comment_list';

    protected $fillable = [
        'post_id',
        'user_id',
        'comment',
        'status',
    ];
    // Relation: Comment belongs to a Post
    public function post()
    {
        return $this->belongsTo(Posts::class, 'post_id');
    }
}
