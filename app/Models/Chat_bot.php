<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat_bot extends Model
{
    protected $table = 'chat_bot';

    protected $fillable = ['c_by', 'amount', 'token', 'used'];

    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'c_by');
    }
}
