<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'category',
        'category_id',
        'reciever',
        'title',
        'body',
        'seen',
        'remainder',
        'status',
        'c_by',
    ];

    public function sender()
    {
        return $this->belongsTo(UserDetail::class, 'c_by');
    }

    public function receiver()
    {
        return $this->belongsTo(UserDetail::class, 'reciever');
    }

    public function order()
    {
        return $this->belongsTo(Orders::class, 'category_id');
    }

    public function post()
    {
        return $this->belongsTo(Posts::class, 'category_id');
    }

    public function job()
    {
        return $this->belongsTo(Jobs::class, 'category_id');
    }

    public function leads()
    {
        return $this->belongsTo(Lead::class, 'category_id');
    }

    public function comment()
    {
        return $this->belongsTo(CommentList::class, 'category_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'category_id');
    }
}
