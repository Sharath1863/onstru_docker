<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    protected $table = 'hashtag';

    protected $fillable = [
        'tag_name',
        'usage_count',
        'status',
        'created_at',
        'updated_at'
    ];
}
