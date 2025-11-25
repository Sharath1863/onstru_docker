<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Payservice
{
    protected $baseUrl;

    protected $appId;

    protected $secretKey;

    protected $payout_appId;

    protected $payout_secretKey;

    protected $payout_baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('CASHFREE_ENV') === 'sandbox'
            ? 'https://sandbox.cashfree.com/pg'
            : 'https://api.cashfree.com/pg';

        $this->appId = env('CASHFREE_APP_ID');
        $this->secretKey = env('CASHFREE_SECRET_KEY');

        $this->payout_baseUrl = env('CASHFREE_ENV') === 'sandbox'
            ? 'https://sandbox.cashfree.com/payout'
            : 'https://payout-api.cashfree.com/payout';

        // log::info($this->appId . "-//////-" . $this->secretKey . "-/////-" . $this->baseUrl);
    }

    protected function headers()
    {
        return [
            'x-api-version' => '2025-01-01',
            'x-client-id' => $this->appId,
            'x-client-secret' => $this->secretKey,
            'Content-Type' => 'application/json',
        ];
    }

    protected function payout_headers()
    {
        return [
            'X-Client-Id' => $this->payout_appId,
            'X-Client-Secret' => $this->payout_secretKey,
            'Content-Type' => 'application/json',

        ];
    }

    public function createOrder($orderId, $amount, $customer)
    {
        $payload = [
            'order_id' => $orderId,
            'order_amount' => $amount,
            'order_currency' => 'INR',
            'customer_details' => [
                'customer_id' => $customer['id'] ?? 1,
                'customer_name' => $customer['name'] ?? 'Onstru User',
                'customer_email' => $customer['email'] ?? 'hari@gmail.com',
                'customer_phone' => $customer['phone'] ?? '9791818968',
            ],
            'order_meta' => [
                // 'return_url' => "https://127.0.0.1.8000/payment/callback?order_id={$orderId}",
                'return_url' => route('cashfree.callback'),
                // 'return_url' => "https://127.0.0.1.8000/payment/callback?order_id={$orderId}",
                'notify_url' => route('cashfree.webhook'),
                // 'notify_url' => 'https://webhook.site/ef041b6c-29a4-4b97-aa2b-22cbeadcd25f',
                // 'payment_methods' => 'cc,dc,upi,netbanking,emi'
            ],
            'payment_methods' => 'cc,dc,upi,nb,wallet,emi,paylater',
            // 'payment_methods' => [
            //     'card',              // Card payments
            //     'upi',               // UPI payments
            //     'netbanking',        // Netbanking
            //     'wallet',            // Wallet payments
            //     'emi',               // EMI payments explicitly enabled
            //     'paylater',           // Pay Later, if you want that too
            // ],
        ];

        // Log::info('Cashfree Payload:', $payload);

        $response = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/orders", $payload);

        if (! $response->successful()) {
            Log::error('Cashfree order creation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Failed to create order with Cashfree');
        }

        $data = $response->json();

        // Log::info('Create Order:', $data);

        return $data; // This should contain 'payment_session_id' correctly
    }

    public function fetchOrderStatus($orderId)
    {
        //  $url = "https://api.cashfree.com/pg/orders/{$orderId}"; // Use sandbox endpoint for testing
        // $url = "https://sandbox.cashfree.com/pg/orders/{$orderId}"; // for sandbox

        $url = "{$this->baseUrl}/orders/{$orderId}";
        //  $url = "{$this->baseUrl}/payments?order_id={$orderId}";

        $headers = [
            'x-api-version' => '2025-01-01',
            'x-client-id' => $this->appId,
            'x-client-secret' => $this->secretKey,
            'Content-Type' => 'application/json',
        ];

        $response = Http::withHeaders($headers)->get($url);

        Log::info('Fetch Order Status Response: '.json_encode($response->json(), JSON_PRETTY_PRINT));

        if ($response->successful()) {
            return $response;
        } else {
            Log::error('Failed to fetch Cashfree order status', [
                'order_id' => $orderId,
                'status_code' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }
    }

    // public function add_vendor($vendor)
    // {
    //     // $response = Http::withHeaders([
    //     //     'x-client-id' => config('services.cashfree.app_id'),
    //     //     'x-client-secret' => config('services.cashfree.secret'),
    //     //     'x-api-version' => '2022-09-01',
    //     // ])->post(config('services.cashfree.base_url').'/easy-split/vendors', [

    //     // ]);

    //     $payload = [
    //         'vendor_id' => $vendor->id,
    //         'name' => $vendor->name,
    //         'email' => $vendor->email,
    //         'phone' => $vendor->phone,
    //         'bank' => [
    //             'account_number' => $vendor->account_number,
    //             'ifsc' => $vendor->ifsc,
    //         ],
    //     ];

    //     $response = Http::withHeaders($this->headers())
    //         ->post("{$this->baseUrl}/easy-split/vendors", $payload);

    //     log::info('Add Vendor Response: '.json_encode($response->json(), JSON_PRETTY_PRINT));

    //     return $response->json();
    // }

    // public function transfer_to_vendor($vendorId, $amount, $remarks = '')
    // {
    //     // $response = Http::withHeaders([
    //     //     'x-client-id' => config('services.cashfree.app_id'),
    //     //     'x-client-secret' => config('services.cashfree.secret'),
    //     //     'x-api-version' => '2022-09-01',
    //     // ])->post(config('services.cashfree.base_url').'/easy-split/transfers', [
    //     //     'transfer_id' => 'TRF_'.uniqid(), // unique transfer id
    //     //     'vendor_id' => $vendorId,
    //     //     'amount' => $amount,
    //     //     'remarks' => $remarks ?: 'Manual settlement',
    //     // ]);

    //     $payload = [
    //         'transfer_id' => 'TRF_'.uniqid(),
    //         'vendor_id' => $vendorId,
    //         'amount' => $amount,
    //         'remarks' => $remarks ?: 'Manual settlement',
    //     ];

    //     $response = Http::withHeaders($this->headers())
    //         ->post("{$this->baseUrl}/easy-split/transfers", $payload);

    //     log::info('Add transfer vendor Response: '.json_encode($response->json(), JSON_PRETTY_PRINT));

    //     return $response->json();
    // }

    // public function check_transfer_status($transferId)
    // {
    //     // $response = Http::withHeaders([
    //     //     'x-client-id' => config('services.cashfree.app_id'),
    //     //     'x-client-secret' => config('services.cashfree.secret'),
    //     //     'x-api-version' => '2022-09-01',
    //     // ])->get(config('services.cashfree.base_url')."/easy-split/transfers/$transferId");

    //     $response = Http::withHeaders($this->headers())
    //         ->get("{$this->baseUrl}/easy-split/transfers/$transferId");

    //     log::info('Add transfer vendor check status Response: '.json_encode($response->json(), JSON_PRETTY_PRINT));

    //     return $response->json();
    // }

    // public function verify_bank($data)
    // {
    //     // $response = Http::withHeaders([
    //     //     'x-client-id' => config('services.cashfree.app_id'),
    //     //     'x-client-secret' => config('services.cashfree.secret'),
    //     //     'x-api-version' => '2022-09-01',
    //     //     'Content-Type' => 'application/json',
    //     // ])->post(config('services.cashfree.verify_base_url').'/verification/bankAccount', [
    //     //     'bankAccount' => $accountNumber,
    //     //     'ifsc' => $ifsc,
    //     //     'name' => $name,
    //     // ]);

    //     $payload = [
    //         'bankAccount' => $data['accountNumber'],
    //         'ifsc' => $data['ifsc'],
    //         'name' => $data['name'],
    //     ];

    //     $response = Http::withHeaders($this->headers())
    //         ->post("{$this->baseUrl}/verification/bankAccount", $payload);

    //     Log::info('Cashfree Bank Verification: '.json_encode($response->json(), JSON_PRETTY_PRINT));

    //     return $response->json();
    // }

    public function placeOrder($orderId, $amount, $customer)
    {
        $payload = [
            'order_id' => $orderId,
            'order_amount' => $amount,
            'order_currency' => 'INR',
            'customer_details' => [
                'customer_id' => $customer['id'] ?? 1,
                'customer_name' => $customer['name'] ?? 'Onstru User',
                'customer_email' => $customer['email'] ?? 'hari@gmail.com',
                'customer_phone' => $customer['phone'] ?? '9791818968',
            ],
            'order_meta' => [
                // 'return_url' => "https://127.0.0.1.8000/payment/callback?order_id={$orderId}",
                'return_url' => route('payment-success'),
                // 'return_url' => "https://127.0.0.1.8000/payment/callback?order_id={$orderId}",
                'notify_url' => route('cashfree.product.webhook'),
                // 'notify_url' => 'https://webhook.site/ef041b6c-29a4-4b97-aa2b-22cbeadcd25f',
                // 'payment_methods' => 'cc,dc,upi,netbanking,emi'
            ],
            'payment_methods' => 'cc,dc,upi,nb,wallet,emi,paylater',
            // 'payment_methods' => [
            //     'card',              // Card payments
            //     'upi',               // UPI payments
            //     'netbanking',        // Netbanking
            //     'wallet',            // Wallet payments
            //     'emi',               // EMI payments explicitly enabled
            //     'paylater',           // Pay Later, if you want that too
            // ],
        ];

        // Log::info('Cashfree Payload:', $payload);

        $response = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/orders", $payload);

        if (! $response->successful()) {
            Log::error('Cashfree order creation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Failed to create order with Cashfree');
        }

        $data = $response->json();
        // dd($data);
        // Log::info('Create Order:', $data);

        return $data; // This should contain 'payment_session_id' correctly
    }

    public function verify_bank($vendor)
    {

        // $response = Http::withHeaders([
        //     //     'x-client-id' => config('services.cashfree.app_id'),
        //     //     'x-client-secret' => config('services.cashfree.secret'),
        //     //     'x-api-version' => '2022-09-01',
        //     // ])->post(config('services.cashfree.base_url').'/easy-split/vendors', [

        //     // ]);

        $payload = [
            // 'vendor_id' => $vendor->id,
            'name' => $vendor->name,
            'bankAccount' => $vendor->account_number,
            'ifsc' => $vendor->ifsc,
            // 'email' => $vendor->email,
            // 'phone' => $vendor->phone,
            // 'bank' => [
            //     'account_number' => $vendor->account_number,
            //     'ifsc' => $vendor->ifsc,
            // ],
        ];

        // payload for upi

        // $payload_upi = [
        //     // 'vendor_id' => $vendor->id,
        //     'name' => $vendor->name,
        //     'vpa' => $vendor->upi_id,
        // ];

        $response = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/v1/validation/bankDetails", $payload);

        log::info('Bank verify Response: '.json_encode($response->json(), JSON_PRETTY_PRINT));

        return $response->json();
    }
}
