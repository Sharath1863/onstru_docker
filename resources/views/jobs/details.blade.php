@extends('layouts.app')

@section('title', 'Onstru | Job Details')

@section('content')

    <style>
        .flex-sidebar {
            display: block !important;
        }

        @media screen and (min-width: 1024px) {
            .flex-side {
                grid-template-columns: 35% 64% !important;
            }
        }
    </style>

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">

    <div class="container main-div">
        <div class="body-head mb-3">
            <a href="javascript:history.back()">
                <h5><i class="fas fa-angle-left pe-1"></i> Back</h5>
            </a>
        </div>
        <div class="flex-side">
            <!-- Flex Left -->
            <div class="flex-sidebar border-0">
                <div class="flex-cards">
                    <div class="body-head mb-3">
                        <h5>Jobs You Might Be Interested In</h5>
                    </div>

                    @foreach ($recommends as $recommend)
                        <div class="side-cards mb-3">
                            <a href="{{ url('job-details/' . $recommend->id) }}">
                                <div class="cards-head">
                                    <h5 class="mb-2">{{ $recommend->title }}</h5>
                                    <h6 class="mb-2">
                                        {{ $job->user->gst->business_legal ?? $job->user->name }} | {{ $job->user->you_are ?? '-' }}
                                    </h6>
                                </div>
                            </a>
                            <div class="cards-content">
                                <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <h6 class="mb-0">{{ $recommend->locationRelation->value }}</h6>
                                    <h6 class="mb-0">{{ $recommend->created_at->diffForHumans() }}</h6>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Flex Right -->
            <div class="flex-cards pt-2">
                <div class="side-cards job-card mb-3 position-relative">
                    <div class="cards-head">
                        <a href="" class="text-decoration-none text-dark">
                            <h5 class="mb-2">{{ $job->title ?? 'Job Title' }}</h5>
                            <h6 class="mb-2">{{ $job->categoryRelation->value ?? '-' }}</h6>
                            @if ($job->highlighted == 1 && $job->created_by == auth()->id())
                                <a href="{{ route('view-job-highlight', $job->id) }}" class="badge" data-bs-toggle="tooltip"
                                    data-bs-title="View Boosted">
                                    <img src="{{ asset('assets/images/icon_fire.png') }}" height="15px" class="pe-1" alt="">
                                    Boosted
                                </a>
                            @elseif ($job->highlighted == 1)
                                <a class="badge " data-bs-toggle="tooltip" data-bs-title="Boosted Job">
                                    <img src="{{ asset('assets/images/icon_fire.png') }}" height="15px" class="pe-1" alt="">
                                    Boosted
                                </a>
                            @endif
                        </a>
                    </div>
                    <div class="cards-content">
                        <div class="d-flex align-items-center justify-content-start flex-wrap gap-5">
                            <h6 class="mb-3 d-flex align-items-center gap-2">
                                <i class="fas fa-briefcase"></i> Experience :
                                {{ $job->experience ?? '-' }}
                            </h6>
                            <h6 class="mb-3 d-flex align-items-center gap-2">
                                <i class="fas fa-indian-rupee-sign"></i>
                                {{ $job->salary ?? 'Not disclosed' }} / Month
                            </h6>
                            <h6 class="mb-3 d-flex align-items-center gap-2">
                                <i class="fas fa-location-dot"></i>
                                {{ $job->locationRelation->value ?? '-' }}
                            </h6>
                            <h6 class="mb-3 d-flex align-items-center gap-2">
                                <i class="fas fa-location"></i>
                                {{ $job->sublocality ?? '-' }}
                            </h6>
                        </div>
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex align-items-center justify-content-start gap-2">
                                <h6 class="mb-0">{{ optional($job->created_at)->diffForHumans() }}</h6>
                                <h6 class="mb-0">|</h6>
                                <h6 class="mb-0">Opening: {{ $job->no_of_openings ?? '-' }}</h6>
                            </div>
                            <h6 class="mb-0 share-btn" style="cursor: pointer;" data-bs-toggle="modal"
                                data-bs-target="#sharePopup" data-share-title='{{ $job->title }}'
                                data-share-url='{{ env('BASE_URL') . 'job-details/' . $job->id }}'
                                data-share-text='{{ $job->categoryRelation->value }}' data-job-id="{{ $job->id }}"
                                data-share-type="job">
                                <i class="fas fa-share-nodes pe-1"></i>
                                Share
                            </h6>
                        </div>
                    </div>
                </div>

                <div class="side-cards job-card mb-3">
                    <div class="cards-content row">
                        <div class="col-sm-12 col-md-4 mb-4">
                            <h5 class="mb-2">Title</h5>
                            <h6 class="mb-0">{{ $job->title ?? 'N/A' }}</h6>
                        </div>
                        <div class="col-sm-12 col-md-4 mb-4">
                            <h5 class="mb-2">Job Category</h5>
                            <h6 class="mb-0">{{ $job->categoryRelation->value ?? '-' }}</h6>
                        </div>
                        <div class="col-sm-12 col-md-4 mb-4">
                            <h5 class="mb-2">Job Location</h5>
                            <h6 class="mb-0">
                                {{ $job->locationRelation->value ?? '-' }}
                            </h6>
                        </div>
                        <div class="col-sm-12 col-md-4 mb-4">
                            <h5 class="mb-2">Job Type</h5>
                            <h6 class="mb-0">{{ $job->shift ?? '-' }}</h6>
                        </div>
                        <div class="col-sm-12 col-md-4 mb-4">
                            <h5 class="mb-2">Qualification</h5>
                            <h6 class="mb-0">
                                {{ $job->qualification ?? "-" }}
                            </h6>
                        </div>
                        <div class="col-sm-12 col-md-4 mb-4">
                            <h5 class="mb-2">Work Experience</h5>
                            <h6 class="mb-0">
                                {{ $job->experience ?? '-' }}
                            </h6>
                        </div>
                        <div class="col-sm-12 col-md-4 mb-4">
                            <h5 class="mb-2">Salary Range</h5>
                            <h6 class="mb-0">₹ {{ $job->salary ?? '-' }} / Month</h6>
                        </div>
                        <div class="col-sm-12 col-md-4 mb-4">
                            <h5 class="mb-2">Required Skills</h5>
                            <h6 class="mb-0">{{ $job->skills ?? '-' }}</h6>
                        </div>
                        <div class="col-sm-12 col-md-4 mb-4">
                            <h5 class="mb-2">Benefits/Perks</h5>
                            <h6 class="mb-0">{{ $job->benfit ?? '-' }}</h6>
                        </div>
                        <div class="col-sm-12 col-md-8 mb-4">
                            <h5 class="mb-2">Description</h5>
                            <h6 class="mb-0">{{ $job->description ?? '-' }}</h6>
                        </div>
                    </div>
                    @if (auth()->user()->you_are == 'Consumer' || auth()->user()->you_are == 'Professional')
                        @if ($job->created_by !== auth()->id() && !$isAppled && $job->approvalstatus == 'approved')
                            <div
                                class="col-sm-12 col-md-12 d-flex align-items-center justify-content-md-end justify-content-sm-start mt-4">
                                <a href="{{ route('job.apply', $job->id) }}">
                                    <button class="formbtn">Apply Job</button>
                                </a>
                            </div>
                        @elseif($job->created_by === auth()->id())
                            <div
                                class="col-sm-12 col-md-12 d-flex align-items-center justify-content-md-end justify-content-sm-start mt-4">
                                <a href="{{ route('job.applicants', $job->id) }}">
                                    <button class="formbtn">View Applicants</button>
                                </a>
                            </div>
                        @elseif($job->approvalstatus == 'pending' || $job->approvalstatus == 'rejected')
                            <div
                                class="col-sm-12 col-md-12 d-flex align-items-center justify-content-md-end justify-content-sm-start mt-4">
                                <button class="formbtn" disabled>Approval Pending</button>
                            </div>
                        @else
                            <div
                                class="col-sm-12 col-md-12 d-flex align-items-center justify-content-md-end justify-content-sm-start mt-4">
                                <button class="followingbtn">Applied</button>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('popups.popup');

    <!-- jQuery from CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Pass Laravel data to JS -->
    <script>
        window.appData = {
            shareUrl: "{{ route('toggle.getShareList') }}",
            csrf: "{{ csrf_token() }}"
        };
    </script>
    <script src="{{ asset('assets/js/share_job.js') }}"></script>

@endsection