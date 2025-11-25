@php
    $services_data = collect($services_data); // flatten in case of nested collections
@endphp
@if ($services_data->isNotEmpty())
    <div class="my-2">
        {{-- @if (count($services) > 0) --}}
        <div class="body-head px-3 mb-2">
            <h5>Explore Service</h5>
            <a href="{{ url('services') }}">
                <h6>See More <i class="fas fa-arrow-right ps-1"></i></h6>
            </a>
        </div>
        {{-- @endif --}}
        <div class="home-carousel">
            @foreach ($services_data as $service)
                <div class="item side-cards">
                    <div class="product-card position-relative">
                        <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $service->image ?? 'assets/images/NoImage.png') }}"
                            class="mb-2 w-100 rounded-3 object-fit-cover object-center" height="175px" alt="">
                        @if ($service->highlighted == 1)
                            <a class="badge d-flex align-items-center">
                                <img src="{{ asset('assets/images/icon_highlights.png') }}" height="15px"
                                    class="pe-1" alt="">
                                <span>Highlighted</span>
                            </a>
                        @endif
                        <div class="cards-head">
                            <h5 class="mb-1 long-text">{{ $service->title ?? '-' }}</h5>
                            <h6 class="mb-1 long-text">
                                {{ $service->serviceType->value ?? '-' }}
                            </h6>
                        </div>

                        <div class="cards-content">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <h6 class="mb-1">₹ {{ $service->price_per_sq_ft ?? '-' }}</h6>
                                <h6 class="mb-1">
                                    <i class="fas fa-location-dot pe-1"></i>
                                    {{ $service->locationRelation->value ?? '-' }}
                                </h6>
                            </div>
                        </div>

                        <div class="row align-items-center justify-content-between">
                            <div id="cart-action-{{ $service->id }}">
                                <a href="{{ route('services.view', ['id' => $service->id]) }}">
                                    <button class="listbtn w-100">View Service</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
