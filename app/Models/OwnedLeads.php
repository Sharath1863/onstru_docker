<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnedLeads extends Model
{
    protected $table = 'owned_leads';

    protected $fillable = [
        'lead_id',
        'created_by',
        'transaction_status',
        'status'
    ];

    public function lead()
    {
        return $this->hasOne(Lead::class, 'id', 'lead_id');
    }

    public function user()
    {
        return $this->hasOne(UserDetail::class, 'id', 'created_by');
    }
}
