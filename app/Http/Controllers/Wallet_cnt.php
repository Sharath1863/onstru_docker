<?php

namespace App\Http\Controllers;

use App\Models\JobBoost;
use App\Models\Notification;
use App\Models\OwnedLeads;
use App\Models\ProductBoost;
use App\Models\Project;
use App\Models\ReadyToWorkBoost;
use App\Models\ServiceBoost;
use App\Models\UserDetail;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Wallet_cnt extends Controller
{
    public function wallet(Request $req)
    {
        // dd('hello');

        try {
            $wallet_list = Wallet::where('user_id', Auth::id() ?? 5)->get();

            $user_details = UserDetail::where('id', Auth::id())->first();
            if ($req->header('Authorization')) {

                return response()->json([
                    'success' => true,
                    'message' => 'Wallet Details',
                    'data' => [
                        'list' => $wallet_list,
                        'wallet_balance' => floatval($user_details->balance),
                    ],
                ], 200);
            }

        } catch (\Exception $e) {

            if ($req->header('Authorization')) {

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'data' => (object) [],
                ], 500);
            }
        }

        // $job_amt = JobBoost::join('job_post', 'job_post.id', '=', 'job_boosts.job_id')
        //     ->where('job_post.created_by', Auth::id())
        //     ->sum('job_boosts.amount');

        // $ser_amt = ServiceBoost::join('services', 'services.id', '=', 'service_boosts.service_id')
        //     ->where('services.created_by', Auth::id())
        //     ->selectRaw('SUM(service_boosts.amount * CASE WHEN service_boosts.click = 0 THEN 1 ELSE service_boosts.click END) as total')
        //     ->value('total');

        // $pro_amt = ProductBoost::join('products', 'products.id', '=', 'product_boosts.product_id')
        //     ->where('products.created_by', Auth::id())
        //     ->selectRaw('SUM(product_boosts.amount * CASE WHEN product_boosts.click = 0 THEN 1 ELSE product_boosts.click END) as total')
        //     ->value('total');

        // $ready_amt = ReadyToWorkBoost::where('readytowork_boost.user_id', Auth::id())
        //     ->sum('readytowork_boost.amount');

        // $leads_amt = OwnedLeads::join('leads', 'leads.id', '=', 'owned_leads.lead_id')
        //     ->where('owned_leads.created_by', Auth::id())
        //     ->sum('leads.admin_charge');

        // $project_amt = Project::where('created_by', Auth::id())->sum('amount');

        // $wallet_amt = $wallet_list->sum('amount');

        // $bal = $wallet_amt - ($job_amt + $ser_amt + $pro_amt + $ready_amt + $leads_amt + $project_amt);
        // Zahir method

        return view('wallet.index', ['list' => $wallet_list, 'balance' => $user_details->balance, 'wallet_balance' => $user_details->balance]);
    }

    // function for wallet insert

    public function wallet_insert(Request $req)
    {
        // dd($req->all());
        $req->validate([
            'amount' => 'required|numeric|min:1',
            'payment_type' => 'required|string',
            'payment_id' => 'required|string',
            'payment_status' => 'required|string',
            // 'type' => 'required|string',
        ]);

        try {
            $user_details = UserDetail::where('id', Auth::id())->first();

            $wallet = new Wallet;
            $wallet->user_id = Auth::id();
            $wallet->type = 'wallet';
            $wallet->amount = $req->amount;
            $wallet->payment_type = $req->payment_type;
            $wallet->payment_id = $req->payment_id;
            $wallet->payment_status = $req->payment_status;
            $wallet->save();

            // Update user balance
            $user_details->balance += $req->amount;
            $user_details->save();

            if ($req->header('Authorization')) {

                return response()->json([
                    'success' => true,
                    'message' => 'Amount added to wallet successfully',
                    // 'data' => (object) [],
                ], 200);
            }

            return redirect()->back()->with('success', 'Amount added to wallet successfully');
        } catch (\Exception $e) {

            if ($req->header('Authorization')) {

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    // 'data' => (object) [],
                ], 500);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
