<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'service_requests';

    // Mass assignable fields
    protected $fillable = [
        'c_by',
        'service_id',
        'service_type',
        'buildup_area',
        'budget',
        'start_date',
        'location',
        'phone_number',
        'description',
        'status',
    ];

    // Cast attributes
    protected $casts = [
        'start_date' => 'date',
        'budget' => 'decimal:2',
        'buildup_area' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The user who made the service request.
     */
    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'c_by'); // Fixed: using 'c_by' instead of 'user_id'
    }

    /**
     * The service being requested.
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id'); // Fixed: using ServiceModel instead of Service
    }

    public function serviceType()
    {
        return $this->belongsTo(DropdownList::class, 'service_type');
    }

    public function locationRelation()
    {
        return $this->belongsTo(DropdownList::class, 'location');
    }
}