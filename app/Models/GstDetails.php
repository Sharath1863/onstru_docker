<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GstDetails extends Model
{
    use HasFactory;

    protected $table = 'gst_details';

    protected $fillable = [
        'user_id',
        'gst_number',
        'gst_verify',
        'name',
        'business_legal',
        'contact_no',
        'email_id',
        'pan_no',
        'register_date',
        'gst_address',
        'nature_business',
        'annual_turnover',
        'status',
        'c_by',

    ];
}
