@extends('layouts.app')

@section('title', 'Onstru | Service Details')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.css') }}">
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

            .flex-side .img-div img {
                height: 200px;
            }
        }

        @media screen and (min-width: 767px) {
            .modal-dialog {
                max-width: 600px;
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
                $images = is_string($service->sub_images)
                    ? json_decode($service->sub_images, true)
                    : $service->sub_images;
                $carouselId = 'mediaCarousel_' . ($service->id ?? uniqid());
            @endphp

            @if (!empty($images) && is_array($images))
                <div id="{{ $carouselId }}" class="carousel slide side-cards mb-4" data-bs-ride="carousel">
                    <!-- Indicators -->
                    <div class="carousel-indicators">
                        @foreach (array_values($images) as $index => $img)
                            <button type="button" data-bs-target="#{{ $carouselId }}"
                                data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"
                                aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}">
                            </button>
                        @endforeach

                        @if ($service->video)
                            <button type="button" data-bs-target="#{{ $carouselId }}"
                                data-bs-slide-to="{{ count($images) }}" aria-label="Video Slide">
                            </button>
                        @endif
                    </div>

                    <!-- Carousel Content -->
                    <div class="carousel-inner rounded-3 img-div">
                        @foreach (array_values($images) as $index => $img)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="https://onstru-social.s3.ap-south-1.amazonaws.com/{{ $img }}"
                                    class="d-block w-100 object-fit-cover object-top rounded-3" height="450"
                                    alt="Image {{ $index + 1 }}">
                            </div>
                        @endforeach

                        @if ($service->video)
                            <div class="carousel-item {{ count($images) === 0 ? 'active' : '' }}">
                                <video class="d-block w-100 rounded-3" height="450" controls loop>
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
                    class="w-100 object-fit-cover object-top rounded-3" height="450" alt="No Image">
                @if ($service->video)
                    <video class="w-100 rounded-3" height="450" controls loop>
                        <source src="https://onstru-social.s3.ap-south-1.amazonaws.com/{{ $service->video }}"
                            type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @endif
            @endif

            <!-- Service Details -->
            <div class="flex-cards">
                <!-- Service Information Card -->
                <div class="side-cards h-100 mx-auto mb-3 position-relative">
                    <div class="cards-head">
                        <h5 class="mb-2">{{ $service->title }}</h5>
                        @if ($service->highlighted == 1 && $service->created_by == auth()->id())
                            <a href="{{ route('view-service-highlight', $service->id) }}" class="badge"
                                data-bs-toggle="tooltip" data-bs-title="View Highlight">
                                <img src="{{ asset('assets/images/icon_highlights.png') }}" height="15px" class="pe-1"
                                    alt="">
                                Highlighted
                            </a>
                        @elseif ($service->highlighted == 1)
                            <a class="badge" data-bs-toggle="tooltip" data-bs-title="Highlighted Service">
                                <img src="{{ asset('assets/images/icon_highlights.png') }}" height="15px" class="pe-1"
                                    alt="">
                                Highlighted
                            </a>
                        @endif
                        <h6 class="mb-2 share-btn" style="cursor: pointer;" data-bs-toggle="modal"
                            data-bs-target="#sharePopup" data-share-title='{{ $service->title ?? 'No service' }}'
                            data-share-url='{{ env('BASE_URL') . 'individual-service/' . $service->id }}'
                            data-share-text='{{ 'service' }}' data-job-id="{{ $service->id }}"
                            data-share-type="service">
                            <i class="fas fa-share-nodes pe-1"></i>
                            Share
                        </h6>
                        <h6 class="mb-2">
                            <i class="fas fa-star text-warning pe-1"></i>
                            {{ number_format($service->reviews_avg_stars, 1) }}
                            ({{ $service->reviews_count }})
                        </h6>
                        <h6 class="mb-2"><i class="fas fa-location-dot pe-1"></i>
                            {{ $service->locationRelation->value ?? '-' }}</h6>
                        <h6 class="mb-2"><i class="fas fa-indian-rupee-sign pe-1"></i>
                            {{ number_format($service->price_per_sq_ft) ?? '-' }} per sqft.
                        </h6>
                        <h6 class="mb-2"><i class="fas fa-tools pe-1"></i> {{ $service->serviceType->value ?? '-' }}
                        </h6>
                        @if ($service->wallet)
                            <h6 class="mb-2"><i class="fas fa-wallet pe-1"></i> Budget:
                                ₹{{ number_format($service->wallet) }}
                            </h6>
                        @endif
                    </div>
                    <div class="cards-content">
                        <label class="mb-1">Description</label>
                        <h6 class="mb-0">
                            {{ $service->description }}
                        </h6>
                    </div>
                </div>

                <!-- Service Provider Details Card -->
                <div class="side-cards h-100 mx-auto">
                    <div class="cards-content">
                        <h5 class="mb-3">Service Provider Details</h5>
                        <h6 class="mb-3 d-flex align-items-center column-gap-2">
                            <i class="fas fa-location-dot pe-1"></i>
                            <span class="text-muted">{{ $service->creator->address ?? '-' }}</span>
                        </h6>
                        <h6 class="mb-3 d-flex align-items-center column-gap-2">
                            <i class="fas fa-phone pe-1"></i>
                            <span class="text-muted">+91 {{ $service->creator->number ?? '-' }}</span>
                        </h6>
                        {{-- <h6 class="mb-3 d-flex align-items-center column-gap-2">
                            <img src="{{ $service->creator->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $service->creator->profile_img : 'assets/images/Avatar.png' }}" class="avatar-30" alt="">
                            <span class="text-muted">{{ $service->creator->name ?? '-' }}</span>
                        </h6>
                        <h6 class="mb-3 d-flex align-items-center column-gap-2">
                            <i class="fas fa-envelope pe-1"></i>
                            <span class="text-muted">{{ $service->creator->email ?? '-' }}</span>
                        </h6>
                        <h6 class="mb-3 d-flex align-items-center column-gap-2">
                            <i class="fas fa-globe pe-1"></i>
                            <span class="text-muted">{{ $service->creator->website ?? '-' }}</span>
                        </h6> --}}
                    </div>
                    <div class="mt-4">
                        <a data-bs-toggle="modal" data-bs-target="#reqService">
                            <button class="editbtn w-100 mb-3">Request Service</button>
                        </a>
                        <a>
                            <button class="formbtn w-100 service_msg" style="font-size: 12px;"
                                data-user-id="{{ $service->created_by }}">Message</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews -->
        @include('services.reviews')

    </div>

    <!-- Request Service Modal -->
    <div class="modal fade" id="reqService" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="reqServiceLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0">Service Information</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('service.request.store') }}" id="service_info" method="POST">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <div class="row">
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="addServicetype">Service Type <span>*</span></label>
                                <input type="hidden" name="service_type" value="{{ $service->service_type }}">
                                <input type="text" class="form-control" name="" id="addServicetype"
                                    value="{{ $service->serviceType->value }}" readonly>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="addBuildup">Buildup Area (sqft) <span>*</span></label>
                                <input type="number" class="form-control" name="addBuildup" id="addBuildup"
                                    value="{{ old('addBuildup') }}" min="1" step="0.01" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="addBudget">Budget <span>*</span></label>
                                <input type="number" class="form-control" name="addBudget" id="addBudget"
                                    value="{{ old('addBudget') }}" min="1" step="0.01"
                                    placeholder="Enter your budget" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="addStartDate">Start Date <span>*</span></label>
                                <input type="date" class="form-control" name="addStartDate" id="addStartDate"
                                    value="{{ old('addStartDate') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                    required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="addLoc">Location <span>*</span></label>
                                <select class="form-select" name="addLoc" id="addLoc" required>
                                    <option value="" selected disabled>Select Location</option>
                                    @foreach ($locations as $id => $location)
                                        <option value="{{ $id }}"
                                            {{ old('addLoc') == $location ? 'selected' : '' }}>
                                            {{ $location }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="addContact">Contact Number <span>*</span></label>
                                <input type="tel" class="form-control" name="addContact" id="addContact"
                                    value="{{ old('addContact') }}" pattern="[6-9][0-9]{9}" maxlength="10"
                                    oninput="validate_contact(this)" required>
                            </div>
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label for="addDescp">Description <span>*</span></label>
                                <textarea rows="2" class="form-control" name="addDescp" id="addDescp" maxlength="1000" required>{{ old('addDescp') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-center align-items-center mt-3">
                                <button type="submit" class="formbtn">Submit Request</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('popups.popup');

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('service_info');
            const submitBtn = document.querySelector('.formbtn');
            let isSubmitting = false;

            form.addEventListener('submit', function(e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }
                isSubmitting = true;
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Submitting...`;
            });
        });
    </script>

    <!-- Select 2 -->
    <script>
        $(function() {
            function initSelect2(modal) {
                modal.find('#addLoc').each(function() {
                    let $select = $(this);
                    if ($select.hasClass('select2-hidden-accessible')) return;
                    $select.select2({
                        width: "100%",
                        placeholder: "Select Options",
                        allowClear: true,
                        dropdownParent: modal
                    });
                });
            }
            $('#reqService').on('shown.bs.modal', function() {
                initSelect2($(this));
            });
        });
    </script>

    <script>
        // $('#followersbtn').trigger('click');
        $('.service_msg').on('click', function() {

            let userId = $(this).data("user-id");
            loadChats(userId);

            // Open offcanvas programmatically
            var offcanvas = new bootstrap.Offcanvas('#chat');
            offcanvas.show();

        });
        // $('.service_msg').on('click', function() {

        //     let userId = $(this).data("user-id");




        //     // $('#followersbtn').on('shown.bs.offcanvas', function() {

        //     //     alert("hello");


        //     let userClicked = $('.service_msg').data('user-id') || null;
        //     alert(userClicked)


        //     let chatList = document.querySelector(".chat-list");
        //     let chatScreen = document.querySelector(".chat-screen");

        //     chatScreen.classList.add("d-none");
        //     chatList.classList.remove("d-none");

        //     loadChats(userId);

        //     // });

        // })
    </script>

    <script>
        window.appData = {
            shareUrl: "{{ route('toggle.getShareList') }}",
            csrf: "{{ csrf_token() }}"
        };
    </script>

    <script src="{{ asset('assets/js/share_job.js') }}"></script>

@endsection
