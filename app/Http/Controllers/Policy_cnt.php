<?php

namespace App\Http\Controllers;

use App\Models\GstDetails;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Policy_cnt extends Controller
{
    public function terms()
    {
        return view('policy.terms');
    }

    public function privacy()
    {
        return view('policy.privacy');
    }

    public function refund()
    {
        return view('policy.refund_cancel');
    }

    public function delete_my_account()
    {
        return view('policy.delete_my_account');
    }

    public function contact_us()
    {
        return view('policy.contactus');
    }
}
