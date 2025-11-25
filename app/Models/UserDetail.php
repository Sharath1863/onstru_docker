<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // ✅ Use this instead of Model
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;

class UserDetail extends Authenticatable
{
    // use HasApiTokens, HasFactory, Searchable;
    use HasApiTokens, HasFactory;

    protected $table = 'user_detail';

    protected $fillable = [
        'name',
        'you_are',
        'as_a',
        'type_of',
        'address',
        'location',
        'user_name',
        'profile_img',
        'bio',
        'gender',
        'email',
        'number',
        'balance',
        'badge',
        'password',
        'otp',
        'otp_status',
        'hash_password',
        'location',
        'open_chat',
        'web_token',
        'mob_token',
        'ref_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'type_of' => $this->type_of_names,
        ];
    }

    public function jobs()
    {
        return $this->hasMany(Jobs::class, 'created_by');
    }

    public function gst()
    {
        return $this->hasOne(GstDetails::class, 'user_id');
    }

    public function user_location()
    {
        return $this->belongsTo(DropdownList::class, 'location', 'id')
            ->where('dropdown_id', 1); // only locations, not categories
    }

    public function cashbacks()
    {
        return $this->hasMany(Cashback::class, 'vendor_id');
    }

    public function orders()
    {
        return $this->hasMany(Orders::class, 'user_id', 'id');
    }

    public function following()
    {
        // people *I* follow
        return $this->belongsToMany(UserDetail::class, 'follows', 'follower_id', 'following_id')
            ->withTimestamps();
    }

    public function followers()
    {
        // people who follow *me*
        return $this->belongsToMany(UserDetail::class, 'follows', 'following_id', 'follower_id')
            ->withTimestamps();
    }

    public function isFollowing(UserDetail $user): bool
    {
        return $this->following()->where('following_id', $user->getKey())->exists();
    }

    public function getTypeOfNamesAttribute()
    {
        $ids = is_array($this->type_of)
            ? $this->type_of
            : ($this->type_of ? explode(',', $this->type_of) : []);

        return DropdownList::whereIn('id', $ids)->pluck('value')->toArray();
    }

    public function typeOfValue()
    {
        return $this->belongsTo(DropdownList::class, 'type_of');
    }

    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class, 'c_by');
    }

    public function userProfile_search()
    {
        return $this->hasOne(UserProfile::class, 'c_by', 'id');
    }

    public function franchise()
    {
        return $this->belongsTo(Franchise::class, 'ref_id', 'code');

    }

    public function profile_report()
    {
        return $this->hasOne(Report::class, 'f_id')
            ->where('type', 'user')
            ->where('user_id', Auth::id());
    }

    public function run(): void
    {
        // Create 20 fake users
        UserDetail::factory()->count(1)->create();
    }
}
