<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'order_id',
        'user_id',
        'address_id',
        'cashback',
        'total',
        'transaction_status',
        'status',
    ];

    public function products()
    {
        return $this->hasMany(OrderProducts::class, 'order_id', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'user_id', 'id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }
}
