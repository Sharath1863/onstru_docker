@extends('layouts.app')

@section('title', 'Onstru | Individual Product')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/individual-product.css') }}">

    <style>
        @media screen and (min-width: 767px) {
            .flex-side {
                grid-template-columns: 50% 49%;
            }
        }
    </style>

    <div class="container main-div">
        <div class="body-head mb-4">
            <a href="javascript:history.back()">
                <h5><i class="fas fa-angle-left pe-1"></i> Back</h5>
            </a>
        </div>

        <div class="individual-product-main">
            <div class="individual-product-left">
                <div class="product-image-gallery">
                    @if (is_array($images))
                        @foreach ($images as $key => $img)
                            <div class="gallery-div">
                                <img src="{{ asset($img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $img : 'assets/images/NoImage.png') }}"
                                    alt="{{ $key }}" alt="">
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="product-image-main">
                    <div class="product-image-main-div">
                        <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $product->cover_img) }}"
                            id="main-image" alt="">
                    </div>
                </div>
            </div>

            <div class="individual-product-right">
                <div class="side-cards border-0 p-0 shadow-none">
                    <div class="body-head mb-2">
                        <div class="d-flex align-items-center column-gap-2">
                            <h5 class="text-decoration-none">{{ $product->name ?? '-' }}</h5>
                            @if ($product->created_by == auth()->id())
                                <button
                                    class="status-toggle-btn {{ $product->status === 'active' ? 'green-label' : 'red-label' }}"
                                    data-id="{{ $product->id }}" data-status="{{ $product->status }}">
                                    {{ ucfirst($product->status) }}
                                </button>
                            @endif
                        </div>
                        <div class="d-flex align-items-center flex-wrap column-gap-3">
                            @if ($product->highlighted == 1 && $product->created_by == auth()->id())
                                <a href="{{ url('view-product-highlight', $product->id) }}" data-bs-toggle="tooltip"
                                    data-bs-title="View Highlight">
                                    <i class="fas fa-star text-warning"></i>
                                </a>
                            @elseif ($product->highlighted == 1)
                                <a data-bs-toggle="tooltip" data-bs-title="Highlighted Product">
                                    <i class="fas fa-star text-warning"></i>
                                </a>
                            @endif
                            @if ($product->created_by == auth()->id())
                                <a href="{{ url('product-list-bill', ['id' => $product->id]) }}" target="_blank"
                                    class="text-muted">
                                    <i class="fas fa-print" data-bs-toggle="tooltip" data-bs-title="Print Invoice"></i>
                                </a>
                            @endif
                            <h6 class="mb-0 share-btn" style="cursor: pointer;" data-bs-toggle="modal"
                                data-bs-target="#sharePopup" data-share-title='{{ $product->name }}'
                                data-share-url='{{ env('BASE_URL') . 'individual-product/' . $product->id }}'
                                data-share-text='{{ $product->categoryRelation->value ?? 'Product' }}'
                                data-job-id="{{ $product->id }}" data-share-type="product">
                                <i class="fas fa-share-nodes pe-1"></i>
                                Share
                            </h6>
                        </div>
                    </div>
                    <div class="cards-content">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-3">Brand : <span class="text-muted">{{ $product->brand_name ?? '-' }}</span>
                            </h5>
                            <h6 class="d-flex align-items-center column-gap-2 mb-3">
                                <div>
                                    @for ($i = 0; $i < 5; $i++)
                                        @if ($i < $product->reviews_avg_stars)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-muted"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span>{{ $product->reviews_count ?? '-' }} Reviews</span>
                            </h6>
                            <h5 class="mb-3">Category :
                                <span class="text-muted">{{ $product->categoryRelation->value ?? '-' }}</span>
                            </h5>
                        </div>
                        <div class="side-cards d-flex align-items-center justify-content-between flex-wrap mb-2 p-2">
                            <div class="d-flex align-items-center justify-content-center flex-column mx-auto">
                                <img src="{{ asset('assets/images/icon_delivery.png') }}" height="30px" class="mb-1"
                                    alt="">
                                <h6 class="text-center mb-1">Delivery</h6>
                                <h6 class="text-center bio">{{ $product->d_days ?? '-' }} days</h6>
                            </div>
                            <div class="d-flex align-items-center justify-content-center flex-column mx-auto">
                                <img src="{{ asset('assets/images/icon_stock.png') }}" height="30px" class="mb-1"
                                    alt="">
                                <h6 class="text-center mb-1">Availability</h6>
                                <h6 class="text-center bio">{{ $product->availability ?? '-' }}</h6>
                            </div>
                            <div class="d-flex align-items-center justify-content-center flex-column mx-auto">
                                <img src="{{ asset('assets/images/icon_location.png') }}" height="30px" class="mb-1"
                                    alt="">
                                <h6 class="text-center mb-1">Location</h6>
                                <h6 class="text-center bio">{{ $product->locationRelation->value ?? '-' }}</h6>
                            </div>
                        </div>
                        <div class="price-div mb-2">
                            <div class="price-div-left">
                                <h5 class="mb-2">PRICE (INR)</h5>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($product->created_by != auth()->id())
                                        <h5 class="text-success mb-2">₹ {{ number_format(($product->base_price + $product->cashback_price + $product->margin) * (1 + $product->tax_percentage / 100), 2) }}</h5>
                                        <h6 class="bio mb-2">
                                            <span class="text-decoration-line-through">₹ {{ number_format(($product->mrp + $product->cashback_price + $product->margin) * (1 + $product->tax_percentage / 100), 2) }}</span> /
                                            <span class="text-capitalize">{{ $product->product_unit ?? '-' }}
                                                (Tax {{ $product->tax_percentage ?? '-' }}% Included)</span>
                                        </h6>
                                    @else
                                        <h5 class="text-success mb-2">₹ {{ $product->sp ?? '-' }}</h5>
                                        <h6 class="bio mb-2">
                                            <span class="text-decoration-line-through">₹ {{ $product->mrp ?? '-' }}</span> /
                                            <span class="text-capitalize">{{ $product->product_unit ?? '-' }}
                                                (Tax {{ $product->tax_percentage ?? '-' }}% Included)</span>
                                        </h6>
                                    @endif
                                </div>
                                <h6 class="mb-2">Minimum Order Quantity :
                                    <span class="text-danger">{{ $product->moq ?? '-' }}</span>
                                </h6>
                                <h6 class="mb-2">Maximum Distance :
                                    <span class="text-muted">{{ $product->d_km ?? '-' }} km</span>
                                </h6>
                                <h6>₹ {{ $product->cashback_price ?? '-' }}
                                    <span class="text-muted">Cashback Offer !</span>
                                </h6>
                            </div>
                            <div class="price-div-right">
                                <div class="body-head d-block">
                                    <h5 class="text-center mb-2">About Vendor</h5>
                                    <h6 class="text-center mb-2">{{ $product->vendor->name ?? '-' }}</h6>
                                    <h6 class="text-center mb-2">GST No - {{ $product->vendor->gst->gst_number ?? '-' }}</h6>
                                    <h6 class="text-center mb-2">{{ $product->vendor->email ?? '-' }}</h6>
                                    <h6 class="text-center mb-2">+91 {{ $product->vendor->number ?? '-' }}</h6>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-2">Key Features :</h5>
                            <h6 class="text-muted mb-2">{{ $product->key_feature ?? '-' }}</h6>
                        </div>
                        @if ($product->created_by == Auth::id())
                            <div>
                                <h5 class="mb-2">Admin Remarks :</h5>
                                <h6 class="text-muted mb-2">{{ $product->remark ?? '-' }}</h6>
                            </div>
                        @endif
                        @if ($product->created_by != Auth::id())
                            <div class="d-flex align-items-center gap-2" id="cart-action-{{ $product->id }}">
                                @if (in_array($product->id, $cartItem))
                                    <a href="{{ route('cart') }}" class="w-100">
                                        <button class="followersbtn w-100 py-2">
                                            <i class="fas fa-truck-fast pe-1"></i> Go to Cart
                                        </button>
                                    </a>
                                @else
                                    <button class="removebtn w-100 py-2 add-to-cart" data-product="{{ $product->id }}"
                                        data-vendor="{{ $product->created_by ?? 1 }}"
                                        data-quantity="{{ $product->moq }}">
                                        <i class="fas fa-shopping-cart pe-1"></i> Add to Cart
                                    </button>
                                @endif
                                @php
                                    $isSaved = in_array($product->id, $savedProducts);
                                @endphp
                                <a href="javascript:void(0);" class="save-product-btn"
                                    data-product-id="{{ $product->id }}" data-bs-toggle="tooltip"
                                    data-bs-title="Wishlist">
                                    <button class="iconbtn py-2">
                                        <i class="{{ $isSaved ? 'fas text-danger' : 'far' }} fa-heart"></i>
                                    </button>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="individual-product-tabs">
            <div class="side-cards shadow-none border-0 p-0">
                <div class="profile-tabs">
                    <ul class="nav nav-tabs d-flex justify-content-between align-items-start column-gap-2 flex-wrap border-0"
                        id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="profilebtn text-start active" data-bs-toggle="tab" type="button"
                                data-bs-target="#description">Description</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="profilebtn text-start" data-bs-toggle="tab" type="button"
                                data-bs-target="#specification">Specification</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="profilebtn text-start" data-bs-toggle="tab" type="button"
                                data-bs-target="#delivery">Delivery Charges</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="profilebtn text-start" data-bs-toggle="tab" type="button"
                                data-bs-target="#review">Reviews</button>
                        </li>
                    </ul>

                    <div class="tab-content mt-4 cards-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="description" role="tabpanel">
                            <div class="body-head mb-3">
                                <h5>Product Description</h5>
                                @if ($product->catlogue != null)
                                    <a href="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $product->catlogue) }}"
                                        target="_blank" class="text-muted">
                                        <h6><i class="fas fa-print" data-bs-toggle="tooltip"
                                                data-bs-title="Catalogue"></i></h6>
                                    </a>
                                @endif
                            </div>
                            <h6 style="line-height: 25px;">
                                {{ $product->description ?? '-' }}
                            </h6>
                        </div>
                        <div class="tab-pane fade" id="specification" role="tabpanel">
                            <div class="body-head mb-3">
                                <h5>Product Specification</h5>
                            </div>
                            <div class="listtable p-0 border-0">
                                <div class="table-wrapper">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td>HSN Code</td>
                                            <td>{{ $product->hsn ?? '-' }}</td>
                                        </tr>
                                        @if (is_array($specs))
                                            @foreach ($specs as $key => $value)
                                                <tr>
                                                    <td>{{ $key }}</td>
                                                    <td>{{ $value }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>No Specifications Found</tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="delivery" role="tabpanel">
                            <div class="body-head mb-3">
                                <h5>Delivery Charges</h5>
                            </div>
                            <div class="listtable p-0 border-0">
                                <div class="table-wrapper">
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>Quantities</th>
                                                <th>Price</th>
                                            </tr>
                                        </thead>
                                        @if (is_array($trans))
                                            @foreach ($trans as $transp)
                                                <tr>
                                                    <td class="text-lowercase">{{ $transp['from'] ?? '-' }} to
                                                        {{ $transp['to'] ?? '-' }}</td>
                                                    <td>₹ {{ $transp['price'] ?? '' }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>No Specifications Found</tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="review" role="tabpanel">
                            @if ($reviews->count() > 0)
                                <div class="body-head mb-3">
                                    <h5>Review List</h5>
                                </div>
                                <ul class="list-unstyled cards-content">
                                    @foreach ($reviews as $review)
                                        <li id="{{ $review->id }}" class="mb-3">
                                            <div>
                                                <div class="mb-2">
                                                    @for ($i = 0; $i < 5; $i++)
                                                        @if ($i < $review->stars)
                                                            <i class="fas fa-star text-warning"></i>
                                                        @else
                                                            <i class="far fa-star text-muted"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <h6 class="mb-2">{{ $review->review }}</h6>
                                                <h6 class="bio mb-3">{{ $review->created_at->format('d M, Y h:i A') }}
                                                </h6>
                                                <div class="d-flex align-items-center column-gap-2">
                                                    <img src="{{ asset('assets/images/Avatar.png') }}" class="avatar-30"
                                                        alt="">
                                                    <h5 class="mb-0">{{ $review->user->name }}</h5>
                                                </div>
                                            </div>
                                        </li>
                                        <hr>
                                    @endforeach
                                </ul>
                            @else
                                <div class="side-cards shadow-none border-0">
                                    <div
                                        class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                                        <img src="{{ asset('assets/images/Empty/NoReviews.png') }}" height="150px"
                                            class="d-flex mx-auto mb-2" alt="">
                                        <h5 class="text-center">No Reviews Found</h5>
                                        <h6 class="text-center">No reviews are available yet - be the first to share your
                                            experience and help others decide.</h6>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="my-3">
            <div class="body-head mb-3">
                <h5>Related Products</h5>
                <a href="{{ url('products') }}">
                    <h6>See More <i class="fas fa-arrow-right ps-1"></i></h6>
                </a>
            </div>
            <div class="product-carousel owl-carousel p-0">
                @foreach ($recommended_products as $product)
                    <div class="item side-cards">
                        <div class="product-card position-relative" data-category="{{ $product->category }}"
                            data-location="{{ $product->location }}" data-stock="{{ $product->availability }}"
                            data-price="{{ $product->sp }}" data-size="{{ $product->size }}">
                            <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $product->cover_img ?? 'assets/images/NoImage.png') }}"
                                class="mb-2 w-100 rounded-3 object-fit-cover object-center" height="175px"
                                alt="">
                            @if ($product->highlighted == '1')
                                <span class="badge text-dark">Highlighted</span>
                            @endif
                            <div class="cards-head">
                                <h5 class="mb-1">{{ $product->name }}</h5>
                                <h6 class="mb-1 long-text">{{ $product->categoryRelation->value }}</h6>
                            </div>
                            <div class="cards-content">
                                <div class="mb-1 d-flex align-items-center justify-content-between flex-wrap">
                                    <h6>
                                        <i class="fas fa-star text-warning"></i>
                                        {{ number_format($product->reviews_avg_stars, 1) }}
                                        ({{ $product->reviews_count }})
                                    </h6>
                                    @if ($product->availability === 'In Stock')
                                        <h6 class="text-success">{{ $product->availability }}</h6>
                                    @else
                                        <h6 class="text-danger">{{ $product->availability }}</h6>
                                    @endif
                                </div>
                                <div class="mb-1 d-flex align-items-center justify-content-between flex-wrap">
                                    <h6>
                                        ₹ {{ $product->sp }}
                                        <span class="dashed-line ps-1"><i
                                                class="fas fa-indian-rupee-sign pe-1"></i>{{ $product->mrp }}</span>
                                    </h6>
                                    <h6><i class="fas fa-location-dot pe-1"></i>
                                        {{ $product->locationRelation->value ?? '' }}</h6>
                                </div>
                            </div>
                            @if ($product->created_by != Auth::id())
                                <div class="row align-items-center justify-content-between">
                                    <a href="{{ url('individual-product/' . $product->id) }}">
                                        <button class="listbtn w-100">View Product</button>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @include('popups.popup')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
                        $('#cart-action-' + productId).html(`
                                <a href={{ route('cart') }} class="w-100">
                                    <button class="followersbtn w-100 py-2 remove-from-cart" data-id="${response.cart_id}" data-product="${productId}">
                                        <i class="fas fa-truck-fast pe-1"></i> Go To Cart
                                    </button>
                                </a>
                                <button class="iconbtn py-2">
                                    <i class="far fa-heart"></i>
                                </button>
                            `);
                        showToast('Added to Cart');
                    }
                });
            });
        });
    </script>

    <!-- Carousel -->
    <script>
        $(document).ready(function() {
            $(".product-carousel.owl-carousel").owlCarousel({
                loop: false,
                margin: 15,
                nav: false,
                dots: false,
                autoplay: true,
                autoplayTimeout: 4000,
                responsive: {
                    0: {
                        items: 1.5
                    },
                    576: {
                        items: 2.8
                    },
                    992: {
                        items: 4
                    },
                    1200: {
                        items: 4.5
                    }
                }
            });
        });
    </script>

    {{-- save product (Wishlist) --}}
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
            });
        });
    </script>

    <script>
        $(document).on('click', '.gallery-div img', function() {
            let clickedImg = $(this);
            let clickedSrc = clickedImg.attr('src');

            let mainImg = $('#main-image');
            let mainSrc = mainImg.attr('src');

            // Swap images
            mainImg.attr('src', clickedSrc);
            clickedImg.attr('src', mainSrc);
        });
    </script>

    <script>
        $(document).on('click', '.status-toggle-btn', function() {
            let button = $(this);
            let productId = button.data('id');
            let currentStatus = button.data('status');

            $.ajax({
                url: "{{ route('product.toggleStatus') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: productId,
                    status: currentStatus
                },
                success: function(response) {
                    if (response.success) {
                        // Update button text, class, and data-status
                        button.text(response.new_status.charAt(0).toUpperCase() + response.new_status
                            .slice(1));
                        button.data('status', response.new_status);

                        if (response.new_status === 'active') {
                            button.removeClass('red-label').addClass('green-label');
                        } else {
                            button.removeClass('green-label').addClass('red-label');
                        }
                    }
                },
                error: function() {
                    showToast('Something went wrong!');
                }
            });
        });
    </script>

    <!-- Pass Laravel data to JS -->
    <script>
        window.appData = {
            shareUrl: "{{ route('toggle.getShareList') }}",
            csrf: "{{ csrf_token() }}"
        };
    </script>

    <script src="{{ asset('assets/js/share_job.js') }}"></script>


@endsection
