<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Lead extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'service_type',
        'buildup_area',
        'budget',
        'start_date',
        'location',
        'repost',
        'description',
        'approval_status',
        'created_by',
        'status',
        'approval_status',
        'remark'
    ];

    protected $dates = [
        'start_date',
        'created_at',
        'updated_at'
    ];

    // protected $casts = [
    //     'start_date' => 'date',
    // ];

    // protected $appends = ['formatted_start_date'];

    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'created_by', 'id');
    }

    // public function getFormattedStartDateAttribute()
    // {
    //     return $this->start_date ? $this->start_date->format('d-m-Y') : null;
    // }

    public function locationRelation()
    {
        return $this->belongsTo(DropdownList::class, 'location');
    }

    public function serviceRelation()
    {
        return $this->belongsTo(DropdownList::class, 'service_type');
    }

    public function ownedLeads()
    {
        return $this->hasMany(OwnedLeads::class, 'lead_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(LeadReview::class, 'lead_id');
    }
}
