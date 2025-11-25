@extends('layouts.app')

@section('title', 'Onstru | Order Tracking')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/tracking.css') }}">

    <style>
        .post-cards .side-cards.active {
            border: 2px solid var(--main);
        }

        .post-cards.scroll-cards {
            display: flex;
            gap: 10px;
            align-items: start;
            justify-content: start;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }

        .post-cards.scroll-cards::-webkit-scrollbar {
            display: none;
        }

        .post-cards.scroll-cards {
            scrollbar-width: none !important;
        }

        .post-cards .side-cards {
            width: 350px !important;
        }

        .star {
            font-size: 24px;
            color: var(--gray);
            cursor: pointer;
        }

        .star.selected,
        .star.hover {
            color: var(--main);
        }
    </style>


    @php
        $cashbacks = json_decode($order->cashback, true); // true = associative array
    @endphp

    <div class="container-xl main-div">
        <div class="body-head mb-3">
            <a href="javascript:history.back()">
                <h5><i class="fas fa-angle-left pe-1"></i> Back</h5>
            </a>
            {{-- <form action="{{ route('customer-inv') }}" method="post" target="_blank">
                @csrf
                <input type="hidden" name="vendor_id" value="{{ $order->products->first()->vendor_order }}">
                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                <button type="submit" class="btn m-0 p-0 border-0 bg-none">
                    <i class="fas fa-print" data-bs-toggle="tooltip" data-bs-title="Print Invoice"></i>
                </button>
            </form> --}}
        </div>

        <div class="tabs-list" id="myTab" role="tablist">
            <div class="post-cards scroll-cards mb-3">
                @foreach ($vendors as $vendorId => $items)
                    <div class="side-cards {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab"
                        data-bs-target="#track_{{ $vendorId }}" role="tab">
                        <div class="cards-content">
                            <div class="mb-1 d-flex align-items-center justify-content-between flex-wrap">
                                <h6 class="text-muted">Order ID</h6>
                                <h6>{{ $items->first()->order_id ?? 'N/A' }}</h6>
                            </div>
                            <div class="mb-1 d-flex align-items-center justify-content-between flex-wrap">
                                <h6 class="text-muted">Order Date</h6>
                                <h6>{{ $items->first()->created_at->format('d M, Y') ?? 'N/A' }}</h6>
                            </div>
                            <div class="mb-1 d-flex align-items-center justify-content-between flex-wrap">
                                <h6 class="text-muted">Business Legal Name</h6>
                                <h6>{{ $items->first()->product->vendor->name ?? 'N/A' }}</h6>
                            </div>
                            <div class="mb-1 d-flex align-items-center justify-content-between flex-wrap">
                                <h6 class="text-muted">Email ID</h6>
                                <h6>{{ $items->first()->product->vendor->email ?? 'N/A' }}</h6>
                            </div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <h6 class="text-muted mb-0">Contact Number</h6>
                                <h6 class="mb-0">+91 {{ $items->first()->product->vendor->number ?? 'N/A' }}</h6>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="tab-content" id="myTabContent">
                @foreach ($vendors as $vendorId => $items)
                    @php
                        $firstProduct = $items->first();
                        $tracking = json_decode($firstProduct->tracking ?? '{}', true);
                    @endphp

                    <!-- Price Summary -->
                    @php
                        $itemCount = $items->sum('quantity');
                        $subTotal = 0;
                        foreach ($items as $item) {
                            $price = $item->base_price + $item->cashback + $item->margin;
                            $qty = $item->quantity;
                            $shipCharge = $item->shipping;
                            $total = $price * $qty * (1 + $item->tax / 100) + $shipCharge;
                            $subTotal += $total;
                        }
                        $cashback = $cashbacks[$vendorId] ?? 0;
                        $finalAmount = $subTotal - $cashback;
                    @endphp
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="track_{{ $vendorId }}"
                        role="tabpanel">
                        <div class="body-head mb-3">
                            <h5>Delivery Details</h5>
                        </div>
                        <div class="product-cards mb-3">
                            <div class="side-cards h-100">
                                <div class="cards-content">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <h6 class="mb-3 text-uppercase">Payment Information</h6>
                                    </div>
                                    <div class="d-flex align-items-start justify-content-between flex-wrap">
                                        <h6 class="mb-2 text-muted">Payment Status</h6>
                                        <h6 class="mb-2 green-label">Paid</h6>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <h6 class="mb-2 text-muted">Transaction ID</h6>
                                        <h6 class="mb-2">TNX0123456789</h6>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <h6 class="mb-2 text-muted">Date</h6>
                                        <h6 class="mb-2">July 20, 2025 - 02:15 PM</h6>
                                    </div>
                                </div>
                            </div>

                            <div class="side-cards h-100">
                                <div class="cards-content">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <h6 class="mb-3 text-uppercase">Billing Address</h6>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <h6 class="mb-2 text-muted">
                                            {{ $order->address->first_name ?? 'N/A' }}
                                            {{ $order->address->last_name ?? 'N/A' }}
                                        </h6>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <h6 class="mb-2 text-dark">
                                            <i class="fas fa-phone pe-1"></i>
                                            <span class="text-muted">+91
                                                {{ $order->address->primary_phone ?? 'N/A' }}
                                                {{ $order->address->secondary_phnoe ?? '' }}</span>
                                        </h6>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <h6 class="mb-2 text-dark">
                                            <i class="fas fa-location-dot pe-1"></i>
                                            <span class="text-muted">
                                                {{ $order->address->billing_address ?? 'N/A' }},
                                                {{ $order->address->billing_city }},
                                                {{ $order->address->billing_pincode ?? '' }}
                                            </span>
                                        </h6>
                                    </div>
                                </div>
                            </div>

                            <div class="side-cards h-100">
                                <div class="cards-content">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <h6 class="mb-3 text-uppercase">Price Details</h6>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <h6 class="mb-2 text-muted">
                                            {{ $items->first()->product->vendor->name ?? 'Vendor' }} ({{ $itemCount }}
                                            Items)
                                        </h6>
                                        <h6 class="mb-2">
                                            ₹ {{ number_format($subTotal, 0) }}
                                        </h6>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <h6 class="mb-2 text-muted">Cashback</h6>
                                        <h6 class="mb-2">
                                            ₹ {{ number_format($cashback, 0) }}
                                        </h6>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <h6 class="mb-2 text-muted">Total Amount</h6>
                                        <h6 class="mb-2">
                                            ₹ {{ number_format($finalAmount, 0) }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-8">
                                <div class="body-head mb-3">
                                    <h5>Order Summary</h5>
                                </div>
                                <div class="p-3 border rounded-2">
                                    <div class="listtable p-0 border-0">
                                        <div class="table-wrapper">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>MRP</th>
                                                        <th>Qty</th>
                                                        <th>Price</th>
                                                        <th>Tax</th>
                                                        <th>Transport</th>
                                                        <th>Total</th>
                                                        {{-- <th>Charges Method</th> --}}
                                                        @if ($item->status == 'delivered')
                                                            <th>Review</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{-- @dd($items) --}}
                                                    @foreach ($items as $item)
                                                        <tr>
                                                            <td>{{ $item->product->name }}</td>
                                                            <td>₹ {{ $item->product->mrp }}</td>
                                                            <td>{{ $item->quantity }}</td>
                                                            <td>₹ {{ $item->base_price + $item->cashback + $item->margin }}
                                                            </td>
                                                            <td>{{ $item->tax }}%</td>
                                                            <td>₹ {{ $item->shipping }}</td>
                                                            <td>₹
                                                                {{ ($item->base_price + $item->cashback + $item->margin) * $item->quantity * (1 + $item->tax / 100) }}
                                                            </td>
                                                            <td>
                                                                @if ($item->status == 'delivered')
                                                                    @if (!isset($userReviews[$item->product->id]))
                                                                        <button type="button" class="listtdbtn px-2"
                                                                            id="reviewButton{{ $item->product->id }}"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#reviewModal{{ $item->product->id }}">
                                                                            Review
                                                                        </button>
                                                                    @else
                                                                        <button class="listtdbtn px-2"
                                                                            disabled>Reviewed</button>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <!-- Modal -->
                                                        <div class="modal fade" id="reviewModal{{ $item->product->id }}"
                                                            data-bs-backdrop="static" data-bs-keyboard="false"
                                                            tabindex="-1" aria-labelledby="reviewModalLabel"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">
                                                                            {{ $item->product->name }}
                                                                        </h4>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body mt-2">
                                                                        <div class="col-sm-12 mb-2">
                                                                            <label
                                                                                for="rating{{ $item->product->id }}">Rating
                                                                                <span>*</span></label>
                                                                            <div class="star-rating"
                                                                                data-product="{{ $item->product->id }}">
                                                                                @for ($i = 1; $i <= 5; $i++)
                                                                                    <i class="fa fa-star star"
                                                                                        data-value="{{ $i }}"></i>
                                                                                @endfor
                                                                            </div>
                                                                            <input type="hidden"
                                                                                id="rating{{ $item->product->id }}"
                                                                                value="0">
                                                                        </div>
                                                                        <div class="col-sm-12 mb-2">
                                                                            <label for="review">Review
                                                                                <span>*</span></label>
                                                                            <textarea rows="2" id="review{{ $item->product->id }}" name="review" class="form-control"></textarea>
                                                                            <input type="hidden"
                                                                                id="product_id{{ $item->product->id }}"
                                                                                value="{{ $item->product->id }}">
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="modal-footer d-flex align-items-center justify-content-center my-2">
                                                                        <button type="button"
                                                                            class="formbtn post-review-btn"
                                                                            data-product="{{ $item->product->id }}">
                                                                            Post
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-4">
                                <div class="body-head mb-3">
                                    <h5>Tracking Details</h5>
                                </div>
                                <div class="col-12">
                                    <div class="side-cards p-0 shadow-none border-0">
                                        <div class="accordion" id="trackAccord">
                                            @foreach ($trackIds as $trackId)
                                                <div class="accordion-item">
                                                    <div class="accordion-header body-head">
                                                        <h6 class="w-50 collapsed" data-bs-toggle="collapse"
                                                            data-bs-target="#track{{ $trackId }}"
                                                            aria-expanded="true"
                                                            aria-controls="track{{ $trackId }}">
                                                            {{ $trackId }}
                                                        </h6>

                                                        <p></p>

                                                        @php
                                                            // Retrieve the tracking JSON data for each trackId
                                                            $trackingData = $groupedTracks[$trackId];
                                                        @endphp
                                                        <a href="https://onstru-social.s3.ap-south-1.amazonaws.com/{{ $trackingData[0]->vendor_invoice }}" download="" data-bs-toggle="tooltip" data-bs-title="Download Invoice">
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                        @foreach ($trackingData as $tracking)
                                                            @php
                                                                // Decode the JSON string into an object for each tracking entry
                                                                $d_type = json_decode($tracking->tracking);
                                                            @endphp

                                                            @if ($d_type && is_object($d_type))
                                                                {{-- Now you can access the fields of $d_type object dynamically --}}
                                                                @if ($d_type->type == 'own_vehicle')
                                                                    <button type="button" class="formbtn trackbtn"
                                                                        data-bs-toggle="modal"
                                                                        data-track_id="{{ $trackId }}"
                                                                        data-bs-target="#trackOrder">Track Order</button>
                                                                @elseif ($d_type->type == 'courier')
                                                                    <button type="button" class="formbtn trackbtn"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#courier{{ $trackId }}">
                                                                        Courier Details</button>

                                                                    <div class="modal fade"
                                                                        id="courier{{ $trackId }}"
                                                                        data-bs-backdrop="static" data-bs-keyboard="false"
                                                                        tabindex="-1" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h4 class="modal-title">Courier Details
                                                                                    </h4>
                                                                                    <button type="button"
                                                                                        class="btn-close"
                                                                                        data-bs-dismiss="modal"></button>
                                                                                </div>
                                                                                <div class="modal-body p-0">
                                                                                    <div class="row py-2 px-3">
                                                                                        <div
                                                                                            class="col-sm-12 col-md-4 mb-2">
                                                                                            <label>Courier Name</label>
                                                                                            <h6 id="courierName">
                                                                                                {{ $d_type->courier_name ?? 'N/A' }}
                                                                                            </h6>
                                                                                        </div>
                                                                                        <div
                                                                                            class="col-sm-12 col-md-4 mb-2">
                                                                                            <label>Tracking ID</label>
                                                                                            <h6 id="courierTrackID">
                                                                                                {{ $d_type->tracking_id ?? 'N/A' }}
                                                                                            </h6>
                                                                                        </div>
                                                                                        <div
                                                                                            class="col-sm-12 col-md-4 mb-2">
                                                                                            <label>Est. Delivery Date</label>
                                                                                            <h6 id="deliveryDate">
                                                                                                {{ $d_type->estimated_delivery_date ?? 'N/A' }}
                                                                                            </h6>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    </div>

                                                    <div id="track{{ $trackId }}"
                                                        class="accordion-collapse collapse show"
                                                        data-bs-parent="#trackAccord">
                                                        <div class="accordion-body p-0">
                                                            <div class="listtable border-0">
                                                                <div class="table-wrapper">
                                                                    <table class="table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>id</th>
                                                                                <th>Product</th>
                                                                                <th>Qty</th>
                                                                                <th>OTP</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($trackingData as $tracking)
                                                                                <tr>
                                                                                    <td>{{ $tracking->id }}</td>
                                                                                    <td>{{ $tracking->product->name ?? 'N/A' }}
                                                                                    </td>
                                                                                    <td>{{ $tracking->qty }}</td>
                                                                                    <td>{{ $tracking->otp }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                            {{-- @foreach ($trackIds as $trackId)
                                                <div class="accordion-item">
                                                    <div class="accordion-header body-head">
                                                        <h6 class="w-50 collapsed" data-bs-toggle="collapse"
                                                            data-bs-target="#track{{ $trackId }}"
                                                            aria-expanded="true"
                                                            aria-controls="track{{ $trackId }}">
                                                            {{ $trackId }}
                                                        </h6>
                                                        @php
                                                            // Retrieve the JSON data (this should be fetched from the database)
                                                            $jsonData = $groupedTracks[$trackId][0]->tracking; // Assuming tracking data is stored in 'tracking' column

                                                            // Decode the JSON string into an object
                                                            $d_type = json_decode($jsonData);

                                                        @endphp

                                                        @if ($d_type && is_object($d_type))
                                                            @if ($d_type->type == 'own_vehicle')
                                                                <button type="button" class="formbtn trackbtn"
                                                                    data-bs-toggle="modal"
                                                                    data-track_id="{{ $trackId }}"
                                                                    data-bs-target="#trackOrder">Track Order</button>
                                                            @elseif ($d_type->type == 'courier')
                                                                <button type="button" class="formbtn trackbtn"
                                                                    data-bs-toggle="modal" data-bs-target="#courier">
                                                                    Courier Details</button>
                                                                <div class="modal fade" id="courier"
                                                                    data-bs-backdrop="static" data-bs-keyboard="false"
                                                                    tabindex="-1" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h4 class="modal-title">Courier Details
                                                                                </h4>
                                                                                <button type="button" class="btn-close"
                                                                                    data-bs-dismiss="modal"></button>
                                                                            </div>
                                                                            <div class="modal-body p-0">
                                                                                <div class="row py-2 px-3">
                                                                                    <div class="col-sm-12 col-md-4 mb-2">
                                                                                        <label>Courier Name</label>
                                                                                        <h6 id="courierName">
                                                                                            {{ $d_type->courier_name ?? '1' }}
                                                                                        </h6>
                                                                                    </div>
                                                                                    <div class="col-sm-12 col-md-4 mb-2">
                                                                                        <label>Tracking ID</label>
                                                                                        <h6 id="courierTrackID">
                                                                                            {{ $d_type->tracking_id ?? '1' }}
                                                                                        </h6>
                                                                                    </div>
                                                                                    {{-- <div class="col-sm-12 col-md-4 mb-2">
                                                                                        <label>Contact Number</label>
                                                                                        <h6 id="driverNo"></h6>
                                                                                    </div> 
                                                                                    <div class="col-sm-12 col-md-4 mb-2">
                                                                                        <label>Estimated Delivery
                                                                                            Date</label>
                                                                                        <h6 id="deliveryDate">
                                                                                            {{ $d_type->estimated_delivery_date ?? '' }}
                                                                                        </h6>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endif

                                                    </div>

                                                    <div id="track{{ $trackId }}"
                                                        class="accordion-collapse collapse show"
                                                        data-bs-parent="#trackAccord">
                                                        <div class="accordion-body p-0">
                                                            <div class="listtable border-0">
                                                                <div class="table-wrapper">
                                                                    <table class="table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>id</th>
                                                                                <th>Product</th>
                                                                                <th>Qty</th>
                                                                                <th>OTP</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($groupedTracks[$trackId] as $tracking)
                                                                                <tr>
                                                                                    <td>{{ $tracking->id }}</td>
                                                                                    <td>{{ $tracking->product->name ?? 'N/A' }}
                                                                                    </td>
                                                                                    <td>{{ $tracking->qty }}</td>
                                                                                    <td>{{ $tracking->otp }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach --}}

                                            {{-- @foreach ($trackIds as $trackId)
                                                <div class="accordion-item">
                                                    <div class="accordion-header body-head">
                                                        <h6 class="w-50 collapsed" data-bs-toggle="collapse"
                                                            data-bs-target="#track{{ $trackId }}"
                                                            aria-expanded="true"
                                                            aria-controls="track{{ $trackId }}">
                                                            {{ $trackId }}
                                                        </h6>
                                                        <button type="button" class="formbtn trackbtn"
                                                            data-bs-toggle="modal" data-track_id="{{ $trackId }}"
                                                            data-bs-target="#trackOrder">Track Order</button>
                                                    </div>

                                                    <div id="track{{ $trackId }}"
                                                        class="accordion-collapse collapse show"
                                                        data-bs-parent="#trackAccord">
                                                        <div class="accordion-body p-0">
                                                            <div class="listtable border-0">
                                                                <div class="table-wrapper">
                                                                    <table class="table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Product</th>
                                                                                <th>Qty</th>
                                                                                <th>OTP</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @php
                                                                                $products = [];
                                                                                foreach (
                                                                                    $groupedTracks[$trackId]
                                                                                    as $tracking
                                                                                ) {
                                                                                    $productName =
                                                                                        $tracking->product->name ??
                                                                                        'N/A';
                                                                                    if (
                                                                                        !isset($products[$productName])
                                                                                    ) {
                                                                                        $products[$productName] = 0;
                                                                                    }
                                                                                    $products[$productName] +=
                                                                                        $tracking->qty;
                                                                                }
                                                                            @endphp

                                                                            @foreach ($products as $productName => $qty)
                                                                                <tr>
                                                                                    <td>{{ $productName }}</td>
                                                                                    <td>{{ $qty }}</td>
                                                                                    <td>{{ $tracking->otp }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @include('orders.trackMap')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.querySelectorAll('.side-cards').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.side-cards').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>

    <script>
        // Star hover effect
        $(document).ready(function() {
            $(document).on('mouseenter', '.star', function() {
                var value = $(this).data('value');
                $(this).parent().find('.star').each(function() {
                    $(this).toggleClass('hover', $(this).data('value') <= value);
                });
            }).on('mouseleave', '.star-rating', function() {
                $(this).find('.star').removeClass('hover');
            });

            // Star click select
            $(document).on('click', '.star', function() {
                var value = $(this).data('value');
                var productId = $(this).closest('.star-rating').data('product');
                $('#rating' + productId).val(value);

                // Update star UI
                $(this).parent().find('.star').each(function() {
                    $(this).toggleClass('selected', $(this).data('value') <= value);
                });
            });

            // Post review via AJAX
            $(document).on('click', '.post-review-btn', function() {
                var productId = $(this).data('product');
                var rating = $('#rating' + productId).val();
                var review = $('#review' + productId).val();

                $.ajax({
                    url: "{{ route('reviews.store') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        product_id: productId,
                        rating: rating,
                        review: review
                    },
                    success: function(response) {
                        // alert(response.message);
                        $('#reviewModal' + productId).modal('hide');
                        var reviewButton = $('#reviewButton' + productId);
                        reviewButton.prop('disabled', true).text('Reviewed');
                        showToast('Review submitted successfully!');
                    }
                });
            });
        });
    </script>
@endsection
