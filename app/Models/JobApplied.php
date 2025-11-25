<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobApplied extends Model
{
    use HasFactory;
    protected $table = 'job_apply';

    protected $fillable = [
        'created_by',
        'job_id',
        'skills',
        'qualification',
        'experience',
        'location',
        'current_salary',
        'expected_salary',
        'notes',
        'resume',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'created_by');
    }
    public function job()
    {
        return $this->belongsTo(Jobs::class, 'job_id');
    }
    public function locationRelation()
    {
        return $this->belongsTo(DropdownList::class, 'location');
    }
}