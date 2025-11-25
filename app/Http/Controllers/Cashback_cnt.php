<?php

namespace App\Http\Controllers;

use App\Models\Cashback;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Cashback_cnt extends Controller
{
    public function cashback(Request $req)
    {
        $cashbacks = Cashback::with(['user:id', 'vendor:id,name,profile_img,badge,user_name,as_a'])->where('user_id', Auth::id())->get();
        if($req->header('Authorization')){
            return response()->json([
                'success' => true,
                'cashbacks' => $cashbacks,
                'total_cashback' => $cashbacks->sum('avail_cb'),
                'total_vendor' => $cashbacks->groupBy('vendor_id')->count(),
                'message' => 'Cashback retrieved successfully'
                
            ]);
        }
        return view('cashback.index', compact('cashbacks'));
    }

}
