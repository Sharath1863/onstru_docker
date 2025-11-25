@extends('layouts.app')

@section('title', 'Onstru | Service Request')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">

    <style>
        .side-cards:hover {
            transform: translate(0px, -5px);
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }
    </style>

    <div class="container-xl main-div">
        <div class="flex-side">
            <!-- Filter Sidebar -->
            @include('services.aside')
            <!-- Cards -->
            <div class="flex-cards pt-2">
                <div class="body-head mb-3">
                    <h5>Services Requests</h5>
                    <button class="border-0 m-0 p-0 filter-responsive" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvas-filter" aria-controls="offcanvas-filter">
                        <i class="fas fa-filter fs-4"></i>
                    </button>
                </div>

                <!-- Empty State -->
                <div class="side-cards shadow-none border-0" id="noCard"
                    style="{{ count($serviceRequests) > 0 ? 'display: none;' : 'display: block;'}}">
                    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                        <img src="{{ asset('assets/images/Empty/NoServices.png') }}" height="200px"
                            class="d-flex mx-auto mb-2" alt="">
                        <h5 class="text-center mb-0">No Service Requests Found Yet</h5>
                        <h6 class="text-center bio">No services requests are available right now - try adjusting your search or
                            browse through other options.</h6>
                    </div>
                </div>

                <!-- Service Cards -->
                <div class="service-cards">
                    @foreach ($serviceRequests as $request)
                        <div class="side-cards service-card w-100 mx-auto row position-relative"
                            data-type="{{ $request->serviceType->value }}" data-budget="{{ $request->budget }}"
                            data-location="{{ $request->locationRelation->value }}"
                            data-highlight="{{ $request->service->highlighted }}">
                            <div class="col-sm-12 col-md-5 m-auto">
                                <img src="{{ asset($request->service->image ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $request->service->image : 'assets/images/NoImage.png') }}"
                                    class="object-fit-cover object-center w-100 rounded-3" height="175px"
                                    alt="{{ $request->title }}">
                            </div>
                            <div class="col-sm-12 col-md-7 m-auto">
                                @if ($request->service->highlighted == '1')
                                    <span class="badge"><img src="{{ asset('assets/images/icon_highlights.png') }}" height="15px"
                                            class="pe-1" alt=""> Highlighted</span>
                                @endif
                                <div class="cards-head">
                                    <h5 class="mb-2 long-text">{{ $request->service->title ?? '-' }}</h5>
                                    <h6 class="mb-2 long-text">
                                        <i class="fas fa-tools pe-1"></i>{{ $request->serviceType->value ?? '-' }}
                                    </h6>
                                    <h6 class="mb-2 long-text">{{ $request->description ?? '-' }}</h6>
                                </div>
                                <div class="cards-content">
                                    <div class="d-flex align-items-start justify-content-between flex-wrap">
                                        <h6 class="mb-2">
                                            <i class="fas fa-location-dot pe-1"></i>
                                            {{ $request->locationRelation->value ?? '-' }}
                                        </h6>
                                        <h6 class="mb-2">
                                            <i class="far fa-calendar pe-1"></i>
                                            {{ $request->start_date->format('d-m-Y') ?? '-' }}
                                        </h6>
                                    </div>
                                    <div class="d-flex align-items-start justify-content-between flex-wrap">
                                        <h6 class="mb-2">
                                            <i class="fas fa-maximize pe-1"></i> {{ $request->buildup_area ?? '-' }} sqft.
                                        </h6>
                                        <h6 class="mb-2">
                                            ₹ {{ $request->budget ?? '-' }}
                                        </h6>
                                    </div>
                                    <div class="d-flex align-items-start justify-content-between flex-wrap">
                                        <h6 class="mb-2">
                                            <i class="fas fa-phone pe-1"></i> +91 {{ $request->phone_number ?? '-' }}
                                        </h6>
                                    </div>
                                    <label class="m-0">
                                        <i class="far fa-clock pe-1"></i> Posted
                                        {{ $request->created_at->diffForHumans() ?? '-' }}
                                    </label>
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
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.filter-checkbox');
            const serviceCards = document.querySelectorAll('.service-card');
            const searchInput = document.getElementById('keywordSearch');
            const budgetInput = document.getElementById('budget');
            const noCard = document.getElementById("noCard");

            checkboxes.forEach(cb => cb.addEventListener('change', applyFilters));
            searchInput.addEventListener('input', applyFilters);
            budgetInput.addEventListener('input', applyFilters);

            function applyFilters() {
                const selectedLocation = [...document.querySelectorAll('.loc-filter:checked')].map(cb => cb.value.toLowerCase());
                const selectedType = [...document.querySelectorAll('.type-filter:checked')].map(cb => cb.value.toLowerCase());
                const nrmlSearch = searchInput.value.toLowerCase();
                const areaBudget = parseFloat(budgetInput.value);
                const selectedHighlights = [...document.querySelectorAll('.highlight-filter:checked')].map(cb => cb.value);

                let noImage = false;

                serviceCards.forEach(card => {
                    const location = card.dataset.location.toLowerCase();
                    const type = card.dataset.type.toLowerCase();
                    const search = card.textContent.toLowerCase();
                    const budget = parseFloat(card.dataset.budget);
                    const highlight = card.dataset.highlight;

                    const locationMatch = selectedLocation.length === 0 || selectedLocation.includes(location);
                    const typeMatch = selectedType.length === 0 || selectedType.includes(type);
                    const serviceMatch = nrmlSearch === '' || search.includes(nrmlSearch);
                    const areaMatch = isNaN(areaBudget) || budget == areaBudget;
                    const highlightMatch = selectedHighlights.length === 0 || selectedHighlights.includes(highlight);

                    if (locationMatch && typeMatch && serviceMatch && areaMatch && highlightMatch) {
                        card.style.display = 'flex';
                        noImage = true;
                    } else {
                        card.style.display = 'none';
                    }
                });
                noCard.style.display = noImage === false ? 'block' : 'none';
            }
            applyFilters();
        });
    </script>

@endsection