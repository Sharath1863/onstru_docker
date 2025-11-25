@extends('layouts.app')

@section('title', 'Onstru | Products')

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
            <!-- Filter Sidebar -->
            @include('products.aside')
            <!-- Cards -->
            <div class="flex-cards pt-2">
                <div class="body-head mb-3">
                    <button class="border-0 m-0 p-0 filter-responsive" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvas-filter" aria-controls="offcanvas-filter">
                        <i class="fas fa-filter fs-4"></i>
                    </button>
                    <h5>Products</h5>
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <a href="{{ route('orders') }}">
                            <button class="followingbtn">
                                <i class="fas fa-box-open pe-1"></i> My Orders
                            </button>
                        </a>
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
                        <img src="{{ asset('assets/images/Empty/NoProducts.png') }}" height="200px"
                            class="d-flex mx-auto mb-2" alt="">
                        <h5 class="text-center mb-0">No Products Found</h5>
                        <h6 class="text-center bio">No products are available at the moment - try a different search or
                            explore
                            other categories.</h6>
                    </div>
                </div>

                <!-- Product Cards -->
                <div class="product-cards" id="initial_products">
                    @foreach ($products as $product)
                        <div class="side-cards product-card position-relative"
                            data-category="{{ $product->categoryRelation->value ?? '' }}"
                            data-location="{{ $product->locationRelation->value ?? '' }}"
                            data-stock="{{ $product->availability ?? '' }}" data-price="{{ $product->sp ?? '' }}"
                            data-size="{{ $product->size ?? '' }}" data-highlight="{{ $product->highlighted ?? '' }}"
                            data-features="{{ strtolower($product->features ?? '') }}">
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
                                        <h5 class="mb-2 long-text w-50">{{ $product->name ?? '-' }}</h5>
                                        @if ($product->cashback_price)
                                            <h6 class="yellow-label">CB {{ $product->cashback_price ?? '-' }}</h6>
                                        @endif
                                    </div>
                                    <h6 class="long-text mb-2">
                                        {{ $product->brand_name ?? '-' }} | {{ $product->categoryRelation->value ?? '-' }}
                                    </h6>
                                </div>
                                <div class="cards-content">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <h6 class="mb-2">
                                            <i class="fas fa-star text-warning"></i>
                                            {{ number_format($product->reviews_avg_stars, 1) }}
                                            ({{ $product->reviews_count ?? 0 }})
                                        </h6>
                                        {{-- @if ($product->availability === 'In Stock')
                                        <h6 class="mb-2 text-success">{{ $product->availability }}</h6>
                                        @else
                                        <h6 class="mb-2 text-danger">{{ $product->availability }}</h6>
                                        @endif --}}
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <h6 class="mb-2">₹
                                            {{ number_format(($product->base_price + $product->cashback_price + $product->margin) * (1 + $product->tax_percentage / 100), 2) }}
                                            <span class="dashed-line ps-1">₹
                                                {{ number_format(($product->mrp + $product->cashback_price + $product->margin) * (1 + $product->tax_percentage / 100), 2) }}
                                            </span>
                                        </h6>

                                        <h6 class="mb-2"><i class="fas fa-location-dot pe-1"></i>
                                            {{ $product->locationRelation->value ?? '-' }}</h6>
                                    </div>
                                </div>
                            </a>
                            <div class="row align-items-center justify-content-between">
                                <div class="col-9">
                                    <div id="cart-action-{{ $product->id }}">
                                        {{-- @if (in_array($product->id, $cartItems) && $product->availability === 'In Stock') --}}
                                        @if ($product->in_cart)
                                            <a href="{{ route('cart') }}">
                                                <button class="listbtn w-100 remove-from-cart">
                                                    <i class="fas fa-truck-fast pe-1"></i> Go To Cart
                                                </button>
                                            </a>
                                            {{-- @elseif ($product->availability === 'Out of Stock')
                                            <button class="removebtn w-100" disabled>
                                                <i class="fas fa-box-open pe-1"></i> Out of Stock
                                            </button> --}}
                                        @else
                                            <button class="removebtn w-100 add-to-cart" data-product="{{ $product->id }}"
                                                data-vendor="{{ $product->created_by ?? '-' }}"
                                                data-quantity="{{ $product->moq }}">
                                                <i class="fas fa-cart-shopping pe-1"></i> Add To Cart
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @php
                                    // $isSaved = in_array($product->id, $savedProducts);
                                @endphp
                                <div class="col-3 ps-0">
                                    <a href="javascript:void(0);" class="save-product-btn"
                                        data-product-id="{{ $product->id }}" data-bs-toggle="tooltip"
                                        data-bs-title="Wishlist">
                                        <button class="iconbtn w-100">
                                            <i
                                                class="{{ $product->in_wishlist ? 'fas text-danger' : 'far' }} fa-heart"></i>
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div id="loadMoreBtn" class="text-center my-3" data-next-url="{{ $next_page_url ?? '' }}"
                    @if (!$next_page_url) style="display:none;" @endif>
                    <img src="{{ asset('assets/images/Favicon.png') }}" alt="Loading" width="50px" height="50px">
                    <h6 class="text-muted" style="font-size: 10px;">Loading</h6>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const loadMoreBtn = document.getElementById("loadMoreBtn");

            function loadMoreContent() {
                loadMoreBtn.click();
            }
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        loadMoreContent();
                    }
                });
            }, {
                root: null,
                threshold: 1.0
            });
            observer.observe(loadMoreBtn);
        });
    </script>

    <script>
        let nextUrl = "/products";
        let loading = false;
        let debounceTimer;

        function getFilters() {
            return {
                keyword: $("#keywordSearch").val(),
                categories: $(".category-filter:checked").map((_, el) => $(el).val()).get(),
                stock: $(".stock-filter:checked").map((_, el) => $(el).val()).get(),
                locations: $(".loc-filter:checked").map((_, el) => $(el).val()).get(),
                minPrice: $("#minPrice").val(),
                maxPrice: $("#maxPrice").val(),
                highlight: $(".highlight-filter:checked").map((_, el) => $(el).val()).get()
            };
        }

        // Load products
        function loadProducts(url, reset = false) {
            $('#loadMoreBtn').show();
            if (!url || loading) return;
            loading = true;

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    ...getFilters(),
                    _token: "{{ csrf_token() }}"
                },
                dataType: 'json',
                beforeSend: function() {
                    loading = true;
                    $('#loadMoreBtn').show();
                },
                success: function(res) {
                    if (res.data.length === 0) {
                        if (reset) {
                            $("#initial_products").html("");
                            $("#noCard").show();
                        }
                        $("#loadMoreBtn").hide();
                        return;
                    }

                    $("#noCard").hide();

                    let html = "";
                    res.data.forEach(product => {
                        html += `
                                <div class="side-cards product-card position-relative" data-category="${product.category_relation?.value}" data-location="${product.location_relation?.value}"
                                    data-stock="${product.availability}" data-price="${product.sp}" data-size="${product.size}" data-highlight="${product.highlighted}"
                                    data-features="${product.features}">
                                    <a href="/individual-product/${product.id}">
                                        <img src="${product.cover_img
                                ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' + product.cover_img
                                : '/assets/images/NoImage.png'}"
                                            class="mb-3 w-100 rounded-3 object-fit-cover object-center" height="175px" alt="">
                                        ${product.highlighted === 1 ? `
                                                                                    <span class="badge">
                                                                                        <img src="/assets/images/icon_highlights.png" height="15px" class="pe-1" alt=""> Highlighted
                                                                                    </span>`
                                : ''}
                                        <div class="cards-head">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="mb-2 long-text w-50">${product.name ?? '-'}</h5>
                                                <h6 class="yellow-label">CB ${product.cashback_price ?? '-'}</h6>
                                            </div>
                                            <h6 class="long-text mb-2">
                                                ${product.brand_name ?? '-'} | ${product.category_relation?.value ?? '-'}
                                            </h6>
                                        </div>

                                        <div class="cards-content">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                <h6 class="mb-2">
                                                    <i class="fas fa-star text-warning"></i>
                                                    ${Number(product.reviews_avg_stars || 0).toFixed(1) ?? '-'}
                                                    (${product.reviews_count ?? 0 ?? '-'})
                                                </h6>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                <h6 class="mb-2">₹ ${product.sp ?? '-'}
                                                    <span class="dashed-line ps-1">₹ ${product.mrp ?? '-'}</span>
                                                </h6>
                                                <h6 class="mb-2"><i class="fas fa-location-dot pe-1"></i>
                                                    ${product.location_relation?.value ?? '-'}
                                                </h6>
                                            </div>
                                        </div>
                                    </a>

                                    <div class="row align-items-center justify-content-between">
                                        <div class="col-9">
                                            <div id="cart-action-${product.id}">
                                                ${product.in_cart && product.availability === 'In Stock'
                                ? `<a href="/cart">
                                                                                            <button class="listbtn w-100 remove-from-cart">
                                                                                                <i class="fas fa-truck-fast pe-1"></i> Go To Cart
                                                                                            </button>
                                                                                        </a>`
                                : product.availability === 'Out of Stock'
                                    ? `<button class="removebtn w-100" disabled>
                                                                                                <i class="fas fa-box-open pe-1"></i> Out of Stock
                                                                                        </button>`
                                    : `<button class="removebtn w-100 add-to-cart" 
                                                                                                data-product="${product.id}" 
                                                                                                data-vendor="${product.created_by ?? '-'}" 
                                                                                                data-quantity="${product.moq ?? '-'}">
                                                                                                <i class="fas fa-cart-shopping pe-1"></i> Add To Cart
                                                                                        </button>`}
                                            </div>
                                        </div>
                                        <div class="col-3 ps-0">
                                            <a href="javascript:void(0);" class="save-product-btn" data-product-id="${product.id}"
                                                data-bs-toggle="tooltip" data-bs-title="Add to Wishlist">
                                                <button class="iconbtn w-100">
                                                    <i class="${product.is_saved ? 'fas text-danger' : 'far'} fa-heart"></i>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `;
                    });
                    if (reset) {
                        $("#initial_products").html(html);
                    } else {
                        $("#initial_products").append(html);
                    }

                    if (res.next_page_url) {
                        $("#loadMoreBtn").data("next-url", res.next_page_url).show();
                    } else {
                        $("#loadMoreBtn").hide();
                    }

                    nextUrl = res.next_page_url;
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                },
                complete: function() {
                    loading = false;
                    $('#loadMoreBtn').hide();
                }
            });
        }

        // Load more button click
        $(document).on("click", "#loadMoreBtn", function() {
            const nextUrl = $(this).data("next-url");
            loadProducts(nextUrl, false);
        });

        // Debounce keyword input
        $('#keywordSearch').on('keyup', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                nextUrl = "/products";
                loadProducts(nextUrl, true);
            }, 1000);
        });

        // // On filter change → reset products
        $(document).on('change',
            '.filter-checkbox, .category-filter, .stock-filter, .loc-filter, .highlight-filter, #minPrice, #maxPrice',
            function() {
                nextUrl = "/products";
                loadProducts(nextUrl, true);
            });
    </script>

    <!-- Add to Cart -->
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
                        // Replace the button with Remove
                        $('#cart-action-' + productId).html(`
                                            <a href={{ route('cart') }}><button class="listbtn w-100 remove-from-cart" data-id="${response.cart_id}" data-product="${productId}">
                                                <i class="fas fa-truck-fast pe-1"></i> Go To Cart
                                            </button></a>
                                        `);
                        showToast('Added to Cart');
                    }
                });
            });
        });
    </script>

    <!-- Save Product (Wishlist) -->
    <script>
        // document.addEventListener("DOMContentLoaded", function() {
        //     document.querySelectorAll('.save-product-btn').forEach(function(btn) {
        //         btn.addEventListener('click', function() {
        //             const productId = this.getAttribute('data-product-id');
        //             const icon = this.querySelector('i');

        //             fetch("{{ route('toggle.saved.product') }}", {
        //                     method: "POST",
        //                     headers: {
        //                         "X-CSRF-TOKEN": "{{ csrf_token() }}",
        //                         "Content-Type": "application/json",
        //                     },
        //                     body: JSON.stringify({
        //                         product_id: productId
        //                     })
        //                 })
        //                 .then(res => res.json())
        //                 .then(data => {
        //                     if (data.status === "saved") {
        //                         icon.classList.remove("far");
        //                         icon.classList.add("fas", "text-danger");
        //                         showToast('Added to Wishlist');
        //                     } else if (data.status === "removed") {
        //                         icon.classList.remove("fas", "text-danger");
        //                         icon.classList.add("far");
        //                         showToast('Removed from Wishlist');
        //                     } else if (data.status === "unauthenticated") {
        //                         alert("Please login to save this product.");
        //                     }
        //                 })
        //                 .catch(error => {
        //                     console.error("Error saving product:", error);
        //                 });
        //         });
        //     });
        // });

        document.addEventListener("click", function(e) {
            const btn = e.target.closest(".save-product-btn"); // look for closest button
            if (!btn) return; // ignore if click was not on a save-product-btn

            const productId = btn.getAttribute("data-product-id");
            const icon = btn.querySelector("i");

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
                        showToast('Added to Wishlist');
                    } else if (data.status === "removed") {
                        icon.classList.remove("fas", "text-danger");
                        icon.classList.add("far");
                        showToast('Removed from Wishlist');
                    } else if (data.status === "unauthenticated") {
                        alert("Please login to save this product.");
                    }
                })
                .catch(error => {
                    console.error("Error saving product:", error);
                });
        });
    </script>

@endsection
