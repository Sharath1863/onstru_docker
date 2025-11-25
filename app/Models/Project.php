<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    // Table name (optional if it matches plural form)
    protected $table = 'projects';

    protected $fillable = [
        'created_by',
        'title',
        'description',
        'location',
        'start_date',
        'end_date',
        'key_outcomes',
        'prjt_budget',
        'job_role',
        'responsibilities',
        'amount',
        'image',
        'sub_image',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(UserDetail::class, 'created_by');
    }

    public function locationDetails()
    {
        return $this->belongsTo(DropdownList::class, 'location');
    }
}
