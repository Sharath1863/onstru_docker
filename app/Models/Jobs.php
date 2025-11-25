<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Jobs extends Model
{
      Use HasFactory;

    protected $table = 'job_post';

    protected $fillable = [
        'created_by',
        'title',
        'category',
        'shift',
        'salary',
        'description',
        'location',
        'sublocality',
        'skills',
        'qualification',
        'highlighted',
        'click',
        'wallet',
        'experience',
        'benfit',
        'no_of_openings',
        'approvalstatus',
        'status',
        
    ];

    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'created_by');
    }

    public function job_cat()
    {
        return $this->belongsTo(DropdownList::class, 'category');
    }
    public function job_loc()
    {
        return $this->belongsTo(DropdownList::class, 'location');
    }

    public function locationRelation()
    {
        return $this->belongsTo(DropdownList::class, 'location');
    }

    public function categoryRelation()
    {
        return $this->belongsTo(DropdownList::class, 'category');
    }

    public function boosts()
    {
        return $this->hasMany(JobBoost::class, 'job_id');
    }

    public function clicks()
    {
        return $this->hasMany(Click::class, 'category_id')
            ->where('category', 'Jobs');
    }

    // scope to order jobs based on highlight and user location
    public function scopeJoblist($query, $userLocation)
    {
        return $query->orderByRaw("
        CASE 
            WHEN highlighted = '1' AND location = ? THEN 1
            WHEN highlighted = '1' THEN 2
            WHEN location = ? THEN 3
            ELSE 4
        END,
        created_at DESC
    ", [$userLocation, $userLocation]);
    }
  
}
