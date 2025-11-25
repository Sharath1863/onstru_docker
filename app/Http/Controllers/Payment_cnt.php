<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Services\Payservice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Payment_cnt extends Controller
{
    public function initiatePayment(Request $req, Payservice $payService)
    {

        // dd($req->all());

    //     try {
    //     $amount = $req->input('amount');
    //     return response()->json(['amount_received' => $amount]);
    // } catch (\Exception $e) {
    //     return response()->json(['error' => $e->getMessage()], 500);
    // }

     $auth = Auth::user();

    //  dd($auth);

        // dd($req->all());
        $orderId = 'ORDER_'.uniqid();
        $amount = $req->amount ?? 5.00; // INR
        $customer = [
            'id' => (string)($auth->id ?? ('user_'.uniqid())),
            'name' => $auth->name ?? 'Test User',
            'email' =>  $auth->email ?? 'test@example.com',
            'phone' => $auth->number ?? '9999999999',
        ];

        $order = $payService->createOrder($orderId, $amount, $customer);

        // Log::info('Order Response: '.json_encode($order, JSON_PRETTY_PRINT));

        $paymentSessionId = $order['payment_session_id'];

        // Step 2: Store in DB as pending
        Wallet::create([
            'user_id' => Auth::id(),
            'type' => 'wallet',
            'order_id' => $orderId,
            'amount' => $amount,
            'payment_status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),

            // 'customer_id' => $customer['id'],
            // 'payment_session_id' => $order['payment_session_id'],
        ]);

        // Step 3: Send session id to frontend
        return response()->json([
            'payment_session_id' => $order['payment_session_id'],
            'order_id' => $orderId,
        ]);

    }

    public function handleCallback(Request $request, Payservice $payService)
    {

        $orderId = $request->input('order_id');

        // Optionally: fetch order from Cashfree using API again
        // $orderStatus = $payService->fetchOrderStatus($orderId);

        // Log::info('Order - '.$orderStatus);

        // $status = $orderStatus['order_status']; // PAID, FAILED, etc.
        // $cfPaymentId = $orderStatus['cf_order_id'] ?? null;
        // $payment_method = $orderStatus['payment_method'] ?? null;

        // Update order in DB
        // Wallet::where('order_id', $orderId)->update([
        //     'payment_status' => $status,
        //     'payment_id' => $cfPaymentId,
        //     'payment_type' => $payment_method,
        //     // 'payment_response' => json_encode($data),
        // ]);

        // return view('route('wallet')', [
        //     'order_id' => $orderId,
        //     'status' => $status,
        // ]);

        return redirect()->route('wallet');

        // return redirect()->route('wallet', [
        //     'order_id' => $orderId,
        //     'status' => $status,
        // ]);

        //  return view('payment.success', ['data' => $data]);

        // return view('payment.success', [
        //     'order_id' => $orderId,
        //     // 'status' => $orderStatus['order_status'] ?? 'UNKNOWN'
        // ]);

        // dd("hello");
        // Handle redirect after payment
        // return view('payment.success', ['order_id' => $request->input('order_id')]);
    }

    public function handleWebhook(Request $request)
    {
        // Verify signature and process status
        // Log::info('Cashfree webhook received:', $request->all());

        // $signature = $request->header('x-webhook-signature');
        // $computed = base64_encode(hash_hmac('sha256', $request->getContent(), env('CASHFREE_SECRET_KEY'), true));

        // if (! hash_equals($computed, $signature)) {
        //     Log::warning('Invalid Cashfree webhook signature!');

        //     return response()->json(['error' => 'Invalid signature'], 400);
        // }

        $data = $request->all();

        $orderId = $data['data']['order']['order_id'] ?? null;
        $status = $data['data']['payment']['payment_status'] ?? 'UNKNOWN';
        $cfPaymentId = $data['data']['payment']['cf_payment_id'] ?? null;
        $payment_method = $data['data']['payment']['payment_group'] ?? null;

        if (! $orderId) {
            Log::warning('Webhook received without order_id', $data);

            return response()->json(['error' => 'Missing order_id'], 400);
        }

        // ✅ Update your DB
        Wallet::where('order_id', $orderId)->update([
            'payment_status' => $status,
            'payment_id' => $cfPaymentId,
            'payment_type' => $payment_method,
            // 'payment_response' => json_encode($data),
        ]);

        $user_wallet = Wallet::where('order_id', $orderId)->first();

        $user_wallet->user->increment('balance', $user_wallet->amount);

        return response()->json(['success' => true]);
    }

    public function handleWebhookProduct(Request $request)
    {
        // Verify signature and process status
        // Log::info('Cashfree webhook received:', $request->all());

        // $signature = $request->header('x-webhook-signature');
        // $computed = base64_encode(hash_hmac('sha256', $request->getContent(), env('CASHFREE_SECRET_KEY'), true));

        // if (! hash_equals($computed, $signature)) {
        //     Log::warning('Invalid Cashfree webhook signature!');

        //     return response()->json(['error' => 'Invalid signature'], 400);
        // }

        $data = $request->all();

        $orderId = $data['data']['order']['order_id'] ?? null;
        $status = $data['data']['payment']['payment_status'] ?? 'UNKNOWN';
        $cfPaymentId = $data['data']['payment']['cf_payment_id'] ?? null;
        $payment_method = $data['data']['payment']['payment_group'] ?? null;

        if (! $orderId) {
            Log::warning('Webhook received without order_id', $data);

            return response()->json(['error' => 'Missing order_id'], 400);
        }

        // ✅ Update your DB
        Wallet::where('order_id', $orderId)->update([
            'payment_status' => $status,
            'payment_id' => $cfPaymentId,
            'payment_type' => $payment_method,
            // 'payment_response' => json_encode($data),
        ]);

        // $user_wallet = Wallet::where('order_id', $orderId)->first();

        // $user_wallet->user->increment('balance', $user_wallet->amount);

        return response()->json(['success' => true]);
    }

    public function productPayment(Request $req, Payservice $payService)
    {

        $auth = Auth::user();
        $orderId = 'ORDER_'.uniqid();
        $amount = $req->amount ?? 5.00; // INR
        $customer = [
            'id' => (string)($auth->id ?? ('user_'.uniqid())),
            'name' => $auth->name ?? 'Test User',
            'email' =>  $auth->email ?? 'test@example.com',
            'phone' => $auth->number ?? '9999999999',
        ];

        $order = $payService->placeOrder($orderId, $amount, $customer);

        // Log::info('Order Response: '.json_encode($order, JSON_PRETTY_PRINT));

        $paymentSessionId = $order['payment_session_id'];

        // Step 2: Store in DB as pending
        Wallet::create([
            'user_id' => Auth::id(),
            'type' => 'product',
            'order_id' => $orderId,
            'amount' => $amount,
            'payment_status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),

            // 'customer_id' => $customer['id'],
            // 'payment_session_id' => $order['payment_session_id'],
        ]);

        // Step 3: Send session id to frontend
        return response()->json([
            'payment_session_id' => $order['payment_session_id'],
            'order_id' => $orderId,
        ]);

    }
}
