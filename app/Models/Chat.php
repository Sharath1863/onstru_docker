<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chat';

    protected $fillable = ['sender', 'receiver', 'message', 'type'];

    public function senderUser()
    {
        return $this->belongsTo(UserDetail::class, 'sender');
    }

    public function receiverUser()
    {
        return $this->belongsTo(UserDetail::class, 'receiver');
    }
}
