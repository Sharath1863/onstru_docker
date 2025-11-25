@extends('layouts.app')

@section('title', 'Onstru | Services')

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
            @include('services.aside')
            <!-- Cards -->
            <div class="flex-cards pt-2">
                <div class="body-head mb-3">
                    <h5>Services</h5>
                    <button class="border-0 m-0 p-0 filter-responsive" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvas-filter" aria-controls="offcanvas-filter">
                        <i class="fas fa-filter fs-4"></i>
                    </button>
                </div>

                <!-- Empty State -->
                <div class="side-cards shadow-none border-0" id="noCard"
                    style="{{ count($services) > 0 ? 'display: none;' : 'display: block;' }}">
                    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                        <img src="{{ asset('assets/images/Empty/NoServices.png') }}" height="200px"
                            class="d-flex mx-auto mb-2" alt="">
                        <h5 class="text-center mb-0">No Services Here Yet</h5>
                        <h6 class="text-center bio">No services match your search right now - try refining your criteria 
                            or check back for new listings soon.</h6>
                    </div>
                </div>

                <!-- Service Cards -->
                <div class="service-cards" id="initial_services">
                    @foreach ($services as $service)
                        <div class="side-cards service-card w-100 mx-auto row position-relative"
                            data-type="{{ $service->serviceType->value ?? '-' }}"
                            data-budget="{{ $service->price_per_sq_ft }}"
                            data-location="{{ $service->locationRelation->value ?? '-' }}"
                            data-highlight="{{ $service->highlighted }}">
                            <div class="col-sm-12 col-md-5 m-auto">
                                <img src="{{ asset($service->image ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $service->image : 'assets/images/NoImage.png') }}"
                                    class="object-fit-cover object-center w-100 rounded-3" height="175px"
                                    alt="{{ $service->title }}">
                            </div>
                            <div class="col-sm-12 col-md-7 m-auto">
                                @if ($service->highlighted == '1')
                                    <span class="badge"><img src="{{ asset('assets/images/icon_highlights.png') }}"
                                            height="15px" class="pe-1" alt=""> Highlighted</span>
                                @endif
                                <div class="cards-head">
                                    <h5 class="mb-2 long-text">{{ $service->title ?? '-' }}</h5>
                                    <h6 class="mb-2 long-text">
                                        <i class="fas fa-tools pe-1"></i> {{ $service->serviceType->value ?? '-' }}
                                    </h6>
                                    <h6 class="mb-2 long-text">{{ Str::limit($service->description, 100) ?? '-' }}</h6>
                                </div>
                                <div class="cards-content">
                                    <h6 class="mb-2">
                                        <i class="fas fa-star text-warning"></i>
                                        {{ number_format($service->reviews_avg_stars, 1) }}
                                        ({{ $service->reviews_count }})
                                    </h6>
                                    <h6 class="mb-2">
                                        <i class="fas fa-location-dot pe-1"></i>
                                        {{ $service->locationRelation->value ?? '-' }}
                                    </h6>
                                    <h6 class="mb-2">
                                        <i class="fas fa-indian-rupee-sign pe-1"></i>
                                        {{ $service->price_per_sq_ft ?? '-' }} per sqft
                                    </h6>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ url('individual-service/' . $service->id) }}">
                                        <button class="removebtn w-100">View Details</button>
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
        let nextUrl = "{{ route('services') }}";
        let loading = false;
        let debounceTimer;

        function getFilters() {
            return {
                keyword: $("#keywordSearch").val(),
                categories: $(".type-filter:checked").map((_, el) => $(el).val()).get(),
                // stock: $(".stock-filter:checked").map((_, el) => $(el).val()).get(),
                locations: $(".loc-filter:checked").map((_, el) => $(el).val()).get(),
                budget: $("#budget").val(),
                // maxPrice: $("#maxPrice").val(),
                highlight: $(".highlight-filter:checked").map((_, el) => $(el).val()).get()
            };
        }

        // Load products
        function loadServices(url, reset = false) {
            $('#loader').show();
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
                    $('#loader').show();
                },
                success: function(res) {
                    if (res.data.length === 0) {
                        if (reset) {
                            $("#initial_services").html("");
                            $("#noCard").show();
                        }
                        $("#loadMoreBtn").hide();
                        return;
                    }

                    $("#noCard").hide();

                    let html = "";
                    res.data.forEach(service => {
                        html += `
                            <div class="side-cards service-card position-relative w-100 mx-auto row position-relative" data-type="${service.serviceType?.value}" data-budget="${service.price_per_sq_ft}"
                                data-location="${service.location_relation?.value}" data-highlight="${service.highlighted}">
                                <div class="col-sm-12 col-md-5 m-auto">
                                    <img src="${service.image
                                        ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' + service.image
                                        : '/assets/images/NoImage.png'}"
                                        class="object-fit-cover object-center w-100 rounded-3" height="175px" alt="${service.title}">
                                </div>
                                <div class="col-sm-12 col-md-7 m-auto">
                                    ${service.highlighted === 1 ? `
                                    <span class="badge">
                                        <img src="/assets/images/icon_highlights.png" height="15px" class="pe-1" alt=""> Highlighted
                                    </span>` 
                                    : ''}
                                    <div class="cards-head">
                                        <h5 class="mb-2 long-text">${service.title ?? '-'}</h5>
                                        <h6 class="mb-2 long-text">
                                           <i class="fas fa-tools pe-1"></i> ${service.service_type?.value ?? '-'}
                                        </h6>
                                        <h6 class="mb-2 long-text">${service.description ?? '-'}</h6>
                                    </div>

                                    <div class="cards-content">
                                        <h6 class="mb-2">
                                            <i class="fas fa-star text-warning"></i>
                                            ${Number(service.reviews_avg_stars || 0).toFixed(1) ?? ''}
                                            (${service.reviews_count ?? 0 ?? '-'})
                                        </h6>
                                        <h6 class="mb-2">
                                            <i class="fas fa-location-dot pe-1"></i>
                                            ${service.location_relation?.value ?? '-'}
                                        </h6>
                                        <h6 class="mb-2">
                                            <i class="fas fa-maximize pe-1"></i> 
                                            ${service.price_per_sq_ft ?? '-'} per sqft.
                                        </h6>
                                    </div>
                                    <div class="mt-2">
                                        <a href="individual-service/${service.id}">
                                            <button class="removebtn w-100">View Details</button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    if (reset) {
                        $("#initial_services").html(html);
                    } else {
                        $("#initial_services").append(html);
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
                    $('#loader').hide();
                }
            });
        }

        // Load more button click
        $(document).on("click", "#loadMoreBtn", function() {
            const nextUrl = $(this).data("next-url");
            // alert(nextUrl);
            loadServices(nextUrl, false);
        });

        // Debounce keyword input
        $('#keywordSearch').on('keyup', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                nextUrl = "{{ route('services') }}";
                loadServices(nextUrl, true);
            }, 500);
        });

        // // On filter change → reset products
        $(document).on('change',
            '.filter-checkbox, .type-filter, .loc-filter, .highlight-filter, #budget',
            function() {
                nextUrl = "{{ route('services') }}";
                loadServices(nextUrl, true);
            });
    </script>


@endsection
