@extends('layouts.app')

@section('title', 'Onstru | Wishlist')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">

    <style>
        .side-cards:hover {
            transform: translate(0px, -5px);
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }
    </style>

    <div class="container-xl main-div">
        <div class="flex-side">
            @include('products.aside')

            <div class="flex-cards pt-2">
                <div class="body-head mb-3">
                    <h5>My Wishlist</h5>
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <a href="{{ route('cart') }}">
                            <button class="listbtn">
                                <i class="fas fa-shopping-cart pe-1"></i> View Cart
                            </button>
                        </a>
                    </div>
                </div>
                <!-- Empty State -->
                <div class="side-cards shadow-none border-0" id="noCard" 
                    style="{{ count($products) > 0 ? 'display: none;' : 'display: block;' }}">
                    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                        <img src="{{ asset('assets/images/Empty/NoWishlist.png') }}" height="200px"
                            class="d-flex mx-auto mb-2" alt="">
                        <h5 class="text-center mb-0">No Products Found</h5>
                        <h6 class="text-center bio">No products are available at the moment - try a different search or explore
                            other categories.</h6>
                    </div>
                </div>

                <!-- Product Cards -->
                <div class="product-cards">
                    @foreach ($products as $product)
                        <div class="side-cards product-card position-relative h-100 mb-3"
                            data-category="{{ $product->category }}" data-location="{{ $product->location }}"
                            data-stock="{{ $product->availability }}" data-price="{{ $product->sp }}"
                            data-size="{{ $product->size }}">
                            <a href="{{ url('individual-product/' . $product->id) }}">
                                <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $product->cover_img ?? 'assets/images/NoImage.png') }}"
                                    class="mb-3 w-100 rounded-3 object-fit-cover object-center" height="175px"
                                    alt="">
                                @if ($product->highlighted == 1)
                                    <span class="badge">
                                        <img src="{{ asset('assets/images/icon_highlights.png') }}" height="15px"
                                            class="pe-1" alt=""> Highlighted
                                    </span>
                                @endif
                                <div class="cards-head">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-2">{{ $product->name }}</h5>
                                        <h6 class="yellow-label">CB {{ $product->cashback_price }}</h6>
                                    </div>
                                    <h6 class="long-text" style="margin-bottom: 15px;">
                                        {{ $product->categoryRelation->value }}
                                    </h6>
                                </div>
                                <div class="cards-content">
                                    <div class="mb-2 d-flex align-items-center justify-content-between flex-wrap">
                                        <h6>
                                            <i class="fas fa-star text-warning"></i>
                                            {{ number_format($product->reviews_avg_stars, 1) }}
                                            ({{ $product->reviews_count }}
                                            Reviews)
                                        </h6>
                                        @if ($product->availability === 'In Stock')
                                            <h6 class="text-success">{{ $product->availability }}</h6>
                                        @else
                                            <h6 class="text-danger">{{ $product->availability }}</h6>
                                        @endif
                                    </div>
                                    <div class="mb-2 d-flex align-items-center justify-content-between flex-wrap">
                                        <h6 class="mb-2">₹
                                            {{ number_format(($product->base_price + $product->cashback_price + $product->margin) * (1 + $product->tax_percentage / 100), 2) }}
                                            <span class="dashed-line ps-1">₹
                                                {{ number_format(($product->mrp + $product->cashback_price + $product->margin) * (1 + $product->tax_percentage / 100), 2) }}
                                            </span>
                                        </h6>
                                        <h6><i class="fas fa-location-dot pe-1"></i>
                                            {{ $product->locationRelation->value ?? '-' }}</h6>
                                    </div>
                                </div>
                            </a>
                            <div class="row align-items-center justify-content-between">
                                <div class="col-9">
                                    <div id="cart-action-{{ $product->id }}">
                                        @if (in_array($product->id, $cartItems))
                                            <a href="{{ route('cart') }}">
                                                <button class="editbtn w-100 remove-from-cart">
                                                    <i class="fas fa-cart-shopping pe-1"></i> Go To Cart
                                                </button>
                                            </a>
                                        @else
                                            <button class="editbtn w-100 add-to-cart"
                                                data-product="{{ $product->id }}"
                                                data-vendor="{{ $product->created_by ?? 1 }}"
                                                data-quantity="{{ $product->moq }}">
                                                <i class="fas fa-cart-shopping pe-1"></i> Add To Cart
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-3">
                                    <a href="javascript:void(0);" class="save-product-btn"
                                        data-product-id="{{ $product->id }}">
                                        <button class="iconbtn">
                                            <i class="fas fa-heart text-danger"></i>
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Filtering Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.filter-checkbox');
            const productCards = document.querySelectorAll('.product-card');
            const keywordInput = document.getElementById('keywordSearch');
            const minPriceInput = document.getElementById('minPrice');
            const maxPriceInput = document.getElementById('maxPrice');
            const noCard = document.getElementById('noCard');

            checkboxes.forEach(cb => cb.addEventListener('change', applyFilters));
            keywordInput.addEventListener('input', applyFilters);
            minPriceInput.addEventListener('input', applyFilters);
            maxPriceInput.addEventListener('input', applyFilters);

            function applyFilters() {
                const selectedLocation = [...document.querySelectorAll('.loc-filter:checked')].map(cb => cb.value
                    .toLowerCase());
                const selectedCategories = [...document.querySelectorAll('.category-filter:checked')].map(cb => cb
                    .value.toLowerCase());
                const selectedStocks = [...document.querySelectorAll('.stock-filter:checked')].map(cb => cb.value
                    .toLowerCase());
                const selectedSizes = [...document.querySelectorAll('.size-filter:checked')].map(cb => cb.value
                    .toLowerCase());
                const minPrice = parseFloat(minPriceInput.value) || 0;
                const maxPrice = parseFloat(maxPriceInput.value) || Infinity;
                const keyword = keywordInput.value.toLowerCase();

                let noImage = false;

                productCards.forEach(card => {
                    const location = card.dataset.location?.toLowerCase();
                    const category = card.dataset.category?.toLowerCase();
                    const stock = card.dataset.stock?.toLowerCase();
                    const price = parseFloat(card.dataset.price) || 0;
                    const size = card.dataset.size?.toLowerCase();
                    const text = card.textContent.toLowerCase();

                    const locationMatch = selectedLocation.length === 0 || selectedLocation.includes(
                        location);
                    const categoryMatch = selectedCategories.length === 0 || selectedCategories.includes(
                        category);
                    const stockMatch = selectedStocks.length === 0 || selectedStocks.includes(stock);
                    const priceMatch = price >= minPrice && price <= maxPrice;
                    const sizeMatch = selectedSizes.length === 0 || selectedSizes.includes(size);
                    const keywordMatch = keyword === '' || text.includes(keyword);

                    if (locationMatch && categoryMatch && stockMatch && priceMatch && sizeMatch &&
                        keywordMatch) {
                        card.style.display = 'block';
                        noImage = true;
                    } else {
                        card.style.display = 'none';
                    }
                });
                noCard.style.display = noImage ? 'none' : 'block';
            }
            applyFilters();
        });
    </script>

    {{-- Add to Cart Script --}}
    <script>
        $(document).ready(function() {
            $(document).on('click', '.add-to-cart', function(e) {
                e.preventDefault();

                let button = $(this);
                let productId = button.data('product');
                let vendorId = button.data('vendor');
                let min_quantity = button.data('quantity');

                $.ajax({
                    url: "{{ route('cart.store') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId,
                        vendor_id: vendorId,
                        quantity: min_quantity
                    },
                    success: function(response) {
                        $('#cart-action-' + productId).html(`
                                            <a href={{ route('cart') }}>
                                                <button class="editbtn w-100 remove-from-cart" data-id="${response.cart_id}">
                                                    <i class="fas fa-cart-shopping pe-1"></i> Go To Cart
                                                </button>
                                            </a>
                                        `);
                    }
                });
            });
        });
    </script>

    {{-- Wishlist toggle --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.save-product-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const icon = this.querySelector('i');

                    fetch("{{ route('toggle.saved.product') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Content-Type": "application/json",
                            },
                            body: JSON.stringify({
                                product_id: productId
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === "saved") {
                                icon.classList.remove("far");
                                icon.classList.add("fas", "text-danger");
                            } else if (data.status === "removed") {
                                icon.classList.remove("fas", "text-danger");
                                icon.classList.add("far");
                                location.reload(); // Remove the card from wishlist page
                            } else if (data.status === "unauthenticated") {
                                alert("Please login to save this product.");
                            }
                        })
                        .catch(error => {
                            console.error("Error saving product:", error);
                        });
                });
            });
        });
    </script>

@endsection
