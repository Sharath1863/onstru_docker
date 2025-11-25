@extends('layouts.app')

@section('title', 'Onstru | Profile')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">
    <!-- Post Tab CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/post.css') }}">

    <!-- Tribute -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.css" />
    <script src="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.min.js"></script>

    <style>
        @media screen and (min-width: 767px) {

            #projects,
            #products,
            #leads,
            #jobs,
            #service {
                .inpleftflex {
                    width: 50% !important;
                }
            }
        }

        .flex-sidebar {
            display: block !important;
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

        @media screen and (max-width: 767px) {
            .profile-head {
                display: grid !important;
                grid-template-columns: 30% 69%;
                align-items: center;
                justify-content: space-between;
            }

            .side-cards .cards-content h5.label {
                width: 100%;
            }

            .profile-content h6 {
                font-size: 14px !important;
            }
        }

        .tab-content .side-cards:hover {
            transform: translate(0px, -5px);
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }
    </style>

    <div class="container-xl main-div">
        <div class="flex-side">
            <!-- Flex Left -->
            <div class="flex-sidebar border-0">
                <div class="flex-cards">

                    <div class="side-cards mb-3">
                        <div class="cards-content">
                            <div class="profile-head">
                                <div class="cards-image">
                                    <div class="avatar-div-100 position-relative mb-2">
                                        <img src="{{ asset($user['profile_img'] ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $user['profile_img'] : 'assets/images/Avatar.png') }}"
                                            class="user-img avatar-100" alt="">
                                        @if ($user->badge != 0 && $user->badge != null)
                                            <img src="{{ asset('assets/images/Badge_' . $user->badge . '.png') }}"
                                                class="badge-100" alt="">
                                        @endif
                                    </div>
                                    <h5 class="text-center mb-2">
                                        <span>{{ $user->name ?? '-' }}</span>
                                        @if ($user->as_a === null)
                                            <span class="label">{{ $user->you_are ?? '-' }}</span>
                                        @else
                                            <span class="label">{{ $user->as_a ?? '-' }}</span>
                                        @endif
                                    </h5>
                                    <h6 class="text-center mb-2 text-muted text-lowercase">
                                        <em>{{ $user->user_name ?? '-' }}</em>
                                    </h6>
                                    <h6 class="text-center mb-2 bio">{{ $user->bio ?? '-' }}</h6>
                                    <!-- <h6 class="text-center mb-2 bio">Services Offered</h6> -->

                                    <div class="verification-badges">
                                        @if ($user->you_are == 'Business')
                                            @if ($gstverified == 'yes')
                                                <img src="{{ asset('assets/images/GST_Verify.png') }}" height="20px"
                                                    data-bs-toggle="tooltip" data-bs-title="GST Verified" alt="">
                                            @endif
                                        @endif
                                        <div class="dropdown">
                                            <a data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis text-dark"></i>
                                            </a>
                                            @php
                                                $id = $user->id ?? null;
                                                $shareUrl = url('user-profile/' . $id);
                                                $shareText = 'Profile Shared' ?? 'Check this out!';
                                            @endphp
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item share-btn" data-bs-toggle="modal"
                                                        data-share-url="{{ $shareUrl }}"
                                                        data-share-text="{{ $shareText }}" data-bs-target="#sharePopup"
                                                        data-post-id="{{ $user->id }}"
                                                        data-share-type="{{ 'profile' }}">
                                                        <i class="fas fa-share-nodes pe-1"></i>
                                                        Share
                                                    </a>
                                                </li>
                                                <li>
                                                    @if ($reports == 0)
                                                        {{-- @php

                                                            Log::info($reports);
                                                        @endphp --}}
                                                        <a class="dropdown-item" data-bs-toggle="modal"
                                                            data-bs-target="#userReport"
                                                            data-user-id="{{ $user->id }}">
                                                            <i class="fas fa-triangle-exclamation text-danger pe-1"></i>
                                                            Report
                                                        </a>
                                                    @else
                                                        <a class="dropdown-item" data-bs-toggle="" data-bs-target="">
                                                            <i class="fas fa-circle text-success pe-1"></i>
                                                            Reported
                                                        </a>
                                                    @endif
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="profile-content">
                                    <div class="d-flex align-items-center justify-content-evenly mt-md-3">
                                        <div style="cursor: pointer" data-bs-toggle="tab" data-bs-target="#posts">
                                            <h6 class="text-center fw-bold mb-0">{{ $post_count }}</h6>
                                            <label class="text-center my-0">Posts</label>
                                        </div>
                                        <div style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#followers">
                                            <h6 class="text-center fw-bold mb-0">
                                                <span
                                                    class="followers-count-of-target">{{ $user->followers_count ?? '0' }}</span>
                                            </h6>
                                            <label class="text-center my-0">Followers</label>
                                        </div>

                                        <div style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#following">
                                            <h6 class="text-center fw-bold mb-0">
                                                <span
                                                    class="following-count-of-target">{{ $user->following_count ?? '0' }}</span>
                                            </h6>
                                            <label class="text-center my-0">Following</label>
                                        </div>

                                    </div>

                                    <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap mt-3">
                                        @php
                                            $isFollowing = auth()->check() ? auth()->user()->isFollowing($user) : false;
                                        @endphp
                                        @if ($isFollowing)
                                            <button class="followingbtn follow-btn" data-user-id="{{ $user->id }}"
                                                data-following="1">
                                                <span class="label">Following</span>
                                            </button>
                                        @else
                                            @if (auth()->id() != $user->id)
                                                <button class="followersbtn follow-btn" data-user-id="{{ $user->id }}"
                                                    data-following="0">
                                                    <span class="label">Follow</span>
                                                </button>
                                            @endif
                                        @endif
                                        <button class="removebtn followersbtn" data-bs-toggle="offcanvas" id="chat_ind"
                                            data-user-id={{ $user->id }} data-bs-target="#chat"
                                            data-user-name="{{ $user->user_name }}"
                                            data-user-img="{{ $user['profile_img'] }}">Message</button>
                                    </div>
                                </div>
                            </div>

                            <hr class="w-75 mx-auto my-3">

                            <div class="profile-tabs">
                                <ul class="nav nav-tabs d-flex justify-content-sm-between justify-content-md-center align-items-start flex-sm-row flex-md-column border-0"
                                    id="myTab" role="tablist">
                                    @include('users.tabs')
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flex Right -->
            <div class="flex-cards pt-2">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="posts" role="tabpanel">
                        @include('users.posts')
                    </div>
                    <div class="tab-pane fade" id="projects" role="tabpanel">
                        @include('users.projects')
                    </div>
                    <div class="tab-pane fade" id="products" role="tabpanel">
                        @include('users.products')
                    </div>
                    <div class="tab-pane fade" id="jobs" role="tabpanel">
                        @include('users.jobs')
                    </div>
                    <div class="tab-pane fade" id="service" role="tabpanel">
                        @include('users.services')
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('popups.follow')

    @include('popups.popup')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            var user_name = $('#chat_ind').data('user-name');
            var user_img = $('#chat_ind').data('user-img');
            var user_id = $('#chat_ind').data('user-id');
            $('#chatName').text(user_name);
            $('#chatUsername').text(user_id);
            $('#chatUserImage').attr('src', 'https://onstru-social.s3.ap-south-1.amazonaws.com/' + user_img);
        });
    </script>

    <script>
        $(document).ready(function() {
            var openPostId = @json($open_post);
            if (openPostId) {
                var $postDiv = $('.post_' + openPostId);
                if ($postDiv.length) {
                    $postDiv.trigger('click');
                }
            }
        });
    </script>

@endsection
