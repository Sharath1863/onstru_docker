@extends('layouts.app')

@section('title', 'Onstru | View Service')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">

    <style>
        @media screen and (min-width: 1098px) {
            .flex-side {
                grid-template-columns: 70% 28%;
            }
        }

        @media screen and (min-width: 767px) and (max-width: 1098px) {
            .flex-side {
                display: grid;
                grid-template-columns: 65% 34%;
                align-items: start;
                justify-content: space-between;
            }
        }

        @media screen and (max-width: 767px) {
            .flex-side {
                display: grid;
                grid-template-columns: repeat(1, 1fr);
                row-gap: 20px;
            }

            .flex-side .img-div img .flex-side .img-div video {
                height: 200px !important;
            }
        }

        .carousel-indicators [data-bs-target] {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--border);
        }

        .carousel-indicators .active {
            background-color: var(--main);
        }
    </style>

    <div class="container main-div">
        <div class="body-head mb-3">
            <a href="javascript:history.back()">
                <h5><i class="fas fa-angle-left pe-1"></i> Back</h5>
            </a>
        </div>

        <div class="flex-side">
            @php
                // Decode JSON if it's a string
                $images = is_string($service->sub_images)
                    ? json_decode($service->sub_images, true)
                    : $service->sub_images;

                // Unique carousel ID
                $carouselId = 'mediaCarousel_' . ($service->id ?? uniqid());
            @endphp

            @if (!empty($images) && is_array($images))
                <div id="{{ $carouselId }}" class="carousel slide side-cards mb-4" data-bs-ride="carousel">
                    <!-- Indicators -->
                    <div class="carousel-indicators">
                        @foreach (array_values($images) as $index => $img)
                            <button type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide-to="{{ $index }}"
                                class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                aria-label="Slide {{ $index + 1 }}">
                            </button>
                        @endforeach

                        @if ($service->video)
                            <button type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide-to="{{ count($images) }}"
                                aria-label="Video Slide">
                            </button>
                        @endif
                    </div>

                    <!-- Carousel Content -->
                    <div class="carousel-inner rounded-3 img-div">
                        @foreach (array_values($images) as $index => $img)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="https://onstru-social.s3.ap-south-1.amazonaws.com/{{ $img }}"
                                    class="d-block w-100 object-fit-cover object-top rounded-3" height="500"
                                    alt="Image {{ $index + 1 }}">
                            </div>
                        @endforeach

                        @if ($service->video)
                            <div class="carousel-item {{ count($images) === 0 ? 'active' : '' }}">
                                <video class="d-block w-100 rounded-3" height="500" controls loop>
                                    <source src="https://onstru-social.s3.ap-south-1.amazonaws.com/{{ $service->video }}"
                                        type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Fallback if no images -->
                <img src="https://onstru-social.s3.ap-south-1.amazonaws.com/{{ $service->image }}"
                    class="d-block w-100 object-fit-cover object-top rounded-3" height="500" alt="No Image">
                @if ($service->video)
                    <video class="d-block w-100 rounded-3" height="500" controls loop>
                        <source src="https://onstru-social.s3.ap-south-1.amazonaws.com/{{ $service->video }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @endif
            @endif


            <div class="flex-cards">
                <div class="side-cards mx-auto mb-3 position-relative">
                    <div class="cards-head">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="mb-3">
                                <h5 class="mb-1">Title</h5>
                                <h6 class="mb-0">{{ $service->title }}</h6>
                            </div>
                            @if ($service->highlighted == 1 && $service->created_by == auth()->id())
                                <a href="{{ route('view-service-highlight', $service->id) }}" class="badge"
                                    data-bs-toggle="tooltip" data-bs-title="View Highlight">
                                    <img src="{{ asset('assets/images/icon_highlights.png') }}" height="15px" class="pe-1"
                                        alt=""> Highlighted
                                </a>
                            @elseif ($service->highlighted == 1)
                                <a class="badge" data-bs-toggle="tooltip" data-bs-title="Highlighted Service">
                                    <img src="{{ asset('assets/images/icon_highlights.png') }}" height="15px" class="pe-1"
                                        alt=""> Highlighted
                                </a>
                            @endif
                        </div>
                        <div class="col-sm-12 mb-3">
                            <h5 class="mb-1">Service Type</h5>
                            <h6 class="mb-0">{{ $service->serviceType->value ?? '-' }}</h6>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <h5 class="mb-1">Location</h5>
                            <h6 class="mb-0">{{ $service->locationRelation->value ?? '-' }}</h6>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <h5 class="mb-1">Price per sq.ft</h5>
                            <h6 class="mb-0">{{ number_format($service->price_per_sq_ft) ?? 'N/A' }} per sqft.</h6>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <h5 class="mb-1">Ratings</h5>
                            <h6 class="mb-0">
                                <i class="fas fa-star text-warning"></i>
                                {{ number_format($service->reviews_avg_stars, 1) }}
                                ({{ $service->reviews_count }})
                            </h6>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <h5 class="mb-1">Description</h5>
                            <h6 class="mb-0">{{ $service->description }}</h6>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <h5 class="mb-1">Highlighted</h5>
                            @if ($service->highlighted == 1)
                                <h6 class="mb-0">Yes</h6>
                            @else
                                <h6 class="mb-0">No</h6>
                            @endif
                        </div>
                        @if ($service->wallet)
                            <div class="col-sm-12 mb-3">
                                <h5 class="mb-1">Budget</h5>
                                <h6 class="mb-2">
                                    ₹ {{ number_format($service->wallet) }}
                                </h6>
                            </div>
                        @endif
                        @if (auth()->user())
                            <div class="col-sm-12 mb-3">
                                <h5 class="mb-1">Admin Remarks</h5>
                                <h6 class="mb-0">{{ $service->remark ?? '-' }}</h6>
                            </div>
                        @endif
                        <div class="col-sm-12 mb-3">
                            <h5 class="mb-1">Status</h5>
                            <button
                                class="status-toggle-btn {{ $service->status === 'active' ? 'green-label' : 'red-label' }}"
                                data-id="{{ $service->id }}" data-status="{{ $service->status }}">
                                {{ ucfirst($service->status) }}
                            </button>
                        </div>
                        <div class="col-sm-12">
                            <h5 class="mb-1">Invoice</h5>
                            <a href="{{ url('service-list-bill', ['id' => $service->id]) }}" target="_blank"
                                class="text-muted">
                                <i class="fas fa-print" data-bs-toggle="tooltip" data-bs-title="Print Invoice"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews -->
        @include('services.reviews')

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusButtons = document.querySelectorAll('.status-toggle-btn');
            statusButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const serviceId = this.dataset.id;
                    const currentStatus = this.dataset.status;

                    fetch("{{ route('service.toggleStatus') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            id: serviceId,
                            status: currentStatus
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            // Update button UI
                            this.innerText = data.status.charAt(0).toUpperCase() + data.status
                                .slice(1);
                            this.dataset.status = data.status;
                            this.classList.remove('green-label', 'red-label');
                            this.classList.add(data.status === 'active' ? 'green-label' :
                                'red-label');
                            // Optional: show toast
                            console.log(data.message);
                        })
                        .catch(err => {
                            console.error("Error updating status:", err);
                            alert("Failed to update status.");
                        });
                });
            });
        });
    </script>

@endsection