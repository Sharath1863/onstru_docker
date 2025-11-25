<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class SavedJobs extends Model
{
    use HasFactory;
    protected $table = 'saved_jobs';

    protected $fillable = [
        'jobs_id',
        'c_by',
        'status',
    ];
}
