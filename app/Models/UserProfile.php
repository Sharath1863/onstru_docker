<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class UserProfile extends Model
{
    use HasFactory;
    protected $table = 'user_profile';

    protected $fillable = [
        'bank_name',
        'acct_holder',
        'acct_no',
        'acct_type',
        'ifsc_code',
        'branch_name',
        'project_category',
        'your_purpose',
        'services_offered',
        'projects_ongoing',
        'ongoing_details',
        'labours',
        'mobilization',
        'strength',
        'client_tele',
        'customer',
        'delivery_timeline',
        'location_catered',
        'professional_status',
        'education',
        'college',
        'designation',
        'employment_type',
        'experience',
        'projects_handled',
        'expertise',
        'current_ctc',
        'notice_period',
        'aadhar_no',
        'pan_no',
        'income_tax',
        'c_by',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'c_by');
    }

    public function projectCatRelation()
    {
        return $this->belongsTo(DropdownList::class, 'project_category');
    }

    public function purposeRelation()
    {
        return $this->belongsTo(DropdownList::class, 'your_purpose');
    }

    public function serviceOffRelation()
    {
        return $this->belongsTo(DropdownList::class, 'services_offered');
    }

    public function designationRelation()
    {
        return $this->belongsTo(DropdownList::class, 'designation');
    }
}
