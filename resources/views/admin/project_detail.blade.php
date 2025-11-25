<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Onstru | Project Details</title>

    <!-- Stylesheets CDN -->
    @include('admin.cdn_style')

    <link rel="stylesheet" href="{{ asset('assets/css/admin/profile.css') }}">

</head>

<body>

    <div class="main">

        <!-- aside -->
        @include('admin.aside')

        <div class="body-main">

            <!-- Navbar -->
            @include('admin.navbar')

            <div class="main-div px-4 py-1">
                <div class="body-head">
                    <h4 class="m-0">Project Details</h4>
                </div>

                <div class="mt-3 profile-card">
                    <div class="cards mb-2">
                        <h6 class="mb-1">Title</h6>
                        <h5 class="mb-0">{{ $project->title ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Location</h6>
                        <h5 class="mb-0">{{ $project->locationRelation->value ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Start Date</h6>
                        <h5 class="mb-0">{{ $project->start_date->format('d-m-Y') ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">End Date</h6>
                        <h5 class="mb-0">{{ $project->end_date->format('d-m-Y') ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Budget</h6>
                        <h5 class="mb-0">{{ $project->prjt_budget ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Job Role</h6>
                        <h5 class="mb-0">{{ $project->job_role ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Responsibilities</h6>
                        <h5 class="mb-0">{{ $project->responsibilities ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Key Outcomes</h6>
                        <h5 class="mb-0">{{ $project->key_outcomes ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Description</h6>
                        <h5 class="mb-0">{{ $project->description ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Created By</h6>
                        <h5 class="mb-0">{{ $project->creator->name ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Listing Charge</h6>
                        <h5 class="mb-0">₹ {{ $project->amount ?? '-' }}</h5>
                    </div>
                    @if (!empty($project->decoded_images))
                        <div class="cards mb-2">
                            <h6 class="mb-1">Images</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach ((array) $project->decoded_images as $imagePath)
                                    <h5 class="mb-0 {{ $loop->iteration == 1 ? '' : 'd-none' }}" data-bs-toggle="tooltip" data-bs-title="View Image">
                                        <a href="{{ $imagePath ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $imagePath : 'assets/images/NoImage.png' }}"
                                            data-fancybox="project">
                                            <i class="fas fa-image"></i>
                                        </a>
                                    </h5>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('admin.toaster')

    <!-- script -->
    @include('admin.cdn_script')

</body>

</html>