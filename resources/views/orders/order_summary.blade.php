@extends('layouts.app')

@section('title', 'Onstru | Order Summary')

@section('content')
{{-- @php
    $addresses = session('address_data', null);
@endphp --}}
{{-- @dd($addresses) --}}
{{-- @dd(session()->all()) --}}
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list.css') }}">

    <style>
        @media screen and (min-width: 767px) {
            .header-section {
                width: 60%;
                display: grid;
                grid-template-columns: repeat(3, 32%);
                align-items: center;
                justify-content: space-between;
                margin-inline: auto;
            }
        }

        @media screen and (max-width: 767px) {
            .header-section {
                width: 100%;
                display: grid;
                grid-template-columns: repeat(1, 1fr);
                align-items: center;
                justify-content: start;
            }
        }
    </style>

    <div class="container-xl main-div">
        <div class="body-head mb-3">
            <a href="javascript:history.back()">
                <h5>
                    <i class="fas fa-angle-left pe-1"></i> Back
                </h5>
            </a>
        </div>

        <div class="body-head header-section my-4">
            <div class="mb-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_1.png') }}" height="30px" alt="">
                    <h6 class="mb-0">Address</h6>
                </div>
                <div class="line bg-warning mt-2"></div>
            </div>
            <div class="mb-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_2.png') }}" height="30px" alt="">
                    <h6 class="mb-0">Order Summary</h6>
                </div>
                <div class="line bg-warning mt-2"></div>
            </div>
            <div class="mb-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_3.png') }}" height="30px" alt="">
                    <h6 class="mb-0">Payment</h6>
                </div>
                <div class="line bg-secondary mt-2"></div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="row mt-3">
            <div class="col-sm-12 col-md-8 mb-2">
                <div class="p-3 border rounded-2">
                    <div class="body-head mb-3">
                        <h5>Order Summary</h5>
                    </div>
                    <div class="listtable p-0 border-0">
                        <div class="table-wrapper">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>MRP</th>
                                        <th>Price</th>
                                        <th>Shipping</th>
                                        <th>Tax%</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalAmount = 0; @endphp
                                    @foreach ($cartItems as $vendorId => $items)
                                        @foreach ($items as $item)
                                            <tr>
                                                <th>{{ $item->product->name }}</th>
                                                <td>{{ $item->quantity }}</td>
                                                <td>₹ {{ $item->product->mrp }}</td>
                                                <td>₹ {{ $item->product->base_price + $item->product->cashback_price + $item->product->margin}}</td>
                                                <td>₹ {{ number_format($item->transport / (1 + ($item->product->tax_percentage / 100)), 0) }}</td>
                                                <td>{{ $item->product->tax_percentage }}%</td>
                                                <td>{{ ((($item->product->base_price + $item->product->cashback_price + $item->product->margin) * $item->quantity) * (1 + $item->product->tax_percentage / 100)) + $item->transport }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Price Summary -->
            <div class="col-sm-12 col-md-4">
                <div class="side-cards shadow-none mb-2">
                    <div class="cards-content">
                        <div class="mb-3">
                            <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                            <h6 class="mb-1">Contact Number</h6>
                            <h6 class="mb-1 text-muted">
                                {{ $addresses['primary_phone'] }} - {{ $addresses['secondary_phone'] ?? '' }}
                            </h6>
                        </div>
                        <div class="mb-3">
                            <h5 class="mb-1">Billing Address</h5>
                            <h6 class="mb-1 text-muted">
                                {{ $addresses['billing_address'] }},
                                {{ $addresses['billing_city'] }},
                                {{ $addresses['billing_state'] }},
                                {{ $addresses['billing_pincode'] }}
                            </h6>
                        </div>
                        <div>
                            <h5 class="mb-1">Shipping Address</h5>
                            <h6 class="mb-1 text-muted">
                                {{ $addresses['shipping_address'] }},
                                {{ $addresses['shipping_city'] }},
                                {{ $addresses['shipping_state'] }},
                                {{ $addresses['shipping_pincode'] }}
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="col-12 p-3 border rounded">
                    <div class="body-head mb-3">
                        <h5>Price Details</h5>
                    </div>
                    <div class="side-cards p-0 shadow-none border-0">
                        @php
                            $totalAmount = 0;
                            $totalCashback = 0;
                        @endphp

                        @foreach ($cartItems as $vendorId => $items)
                            @php
                                $vendor = $items->first()->vendor;
                                $vendorTotal = 0;

                                foreach ($items as $item) {
                                    $qty = $item->quantity;
                                    $product = $item->product;

                                    $pricePerUnit = $product->base_price + $product->cashback_price + $item->product->margin;
                                    $shipping = $item->transport / (1 + ($item->product->tax_percentage / 100)) ?? 0;
                                    $subtotal = ($pricePerUnit * $qty) + $shipping;

                                    $tax = ($subtotal * $product->tax_percentage) / 100;

                                    $itemTotal = $subtotal + $tax;

                                    $vendorTotal += $itemTotal;
                                }

                                $vendorCashback = $appliedCashbacks[$vendorId] ?? 0;
                                $vendorPayable = $vendorTotal - $vendorCashback;

                                $totalAmount += $vendorTotal;
                                $totalCashback += $vendorCashback;
                            @endphp

                            {{-- Vendor Price Summary --}}
                            <div class="cards-content mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h6 class="mb-1">{{ $vendor->name }}</h6>
                                    <h6 class="mb-1">₹ {{ number_format($vendorTotal, 2) }}</h6>
                                </div>

                                @if ($vendorCashback > 0)
                                    <div class="d-flex align-items-center justify-content-between text-success small">
                                        <h6 class="mb-1">Cashback Applied</h6>
                                        <h6 class="mb-1">- ₹ {{ number_format($vendorCashback, 2) }}</h6>
                                    </div>
                                @endif

                                <div class="d-flex align-items-center justify-content-between">
                                    <h6 class="mb-1">Payable</h6>
                                    <h6 class="mb-1">₹ {{ number_format($vendorPayable, 2) }}</h6>
                                </div>
                            </div>
                        @endforeach
                        <hr class="my-2">
                        <div class="cards-content d-flex align-items-center justify-content-between mb-1">
                            <h6>Total Cashback</h6>
                            <h6 class="text-success">₹ {{ number_format($totalCashback, 2) }}</h6>
                        </div>
                        <div class="cards-content d-flex align-items-center justify-content-between mb-2">
                            <h5>Grand Total</h5>
                            <h5 id="grandTotal">₹ {{ number_format($totalAmount - $totalCashback, 0) }}</h5>
                        </div>

                        {{-- <div>
                            <a href="{{ route('payment-process') }}">
                                <button class="formbtn w-100">Place Order</button>
                            </a>
                        </div> --}}
                        <div>
                            <button id="payBtn" class="formbtn w-100">Pay Now</button>
                        </div>
                        <!-- <div>
                            <a href="{{ url('payment-success') }}">
                                <button class="formbtn w-100">Pay Now</button>
                            </a>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#payBtn').click(function(e) {
                e.preventDefault();

                if (!confirm("Are you sure you want to proceed with the payment?")) {
                    return;
                }

                let payBtn = $(this);
                payBtn.prop('disabled', true).text('Processing...');

                let amountText = $('#grandTotal').text().replace(/[₹, ]/g, '');
                let amount = parseFloat(amountText);

                const token = $('meta[name="csrf-token"]').attr('content');
                const mode = "{{ env('CASHFREE_ENV') === 'sandbox' ? 'sandbox' : 'production' }}";

                $.ajax({
                    url: "{{ route('product-pay') }}",
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": token },
                    data: JSON.stringify({ amount: amount }),
                    contentType: "application/json",
                    success: function(data) {
                        payBtn.prop('disabled', false).text('Pay Now');

                        if (data.payment_session_id) {
                            const cashfree = Cashfree({ mode: mode });

                            cashfree.checkout({
                                paymentSessionId: data.payment_session_id,
                                redirectTarget: "_blank" // or "_self" for same tab
                            });
                        } else {
                            alert("Payment session ID not found!");
                        }
                    },
                    error: function() {
                        payBtn.prop('disabled', false).text('Pay Now');
                        alert("Something went wrong. Please try again.");
                    }
                });
            });
        });
    </script>

@endsection