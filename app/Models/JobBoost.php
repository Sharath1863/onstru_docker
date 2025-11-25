<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobBoost extends Model
{
    use HasFactory;

    protected $table = 'job_boosts';

    protected $fillable = [
        'job_id',
        'from',
        'to',
        'amount',
        'day_charge',
        'click',
        'status',
    ];

    public function job()
    {
        return $this->belongsTo(Jobs::class, 'job_id');
    }
}
