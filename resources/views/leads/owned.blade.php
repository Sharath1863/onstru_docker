@extends('layouts.app')

@section('title', 'Onstru | Leads')

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
    </style>

    <div class="container-xl main-div">
        <div class="flex-side">
            <!-- Filter Sidebar -->
            @include('leads.aside')
            <!-- Cards -->
            <div class="flex-cards pt-2">
                <div class="body-head mb-3">
                    <h5>Leads Owned</h5>
                    <button class="border-0 m-0 p-0 filter-responsive" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvas-filter" aria-controls="offcanvas-filter">
                        <i class="fas fa-filter fs-4"></i>
                    </button>
                </div>

                <!-- Empty State -->
                <div class="side-cards shadow-none border-0" id="noCard"
                    style="{{ count($leads) > 0 ? 'display: none;' : 'display: block;'}}">
                    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                        <img src="{{ asset('assets/images/Empty/NoLeads.png') }}" height="200px" class="d-flex mx-auto mb-2"
                            alt="">
                        <h5 class="text-center mb-0">No Leads Found</h5>
                        <h6 class="text-center bio">Currently, no leads are available - try adjusting your filters or check back
                            later for new opportunities.</h6>
                    </div>
                </div>

                <!-- Lead Cards -->
                @foreach ($leads as $lead)
                    <div class="side-cards service-card mb-2" data-type="{{ $lead->serviceRelation->value }}"
                        data-price="{{ $lead->budget }}" data-buildup="{{ $lead->buildup_area }}"
                        data-location="{{ $lead->location }}">
                        <div class="cards-content">
                            <div class="row">
                                <div
                                    class="col-sm-12 col-md-4 d-flex align-items-start justify-content-start column-gap-3 mb-2">
                                    <img src="{{ asset($lead->image ?? 'assets/images/Favicon.png') }}" height="40px" alt="">
                                    <div>
                                        <h5 class="mb-1">{{ $lead->title ?? '-' }}</h5>
                                        <label class="m-0">{{ Str::limit($lead->description, 100) ?? '-' }}</label>
                                    </div>
                                </div>
                                <div
                                    class="col-sm-12 col-md-8 d-flex align-items-start justify-content-between flex-wrap column-gap-3">
                                    <div class="mb-2">
                                        <label class="mb-2">
                                            <i class="fas fa-cogs pe-2"></i> Service Type
                                        </label>
                                        <h6 class="mb-0">{{ $lead->serviceRelation->value ?? '-' }}</h6>
                                    </div>
                                    <div class="mb-2">
                                        <label class="mb-2">
                                            <i class="fas fa-street-view pe-2"></i> Build Up Area
                                        </label>
                                        <h6 class="mb-0">{{ $lead->buildup_area ?? '-' }} sqft</h6>
                                    </div>
                                    <div class="mb-2">
                                        <label class="mb-2">
                                            <i class="fas fa-chart-column pe-2"></i> Budget
                                        </label>
                                        <h6 class="mb-0">₹ {{ number_format($lead->budget) ?? '-' }}</h6>
                                    </div>
                                    <div class="mb-2">
                                        <label class="mb-2">
                                            <i class="fas fa-location-dot pe-2"></i> Location
                                        </label>
                                        <h6 class="mb-0">{{ $lead->locationRelation->value ?? '-' }}</h6>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4 mb-2">
                                    <label>
                                        <i class="far fa-clock pe-1"></i> Posted {{ $lead->created_at->diffForHumans() ?? '-' }}
                                    </label>
                                </div>
                                <div
                                    class="col-sm-12 col-md-8 d-flex align-items-start justify-content-between flex-wrap column-gap-3">
                                    <div class="mb-2">
                                        <label class="mb-2">
                                            <i class="far fa-calendar pe-2"></i>Expected Start Date
                                        </label>
                                        <h6 class="mb-0">{{ \Carbon\Carbon::parse($lead->start_date)->format('d-m-Y') ?? '-' }}
                                        </h6>
                                    </div>
                                    {{-- <div class="mb-2">
                                        <label class="mb-2">
                                            <i class="far fa-calendar pe-2"></i>Rating
                                        </label>
                                        <h6 class="mb-0">{{ $lead->review ?? '-' }}
                                        </h6>
                                    </div> --}}
                                    <div class="d-block ms-auto my-auto mb-2">
                                        <a href="{{ url('lead-owned-bill', ['id' => $lead->id]) }}" target="_blank" class="text-muted">
                                            <button class="iconbtn" data-bs-toggle="tooltip" data-bs-title="Print Invoice">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        </a>
                                        <span class="review-btn-container" data-lead-id="{{ $lead->id }}">
                                            @if ($userReviews->has($lead->id))
                                                <a class="reviewed-btn">
                                                    <button class="listbtn">Reviewed</button>
                                                </a>
                                            @else
                                                <a data-bs-toggle="modal" data-bs-target="#review{{ $lead->id }}"
                                                    class="review-btn">
                                                    <button class="removebtn">Review</button>
                                                </a>
                                            @endif
                                        </span>
                                        <button class="followersbtn" data-bs-toggle="modal"
                                            data-bs-target="#leadOwner{{ $lead->user->id }}">
                                            Lead Owner Details
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Lead Owner Modal -->
                    <div class="modal fade" id="leadOwner{{ $lead->user->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="payLeadsLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="m-0">Lead Owner Details</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mt-2">
                                        <div class="col-sm-12 col-md-6 mb-2">
                                            <label>Name</label>
                                            <h6>{{ $lead->user->name }}</h6>
                                        </div>
                                        <div class="col-sm-12 col-md-6 mb-2">
                                            <label>Username</label>
                                            <h6 class="text-lowercase">{{ $lead->user->user_name }}</h6>
                                        </div>
                                        <div class="col-sm-12 col-md-6 mb-2">
                                            <label>Role</label>
                                            <h6>{{ $lead->user->as_a ?? 'Consumer' }}</h6>
                                        </div>
                                        <div class="col-sm-12 col-md-6 mb-2">
                                            <label>Contact Number</label>
                                            <h6>+91 {{ $lead->user->number }}</h6>
                                        </div>
                                        <div class="col-sm-12 col-md-6 mb-2">
                                            <label>Email ID</label>
                                            <h6>{{ $lead->user->email }}</h6>
                                        </div>
                                        <div class="col-sm-12 col-md-6 mb-2">
                                            <label>Address</label>
                                            <h6>{{ $lead->user->address }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Review Modal -->
                    <div class="modal fade" id="review{{ $lead->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="reviewLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">{{ $lead->title ?? '-' }}</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body mt-2">
                                    <div class="col-sm-12 mb-2">
                                        <label for="rating">Rating <span>*</span></label>
                                        <div class="star-rating" data-lead-id="{{ $lead->id }}">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fa fa-star star" data-value="{{ $i }}"></i>
                                            @endfor
                                        </div>
                                        <input type="hidden" class="rating-value" value="0">
                                    </div>
                                    <div class="col-sm-12 mb-2">
                                        <label for="review">Review <span>*</span></label>
                                        <textarea rows="2" class="form-control review-text"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer d-flex align-items-center justify-content-center my-2">
                                    <button type="button" class="formbtn post-review-btn" data-lead-id="{{ $lead->id }}">
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
                var leadId = $(this).data('lead-id');
                var rating = $modal.find('.rating-value').val();
                var review = $modal.find('.review-text').val();

                $.ajax({
                    url: "{{ route('lead-reviews.store') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        lead_id: leadId,
                        rating: rating,
                        review: review,
                    },
                    success: function (response) {
                        $modal.modal('hide');
                        showToast('Review submitted successfully!', 'success');
                        $modal.find('.rating-value').val(0);
                        $modal.find('.review-text').val('');
                        $modal.find('.star').removeClass('selected');
                        var leadId = response.lead_id || $modal.find('.post-review-btn').data('lead-id');
                        var $btnContainer = $('.review-btn-container[data-lead-id="' + leadId + '"]');
                        $btnContainer.html(`
                                    <a class="reviewed-btn">
                                        <button class="listbtn">Reviewed</button>
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