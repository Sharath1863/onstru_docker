<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadyToWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'job_titles',
        'work_types',
        'locations',
        'experience',
        'days',
        'amount',
        'resume_path',
        'expiry',
        'status',
    ];

    protected $casts = [
        'job_titles' => 'array',
        'work_types' => 'array',
        'locations' => 'array',

    ];

    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'created_by');
    }

    public function getJobTitlesTextAttribute()
    {
        return implode(', ', $this->job_titles ?? []);
    }

    public function getWorkTypesTextAttribute()
    {
        return implode(', ', $this->work_types ?? []);
    }

    public function getLocationsTextAttribute()
    {
        return implode(', ', $this->locations ?? []);
    }

    public function getLocationNamesAttribute()
    {
        return DropdownList::whereIn('id', $this->locations ?? [])
            ->pluck('value') // assuming column is `name`
            ->toArray();
    }
    public function locationDetails()
    {
        return $this->belongsTo(DropdownList::class, 'location');
    }
}
