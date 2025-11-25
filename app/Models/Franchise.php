<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;



class Franchise extends Authenticatable
{
    //
    use HasApiTokens, HasFactory;
    protected $table = 'franchise';
    protected $fillable = [
        'name',
        'mobile',
        'mail_id',
        'type_of',
        'password',
        'hash_password',
        'otp',
        'otp_status',
        'mob_token',
        'code',
        'Contractor',
        'Vendor',
        'Consultant',
        'Technical',
        'Non-Technical',
        'Consumer'
    ];

        public function payment()
    {
        return $this->hasOne(FranchiseWallet::class, 'user_id', 'id');
    }
}
