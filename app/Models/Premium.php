<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Premium extends Model
{
    use HasFactory;

    protected $table = 'premium';
    protected $primaryKey = 'id';

    protected $fillable = [
        'premium_type',
        'caption',
        'image',
        'video',
        'status',
        'c_by',
    ];

    protected $attributes = [
        'status' => 'active',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function user() {
        return $this->belongsTo(UserDetail::class, 'created_by');
    }
}
