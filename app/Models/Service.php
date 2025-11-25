<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'created_by',
        'title',
        'service_type',
        'price_per_sq_ft',
        'location',
        'image',
        'sub_images',
        'video',
        'wallet',
        'click',
        'approvalstatus',
        'status',
        'description',
        'highlighted',
    ];

    // Relation for creator
    public function creator()
    {
        return $this->belongsTo(UserDetail::class, 'created_by');
    }

    // Relation for service type
    public function serviceType()
    {
        return $this->belongsTo(DropdownList::class, 'service_type');
    }

    public function locationRelation()
    {
        return $this->belongsTo(DropdownList::class, 'location');
    }

    public function boosts()
    {
        return $this->hasMany(ServiceBoost::class, 'service_id');
    }

    public function reviews()
    {
        return $this->hasMany(ServiceReview::class, 'service_id');
    }

    public function clicks()
    {
        return $this->hasMany(Click::class, 'category_id')
            ->where('category', 'Service');
    }

    // scope to order jobs based on highlight and user location
    public function scopeServicelist($query, $userLocation)
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
