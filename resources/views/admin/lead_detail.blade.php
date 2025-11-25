<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Onstru | Lead Details</title>

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
                    <h4 class="m-0">Lead Details</h4>
                </div>

                <div class="mt-3 profile-card">
                    <div class="cards mb-2">
                        <h6 class="mb-1">Title</h6>
                        <h5 class="mb-0">{{ $lead->title ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Service Type</h6>
                        <h5 class="mb-0">{{ $lead->serviceRelation->value ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Buildup Area</h6>
                        <h5 class="mb-0">{{ $lead->buildup_area ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Budget</h6>
                        <h5 class="mb-0">{{ $lead->budget ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Start Date</h6>
                        <h5 class="mb-0">{{ $lead->start_date ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Location</h6>
                        <h5 class="mb-0">{{ $lead->locationRelation->value ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Description</h6>
                        <h5 class="mb-0">{{ $lead->description ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Created By</h6>
                        <h5 class="mb-0">{{ $lead->user->name ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Repost Count</h6>
                        <h5 class="mb-0">{{ $lead->repost ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Admin Approval</h6>
                        @if ($lead->approval_status == 'pending')
                            <h5 class="mb-0 text-warning text-capitalize">{{ $lead->approval_status }}</h5>
                        @elseif ($lead->approval_status == 'approved')
                            <h5 class="mb-0 text-success text-capitalize">{{ $lead->approval_status }}</h5>
                        @elseif ($lead->approval_status == 'rejected')
                            <h5 class="mb-0 text-danger text-capitalize">{{ $lead->approval_status }}</h5>
                        @endif
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Adimn Charges</h6>
                        <h5 class="mb-0">₹ {{ $lead->admin_charge * 1.18 ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Admin Remarks</h6>
                        <h5 class="mb-0">{{ $lead->remark ?? '-' }}</h5>
                    </div>
                </div>

                @if (count($ownedUsers) > 0)
                <div class="body-head my-3">
                    <h4>Owned User</h4>
                </div>
                <div class="m-0">
                    <ul class="list-unstyled m-0 profile-card cards">
                        @foreach ($ownedUsers as $user)
                            <li class="">
                                <div class="d-flex align-items-center column-gap-2 mb-2">
                                    <img src="{{ asset(optional($user->user)->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $user->user->profile_img : 'assets/images/Avatar.png') }}"
                                        height="40px" width="40px" class="avatar" alt="">
                                    <div>
                                        <h5 class="mb-1">{{ $user->user->name ?? '-' }}</h5>
                                        <h6 class="mb-0">{{ $user->created_at->format('d M, Y h:i A') ?? '-' }}</h6>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="body-head my-3">
                    <h4>Review List</h4>
                </div>
                @if (count($lead->reviews) > 0)
                    <div class="m-0">
                        <ul class="list-unstyled m-0 profile-card cards">
                            @foreach ($lead->reviews as $review)
                                <li class="">
                                    <div>
                                        <h6 class="mb-2">
                                            <i class="fas fa-star text-warning"></i>
                                            {{ number_format($review->stars) }}
                                        </h6>
                                        <h6 class="mb-2">{{ $review->review ?? '-' }}</h6>
                                        <div class="d-flex align-items-center column-gap-2 mb-2">
                                            <img src="{{ asset(optional($review->user)->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $review->user->profile_img : 'assets/images/Avatar.png') }}"
                                                height="30px" width="30px" class="avatar" alt="">
                                            <div>
                                                <h5 class="mb-1">{{ $review->user->name ?? '-' }}</h5>
                                                <h6 class="mb-0">{{ $review->created_at->format('d M, Y h:i A') ?? '-' }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="side-cards shadow-none border-0">
                        <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                            <img src="{{ asset('assets/images/Empty/NoReviews.png') }}" height="150px"
                                class="d-flex mx-auto mb-2" alt="">
                            <h5 class="text-center">No Reviews Found</h5>
                            <h6 class="text-center">No reviews are available yet - be the first to share your
                                experience and help others decide.</h6>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    @include('admin.toaster')

    <!-- script -->
    @include('admin.cdn_script')

</body>

</html>