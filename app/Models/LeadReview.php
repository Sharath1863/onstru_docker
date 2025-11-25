<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadReview extends Model
{
    protected $table = 'lead_review';
    protected $fillable = ['lead_id', 'c_by', 'review', 'stars', 'status'];

    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'c_by');
    }
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}
