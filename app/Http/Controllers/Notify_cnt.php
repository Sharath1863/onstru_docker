<?php

namespace App\Http\Controllers;

use App\Models\GstDetails;
use App\Models\Notification;
use App\Models\Posts;
use Illuminate\Support\Facades\Auth;

// google fcm

class Notify_cnt extends Controller
{
    public function notification()
    {
        $notifications = Notification::with(['order', 'post'])->where('reciever', Auth::id())->latest()->get();
        $notificationCount = Notification::where('reciever', Auth::id())->where('status', 'active')->count();
        Notification::where('reciever', Auth::id())->update(['status' => 'inactive', 'seen' => 1]);
        $gstverified = GstDetails::where('user_id', Auth::id())->where('gst_verify', 'yes')->first();
        $post_count = Posts::where('created_by', Auth::id())->where('status', 'active')->count();
        if ($gstverified === null) {
            $gstverified = 'no';
        } else {
            $gstverified = 'yes';
        }

        return view('notification.index', compact('notifications', 'notificationCount', 'gstverified', 'post_count'));
    }
}
