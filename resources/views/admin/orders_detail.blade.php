<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Onstru | Order Details</title>

    <!-- Stylesheets CDN -->
    @include('admin.cdn_style')

    <link rel="stylesheet" href="{{ asset('assets/css/admin/profile.css') }}">

</head>

<body>

    <div class="main">

        <!-- aside -->
        @include('admin.aside')

        <div class="body-main">

            <!-- Navbar -->
            @include('admin.navbar')

            <div class="main-div px-4 py-1">
                <div class="body-head">
                    <h4 class="m-0">Order Details</h4>
                </div>
                <div class="mt-3 profile-card">
                    <div class="cards mb-2">
                        <h6 class="mb-1">Order ID</h6>
                        <h5 class="mb-0">{{ $order->order_id ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Buyer Name</h6>
                        <h5 class="mb-0">{{ $order->user->name ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Username</h6>
                        <h5 class="mb-0 text-lowercase">{{ $order->user->user_name ?? '-'  }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Role</h6>
                        <h5 class="mb-0">{{ $order->user->as_a ?? 'Consumer' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Gender</h6>
                        <h5 class="mb-0">{{ $order->user->gender ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Primary Contact Number</h6>
                        <h5 class="mb-0">+91 {{ $order->address->primary_phone ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Secondary Contact Number</h6>
                        <h5 class="mb-0">+91 {{ $order->address->secondary_phone ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Email ID</h6>
                        <h5 class="mb-0">{{ $order->user->email ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Balance</h6>
                        <h5 class="mb-0">{{ $order->user->balance ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">GST Number</h6>
                        <h5 class="mb-0">{{ $order->user->gst_number ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">GST Billing</h6>
                        <h5 class="mb-0">{{ $order->address->gst_billing ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Billing Address</h6>
                        <h5 class="mb-0">{{ $order->address->billing_address ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Shipping Address</h6>
                        <h5 class="mb-0">{{ $order->address->shipping_address ?? '-' }}</h5>
                    </div>
                </div>

                <div class="body-head mt-3">
                    <h4 class="m-0">Product Details</h4>
                </div>
                @php
                    $grouped = $order_products->groupBy(fn($op) => $op->product->vendor->id ?? 'no_vendor');
                @endphp
                @foreach ($grouped as $vendorId => $products)
                    <div class="mt-2 ">
                        {{-- Vendor Info --}}
                        <div class="profile-card" style="border-radius: 6px 6px 0px 0px;">
                            <div class="cards">
                                <h6 class="mb-1">Vendor ID</h6>
                                <h5 class="mb-0">{{ $products->first()->vendor_order ?? '-' }}</h5>
                            </div>
                            <div class="cards">
                                <h6 class="mb-1">Vendor Name</h6>
                                <h5 class="mb-0">{{ $products->first()->product->vendor->name ?? '-' }}</h5>
                            </div>
                            <div class="cards">
                                <h6 class="mb-1">Print PO</h6>
                                <h5 class="mb-0">
                                    <a target="_blank">
                                        <form action="{{ route('order-po') }}" method="post" target="_blank">
                                            @csrf
                                            <input type="hidden" name="vendor_id"
                                                value="{{ $products->first()->vendor_order }}">
                                            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                            <button type="submit" class="btn p-0 m-0"
                                                style="border: none; background: none;">
                                                <i class="fas fa-print" data-bs-toggle="tooltip"
                                                    data-bs-title="Print Purchase Order"></i>
                                            </button>
                                        </form>
                                    </a>
                                </h5>
                            </div>
                            @if ($products->first()->vendor_invoice)
                                <div class="cards">
                                    <h6 class="mb-1">Vendor Invoice</h6>
                                    <h5 class="mb-0">
                                        <a href="{{ asset($products->first()->vendor_invoice ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $products->first()->vendor_invoice : 'assets/images/NoImage.png') }}"
                                            download>
                                            <button type="button" class="btn p-0 m-0" style="border: none; background: none;">
                                                <i class="fas fa-print" data-bs-toggle="tooltip"
                                                    data-bs-title="Print Vendor Invoice"></i>
                                            </button>
                                        </a>
                                    </h5>
                                </div>
                            @endif
                        </div>
                        <div class="listtable border-0">
                            <div class="table-wrapper">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product Name</th>
                                            <th>Brand Name</th>
                                            <th>Category</th>
                                            <th>SP / Tax % / CB / Margin</th>
                                            <th>Quantity</th>
                                            <th>Shipping</th>
                                            <th>Availability</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $op)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $op->product->name ?? '-' }}</td>
                                                <td>{{ $op->product->brand_name ?? '-' }}</td>
                                                <td>{{ $op->product->categoryRelation->value ?? '-' }}</td>
                                                <td>{{ $op->base_price ?? '-' }} / {{ $op->tax ?? '-' }}% /
                                                    {{ $op->product->cashback_price ?? '-' }} / {{ $op->margin ?? '-' }}
                                                </td>
                                                <td>{{ $op->quantity ?? '-' }}</td>
                                                <td>{{ $op->shipping ?? '-' }}</td>
                                                <td>{{ $op->product->availability ?? '-' }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a href="{{ asset($op->product->cover_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $op->product->cover_img : 'assets/images/NoImage.png') }}"
                                                            data-fancybox="product">
                                                            <i class="fas fa-image" data-bs-toggle="tooltip"
                                                                data-bs-title="View Image"></i>
                                                        </a>
                                                        <a href="{{ url('product/' . $op->product->id) }}">
                                                            <i class="fas fa-external-link"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @include('admin.toaster')

    <!-- script -->
    @include('admin.cdn_script')

</body>

</html>