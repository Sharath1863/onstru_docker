<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    protected $table = 'hubs';

    protected $fillable = [
        'vendor_id',
        'location_id',
        'hub_name',
        'address',
        'city',
        'state',
        'pincode',
        'latitude',
        'longitude',
        'status',
    ];

    protected $casts = [
        'address_components' => 'array',
    ];

    
}
