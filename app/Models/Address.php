<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';

    protected $fillable = [
        'first_name',
        'last_name',
        'primary_phone',
        'secondary_phone',
        'gst_billing',
        'billing_address',
        'billing_pincode',
        'billing_city',
        'billing_state',
        'billing_gst',
        'shipping_address',
        'shipping_pincode',
        'shipping_city',
        'shipping_state',
        'shipping_gst',
        'latitude',
        'longitude',
        'c_by',
        'status'
    ];
}
