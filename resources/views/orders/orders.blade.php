@extends('layouts.app')

@section('title', 'Onstru | Orders')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">

    <style>
        @media screen and (min-width: 767px) {
            .inpleftflex {
                width: 40% !important;
            }
        }
    </style>

    <div class="container-xl main-div">
        <div class="flex-side d-block">
            <!-- Flex Cards -->
            <div class="flex-cards form-div pt-2">
                <div class="body-head mb-3">
                    <button class="border-0 m-0 p-0 filter-responsive" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvas-filter" aria-controls="offcanvas-filter">
                        <i class="fas fa-filter fs-4"></i>
                    </button>
                    <h5>My Orders</h5>
                </div>
                <div class="inpleftflex mb-3">
                    <i class="fas fa-search"></i>
                    <input type="text" name="keyword" id="keywordSearch" class="form-control border-0" placeholder="Search"
                        value="{{ request('keyword') }}">
                </div>
                @if ($orders->isEmpty())
                    <div class="side-cards shadow-none border-0" id="noCard" style="display: none;">
                        <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                            <img src="{{ asset('assets/images/Empty/NoOrders.png') }}" height="200px"
                                class="d-flex mx-auto mb-2" alt="">
                            <h5 class="text-center mb-0">No Orders Found</h5>
                            <h6 class="text-center bio">No orders found - once you make a purchase, your orders will appear here.
                            </h6>
                        </div>
                    </div>
                @else
                    <div class="listtable">
                        <div class="table-wrapper">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Order ID</th>
                                        <th>Order Date</th>
                                        <th>Total Item</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $odr)
                                        <tr id="{{ $odr->id }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $odr->order_id }}</td>
                                            <td>{{ $odr->created_at->format('Y-m-d') }}</td>
                                            <td>{{ $odr->products_count }} Items</td>
                                            <td>{{ $odr->status }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ route('tracking', $odr->order_id) }}" data-bs-toggle="tooltip"
                                                        data-bs-title="View Tracking">
                                                        <i class="fas fa-external-link"></i>
                                                    </a>
                                                    <a data-bs-toggle="tooltip" data-bs-title="Print Invoice">
                                                        <form action="{{ route('customer-inv') }}" method="post" target="_blank">
                                                            @csrf
                                                            <input type="hidden" name="vendor_id"
                                                                value="{{ $odr->products->first()->vendor_order }}">
                                                            <input type="hidden" name="order_id" value="{{ $odr->order_id }}">
                                                            <button type="submit" class="btn m-0 p-0 border-0 bg-none">
                                                                <i class="fas fa-print"></i>
                                                            </button>
                                                        </form>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Filtering Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const keywordInput = document.getElementById('keywordSearch');
            const table = document.querySelector('.listtable');
            const orderRows = document.querySelectorAll('table tbody tr');
            const noCard = document.getElementById('noCard');

            keywordInput.addEventListener('input', applyFilters);

            function applyFilters() {
                const keyword = keywordInput.value.toLowerCase();
                let visibleCount = 0;

                orderRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    const match = keyword === '' || text.includes(keyword);

                    if (match) {
                        table.style.display = 'block';
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        table.style.display = 'none';
                        row.style.display = 'none';
                    }
                });
                noCard.style.display = (visibleCount === 0) ? 'block' : 'none';
            }
            applyFilters();
        });
    </script>


    {{-- Add to Cart --}}
    <script>
        $(document).ready(function () {
            $(document).on('click', '.add-to-cart', function (e) {
                e.preventDefault();

                let button = $(this);
                let productId = button.data('product');
                let vendorId = button.data('vendor');
                let min_quantity = button.data('quantity');

                $.ajax({
                    url: "/{{ route('cart.store') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId,
                        vendor_id: vendorId,
                        quantity: min_quantity
                    },
                    success: function (response) {
                        // Replace the button with Remove
                        $('#cart-action-' + productId).html(`
                            <a href={{ route('cart') }}><button class="editbtn w-100 remove-from-cart" data-id="${response.cart_id}" data-product="${productId}">
                                <i class="fas fa-cart-shopping pe-1"></i> Go To Cart
                            </button></a>
                        `);
                    }
                });
            });
        });
    </script>

@endsection