@php
    $products_data = collect($products_data); // flatten in case of nested collections
@endphp
@if ($products_data->isNotEmpty())
    <div class="my-2">
        {{-- @if (count($products) > 0) --}}
        <div class="body-head px-3 mb-2">
            <h5>Explore Products</h5>
            <a href="{{ url('products') }}">
                <h6>See More <i class="fas fa-arrow-right ps-1"></i></h6>
            </a>
        </div>
        {{-- @endif --}}
        <div class="home-carousel">
            @foreach ($products_data as $product)
                <div class="item side-cards">
                    <div class="product-card position-relative">
                        <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $product->cover_img ?? 'assets/images/NoImage.png') }}"
                            class="mb-2 w-100 rounded-3 object-fit-cover object-center" height="175px" alt="">
                        @if ($product->highlighted == '1')
                            <a class="badge d-flex align-items-center">
                                <img src="{{ asset('assets/images/icon_highlights.png') }}" height="15px"
                                    class="pe-1" alt="">
                                <span>Highlighted</span>
                            </a>
                        @endif

                        <div class="cards-head">
                            <h5 class="mb-1">{{ $product->name ?? '-' }}</h5>
                            <h6 class="mb-1 long-text">{{ $product->categoryRelation->value ?? '-' }}</h6>
                        </div>
                        <div class="cards-content">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <h6 class="mb-1">
                                    <i class="fas fa-star pe-1 text-warning"></i>
                                    {{ number_format($product->reviews_avg_stars, 1) }}
                                    ({{ $product->reviews_count ?? 0 }})
                                </h6>
                                {{-- @if ($product->availability === 'In Stock')
                                <h6 class="text-success">{{ $product->availability }}</h6>
                            @else
                                <h6 class="text-danger">{{ $product->availability }}</h6>
                            @endif --}}
                            </div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <h6 class="mb-1">
                                    ₹ {{ $product->sp ?? '-' }}
                                    <span class="dashed-line ps-1"><i
                                            class="fas fa-indian-rupee-sign pe-1"></i>{{ $product->mrp ?? '-' }}</span>
                                </h6>
                                <h6 class="mb-1"><i class="fas fa-location-dot pe-1"></i>
                                    {{ $product->locationRelation->value ?? '-' }}</h6>
                            </div>
                        </div>
                        <a href="{{ url('individual-product/' . $product->id) }}">
                            <button class="listbtn w-100">View Product</button>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
