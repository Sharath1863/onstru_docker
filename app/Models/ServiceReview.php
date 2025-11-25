<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceReview extends Model
{
    protected $table = 'service_review';
    protected $fillable = ['service_id', 'c_by', 'review', 'stars'];

    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'c_by');
    }
    public function service()
    {
        return $this->belongsTo(Service::class, 'Service_id');
    }
}
