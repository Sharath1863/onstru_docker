@extends('layouts.app')

@section('title', 'Onstru | Requested Services')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">

    <style>
        .star {
            font-size: 24px;
            color: var(--gray);
            cursor: pointer;
        }

        .star.selected,
        .star.hover {
            color: var(--main);
        }

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
                    <h5>Requested Services</h5>
                    <button class="border-0 m-0 p-0 filter-responsive" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvas-filter" aria-controls="offcanvas-filter">
                        <i class="fas fa-filter fs-4"></i>
                    </button>
                </div>

                <!-- Empty State -->
                <div class="side-cards shadow-none border-0" id="noCard"
                    style="{{ count($serviceRequests) > 0 ? 'display: none;' : 'display: block;' }}">
                    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                        <img src="{{ asset('assets/images/Empty/NoServices.png') }}" height="200px"
                            class="d-flex mx-auto mb-2" alt="">
                        <h5 class="text-center mb-0">No Requested Service Found Yet</h5>
                        <h6 class="text-center bio">You haven't requested any services yet - explore available options and
                            submit a request to get started.</h6>
                    </div>
                </div>

                <!-- Service Cards -->
                <div class="service-cards">
                    @foreach ($serviceRequests as $requested)
                        <div class="side-cards service-card w-100 mx-auto row position-relative"
                            data-type="{{ $requested->serviceType->value }}" data-budget="{{ $requested->budget }}"
                            data-location="{{ $requested->locationRelation->value }}"
                            data-highlight="{{ $requested->service->highlighted }}">
                            <div class="col-sm-12 col-md-5 m-auto">
                                <img src="{{ asset($requested->service->image ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $requested->service->image : 'assets/images/NoImage.png') }}"
                                    class="object-fit-cover object-center w-100 rounded-3" height="160px"
                                    alt="{{ $requested->title }}">
                            </div>
                            <div class="col-sm-12 col-md-7 m-auto">
                                @if ($requested->service->highlighted == '1')
                                    <span class="badge"><img src="{{ asset('assets/images/icon_highlights.png') }}" height="15px"
                                            class="pe-1" alt=""> Highlighted</span>
                                @endif
                                <div class="cards-head">
                                    <h5 class="mb-2 long-text">{{ $requested->service->title ?? '-' }}</h5>
                                    <h6 class="mb-2 long-text">
                                        <i class="fas fa-tools pe-1"></i>{{ $requested->serviceType->value ?? '-' }}
                                    </h6>
                                    <h6 class="mb-2 long-text">{{ $requested->description ?? '-' }}</h6>
                                </div>
                                <div class="cards-content">
                                    <div class="d-flex align-items-start justify-content-between flex-wrap">
                                        <h6 class="mb-2">
                                            <i class="fas fa-location-dot pe-1"></i>
                                            {{ $requested->locationRelation->value ?? '-' }}
                                        </h6>
                                        <h6 class="mb-2">
                                            <i class="far fa-calendar pe-1"></i>
                                            {{ $requested->start_date->format('d-m-Y') ?? '-' }}
                                        </h6>
                                    </div>
                                    <div class="d-flex align-items-start justify-content-between flex-wrap">
                                        <h6 class="mb-2">
                                            <i class="fas fa-maximize pe-1"></i> {{ $requested->buildup_area ?? '-' }} sqft
                                        </h6>
                                        <h6 class="mb-2">
                                            <i class="fas fa-indian-rupee-sign pe-1"></i> {{ $requested->budget ?? '-' }}
                                        </h6>
                                    </div>
                                </div>
                                <div class="mt-2 row align-items-center justify-content-between">
                                    <div class="col-9 pe-0 review-btn-container"
                                        data-service-id="{{ $requested->service->id }}">
                                        @if ($userReviews->has($requested->service->id))
                                            <a class="reviewed-btn">
                                                <button class="listbtn w-100">Reviewed</button>
                                            </a>
                                        @else
                                            <a data-bs-toggle="modal" data-bs-target="#review{{ $requested->service->id }}"
                                                class="review-btn">
                                                <button class="removebtn w-100">Review</button>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="col-3 pe-0">
                                        <a href="{{ url('individual-service/' . $requested->service->id) }}"
                                            data-bs-toggle="tooltip" data-bs-title="View Service">
                                            <button class="iconbtn w-100">
                                                <i class="fas fa-external-link"></i>
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Review Modal -->
                        <div class="modal fade" id="review{{ $requested->service->id }}" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="reviewLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">{{ $requested->service->title }}</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body mt-2">
                                        <div class="col-sm-12 mb-2">
                                            <label>Rating <span>*</span></label>
                                            <div class="star-rating" data-service-id="{{ $requested->service->id }}">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fa fa-star star" data-value="{{ $i }}"></i>
                                                @endfor
                                            </div>
                                            <input type="hidden" class="rating-value" value="0">
                                        </div>
                                        <div class="col-sm-12 mb-2">
                                            <label>Review <span>*</span></label>
                                            <textarea rows="2" class="form-control review-text"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex align-items-center justify-content-center my-2">
                                        <button type="button" class="formbtn post-review-btn"
                                            data-service-id="{{ $requested->service->id }}">
                                            Post Review
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
                const budget = parseFloat(budgetInput.value);
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
                    const budgetMatch = isNaN(budget) || budget == budget;
                    const highlightMatch = selectedHighlights.length === 0 || selectedHighlights.includes(highlight);

                    if (locationMatch && typeMatch && serviceMatch && budgetMatch && highlightMatch) {
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

    <script>
        $(document).ready(function () {
            // Star hover effect
            $(document).on('mouseenter', '.star', function () {
                var value = $(this).data('value');
                $(this).siblings('.star').each(function () {
                    $(this).toggleClass('hover', $(this).data('value') <= value);
                });
                $(this).toggleClass('hover', true);
            }).on('mouseleave', '.star-rating', function () {
                $(this).find('.star').removeClass('hover');
            });

            // Star click: select rating
            $(document).on('click', '.star', function () {
                var $ratingGroup = $(this).closest('.star-rating');
                var value = $(this).data('value');
                $ratingGroup.find('.star').each(function () {
                    $(this).toggleClass('selected', $(this).data('value') <= value);
                });
                $ratingGroup.siblings('.rating-value').val(value);
            });

            // Submit review
            $(document).on('click', '.post-review-btn', function (e) {
                e.preventDefault();
                var $modal = $(this).closest('.modal');
                var serviceId = $(this).data('service-id');
                var rating = $modal.find('.rating-value').val();
                var review = $modal.find('.review-text').val();

                $.ajax({
                    url: "{{ route('service-reviews.store') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        service_id: serviceId,
                        rating: rating,
                        review: review,
                    },
                    success: function (response) {
                        $modal.modal('hide');
                        showToast('Review submitted successfully!', 'success');
                        $modal.find('.rating-value').val(0);
                        $modal.find('.review-text').val('');
                        $modal.find('.star').removeClass('selected');
                        var serviceId = response.service_id || $modal.find('.post-review-btn')
                            .data('service-id');
                        var $btnContainer = $('.review-btn-container[data-service-id="' +
                            serviceId + '"]');
                        $btnContainer.html(`
                                        <a class="reviewed-btn">
                                            <button class="listbtn w-100">Reviewed</button>
                                        </a>
                                    `);
                    },
                    error: function () {
                        alert('Failed to submit review. Try again.');
                    }
                });
            });
        });
    </script>

@endsection