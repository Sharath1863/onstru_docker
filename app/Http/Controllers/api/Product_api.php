<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Product_cnt;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Cashback;
use App\Models\OrderProducts;
use App\Models\Orders;
use App\Models\OrderTracking;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Product_api extends Controller
{
    // functions will be here

    public function product_created_list(Request $req)
    {

        $auth = Auth::user()->id;

        // if($auth==$req->user_id){

        // }

        $products = Products::with('categoryRelation:id,value')->withAvg('reviews', 'stars')->where('created_by', $req->user_id)->latest()->get()->map(function ($product) use ($auth, $req) {

            // $image_url = null;

            if ($auth != $req->user_id) {

                $product->sp = $product->sp + $product->cashback_price;
                $product->mrp = $product->mrp + $product->cashback_price;
            }

            return $product;
        });

        return response()->json(['success' => true, 'data' => $products], 200);
    }

    // cart 
    public function cart_api()
    {
        $auth = Auth::user()->id;
        $cartItems = Cart::with([
            'product.hub',
            'vendor:id,id,name',
        ])
            ->where('c_by', $auth)
            ->where('status', 'cart')
            ->get()
            ->groupBy('vendor_id');
        // dd($cartItems);
        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => 'Cart is Empty',
            ], 200);
        }

        $vendorAssuranceStatus = [];

        foreach ($cartItems as $vendorId => $items) {
            // Check if any product of this vendor is highlighted
            $hasAssuredProduct = Products::where('created_by', $vendorId)
                ->where('highlighted', 1)
                ->exists();

            $vendorAssuranceStatus[$vendorId] = $hasAssuredProduct ? 'highlighted' : 'not_highlighted';
        }
        $available_cashback = Cashback::where('user_id', $auth)
            ->get()
            ->keyBy('vendor_id');

        return response()->json([
            'success' => true,
            'data' => $cartItems,
            'cashback' => $available_cashback,
            'vendor_status' => $vendorAssuranceStatus,
        ], 200);
    }

    public function updateQuantity_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|numeric',
            'action' => 'required|in:plus,minus'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $itemId = $request->input('item_id');
        $action = $request->input('action');

        $cartItem = Cart::with('product')->find($itemId);

        if (! $cartItem) {
            return response()->json(['success' => false, 'msg' => 'Cart item not found']);
        }

        // Adjust quantity
        if ($action === 'plus') {
            $cartItem->quantity += 1;
        } elseif ($action === 'minus') {
            $moq = $cartItem->product->moq ?? 1;
            if ($cartItem->quantity > $moq) {
                $cartItem->quantity -= 1;
            } else {
                return response()->json([
                    'success' => false,
                    'msg' => "Minimum order quantity is {$moq}",
                ]);
            }
        }
        $cartItem->save();
        return response()->json([
            'success' => true,
            'message' => 'Quantity Updated Succesfully'
        ], 200);
    }

    public function cart_savelater_api()
    {
        $auth = Auth::user()->id;
        $cartItems = Cart::with([
            'product.hub',
            'vendor:id,id,name',
        ])
            ->where('c_by', $auth)
            ->where('status', 'saved_for_later')
            ->get()
            ->groupBy('vendor_id');

        $vendorAssuranceStatus = [];

        foreach ($cartItems as $vendorId => $items) {
            // Check if any product of this vendor is highlighted
            $hasAssuredProduct = Products::where('created_by', $vendorId)
                ->where('highlighted', 1)
                ->exists();

            $vendorAssuranceStatus[$vendorId] = $hasAssuredProduct ? 'highlighted' : 'not_highlighted';
        }
        $available_cashback = Cashback::where('user_id', $auth)
            ->get()
            ->keyBy('vendor_id');

        return response()->json([
            'success' => true,
            'data' => $cartItems,
            'cashback' => $available_cashback,
            'vendor_status' => $vendorAssuranceStatus,
        ], 200);
    }

    public function cart_toggle_api(Request $request)
    {
        $itemId = $request->input('item_id');
        $cartItem = Cart::find($itemId);
        if (! $cartItem) {
            return response()->json(['success' => false, 'message' => 'Cart item not found']);
        }
        if ($cartItem->status == 'cart') {
            $cartItem->status = 'saved_for_later';
            $cartItem->save();
            return response()->json([
                'success' => true,
                'message' => 'Item moved to Saved for Later',
            ]);
        } else {
            $cartItem->status = 'cart';
            $cartItem->save();
            return response()->json([
                'success' => true,
                'message' => 'Item moved to Cart',
            ]);
        }
    }

    public function removeFromCart_api(Request $request)
    {
        $itemId = $request->input('item_id');

        $cartItem = Cart::find($itemId);
        if (! $cartItem) {
            return response()->json(['success' => false, 'message' => 'Cart item not found']);
        }
        $cartItem->delete();
        return response()->json([
            'success' => true,
            'message' => 'Item Removed from Cart'
        ]);
    }

    public function previous_address(Request $request)
    {
        $userId = Auth::user()->id;
        $addresses =  Address::where('c_by', $userId)->latest()->take(3)->get();
        return response()->json(['success' => true, 'data' => $addresses], 200);
    }

    public function place_order_check(Request $request)
    {
        Log::info('Request Data:', $request->all());
        // dd($request->all());
        // $products = json_decode($request->products, true);
        $products = $request->products;
        $productIdsFromRequest = array_column($products, 'productId');

        $userId = Auth::user()->id;
        $cartItems = Cart::where('c_by', $userId)->get();
        if ($cartItems->isEmpty()) {
            return response()->json(
                [
                    'success' => true,
                    'data' => 'Your Cart is Empty.'
                ],
                200
            );
        }
        $cartProductIds = $cartItems->pluck('product_id')->toArray();
        $missingProducts = array_diff($productIdsFromRequest, $cartProductIds);

        // $cashbackData = json_decode($request->cashback, true); 
        $cashbackData = $request->cashback;

        foreach ($cashbackData as $cashbackItem) {
            $vendorId = $cashbackItem['vendorId'];
            $appliedCashback = $cashbackItem['vendorAppliedCashback'];

            $availableCashback = Cashback::where('vendor_id', $vendorId)->value('avail_cb');

            if ($availableCashback < $appliedCashback) {
                return response()->json([
                    'error' => 'Insufficient cashback for Vendor ' . $vendorId,
                    'vendor_id' => $vendorId,
                    'available_cashback' => $availableCashback,
                    'applied_cashback' => $appliedCashback,
                ], 422);
            }
        }

        return response()->json([
            'success' => true,
            'cartProductIds' => $cartProductIds,
            'missing_products' => $missingProducts,
            'message' => 'Cashback is sufficient for all vendors.'
        ], 200);
    }

    public function place_order(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'primary_phone' => 'required|string|max:15',
            'secondary_phone' => 'nullable|string|max:15',
            'gst_billing' => 'nullable|in:yes,no',
            // 'pre_booking' => 'nullable|in:yes,no',
            'billing_address' => 'required|string',
            'billing_pincode' => 'required|string',
            'billing_city' => 'required|string',
            'billing_state' => 'required|string',
            'billing_gst' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'shipping_pincode' => 'nullable|string',
            'shipping_city' => 'nullable|string',
            'shipping_state' => 'nullable|string',
            'shipping_gst' => 'nullable|string',
            'same_as_billing' => 'nullable|boolean',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);
        $userId = Auth::user()->id;
        $deliverableProducts = json_decode($request->products, true);
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

        $appliedCashbacks = json_decode($request->cashbacks, true);

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
            return response()->json(['success' => true, 'data' => 'Your Cart is Empty'], 200);
        }
        $validated['c_by'] = $userId;
        $address = Address::create($validated);
        $addressId = $address->id;
        // dd('here');  
        // $address_data = session('address_data');
        // dd($request->address);
        // $address_data = json_decode($request->address, true);
        // $address_data = $request->address;
        // dd($address_data);
        // $address_data['status'] = 'active';
        // $address = Address::create($address_data);
        // $addressId = $address->id;

        // $address_raw = $request->address;

        // // Try to decode JSON if possible
        // if (is_string($address_raw)) {
        //     // Clean up malformed JSON-like string if needed
        //     $address_raw = trim($address_raw);

        //     // If it looks like JSON but has unquoted keys, fix that (optional)
        //     $fixed = preg_replace('/(\w+):/', '"$1":', $address_raw);

        //     $address_data = json_decode($fixed, true);

        //     if (json_last_error() !== JSON_ERROR_NONE) {
        //         // Fallback: handle it as empty or error
        //         throw new \Exception('Invalid address format received.');
        //     }
        // } else {
        //     $address_data = $address_raw; // already array
        // }
        // $address_data['c_by'] = $userId;
        // $address_data['status'] = 'active';
        // $address = Address::create($address_data);
        // $addressId = $address->id;

        $order = Orders::create([
            // 'order_id' => 'ONSTRUORD00' . strtoupper(Str::random(2)),
            'user_id' => $userId,
            'address_id' => $addressId ?? 1,
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
        // Log::info('Sending notification to vendor: ' . $vendorId);

        // Ensure vendor is present and tokens exist
        // $vendor = UserDetail::find($vendorId);
        // $this->notifyUser(
        //     $vendorId,
        //     'New Order Received',
        //     'You have received a new order #' . $order->order_id . ' from ' . Auth::user()->name,
        //     $order->id
        // );
        // if ($vendor && ($vendor->web_token || $vendor->mob_token)) {
        // Log::info('Vendor Tokens - Web: ' . $vendor->web_token . ' | Mob: ' . $vendor->mob_token);

        // $data = [
        //         'web_token' => $vendor->web_token,
        //         'mob_token' => $vendor->mob_token ?? null,
        //         'title' => 'New Order Received',
        //         'body' => 'You have received a new order #' . $order->order_id . ' from ' . Auth::user()->name,
        //         'id' => $order->id,
        //         'link' => url('order-accept/', ['id' => $order->id])
        //     ];

        // } else {
        //     Log::info('No valid tokens found for vendor: ' . $vendorId);
        // }
        // }


        Cart::where('c_by', $userId)
            ->where('status', 'cart')
            ->whereIn('product_id', $deliverableProductIds)
            ->delete();

        return response()->json(['success' => true, 'data' => 'Order Placed successfully'], 200);
    }

    //order api
    public function order_list_api()
    {
        $auth = Auth::user()->id;
        $orders = Orders::where('user_id', $auth)->withCount(['products'])->latest()->get();
        return response()->json(['success' => true, 'data' => $orders], 200);
    }

    public function buyer_product_list_api(Request $request)
    {
        // dd('here');
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,order_id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $order_id = $request->order_id;
        $products = OrderProducts::where('order_id', $order_id)->with('product')->get();

        $order_details = Orders::where('order_id', $order_id)
            ->with([
                'user:id,name,user_name,profile_img',
                'address',
            ])
            ->first();

        $order_details->subtotal = $products->sum(function ($product) {
            return ($product->base_price + $product->cashback + $product->margin + (($product->base_price + $product->cashback + $product->margin) * $product->tax / 100)) * $product->quantity;
        });
        
        foreach($products as $product){
            $product->base_price = $product->base_price + $product->cashback + $product->margin;
        }
        // $products->subtotal = $products->sum(function ($product) {
        //     return ($product->base_price + $product->cashback_price + $product->margin);
        // });
        $order_details->shipping_total = $products->sum('shipping');

        $order_details->grand_total = floor($order_details->subtotal + $order_details->shipping_total);
        $data = [
            'products' => $products,
            'order_details' => $order_details
        ];
        return response()->json([
            'success' => true,
            'data' => $data,
            // 'Order_details' => $order_details
        ], 200);
    }

    public function order_products_list_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,order_id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $auth = Auth::user()->id;
        $order_id = $request->order_id;
        // $products = OrderProducts::with('product')
        //     ->whereHas('product', function ($q2) use ($auth) {
        //         $q2->where('created_by', $auth);
        //     })
        //     ->where('order_id', $order_id)
        //     ->get();
        $order_details = Orders::where('order_id', $order_id)
            ->with([
                'products' => function ($q) use ($auth) {
                    $q->whereHas('product', function ($q2) use ($auth) {
                        $q2->where('created_by', $auth);
                    })
                        ->with('product:id,id,created_by,name,cover_img');
                },
                'user:id,name,user_name,profile_img',
                'address',
            ])
            ->first();
        $order_details->subtotal = $order_details->products->sum(function ($product) {
            return ($product->base_price + ($product->base_price * $product->tax / 100)) * $product->quantity;
        });
        $order_details->shipping_total = $order_details->products->sum('shipping');
        // }
        $order_details->grand_total = floor($order_details->subtotal + $order_details->shipping_total);

        return response()->json(
            [
                'success' => true,
                'data' => $order_details,
                // 'Order_details' => $order_details
            ],
            200
        );
    }

    public function order_products_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,order_id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $auth = Auth::user()->id;
        $order_id = $request->order_id;
        // $products = OrderProducts::where('order_id', $order_id)->with('product.hub')->get();
        $products = OrderProducts::where('order_id', $order_id)
            ->where('bal_qty', '!=', 0)
            ->whereHas(
                'product',
                function ($q) use ($auth) {
                    $q->where('created_by', $auth);
                }
            )
            ->with('product.hub')
            ->get();
        $tracking = OrderTracking::where('order_id', $order_id)
            ->whereHas(
                'product',
                function ($q) use ($auth) {
                    $q->where('created_by', $auth);
                }
            )
            ->exists();
        $grouped = $products->groupBy(function ($item) {
            return optional($item->product)->hub_id;
        });
        $order_details = Orders::where('order_id', $order_id)
            ->with([
                'user:id,name,user_name,profile_img',
                'address',
            ])
            ->first();

        $order_details->subtotal = $products->sum(function ($product) {
            return ($product->base_price + ($product->base_price * $product->tax / 100)) * $product->quantity;
        });
        $order_details->shipping_total = $products->sum('shipping');

        $order_details->grand_total = floor($order_details->subtotal + $order_details->shipping_total);

        return response()->json(
            [
                'success' => true,
                'data' => $grouped,
                'order_details' => $order_details,
                'tracking' => $tracking
            ],
            200
        );
    }

    public function vendor_pending_orders_api()
    {
        $auth = Auth::user()->id;
        $orders = Orders::select('id', 'order_id', 'created_at', 'user_id', 'address_id')
            ->whereHas('products', function ($q) use ($auth) {
                // same constraints as in your with
                $q->where('status', 'pending')
                    ->whereHas('product', function ($q2) use ($auth) {
                        $q2->where('created_by', $auth);
                    });
            })
            ->with([
                'products' => function ($q) use ($auth) {
                    $q->where('status', 'pending')
                        ->whereHas('product', function ($q2) use ($auth) {
                            $q2->where('created_by', $auth);
                        })
                        ->with('product:id,id,created_by');
                },
                'user:id,name,user_name,profile_img',
                'address',
            ])
            ->latest()
            ->get();

        // dd($orders);
        return response()->json(
            [
                'success' => true,
                'data' => $orders
            ],
            200
        );
    }

    public function vendor_processing_orders_api()
    {
        $auth = Auth::user()->id;
        $orders = Orders::select('id', 'order_id', 'created_at', 'user_id', 'address_id')
            ->whereHas('products', function ($q) use ($auth) {
                // same constraints as in your with
                $q->where('status', 'processing')
                    ->whereHas('product', function ($q2) use ($auth) {
                        $q2->where('created_by', $auth);
                    });
            })
            ->with([
                'products' => function ($q) use ($auth) {
                    $q->where('status', 'processing')
                        ->whereHas('product', function ($q2) use ($auth) {
                            $q2->where('created_by', $auth);
                        })
                        ->with('product:id,id,created_by');
                },
                'user:id,name,user_name,profile_img',
                'address',
            ])
            ->latest()
            ->get();

        // dd($orders);
        return response()->json(
            [
                'success' => true,
                'data' => $orders
            ],
            200
        );
    }

    public function vendor_shipped_orders_api()
    {
        $auth = Auth::user()->id;
        $orders = Orders::select('id', 'order_id', 'created_at', 'user_id', 'address_id')
            ->whereHas('products', function ($q) use ($auth) {
                // same constraints as in your with
                $q->where('status', 'shipped')
                    ->whereHas('product', function ($q2) use ($auth) {
                        $q2->where('created_by', $auth);
                    });
            })
            ->with([
                'products' => function ($q) use ($auth) {
                    $q->where('status', 'shipped')
                        ->whereHas('product', function ($q2) use ($auth) {
                            $q2->where('created_by', $auth);
                        })
                        ->with('product:id,id');
                },
                'user:id,name,user_name,profile_img',
                'address',
            ])
            ->latest()
            ->get();

        // dd($orders);
        return response()->json(
            [
                'success' => true,
                'data' => $orders
            ],
            200
        );
    }

    public function vendor_delivered_orders_api()
    {
        $auth = Auth::user()->id;
        $orders = Orders::select('id', 'order_id', 'created_at', 'user_id', 'address_id')
            ->whereHas('products', function ($q) use ($auth) {
                // same constraints as in your with
                $q->where('status', 'delivered')
                    ->whereHas('product', function ($q2) use ($auth) {
                        $q2->where('created_by', $auth);
                    });
            })
            ->with([
                'products' => function ($q) use ($auth) {
                    $q->where('status', 'delivered')
                        ->whereHas('product', function ($q2) use ($auth) {
                            $q2->where('created_by', $auth);
                        })
                        ->with('product:id,id');
                },
                'user:id,name,user_name,profile_img',
                'address',
            ])
            ->latest()
            ->get();

        // dd($orders);
        return response()->json(
            [
                'success' => true,
                'data' => $orders
            ],
            200
        );
    }

    public function order_accept_api(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,order_id'
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $validatedData = $validated->validated();
        $orderId = $validatedData['order_id'];

        $userId = Auth::user()->id;

        $orderProducts = OrderProducts::where('order_id', $orderId)
            ->whereHas('product', function ($query) use ($userId) {
                $query->where('created_by', $userId);
            })
            ->get();

        if ($orderProducts->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'There is no product for you in this order list']);
        }

        foreach ($orderProducts as $orderProduct) {
            $orderProduct->status = 'processing';
            $orderProduct->save();
        }

        return response()->json(['success' => true, 'message' => 'Order Products Accepted', 'data' => $orderProducts], 200);
    }

    public function product_tracking(Request $request)
    {
        // dd('here');
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,order_id',
            'product_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $order_id = $request->order_id;
        $product_id = $request->product_id;
        $trackings = OrderTracking::where('order_id', $order_id)->where('product_id', $product_id)->with('product')->get();
        $op = OrderProducts::where('order_id', $order_id)->where('product_id', $product_id)->with(['order.address', 'order.user'])->first();
        $baseSubtotal = $op->base_price + $op->cashback + $op->margin;
        $taxAmount = ($baseSubtotal * $op->tax) / 100;
        $op->subtotal = $baseSubtotal + $taxAmount + $op->shipping;
        $data = [
            'tracking' => $trackings,
            'order_product' => $op,
        ];
        return response()->json(
            [
                'success' => true,
                'data' => $data
            ],
            200
        );
    }

    public function tracking_list_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,order_id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $userId = Auth::user()->id;
        $order_id = $request->order_id;
        $trackings = OrderTracking::select('id', 'order_id', 'tracking_id', 'tracking', 'product_id', 'qty', 'status', 'created_at', 'updated_at')
            ->where('order_id', $order_id)
            ->whereHas('product', function ($query) use ($userId) {
                $query->where('created_by', $userId);
            })
            ->with('product:id,name,cover_img')
            ->get();
        $groupedTrackings = $trackings->groupBy('tracking_id');

        $order_details = Orders::where('order_id', $order_id)
            ->with([
                'products' => function ($q) use ($userId) {
                    $q->whereHas('product', function ($q2) use ($userId) {
                        $q2->where('created_by', $userId);
                    })
                        ->with('product:id,id,created_by,name,cover_img');
                },
                'user:id,name,user_name,profile_img',
                'address',
            ])
            ->first();
        $order_details->subtotal = $order_details->products->sum(function ($product) {
            return ($product->base_price + ($product->base_price * $product->tax / 100)) * $product->quantity;
        });
        $order_details->shipping_total = $order_details->products->sum('shipping');
        // }
        $order_details->grand_total = floor($order_details->subtotal + $order_details->shipping_total);
        return response()->json([
            'success' => true,
            'data' => $groupedTrackings,
            'Order_details' => $order_details
        ], 200);
    }

    public function track_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tracking_id' => 'required|exists:order_tracking,tracking_id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $track = OrderTracking::where('tracking_id', $request->tracking_id)->first();

        $order = Orders::select('id', 'order_id', 'address_id')->where('order_id', $track->order_id)->with('address:id,latitude,longitude')->first();
        $product = Products::select('id', 'name', 'hub_id')->where('id', $track->product_id)->with('hub:id,latitude,longitude')->first();
        return response()->json([
            'success' => true,
            'data' => $track,
            'order' => $order,
            'product' => $product
        ], 200);
    }

    // function for product profile api

    public function product_profile_api(Request $req)
    {
        $product = Products::find($req->product_id);

        if (! $product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Decode images field (JSON)
        $images = [];
        if (! empty($product->image)) {
            $images = is_array($product->image) ? $product->image : json_decode($product->image, true);
        }

        // Combine cover_img and other images
        $allImages = [];

        // Add cover_img first (if exists)
        if (! empty($product->cover_img)) {
            $allImages[] = ($product->cover_img);
        }

        // Add remaining images from `image` field
        foreach ($images as $imgPath) {
            if (! empty($imgPath)) {
                $allImages[] = ($imgPath);
            }
        }

        $specifications = [];
        if (! empty($product->specifications)) {
            $specifications = is_array($product->specifications) ? $product->specifications : json_decode($product->specifications, true);
        }

        return response()->json([
            'id' => $product->id ?? null,
            'name' => $product->name ?? '',
            'brand_name' => $product->brand_name ?? '',
            'category' => $product->category ?? '',
            'd_from' => $product->d_from ?? 0,
            'd_to' => $product->d_to ?? 0,
            'availability' => $product->availability ?? '',
            'location' => $product->location ?? '',
            'hsn' => $product->hsn ?? '',
            'mrp' => $product->mrp ?? 0,
            'sp' => $product->sp ?? 0,
            'tax_percentage' => $product->tax_percentage ?? 0,
            'product_unit' => $product->product_unit ?? '',
            'cashback_price' => $product->cashback_price ?? 0,
            'cashback_percentage' => $product->cashback_percentage ?? 0,
            'moq' => $product->moq ?? 1,
            'base_price' => $product->base_price ?? 0,
            'ship_method' => $product->ship_method ?? '',
            'ship_charge' => $product->ship_charge ?? 0,
            'key_feature' => $product->key_feature ?? '',
            'size' => $product->size ?? '',
            'catlogue' => $product->catlogue ?? '',
            'remark' => $product->remark ?? '',
            'description' => $product->description ?? '',
            'approvalstatus' => $product->approvalstatus ?? 'pending',
            'images' => $allImages ?? [],
            'specifications' => $specifications ?? [],
            'created_at' => $product->created_at ? $product->created_at->toDateTimeString() : null,
        ], 200);
    }

    // fucntion for product created by list

    // public function product_search_api(Request $req)
    // {
    //     $query = Products::query();

    //     if ($req->has('name')) {
    //         $query->where('name', 'like', '%' . $req->name . '%');
    //     }

    //     if ($req->has('category')) {
    //         $query->where('category', $req->category);
    //     }

    //     if ($req->has('brand_name')) {
    //         $query->where('brand_name', 'like', '%' . $req->brand_name . '%');
    //     }

    //     if ($req->has('min_price')) {
    //         $query->where('sp', '>=', $req->min_price);
    //     }

    //     if ($req->has('max_price')) {
    //         $query->where('sp', '<=', $req->max_price);
    //     }

    //     // Add more filters as needed

    //     $products = $query->where('approvalstatus', 'approved')->latest()->get()->map(function ($product) {

    //         $image_url = null;

    //         if (!empty($product->cover_img)) {
    //             $image_url = $product->cover_img;
    //         } elseif (!empty($product->image)) {
    //             $images = is_array($product->image) ? $product->image : json_decode($product->image, true);
    //             $image_url = $images['image1'] ?? null;
    //         }

    //         return [
    //             'id' => $product->id,
    //             'name' => $product->name,
    //             'description' => $product->description,
    //             'mrp' => $product->mrp,
    //             'sp' => $product->sp,
    //             'approvalstatus' => $product->approvalstatus,
    //             'image_url' => $image_url,
    //             'created_at' => $product->created_at->toDateTimeString(),
    //             'specifications' => $product->specifications ? json_decode($product->specifications, true) : [],
    //         ];
    //     });

    //     return response()->json(['products' => $products], 200);
    // }
}
