<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\GstDetails;
use App\Models\Notification;
use App\Models\Posts;
use App\Models\Premium;
use App\Models\PremiumUser;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Premium_cnt extends Controller
{
    public function premium()
    {
        $id = Auth::id();
        $user = Auth::user();
        $following = Auth::user()->following()->latest('follows.created_at')->paginate(20);
        $followers = Auth::user()->followers()->latest('follows.created_at')->paginate(20);
        $gstverified = GstDetails::where('user_id', Auth::id())->where('gst_verify', 'yes')->first();
        if ($gstverified === null) {
            $gstverified = 'no';
        } else {
            $gstverified = 'yes';
        }
        $post_count = Posts::where('created_by', Auth::id())->where('status', 'active')->count();
        $hasSubscription = PremiumUser::where('user_id', Auth::id())
            // ->whereMonth('created_at', Carbon::now()->month)
            // ->whereYear('created_at', Carbon::now()->year)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->exists();
        $premium = Premium::latest()->get();
        $premium_charge = Charge::where('category', 'premium')->latest()->value('charge') * 1.18;
        $buy_list = PremiumUser::where('user_id', Auth::id())->get();
        // dd($premium_charge);
        return view('premium.index', compact('following', 'followers', 'hasSubscription', 'premium', 'gstverified', 'premium_charge', 'post_count', 'buy_list'));
    }

    public function subscribe(Request $request)
    {
        $premiumCharge = Charge::where('category', 'premium')->latest()->value('charge') * 1.18;

        if (Auth::user()->balance < $premiumCharge) {
            if ($request->header('Authorization')) {
                return response()->json(['success' => true, 'status' => 'error', 'message' => 'Insufficient balance. Please recharge your wallet.']);
            }

            return redirect()->route('wallet.page')->with('error', 'Insufficient balance. Please recharge your wallet.');
        }

        UserDetail::where('id', Auth::id())->update([
            'balance' => Auth::user()->balance - $premiumCharge,
        ]);

        PremiumUser::create([
            'user_id' => Auth::id(),
            'price' => $premiumCharge,
            'status' => 'active',
        ]);

        if (Auth::user()->you_are == 'Consumer' || Auth::user()->you_are == 'Professional') {
            $premiumCount = PremiumUser::where('user_id', Auth::id())->count();
            if ($premiumCount > 9) {
                $badge = '9PM';
            } elseif ($premiumCount > 6) {
                $badge = '6PM';
            } elseif ($premiumCount > 3) {
                $badge = '3PM';
            } else {
                $badge = null;
            }

            if ($badge) {
                UserDetail::where('id', Auth::id())->update(['badge' => $badge]);
            }
        }

        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'status' => 'success', 'message' => 'Premium subscription successful!']);
        }

        return back()->with('success', 'Premium Subscribed Successfully!');
    }
}
