@extends('layouts.app')

@section('title', 'Onstru | Jobs')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">

    <div class="container-xl main-div">
        <div class="flex-side">
            <!-- Filter Sidebar -->
            @include('jobs.aside')
            <!-- Cards -->
            <div class="flex-cards pt-2" id="job-container">
                <div class="body-head mb-3">
                    <h5>Applied Jobs!</h5>
                    <button class="border-0 m-0 p-0 filter-responsive" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvas-filter" aria-controls="offcanvas-filter">
                        <i class="fas fa-filter fs-4"></i>
                    </button>
                </div>

                <!-- Empty State -->
                <div class="side-cards shadow-none border-0" id="noCard"
                    style="{{ count($jobs) > 0 ? 'display: none;' : 'display: block;'}}">
                    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                        <img src="{{ asset('assets/images/Empty/NoJobsApply.png') }}" height="200px"
                            class="d-flex mx-auto mb-2" alt="">
                        <h5 class="text-center mb-0">No Jobs Application Found</h5>
                        <h6 class="text-center bio">No jobs match your search right now - try refining your criteria or
                            check back for new listings soon.</h6>
                    </div>
                </div>

                <!-- Job Cards -->
                <div class="flex-cards">
                    @foreach ($jobs as $job)
                        <div class="side-cards job-card mb-2 position-relative"
                            data-category="{{ $job->job->categoryRelation->value }}" data-type="{{ $job->job->shift }}"
                            data-exp="{{ $job->job->experience }}" data-salary="{{ $job->job->salary }}"
                            data-location="{{ $job->job->location }}" data-sublocation="{{ $job->job->sublocality }}"
                            data-highlight="{{ $job->job->highlighted }}">
                            <div class="cards-head">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-2">{{ $job->job->title ?? 'Job Title' }}</h5>
                                </div>
                                <h6 class="mb-2">
                                    {{ $job->job->user->gst->business_legal ?? '-' }} |
                                    {{ $job->user->you_are ?? '-' }}
                                </h6>
                                @if ($job->job->highlighted == '1')
                                    <a class="badge d-flex align-items-center">
                                        <img src="{{ asset('assets/images/icon_fire.png') }}" height="15px" class="pe-1" alt="">
                                        <span>Boosted</span>
                                    </a>
                                @endif
                            </div>
                            <div class="cards-content">
                                <div class="d-flex align-items-center justify-content-start flex-wrap column-gap-4">
                                    <h6 class="my-2 d-flex align-items-center gap-2">
                                        <i class="fas fa-clipboard-list"></i>
                                        {{ $job->job->categoryRelation->value ?? '-' }}
                                    </h6>
                                    <h6 class="my-2 d-flex align-items-center gap-2">
                                        <i class="fas fa-briefcase"></i> Exp :
                                        {{ $job->job->experience == 0 ? 'Fresher' : ($job->job->experience ? $job->job->experience . '+ Years' : '-') }}
                                    </h6>
                                    <h6 class="my-2 d-flex align-items-center gap-2">
                                        <i class="fas fa-indian-rupee-sign"></i>
                                        {{ $job->job->salary ?? 'Not disclosed' }}
                                    </h6>
                                    <h6 class="my-2 d-flex align-items-center gap-2">
                                        <i class="fas fa-location-dot"></i>
                                        {{ $job->job->locationRelation->value ?? '-' }}
                                    </h6>
                                    <h6 class="my-2 d-flex align-items-center gap-2">
                                        <i class="fas fa-location"></i>
                                        {{ $job->job->sublocality ?? '-' }}
                                    </h6>
                                </div>
                                <h6 class="my-2"><span class="text-muted">Required Skills :</span>
                                    {{ $job->job->skills ?? 'N/A' }}</h6>
                                <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <h6 class="bio mb-0">{{ $job->created_at->diffForHumans() }}</h6>
                                    <div class="d-flex align-items-center flex-wrap column-gap-3">
                                        <h6 class="mb-0" style="cursor: pointer;" data-bs-toggle="modal"
                                            data-bs-target="#sharePopup" data-share-title='{{ $job->title }}'
                                            data-share-url='{{ env('BASE_URL') . 'job-details/' . $job->id }}'
                                            data-share-text='{{ $job->job->categoryRelation->value }}'>
                                            <i class="fas fa-share-nodes pe-1" data-bs-toggle="tooltip"
                                                data-bs-title="Share"></i>
                                        </h6>
                                        <a href="{{ url('job-details/' . $job->job->id) }}">
                                            <button class="followingbtn">View Job</button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @include('popups.popup')

    <script>
        $(document).ready(function () {
            $(document).on('click', '.save-job', function (e) {
                e.preventDefault();
                let jobId = $(this).data('id');
                let btn = $(this);
                $.ajax({
                    url: "{{ route('jobs.toggleSave', ['id' => '__jobId__']) }}".replace('__jobId__', jobId),
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.saved) {
                            btn.find("i").removeClass("far").addClass("fas text-warning");
                            showToast('Job Saved!');
                        } else {
                            btn.find("i").removeClass("fas text-warning").addClass("far");
                            showToast('Job Unsaved!');
                        }
                    },
                    error: function () {
                        showToast("Something went wrong!");
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $(document).on('click', '.save-job', function (e) {
                e.preventDefault();
                let jobId = $(this).data('id');
                let btn = $(this);
                $.ajax({
                    url: "/jobs/" + jobId + "/toggle-save",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            btn.find("i").removeClass("far").addClass("fas text-warning");
                            showToast('Job Saved!');
                        } else {
                            btn.find("i").removeClass("fas text-warning").addClass("far");
                            showToast('Job Unsaved!');
                        }
                    },
                    error: function () {
                        showToast("Something went wrong!");
                    }
                });
            });

            let nextUrl = "/job_location";
            let loading = false;
            // let debounceTimer; // for keyword input

            // Collect all filters into params
            function getFilters() {
                return {
                    loc: $(".location-filter:checked").map((_, el) => $(el).val()).get(),
                };
            }

            // Load products
            function loadProducts(url, reset = false) {
                // $('#loader').show();
                // if (!url || loading) return;
                // loading = true;

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        ...getFilters(),
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    beforeSend: function () { },
                    success: function (res) {
                        let html = "";
                        res.data.forEach(sub_loc => {
                            html += `
                                <li>
                                    <input type="checkbox" class="filter-checkbox sub-location-filter" id="${sub_loc.sublocality}"
                                        value="${sub_loc.sublocality}">
                                    <label for="${sub_loc.sublocality}">${sub_loc.sublocality}</label>
                                </li>`;
                        });

                        if (res.data) {
                            $("#sub_loc").html(html);
                        }
                        nextUrl = res.next_page_url;
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                    },
                    complete: function () { }
                });
            }
            $(document).on('change', '.location-filter', function () {
                nextUrl = "/job_location"; // reset cursor
                loadProducts(nextUrl, true);
            });
        });
    </script>

@endsection