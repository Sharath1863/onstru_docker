@extends('layouts.app')

@section('title', 'Onstru | Payment Process')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">

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
        <div class="body-head">
            <a href="javascript:history.back()">
                <h5><i class="fas fa-angle-left pe-1"></i> Back</h5>
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
                <div class="line bg-warning mt-2"></div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-sm-12 col-md-3">
                <div class="col-12 p-3 border shadow-sm rounded form-div">
                    <div class="body-head d-block mb-3">
                        <h5 class="mb-1">Payment</h5>
                        <h6>Choose Payment Method</h6>
                    </div>
                    <label for="upi" class="p-2 d-flex align-items-center gap-2 border rounded-2 w-100">
                        <input type="radio" name="pay_method" id="upi">
                        <label for="upi" class="mb-0">UPI Payment</label>
                    </label>
                    <label for="credit" class="p-2 d-flex align-items-center gap-2 border rounded-2 w-100">
                        <input type="radio" name="pay_method" id="credit">
                        <label for="credit" class="mb-0">Credit Card</label>
                    </label>
                    <label for="bank" class="p-2 d-flex align-items-center gap-2 border rounded-2 w-100">
                        <input type="radio" name="pay_method" id="bank">
                        <label for="bank" class="mb-0">Bank Transfer</label>
                    </label>
                    <label for="net" class="p-2 d-flex align-items-center gap-2 border rounded-2 w-100">
                        <input type="radio" name="pay_method" id="net">
                        <label for="net" class="mb-0">Net Banking</label>
                    </label>
                </div>
            </div>

            <div class="col-sm-12 col-md-6 form-div p-0">
                <div class="rounded border p-3">
                    <div class="col-sm-12 col-md-12 mb-2">
                        <label for="payment_in">Payment Method</label>
                        <select name="" id="payment_in" class="form-select">
                            <option value="dc" selected>Debit Card</option>
                            <option value="cc">Credit Card</option>
                            <option value="upi">UPI</option>
                            <option value="">Net Banking</option>
                        </select>
                    </div>
                    <div id="card_details" class="opt-div row">
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="cardno">Card Number</label>
                            <input type="number" name="" id="cardno" class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="expdate">Expiry Date</label>
                            <input type="month" name="" id="expdate" class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="ccv">CCV</label>
                            <input type="text" name="" id="ccv" class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="cardname">Card Holder Name</label>
                            <input type="text" name="" id="cardname" class="form-control">
                        </div>
                    </div>
                    <div id="upi_details" class="opt-div row">
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="upiid">UPI ID <span>*</span></label>
                            <input type="number" name="" id="upiid" class="form-control">
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ url('payment-success') }}">
                            <button class="formbtn w-100">Pay Now</button>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-3">
                <div class="col-12 p-3 mt-3 shadow-sm border rounded">
                    <div class="body-head mb-3">
                        <h5>Price Details</h5>
                    </div>
                    <div class="side-cards p-0 shadow-none border-0">
                        @php
                            $totalAmount = 0;
                        @endphp

                        @foreach ($cartItems as $vendorId => $items)
                            @php
                                $vendor = $items->first()->vendor;
                                $vendorTotal = 0;

                                foreach ($items as $item) {
                                    $qty = $item->quantity;
                                    $product = $item->product;
                                    $price = $product->sp;
                                    $shipping = $product->ship_charge;

                                    if ($product->ship_method === 'Fixed') {
                                        $vendorTotal += $price * $qty + $shipping;
                                    } else {
                                        $vendorTotal += ($price + $shipping) * $qty;
                                    }
                                }

                                $totalAmount += $vendorTotal; // Add vendor total to totalAmount
                            @endphp
                            <div class="cards-content d-flex align-items-center justify-content-between mb-1">
                                <h6>{{ $vendor->name }}</h6>
                                <h6>₹ {{ number_format($vendorTotal, 2) }}</h6>
                            </div>
                        @endforeach
                        @php
                            $totalCashback = 0;
                            foreach ($cartItems as $vendorId => $items) {
                                if ($available_cashback->has($vendorId)) {
                                    $totalCashback += $available_cashback[$vendorId]->applied_cb ?? 0;
                                }
                            }
                            $grandTotal = $totalAmount - $totalCashback;
                        @endphp
                        <div class="cards-content d-flex align-items-center justify-content-between mb-1">
                            <h6>Cashback</h6>
                            <h6 class="text-success">- ₹ {{ number_format($totalCashback, 2) }}</h6>
                        </div>
                        <hr class="my-2">
                        <div class="cards-content d-flex align-items-center justify-content-between mb-2">
                            <h5>Total Amount</h5>
                            <h5>₹ {{ number_format($grandTotal, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            $('.opt-div').hide();

            function togglePaymentDetails() {
                $('.opt-div').hide();
                var selected = $('#payment_in').val();
                if (selected === 'dc' || selected === 'cc') {
                    $('#card_details').show();
                } else if (selected === 'upi') {
                    $('#upi_details').show();
                }
            }
            togglePaymentDetails();
            $('#payment_in').change(function () {
                togglePaymentDetails();
            });
        });
    </script>

@endsection