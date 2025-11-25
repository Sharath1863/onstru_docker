<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    protected $table = 'report';

    use HasFactory;

    protected $fillable = [
        'type',
        'f_id',
        'user_id',
        'message',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'f_id');
    }

    public function post()
    {
        return $this->belongsTo(Posts::class, 'f_id');
    }

    public function reporter()
    {
        return $this->belongsTo(UserDetail::class, 'user_id');
    }
}
