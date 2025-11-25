<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Cashback;
use App\Models\Notification;
use App\Models\OrderProducts;
use App\Models\Orders;
use App\Models\OrderTracking;
use App\Models\Review;
use App\Models\UserDetail;
use App\Models\Wallet;
use App\Services\Aws;
use App\Services\NotificationService;
use App\Services\Payservice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Orders_cnt extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    private function notifyUser($userId, $title, $body, $category_id)
    {
        $this->notificationService->create([
            'category' => 'order',
            'category_id' => $category_id,
            'reciever' => $userId,
            'title' => $title,
            'body' => $body,
            'status' => 'active',
            'seen' => false,
            'c_by' => Auth::id(),
            'remainder' => null,
        ]);
    }

    public function track_order($trackId)
    {
        $track = OrderTracking::where('tracking_id', $trackId)->first();
        $type = json_decode($track->tracking)->type;
        if ($type == 'own_vehicle') {
            $track->drivername = json_decode($track->tracking)->driver_name;
        }
        return view('track', compact('track'));
    }

    public function track_location(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'trackId' => 'required|exists:order_tracking,tracking_id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // $track_id = 'ORDTRK-0028';
        $track_id = $request->input('trackId');

        $ordtrk = OrderTracking::where('tracking_id', $track_id)->with('product.hub:id,latitude,longitude')->first();
        // dd($ordtrk);
        if (!$ordtrk) {
            return response()->json(['error' => 'Tracking ID not found'], 404);
        }
        $order = Orders::where('order_id', $ordtrk->order_id)->with('address:id,latitude,longitude')->first();
        $hub_lat = $ordtrk->product->hub->latitude;
        $hub_lng = $ordtrk->product->hub->longitude;
        $driver_lat = $ordtrk->ord_lat == '' ? $hub_lat : $ordtrk->ord_lat;
        $driver_lng = $ordtrk->ord_long == '' ? $hub_lat : $ordtrk->ord_long;
        $buyer_lat = $order->address->latitude;
        $buyer_lng = $order->address->longitude;
        $track = [
            'hub_lat' => $hub_lat,
            'hub_lng' => $hub_lng,
            'driver_lat' => $driver_lat,
            'driver_lng' => $driver_lng,
            'buyer_lat' => $buyer_lat,
            'buyer_lng' => $buyer_lng,
            'detail' => json_decode($ordtrk->tracking)
        ];
        return response()->json($track);
    }

    // update the tracking page

    public function update_track(Request $req)
    {

        $ord_track = OrderTracking::where('tracking_id', $req->order_id)->update([
            'ord_lat' => $req->ord_lat,
            'ord_long' => $req->ord_lng,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Location updated successfully.']);
    }

    public function tracking($orderId)
    {
        $order = Orders::where('user_id', Auth::id())
            ->where('order_id', $orderId)
            ->with(['products.product.vendor.gst'])
            ->firstOrFail();
        // dd($order);
        $productIds = $order->products->pluck('product_id');
        $userReviews = Review::where('c_by', Auth::id())
            ->whereIn('product_id', $productIds)
            ->get()
            ->keyBy('product_id');

        $track = OrderTracking::with('product')
            ->where('order_id', $orderId)
            ->get();
        $groupedTracks = $track->groupBy('tracking_id');
        $trackIds = $groupedTracks->keys()->toArray();

        $vendors = $order->products->groupBy(function ($item) {
            return $item->product->created_by;
        });

        return view(
            'orders.tracking',
            ['trackIds' => $trackIds, 'track' => $track, 'groupedTracks' => $groupedTracks],
            compact('order', 'vendors', 'userReviews')
        );
    }

    public function payment_success()
    {
        $userId = Auth::id();
        $deliverableProducts = session('deliverableProducts', []);
        $deliverableProductIds = array_keys($deliverableProducts);
        $cartItems = Cart::with('product', 'vendor')
            ->where('c_by', $userId)
            ->where('status', 'cart')
            ->whereIn('product_id', $deliverableProductIds)
            ->get()
            ->map(function ($item) use ($deliverableProducts) {
                $productId = $item->product_id;
                $item->transport = isset($deliverableProducts[$productId]) ? (float) $deliverableProducts[$productId] : 0.0;

                return $item;
            })
            ->groupBy('vendor_id');

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }
        $appliedCashbacks = session('appliedCashbacks', []);

        $totalAmount = 0;
        $totalCashback = 0;
        $totalTransport = 0;
        $vendorsInCart = [];
        foreach ($cartItems as $vendorId => $items) {
            $vendorTotal = 0;
            $inc_cb = 0;
            foreach ($items as $item) {
                if ($item->vendor_id) {
                    $vendorsInCart[] = $item->vendor_id;
                }
                $qty = $item->quantity;
                $product = $item->product;

                $pricePerUnit = $product->base_price + $product->cashback_price + $product->margin;
                $subtotal = $pricePerUnit * $qty;

                $tax = ($subtotal * $product->tax_percentage) / 100;
                $shipping = $item->transport ?? 0;

                $itemTotal = $subtotal + $tax + $shipping;

                $vendorTotal += $itemTotal;
                $inc_cb += $product->cashback_price * $qty;
            }

            $vendorCashback = $appliedCashbacks[$vendorId] ?? 0;
            $vendorPayable = $vendorTotal - $vendorCashback;

            $totalAmount += $vendorTotal;
            $totalCashback += $vendorCashback;

            Cashback::where('user_id', Auth::id())->where('vendor_id', $vendorId)
                ->decrement('avail_cb', $vendorCashback);
            $cashback = Cashback::firstOrCreate(
                [
                    'user_id' => Auth::id(),
                    'vendor_id' => $vendorId,
                ],
                [
                    'avail_cb' => 0,
                ]
            );
            $cashback->increment('avail_cb', $inc_cb);
        }
        $grandTotal = round($totalAmount - $totalCashback, 0);
        if ($grandTotal == 0) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }
        $address_data = session('address_data');
        $address = Address::create($address_data);
        $addressId = $address->id;
        $order = Orders::create([
            // 'order_id' => 'ONSTRUORD00' . strtoupper(Str::random(2)),
            'user_id' => $userId,
            'address_id' => $addressId,
            'cashback' => json_encode($appliedCashbacks),
            'total' => $grandTotal,
            'transaction_status' => 'success',
            'status' => 'pending',
        ]);
        $order->order_id = 'ONSTRUORD00' . $order->id;
        $order->save();
        foreach ($cartItems as $items) {
            $v = 'VEN' . strtoupper(Str::random(6)) . Auth::id();
            foreach ($items as $item) {
                // dd($cartItems);
                OrderProducts::create([
                    'order_id' => $order->order_id,
                    'vendor_order' => $v,
                    'product_id' => $item->product_id,
                    'base_price' => $item->product->base_price,
                    'cashback' => $item->product->cashback_price,
                    'shipping' => $item->transport,
                    'tax' => $item->product->tax_percentage,
                    'margin' => $item->product->margin,
                    'quantity' => $item->quantity,
                    'bal_qty' => $item->quantity,
                    'status' => 'pending',
                ]);
                $totalTransport += $item->transport;
            }
        }
        $vendorsInCart = array_unique($vendorsInCart);
        // foreach ($vendorsInCart as $vendorId) {
        //     $this->notifyUser(
        //         $vendorId,
        //         'New Order Received',
        //         'You have received a new order #' . $order->order_id . ' from ' . Auth::user()->name,
        //         $order->id
        //     );
        //     if ($item->vendor && ($item->vendor->web_token || $item->vendor->mob_token)) {
        //         $data = [
        //             'web_token' => $item->vendor->web_token,
        //             'mob_token' => $item->vendor->mob_token ?? null,
        //             'title' => 'New Order Received',
        //             'body' => 'You have received a new order #' . $order->order_id . ' from ' . Auth::user()->name,
        //             'id' => $order->id,
        //             'link' => url('order-accept/', ['id' => $order->id])
        //         ];
        //         Log::info('vendor web token : ' . $item);
        //         $this->notificationService->token($data);
        //     }
        // }
        foreach ($vendorsInCart as $vendorId) {
            // Log::info('Sending notification to vendor: ' . $vendorId);

            // Ensure vendor is present and tokens exist
            $vendor = UserDetail::find($vendorId);
            $this->notifyUser(
                $vendorId,
                'New Order Received',
                'You have received a new order #' . $order->order_id . ' from ' . Auth::user()->name,
                $order->id
            );
            if ($vendor && ($vendor->web_token || $vendor->mob_token)) {
                // Log::info('Vendor Tokens - Web: ' . $vendor->web_token . ' | Mob: ' . $vendor->mob_token);

                $data = [
                    'web_token' => $vendor->web_token,
                    'mob_token' => $vendor->mob_token ?? null,
                    'title' => 'New Order Received',
                    'body' => 'You have received a new order #' . $order->order_id . ' from ' . Auth::user()->name,
                    'id' => $order->id,
                    'link' => url('order-accept/', ['id' => $order->id])
                ];

                try {
                    $this->notificationService->token($data);
                    Log::info('Notification sent successfully to vendor: ' . $vendorId);
                } catch (\Exception $e) {
                    Log::error('Error sending notification: ' . $e->getMessage());
                }
            } else {
                Log::info('No valid tokens found for vendor: ' . $vendorId);
            }
        }

        Cart::where('c_by', $userId)
            ->where('status', 'cart')
            ->whereIn('product_id', $deliverableProductIds)
            ->delete();

        session()->forget('appliedCashbacks');
        session()->forget('deliverableProducts');
        session()->save();

        return view('payment.payment_success', [
            'order' => $order,
            'totalAmount' => $totalAmount,
            'totalTransport' => $totalTransport,
            'totalCashback' => $totalCashback,
            'grandTotal' => $grandTotal,
            'order_details' => $order,
        ]);
    }

    public function order_summary()
    {
        $userId = Auth::id();
        $deliverableProducts = session('deliverableProducts', []);
        $deliverableProductIds = array_keys($deliverableProducts);

        $cartItems = Cart::with('product', 'vendor')
            ->where('c_by', $userId)
            ->where('status', 'cart')
            ->whereIn('product_id', $deliverableProductIds)
            ->get()
            ->map(function ($item) use ($deliverableProducts) {
                $productId = $item->product_id;
                $item->transport = isset($deliverableProducts[$productId]) ? (float) $deliverableProducts[$productId] : 0.0;

                return $item;
            })
            ->groupBy('vendor_id');

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }
        $addressId = session('address_id');
        // $addresses = Address::where('id', $addressId)->first();
        $addresses = session('address_data', null);

        $appliedCashbacks = session('appliedCashbacks', []);

        // $available_cashback = Cashback::where('user_id', $userId)
        //     ->get()
        //     ->keyBy('vendor_id');

        return view('orders.order_summary', compact('cartItems', 'addresses', 'appliedCashbacks'));
    }

    public function order_accept($order_id)
    {
        $order = Orders::with([
            'products' => function ($q) {
                $q->whereHas('product', function ($q2) {
                    $q2->where('created_by', Auth::id());
                })->with('product.hub');
            },
            'user',
            'address',
        ])
            ->where('order_id', $order_id)
            ->firstOrFail();

        $address = Address::where('id', $order->address_id)->first();
        $hubIds = $order->products->pluck('product.hub_id')
            ->unique()
            ->values()
            ->toArray();

        $ship_total = 0;
        $price = $order->products->sum(function ($op) use (&$ship_total) {
            $subtotal = $op->base_price * $op->quantity;
            $taxAmount = $subtotal * ($op->tax / 100);
            $ship_total += $op->shipping;

            return $subtotal + $taxAmount;
        });

        $vendorId = Auth::id();
        $cashbackData = json_decode($order->cashback, true);
        $cashback = 0;
        if (is_array($cashbackData) && isset($cashbackData[$vendorId])) {
            $cashback = (int) $cashbackData[$vendorId];
        }
        $track = OrderTracking::with('product')
            ->where('order_id', $order_id)
            // ->where('status', 'shipped')
            ->whereHas('product', function ($query) {
                $query->where('created_by', Auth::id());
            })
            ->get();
        $groupedTracks = $track->groupBy('tracking_id');
        $trackIds = $groupedTracks->keys()->toArray();
        // $track = OrderTracking::with([
        //     'OrderProducts' => function ($q) {
        //         $q->whereHas('product', function ($q2) {
        //             $q2->where('created_by', Auth::id());
        //         })->with('product');
        //     }
        // ])
        //     ->where('order_id', $order_id)
        //     ->get();
        // $trackIds = $track->pluck('tracking_id')
        // ->unique()
        // ->values()
        // ->toArray();
        // dd($track);
        $total = floor($price + $ship_total);
        return view(
            'orders.order_accept',
            ['hubIds' => $hubIds, 'trackIds' => $trackIds, 'track' => $track, 'groupedTracks' => $groupedTracks],
            compact('order', 'price', 'ship_total', 'cashback', 'total', 'address')
        );
    }

    public function order_outfordelivery(Request $request)
    {
        // dd($request->all());
        $orderId = $request->input('order_id');
        $submittedProducts = $request->input('products', []);

        // Filter only selected products
        $selectedProductIds = [];
        $quantities = [];

        foreach ($submittedProducts as $productId => $data) {
            if (isset($data['selected'])) {
                $selectedProductIds[] = $productId;
                $quantities[$productId] = $data['quantity'] ?? 100;
            }
        }

        // Fetch product details
        $products = OrderProducts::where('order_id', $orderId)->whereIn('product_id', $selectedProductIds)->with('product')->get();
        // dd($products);
        // Add selected quantity to each product
        $products->map(function ($product) use ($quantities) {
            $product->selected_quantity = $quantities[$product->product_id] ?? 1;

            return $product;
        });

        $notificationCount = Notification::where('reciever', Auth::id())
            ->where('status', 'active')
            ->count();

        // Render the view directly
        return view('orders.order_outfordelivery', [
            'orderId' => $orderId,
            'products' => $products,
            'notificationCount' => $notificationCount,
        ]);
    }

    public function orders()
    {
        $orders = Orders::where('user_id', Auth::id())
            ->withCount('products')
            ->latest()
            ->get();
        return view('orders.orders', compact('orders'));
    }

    public function order_status()
    {
        $orders = Orders::with([
            'products' => function ($q) {
                $q->whereHas('product', function ($q2) {
                    $q2->where('created_by', Auth::id());
                })->with('product');
            },
            'user',
            'address',
        ])
            ->latest()
            ->get();
        return view('orders.order_status', compact('orders'));
    }

    public function order_update(Request $request, Aws $aws)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'type' => 'nullable|string',
            'courier_name' => 'nullable|string',
            'vehicle_number' => 'nullable|string',
            'estimated_delivery_date' => 'nullable|date',
            'driver_name' => 'nullable|string',
            'driver_number' => 'nullable|string',
            'tracking_id' => 'nullable|string',
            'vendor_invoice' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'otp' => ['required_if:status,delivered', 'numeric'],
        ]);

        $userId = Auth::id();
        $order = Orders::where('order_id', $request->order_id)->with('user')->first();

        $orderProducts = OrderProducts::where('order_id', $request->order_id)
            ->whereHas('product', function ($query) use ($userId) {
                $query->where('created_by', $userId);
            })
            ->get();

        // if ($validated['status'] === 'cancelled') {
        //     foreach ($orderProducts as $orderProduct) {
        //         $orderProduct->status = 'cancelled';
        //         $orderProduct->updated_at = now();
        //         $orderProduct->save();
        //     }

        //     if ($order && $order->cashback) {
        //         $cashbacks = json_decode($order->cashback, true);

        //         $vendorId = $orderProducts->first()->product->created_by ?? null;

        //         if ($vendorId && isset($cashbacks[$vendorId])) {
        //             $cashbackAmount = $cashbacks[$vendorId];

        //             $cb = Cashback::firstOrCreate(
        //                 [
        //                     'user_id' => $order->user_id,
        //                     'vendor_id' => $vendorId,
        //                 ],
        //                 [
        //                     'avail_cb' => 0,
        //                 ]
        //             );

        //             $cb->increment('avail_cb', $cashbackAmount);
        //         }
        //     }
        //     $this->notifyUser($order->user_id, 'Order Cancelled', 'Your order #' . $order->order_id . ' has been cancelled.', $order->id);
        // }

        // $otp = rand(1000, 9999);
        if ($validated['status'] === 'delivered') {
            foreach ($orderProducts as $orderProduct) {
                $tracking = json_decode($orderProduct->tracking, true);
                if (! isset($tracking['otp']) || $tracking['otp'] != $validated['otp']) {
                    return back()->withErrors(['otp' => 'Incorrect OTP.']);
                }
            }

            foreach ($orderProducts as $orderProduct) {
                $orderProduct->status = 'delivered';
                $orderProduct->updated_at = now();
                $orderProduct->save();
            }
            $this->notifyUser($order->user_id, 'Order Delivered', 'Your Product #' . $orderProduct->product->name . ' has been delivered.', $order->id);
            if ($order->user && ($order->user->web_token || $order->user->mob_token)) {
                $data = [
                    'web_token' => $order->user->web_token,
                    'mob_token' => $order->user->mob_token ?? null,
                    'title' => 'Order ' . ucfirst($request->status),
                    'body' => 'Your Order "' . $orderProduct->product->name . '" has been ' . $request->status . '.',
                    'id' => $order->id,
                    'link' => route('tracking', ['id' => $order->id]),
                ];
                $this->notificationService->token($data);
            }
            // return redirect()->route('order-accept', ['order_id' => $request->order_id])
            //     ->with('success', 'Order marked as delivered.');
        }

        if ($request->hasFile('vendor_invoice')) {
            $file = $request->file('vendor_invoice');
            if (! is_array($file)) {
                $file = [$file];
            }
            $folder = 'invoice';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $vendor_invoice = is_array($s3Result) ? $s3Result[0] : $s3Result;
            // $orderProduct->vendor_invoice = $vendor_invoice;
            OrderProducts::where('order_id', $request->order_id)
                ->whereHas('product', function ($query) use ($userId) {
                    $query->where('created_by', $userId);
                })
                ->update(['vendor_invoice' => $vendor_invoice]);
        }

        foreach ($orderProducts as $orderProduct) {
            $orderProduct->status = $validated['status'];

            if ($validated['status'] === 'processing') {

                $orderProduct->accepted_at = now();

                $this->notifyUser($order->user_id, 'Order Accepted', 'Your ordered Product #' . $orderProduct->product->name . ' is now processing.', $order->id);
                if ($order->user && ($order->user->web_token || $order->user->mob_token)) {
                    $data = [
                        'web_token' => $order->user->web_token,
                        'mob_token' => $order->user->mob_token ?? null,
                        'title' => 'Order Accepted',
                        'body' => 'Your Order "' . $orderProduct->product->name . '" has been ' . $request->status . '.',
                        'id' => $order->id,
                        'link' => route('tracking', ['id' => $order->id]),
                    ];
                    $this->notificationService->token($data);
                }
            }

            // if ($validated['status'] === 'shipped') {
            //     $orderProduct->shipped_at = now();

            //     $trackingData = [
            //         'type' => $validated['type'],
            //         'courier_name' => $validated['courier_name'],
            //         'vehicle_number' => $validated['vehicle_number'],
            //         'estimated_delivery_date' => $validated['estimated_delivery_date'],
            //         'driver_name' => $validated['driver_name'],
            //         'driver_number' => $validated['driver_number'],
            //         'tracking_id' => $validated['tracking_id'],
            //         'otp' => $otp,
            //     ];

            //     $orderProduct->tracking = json_encode($trackingData);
            //     $this->notifyUser($order->user_id, 'Order Shipped', 'Your Product #' . $orderProduct->product->name . ' has been shipped.', $order->id);
            //     if ($order->user && ($order->user->web_token || $order->user->mob_token)) {
            //         $data = [
            //             'web_token' => $order->user->web_token,
            //             'mob_token' => $order->user->mob_token ?? null,
            //             'title' => 'Order ' . ucfirst($request->status),
            //             'body' => 'Your Ordered Product "' . $orderProduct->product->name . '" has been ' . $request->status . '.',
            //             'id' => $order->id,
            //             'link' => route('tracking', ['id' => $order->id])
            //         ];
            //         $this->notificationService->token($data);
            //     }
            // }

            $orderProduct->save();
        }

        $order = Orders::where('order_id', $request->order_id)->first();
        if ($order) {
            $allProductsDeliveredOrCancelled = $orderProducts->every(function ($orderProduct) {
                return in_array($orderProduct->status, ['delivered', 'cancelled']);
            });

            if ($allProductsDeliveredOrCancelled) {
                $order->status = 'delivered';
                $order->save();
            }
        }

        return redirect()->route('order-accept', ['order_id' => $request->order_id]);
    }

    public function orderTrackingUpdate(Request $request, Aws $aws)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|string',
            'products' => 'required|string',
            'vendor_invoice' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'type' => 'required|in:own_vehicle,courier',
            'estimated_delivery_date' => 'nullable|date|after:today',
        ]);
        // Log::info('Request Data:', $request->all());
        if ($validator->fails()) {
            if ($request->header('Authorization')) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $products = json_decode($request->products, true);
        if ($request->hasFile('vendor_invoice')) {
            $file = $request->file('vendor_invoice');
            if (! is_array($file)) {
                $file = [$file];
            }
            $folder = 'invoice';
            $s3Result = $aws->common_upload_to_s3($file, $folder);
            $invoicePath = is_array($s3Result) ? $s3Result[0] : $s3Result;
        }
        $trackingData = [];

        if ($request->type === 'own_vehicle') {
            $trackingData = [
                'type' => 'own_vehicle',
                'vehicle_number' => $request->vehicle_number,
                'driver_name' => $request->driver_name,
                'driver_number' => $request->driver_number,
            ];
        } elseif ($request->type === 'courier') {
            $trackingData = [
                'type' => 'courier',
                'courier_name' => $request->courier_name,
                'tracking_id' => $request->tracking_id,
            ];
        }

        if ($request->estimated_delivery_date) {
            $trackingData['estimated_delivery_date'] = $request->estimated_delivery_date;
        }
        $tracking_id = OrderTracking::max('id');
        $tracking_id++;
        $otp = rand(1000, 9999);
        $order = Orders::where('order_id', $request->order_id)->first();
        foreach ($products as $product) {
            $ot = OrderTracking::create([
                'order_id' => $request->order_id,
                'tracking_id' => 'ORDTRK-00' . $tracking_id,
                'product_id' => $product['product_id'],
                'qty' => $product['qty'],
                'tracking' => json_encode($trackingData),
                'vendor_invoice' => $invoicePath,
                'otp' => $otp,
                'status' => $request->status ?? 'shipped',
                'created_by' => Auth::id(),
            ]);

            $orderProduct = OrderProducts::where('order_id', $request->order_id)
                ->where('product_id', $product['product_id'])
                ->first();

            if ($orderProduct) {
                $orderProduct->bal_qty -= $product['qty'];

                if ($orderProduct->bal_qty <= 0) {
                    $orderProduct->status = 'shipped';
                    $this->notifyUser($order->user_id, 'Order Shipped', 'Your Product #' . $orderProduct->product->name . ' is Shipped ' . $ot->qty . ' items.', $order->id);
                    if ($order->user && ($order->user->web_token || $order->user->mob_token)) {
                        $data = [
                            'web_token' => $order->user->web_token,
                            'mob_token' => $order->user->mob_token ?? null,
                            'title' => 'Order Accepted & Processing',
                            'body' => 'Your Order "' . $orderProduct->product->name . '" has been Shipped ' . $ot->qty . ' items.',
                            'id' => $order->id,
                            'link' => route('tracking', ['id' => $order->id]),
                        ];
                        $this->notificationService->token($data);
                    }
                } else {
                    $this->notifyUser($order->user_id, 'Order Shipped', 'Your Product #' . $orderProduct->product->name . ' is partially Shipped ' . $ot->qty . ' items.', $order->id);
                    if ($order->user && ($order->user->web_token || $order->user->mob_token)) {
                        $data = [
                            'web_token' => $order->user->web_token,
                            'mob_token' => $order->user->mob_token ?? null,
                            'title' => 'Order Accepted & Processing',
                            'body' => 'Your Order "' . $orderProduct->product->name . '" has been partially Shipped ' . $ot->qty . ' items.',
                            'id' => $order->id,
                            'link' => route('tracking', ['id' => $order->id]),
                        ];
                        $this->notificationService->token($data);
                    }
                }

                $orderProduct->save();
            }
        }
        if ($request->header('Authorization')) {

            return response()->json(['success' => true, 'message' => 'Order tracking updated successfully.'], 200);
        }
        return redirect()->route('order-accept', $request->order_id)->with('success', 'Order Tracking Updated Successfully!');
    }

    public function order_otp_update(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,order_id',
            'tracking_id' => 'required|string',
            'otp' => ['required_if:status,delivered', 'numeric'],
        ]);

        if ($validated->fails()) {
            if ($request->header('Authorization')) {
                return response()->json([
                    'success' => false,
                    'errors' => $validated->errors()
                ], 200);
            }

            return redirect()->back()
                ->withErrors($validated)
                ->withInput();
        }

        $trackings = OrderTracking::where('tracking_id', $request->tracking_id)->get();

        if (! $trackings) {
            if ($request->header('Authorization')) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Tracking ID not found.'
                ], 200);
            }
            return redirect()->back()->with('error', 'Tracking ID not found.');
        }

        // Assuming OTP is stored in a column called 'otp_code'
        if ($trackings->first()->otp != $request->otp) {
            if ($request->header('Authorization')) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Invalid OTP.'
                ], 200);
            }
            return redirect()->back()->with('success', 'Invalid OTP.');
        }

        // Update status to delivered
        OrderTracking::where('order_id', $request->order_id)->where('tracking_id', $request->tracking_id)
            ->update(['status' => 'delivered']);

        foreach ($trackings as $tracking) {
            $productId = $tracking->product_id;

            $orderProduct = OrderProducts::where('order_id', $request->order_id)
                ->where('product_id', $productId)
                ->first();

            if (! $orderProduct) {
                continue;
            }

            if ($orderProduct->bal_qty > 0) {
                continue;
            }

            $otherStatuses = OrderTracking::where('order_id', $request->order_id)
                ->where('product_id', $productId)
                ->where('status', 'shipped')
                ->exists();
            $order = Orders::where('order_id', $request->order_id)->with('user')->first();
            if (! $otherStatuses) {
                $orderProduct->status = 'delivered';
                $orderProduct->save();
                $this->notifyUser($order->user_id, 'Order Delivered', 'Your Product #' . $orderProduct->product->name . ' is Delivere.', $order->id);
                if ($order->user && ($order->user->web_token || $order->user->mob_token)) {
                    $data = [
                        'web_token' => $order->user->web_token,
                        'mob_token' => $order->user->mob_token ?? null,
                        'title' => 'Order Deliered',
                        'body' => 'Your Order "' . $orderProduct->product->name . '" has been Delivered.',
                        'id' => $order->id,
                        'link' => route('tracking', ['id' => $order->id]),
                    ];
                    $this->notificationService->token($data);
                } else {
                    $this->notifyUser($order->user_id, 'Order Partially Delivered ', 'Your Product #' . $orderProduct->product->name . ' is Partially Deliered.', $order->id);
                    if ($order->user && ($order->user->web_token || $order->user->mob_token)) {
                        $data = [
                            'web_token' => $order->user->web_token,
                            'mob_token' => $order->user->mob_token ?? null,
                            'title' => 'Order Partially Deliered',
                            'body' => 'Your Order "' . $orderProduct->product->name . '" has been Partially Deliered.',
                            'id' => $order->id,
                            'link' => route('tracking', ['id' => $order->id]),
                        ];
                        $this->notificationService->token($data);
                    }
                }
            }
        }

        $orderProducts = OrderProducts::where('order_id', $request->order_id)->get();
        $order = Orders::where('order_id', $request->order_id)->first();
        if ($order) {
            $allProductsDeliveredOrCancelled = $orderProducts->every(function ($orderProduct) {
                return in_array($orderProduct->status, ['delivered', 'cancelled']);
            });

            if ($allProductsDeliveredOrCancelled) {
                $order->status = 'delivered';
                $order->save();
            }
        }

        if ($request->header('Authorization')) {
            return response()->json(['success' => true, 'message' => 'Order marked as delivered.'], 201);
        }

        return redirect()->back()->with('success', 'Order Marked as Delivered!');
    }
}
