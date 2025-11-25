@extends('layouts.app')

@section('title', 'Onstru | Payment Success')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">

    <style>
        @media screen and (min-width: 767px) {
            .thanks-card {
                width: 35%;
            }

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
            .thanks-card {
                width: 100%;
            }

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
    </div>
    <div class="thanks-card side-cards border-0 shadow-none mt-4 mx-auto">
        <div class="cards-head d-block">
            <h5 class="text-center mb-3">Thanks for Shopping with Us...!</h5>
            {{-- <h6 class="text-center">Delivery by Monday, Jul 27, 2025</h6> --}}
        </div>
        <div class="cards-content">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-xl-12 mb-4">
                    <img src="{{ asset('assets/images/success.png') }}" class="d-flex mx-auto" height="150px" alt="">
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6>Order Code</h6>
                        <h6>#{{ $order_details->order_id }}</h6>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6>Date</h6>
                        <h6>{{ $order_details->created_at->format('d M, Y, H:i A') }}</h6>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6>Total</h6>
                        <h6>₹ {{ $order_details->total }}</h6>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6>Payment Method</h6>
                        <h6>Credit Card</h6>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-center flex-column">
                    <a href="{{ url('products') }}" class="mb-3">
                        <button class="formbtn">Continue Shopping</button>
                    </a>
                    <a href="{{ url('tracking/' .$order_details->order_id) }}">
                        <button class="removebtn">Track Your Order</button>
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection