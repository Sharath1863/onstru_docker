@extends('layouts.app')

@section('title', 'Onstru | Order Status Update')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/timeline.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('assets/css/list.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/tracking.css') }}">

    <style>
        .order-flow {
            max-height: 70vh;
            overflow-y: auto;
        }

        .order-flow::-webkit-scrollbar {
            display: none;
        }

        .order-flow {
            scrollbar-width: none !important;
        }

        .orderqty {
            display: grid;
            grid-template-columns: 30px 75px 30px;
            align-items: center;
            justify-content: start;
        }

        .op-main {
            display: grid;
            align-items: start;
            justify-content: space-between;
        }

        @media screen and (min-width: 767px) {
            .op-main {
                grid-template-columns: repeat(3, 33%);
            }
        }

        @media screen and (max-width: 767px) {
            .op-main {
                grid-template-columns: repeat(1, 1fr);
            }
        }
    </style>
    {{-- @dd($hubIds) --}}
    <div class="container-xl main-div">

        <div class="row">
            <div class="side-cards shadow-none border-0 py-0 col-sm-12 col-md-4 mb-2">
                <!-- Order Details -->
                <div class="body-head mb-3">
                    <h5>Order Details</h5>
                </div>
                <div class="border rounded cards-content p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="text-muted mb-2"><i class="fas fa-clock pe-1"></i> Order ID :</h6>
                        <h6 class="mb-2">{{ $order->order_id }}</h6>
                    </div>
                    <div class="d-flex justify-content-between">
                        <h6 class="text-muted mb-2"><i class="far fa-calendar pe-1"></i> Orderd On :</h6>
                        <h6 class="mb-2">{{ $order->created_at->format('d-m-Y') }}</h6>
                    </div>
                    <div class="d-flex justify-content-between">
                        <h6 class="text-muted mb-3"><i class="fas fa-box-open pe-1"></i> Total products :</h6>
                        <h6 class="mb-3">{{ $order->products->count() }} items</h6>
                    </div>
                    <div class="d-flex align-items-center column-gap-3">
                        <form action="{{ route('commission-inv') }}" method="post" target="_blank">
                            @csrf
                            <input type="hidden" name="vendor_id" value="{{ $order->products->first()->vendor_order }}">
                            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                            <button type="submit" class="iconbtn" data-bs-toggle="tooltip"
                                data-bs-title="Commission Invoice">
                                <i class="fas fa-receipt"></i>
                            </button>
                        </form>
                        <form action="{{ route('order-po') }}" method="post" target="_blank">
                            @csrf
                            <input type="hidden" name="vendor_id" value="{{ $order->products->first()->vendor_order }}">
                            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                            <button type="submit" class="iconbtn" data-bs-toggle="tooltip" data-bs-title="Print PO">
                                <i class="fas fa-print"></i>
                            </button>
                        </form>
                        <form action="{{ route('vendor-inv') }}" method="post" target="_blank">
                            @csrf
                            <input type="hidden" name="vendor_id" value="{{ $order->products->first()->vendor_order }}">
                            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                            <button type="submit" class="iconbtn" data-bs-toggle="tooltip" data-bs-title="Vendor Invoice">
                                <i class="fas fa-file-invoice"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="side-cards shadow-none border-0 py-0 col-sm-12 col-md-4 mb-2">

                <!-- Order Summary -->
                <div class="body-head mb-3">
                    <h5>Order Summary</h5>
                </div>
                <div class="cards-content border rounded p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Price ({{ $order->products->count() }} items)</h6>
                        <h6 class="mb-2">₹ {{ $price }}</h6>
                    </div>
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Shipping Charges</h6>
                        <h6 class="mb-2">₹ {{ $ship_total }}</h6>
                    </div>
                    {{-- <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Cash back</h6>
                        <h6 class="mb-2">₹ {{ $cashback }}</h6>
                    </div> --}}
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">GST billing request</h6>
                        <h6 class="mb-2 text-capitalize">{{ $address->gst_billing }}</h6>
                    </div>
                    @if($address->gst_billing == 'yes')
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-0">GST Number</h6>
                            <h6 class="mb-0">{{ $address->billing_gst }}</h6>
                        </div>
                    @endif
                    <hr class="my-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0">Order total</h6>
                        <h6 class="text-success mb-0">₹ {{ $total }}</h6>
                    </div>
                </div>
            </div>

            <div class="side-cards shadow-none border-0 py-0 col-sm-12 col-md-4 mb-2">
                <!-- Address -->
                <div class="body-head mb-3">
                    <h5>Address Details</h5>
                </div>
                <div class="border rounded cards-content p-3 mt-2" style="max-height: 22vh; overflow-y: auto;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . auth()->user()->profile_img ?? 'assets/images/Avatar.png') }}"
                            height="35px" class="object-fit-cover rounded-circle" alt="">
                        <div>
                            <h6 class="mb-0">{{ $address->first_name }} {{ $address->last_name }}</h6>
                            <label class="m-0">
                                <i class="fas fa-phone pe-1"></i> {{ $address->primary_phone }}
                            </label>
                        </div>
                    </div>
                    <label class="mb-1">Billing Address</label>
                    <h6>
                        <i class="fas fa-location pe-1"></i> {{ $address->billing_address }},
                        {{ $address->billing_city }},
                        {{ $address->billing_state }}, {{ $address->billing_pincode }}
                    </h6>
                    <label class="mb-1">Shipping Address</label>
                    <h6>
                        <i class="fas fa-location-dot pe-1"></i> {{ $address->shipping_address }},
                        {{ $address->shipping_city }},
                        {{ $address->shipping_state }}, {{ $address->shipping_pincode }}
                    </h6>
                </div>
            </div>

            <div class="side-cards shadow-none border-0 col-sm-12 col-md-12">
                <div class="order-flow mb-2">
                    @if ($order->products->contains('status', 'processing'))
                        <div class="body-head mb-3">
                            <h5>Order Requested</h5>
                        </div>
                        <div class="accordion" id="hubAccordion">
                            @foreach ($hubIds as $hubId)
                                @php
                                    $hubProducts = $order->products->filter(
                                        fn($op) => $op->product->hub_id == $hubId && $op->status != 'shipped',
                                    );
                                @endphp

                                @if ($hubProducts->isNotEmpty())
                                    <form action="{{ route('order-outfordelivery') }}" method="post" id="orderForm">
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                        <div class="accordion-item">
                                            <div class="accordion-header body-head">
                                                @foreach ($order->products as $op)
                                                    @if ($op->product->hub_id == $hubId)
                                                        @php
                                                            $hubName = $op->product->hub->hub_name ?? 'Unknown Hub';
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <h6 class="w-75" data-bs-toggle="collapse"
                                                    data-bs-target="#hub1{{ $hubId }}" aria-expanded="true"
                                                    aria-controls="hub1"> {{ $hubName }}</h6>
                                                {{-- <a href="{{ url('order-outfordelivery', ['order_id' => $order->order_id]) }}"> --}}
                                                <button type="submit" class="formbtn">Out for Delivery</button>
                                                {{-- </a> --}}
                                            </div>
                                            <div id="hub1{{ $hubId }}" class="accordion-collapse collapse show"
                                                data-bs-parent="#hubAccordion">
                                                <div class="accordion-body p-2 op-main"
                                                    style="max-height: 50vh; overflow-y: auto;">
                                                    @foreach ($order->products as $op)
                                                        @if ($op->product->hub_id == $hubId)
                                                            @if ($op->status == 'processing')
                                                                <div
                                                                    class="d-flex align-items-start gap-2 border rounded mb-2 cards-content p-2">
                                                                    <img src="{{ asset($op->product->cover_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $op->product->cover_img : '') }}"
                                                                        height="125px" width="125px"
                                                                        class="object-fit-cover rounded" alt="">
                                                                    <div class="w-100">
                                                                        <div
                                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                                            <h5 class="mb-1">{{ $op->product->name }}
                                                                            </h5>
                                                                            <input type="checkbox" class="mb-1"
                                                                                name="products[{{ $op->product->id }}][selected]"
                                                                                value="1"
                                                                                id="check-{{ $op->product->id }}" checked>

                                                                        </div>
                                                                        <h6 class="text-muted mb-2">₹
                                                                            {{ $op->base_price }} +
                                                                            {{ $op->tax }}% + {{ $op->shipping }}
                                                                        </h6>
                                                                        <h6 class="text-muted mb-2">Total Qty:
                                                                            {{ $op->quantity }}</h6>
                                                                        <div class="orderqty form-div mb-2">
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-light border fw-bold change-qty"
                                                                                data-id="{{ $op->product->id }}"
                                                                                data-type="minus">-</button>
                                                                            <input type="number"
                                                                                name="products[{{ $op->product->id }}][quantity]"
                                                                                class="form-control text-center"
                                                                                id="qty-{{ $op->product->id }}"
                                                                                value="{{ $op->bal_qty }}"
                                                                                min="1" max="{{ $op->bal_qty }}"
                                                                                required>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-light border fw-bold change-qty"
                                                                                data-id="{{ $op->product->id }}"
                                                                                data-type="plus">+</button>
                                                                        </div>
                                                                        <h6 class="text-success mb-0 text-capitalize">
                                                                            {{ $op->status }}</h6>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            @endforeach
                        </div>
                    @elseif ($order->products->first()?->status == 'pending')
                        <div class="body-head mb-3">
                            <h5>Pending Products</h5>
                        </div>
                        <div class="listtable">
                            <div class="table-wrapper">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Shipping</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->products as $op)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <img src="{{ asset($op->product->cover_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $op->product->cover_img : '') }}"
                                                            height="50px" width="50px"
                                                            class="object-fit-cover rounded" alt="">
                                                        <span>{{ $op->product->name }}</span>
                                                    </div>
                                                </td>
                                                <td>₹ {{ $op->base_price }} + {{ $op->tax }}%</td>
                                                <td>{{ $op->quantity }}</td>
                                                <td>{{ $op->shipping }}</td>
                                                <td>₹
                                                    {{ floor($op->bal_qty * $op->base_price + ($op->bal_qty * $op->base_price * $op->tax) / 100) + $op->shipping }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex align-items-center justify-content-end">
                                <form action="{{ route('order-update') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="order_id" value="{{ $order->order_id }}"
                                        id="">
                                    <input type="hidden" name="status" value="processing">
                                    <button class="listbtn d-block ms-auto">Accept Order</button>
                                </form>
                            </div>
                        </div>

                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-8">
                    <div class="mb-3">
                        @if ($order->products->contains('status', 'shipped') || $order->products->contains('status', 'delivered'))
                            <div class="body-head mb-3">
                                <h5>Shipped Products</h5>
                            </div>

                            <div class="listtable">
                                <div class="table-wrapper">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($order->products as $op)
                                                @if ($op->status == 'shipped' || $op->status == 'delivered')
                                                    <tr>
                                                        <td>{{ $op->product->name }}</td>
                                                        {{-- <td>₹ {{ $op->base_price }} + {{ $op->tax }}%</td> --}}
                                                        <td>{{ $op->quantity }}</td>
                                                        <td>₹
                                                            {{ $op->quantity * $op->base_price + ($op->quantity * $op->base_price * $op->tax) / 100 }}
                                                        </td>
                                                        <td>{{ $op->status }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                {{-- @if ($order->products->contains('status', 'shipped')) --}}
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
                                                data-bs-target="#track{{ $trackId }}" aria-expanded="true"
                                                aria-controls="track{{ $trackId }}">
                                                {{ $trackId }}
                                            </h6>
                                            <div class="d-flex align-items-center column-gap-2">
                                                <a data-bs-toggle="tooltip" data-bs-title="Live Track Map">
                                                    <button class="iconbtn trackbtn px-2" type="button" data-bs-toggle="modal"
                                                        data-bs-target="#trackOrder" data-track_id="{{ $trackId }}">
                                                        <i class="fas fa-location-dot"></i>
                                                    </button>
                                                </a>
                                                <a href="javascript:void(0);" class="copy-link"
                                                    data-link="{{ url('track/' . $trackId) }}" data-bs-toggle="tooltip"
                                                    data-bs-title="Copy Driver Link">
                                                    <button class="iconbtn px-2" type="button">
                                                        <i class="fas fa-link"></i>
                                                    </button>
                                                </a>
                                                @if ($groupedTracks[$trackId]->first()->status == 'shipped')
                                                    <button type="button" class="formbtn otp-button"
                                                        data-tracking-id="{{ $trackId }}" data-bs-toggle="modal"
                                                        data-bs-target="#otpmodal">
                                                        OTP
                                                    </button>
                                                @elseif ($groupedTracks[$trackId]->first()->status == 'delivered')
                                                    <button type="button" class="followingbtn">
                                                        Delivered
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <div id="track{{ $trackId }}" class="accordion-collapse collapse"
                                            data-bs-parent="#trackAccord">
                                            <div class="accordion-body p-0">
                                                <div class="listtable border-0">
                                                    <div class="table-wrapper">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th>Product</th>
                                                                    <th>Qty</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $products = [];
                                                                    foreach ($groupedTracks[$trackId] as $tracking) {
                                                                        $productName =
                                                                            $tracking->product->name ?? 'N/A';
                                                                        if (!isset($products[$productName])) {
                                                                            $products[$productName] = 0;
                                                                        }
                                                                        $products[$productName] += $tracking->qty;
                                                                    }
                                                                @endphp

                                                                @foreach ($products as $productName => $qty)
                                                                    <tr>
                                                                        <td>{{ $productName }}</td>
                                                                        <td>{{ $qty }}</td>
                                                                        <td>{{ $tracking->status }}</td>
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
                            </div>
                        </div>
                    </div>
                </div>
                {{-- @endif --}}
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal modal-sm fade" id="otpmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirm Completion</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body mt-3">
                    <h6 class="text-center mb-2">Are you sure you want to mark this order as completed?</h6>
                    <form action="{{ route('order-otp-update') }}" method="post">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->order_id }}" id="">
                        <input type="hidden" name="status" value="delivered" id="">
                        <input type="hidden" name='tracking_id' id="tracking_id">
                        <div class="col-sm-12 mb-2">
                            <label for="occ">Order Completion Code <span>*</span></label>
                            <input type="number" name="otp" id="occ" class="form-control" min="1000"
                                max="9999" oninput="validate_otp(this)" required>
                        </div>
                        <div class="col-sm-12 d-flex align-items-center justify-content-center mt-3">
                            <button type="submit" class="listbtn">Complete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('orders.trackMap', ['items' => $order->products])

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const changeQtyButtons = document.querySelectorAll('.change-qty');

            changeQtyButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-id');
                    const type = this.getAttribute('data-type');
                    const input = document.getElementById(`qty-${productId}`);
                    const plusBtn = document.querySelector(
                        `.change-qty[data-id="${productId}"][data-type="plus"]`);
                    const minusBtn = document.querySelector(
                        `.change-qty[data-id="${productId}"][data-type="minus"]`);

                    if (!input) return;

                    let currentQty = parseInt(input.value) || 0;
                    const maxQty = parseInt(input.getAttribute('max')) || 9999;

                    if (type === 'plus' && currentQty < maxQty) {
                        input.value = currentQty + 1;
                    } else if (type === 'minus' && currentQty > 1) {
                        input.value = currentQty - 1;
                    }

                    updateButtonStates(input, plusBtn, minusBtn, maxQty);
                });
            });

            document.querySelectorAll('input[id^="qty-"]').forEach(input => {
                const productId = input.id.replace('qty-', '');
                const plusBtn = document.querySelector(
                    `.change-qty[data-id="${productId}"][data-type="plus"]`);
                const minusBtn = document.querySelector(
                    `.change-qty[data-id="${productId}"][data-type="minus"]`);
                const maxQty = parseInt(input.getAttribute('max')) || 9999;

                input.addEventListener('keyup', function() {
                    let val = this.value;

                    if (val === '') {
                        // Allow temporarily empty
                        updateButtonStates(this, plusBtn, minusBtn, maxQty);
                        return;
                    }

                    let numVal = parseInt(val);

                    if (isNaN(numVal) || numVal < 1) {
                        this.value = 1;
                    } else if (numVal > maxQty) {
                        this.value = maxQty;
                    }

                    updateButtonStates(this, plusBtn, minusBtn, maxQty);
                });

                // Initialize button states
                updateButtonStates(input, plusBtn, minusBtn, maxQty);
            });

            function updateButtonStates(input, plusBtn, minusBtn, maxQty) {
                const val = input.value;

                if (val === '') {
                    if (plusBtn) plusBtn.disabled = false;
                    if (minusBtn) minusBtn.disabled = true;
                    return;
                }

                const currentQty = parseInt(val) || 0;

                if (plusBtn) plusBtn.disabled = currentQty >= maxQty;
                if (minusBtn) minusBtn.disabled = currentQty <= 1;
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const otpButtons = document.querySelectorAll('.otp-button');
            const trackingInput = document.getElementById('tracking_id');

            otpButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const trackingId = this.getAttribute('data-tracking-id');
                    trackingInput.value = trackingId;
                });
            });
        });
    </script>

    <script>
        document.getElementById('orderForm').addEventListener('submit', function(event) {
            // Check if any checkbox is selected
            let checkboxes = document.querySelectorAll('input[type="checkbox"]');
            let isChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

            // If no checkbox is selected, prevent form submission and alert user
            if (!isChecked) {
                event.preventDefault(); // Prevent form submission
                showToast('Please select at least one product before submitting.', 'error');
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".copy-link").forEach(el => {
                el.addEventListener("click", function(e) {
                    e.preventDefault();
                    const link = this.dataset.link;

                    navigator.clipboard.writeText(link).then(() => {
                        const tooltip = bootstrap.Tooltip.getOrCreateInstance(this);
                        tooltip.setContent({
                            '.tooltip-inner': 'Copied!'
                        });
                        tooltip.show();
                        showToast('Link copied to clipboard!');
                        setTimeout(() => {
                            tooltip.hide();
                            tooltip.setContent({
                                '.tooltip-inner': 'Copy Driver Link'
                            });
                        }, 1500);
                    }).catch(err => {
                        console.error('Failed to copy: ', err);
                    });
                });
            });
        });
    </script>

@endsection
