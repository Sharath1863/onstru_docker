<?php

namespace App\Http\Controllers;

use App\Models\DropdownList;
use App\Models\GstDetails;
use App\Models\Jobs;
use App\Models\Notification;
use App\Models\PostLike;
use App\Models\Posts;
use App\Models\Products;
use App\Models\Save_post;
use App\Models\SavedJobs;
use App\Models\SavedProduct;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class Settings_cnt extends Controller
{
    public function settings()
    {
        $id = Auth::id();

        $locations = DropdownList::where('dropdown_id', 1)->pluck('value', 'id');
        $savedProductIds = SavedProduct::where('c_by', $id)->pluck('product_id')->toArray();
        $savedJobIds = SavedJobs::where('c_by', $id)->pluck('jobs_id')->toArray();
        $savedpost = Save_post::where('user_id', $id)->pluck('post_id')->toArray();
        $post = Posts::with(['user', 'likedByAuth', 'post_report'])
            ->where('status', 'active')
            ->whereIn('id', $savedpost)
            ->latest()
            ->get();
        $post->transform(function ($pt) {
            $pt->is_liked = $pt->likedByAuth !== null;
            $pt->is_reported = $pt->post_report !== null;

            return $pt;
        });

        $like_data = PostLike::with(['post_data'])->where('user_id', $id)->pluck('post_id')->toArray();

        $like = Posts::whereIn('id', $like_data)
            ->latest()
            ->get();

        // dd($post);
        $post_count = Posts::where('created_by', Auth::id())->where('status', 'active')->count();
        $products = Products::whereIn('id', $savedProductIds)->latest()->get();
        $jobs = Jobs::whereIn('id', $savedJobIds)->where('status', 'active')->latest()->get();
        $following = Auth::user()->following()->latest('follows.created_at')->paginate(20);
        $followers = Auth::user()->followers()->latest('follows.created_at')->paginate(20);
        $users = UserDetail::where('id', '!=', Auth::id())->take(10)->get();
        $gstverified = GstDetails::where('user_id', Auth::id())->where('gst_verify', 'yes')->first();
        if ($gstverified === null) {
            $gstverified = 'no';
        } else {
            $gstverified = 'yes';
        }

        return view('settings.index', compact('jobs', 'post', 'like', 'post_count', 'products', 'locations', 'savedJobIds', 'following', 'followers', 'users', 'gstverified'));
    }

    public function admin_dashboard()
    {
        return view('admin.dashboard_admin');
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:6|regex:/[A-Z]/|regex:/\d/',
            'confirm_password' => 'required|same:new_password',
        ]);
      // dd($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        if (! Hash::check($request->old_password, $user->hash_password)) {
            return redirect()->back()->withErrors(['old_password' => 'Given Credentials is incorrect.']);
        }

        $user->hash_password = Hash::make($request->new_password);
        $user->password = $request->new_password;
        $user->save();

        return redirect()->back()->with('success', 'Password updated successfully.');
    }
}
