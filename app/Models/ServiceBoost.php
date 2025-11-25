<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceBoost extends Model
{
    protected $table = 'service_boosts';

    protected $fillable = [
        'service_id',
        'type',
        'amount',
        'click',
        'status',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
