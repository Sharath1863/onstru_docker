@extends('layouts.app')

@section('title', 'Onstru | Notification')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">

    @php
        $my = auth()
            ->user()
            ->loadCount(['followers', 'following']);
    @endphp

    <style>
        .flex-sidebar {
            display: block !important;
        }

        hr {
            border-top: 1px solid var(--bg);
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

        /* .cards-content .notify-link:hover {
            background-color: var(--gray) !important;
        } */
    </style>

    <div class="container-xl main-div">
        <div class="flex-side">
            <!-- Flex Left -->
            <div class="flex-sidebar border-0">
                <div class="flex-cards">

                    <div class="side-cards mb-3">
                        <div class="cards-content">
                            <!-- Profile Card -->
                            @include('flexleft.profile-card')
                            <hr class="w-75 mx-auto my-3">
                            <!-- Tabs -->
                            @include('flexleft.tabs')
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flex Right -->
            <div class="flex-cards pt-2">
                <div class="body-head d-block mb-3">
                    <h5 class="mb-2">Notifications</h5>
                    <h6>Stay updated with your orders, offers, and account activities</h6>
                </div>

                <!-- Empty State -->
                <div class="side-cards shadow-none border-0"
                    style="{{ count($notifications) > 0 ? 'display: none;' : 'display: block;' }}">
                    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                        <img src="{{ asset('assets/images/Empty/NoNotify.png') }}" height="200px"
                            class="d-flex mx-auto mb-2" alt="">
                        <h5 class="text-center mb-0">No Notifications Available</h5>
                        <h6 class="text-center bio">You have no new notifications right now - stay tuned for updates,
                            alerts,
                            and important information here.</h6>
                    </div>
                </div>

                <!-- Notification Card -->
                @if (count($notifications) > 0)
                    <div class="side-cards" style="height: 75vh; overflow-y: auto;">
                        <div class="cards-content">
                            @foreach ($notifications as $notification)
                                @php
                                    $category = $notification->category;
                                @endphp
                                @if ($category == 'user')
                                    <a href="{{ url('user-profile/' . $notification->category_id) }}">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap notify-link">
                                            <div class="d-flex align-items-center justify-content-start column-gap-2 flex-wrap">
                                                <div class="avatar-div-40 position-relative">
                                                    <img src="{{ asset($notification->sender->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $notification->sender->profile_img : 'assets/images/Avatar.png') }}"
                                                        class="avatar-40" alt="">
                                                    @if ($notification->sender->badge != 0 && $notification->sender->badge != null)
                                                        <img src="{{ asset('assets/images/Badge_' . $notification->sender->badge . '.png' ?? 'assets/images/Badge_0.png') }}"
                                                            class="badge-40" alt="">
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">
                                                        <span
                                                            class="fw-bold text-lowercase">{{ $notification->sender->user_name }}</span>
                                                        <span class="ps-1 text-muted">{{ $notification->body }}</span>
                                                    </h6>
                                                    <h6 class="mb-0 text-muted" style="font-size: 10px;">
                                                        {{ $notification->created_at->diffForHumans() }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    </a>
                                @elseif ($category == 'post' || $category == 'comment')
                                    <a href="{{ url('user-profile/' . $notification->reciever . '/' . $notification->category_id) }}">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap notify-link">
                                            <div class="d-flex align-items-center justify-content-start column-gap-2 flex-wrap">
                                                <div class="avatar-div-40 position-relative">
                                                    <img src="{{ asset($notification->sender->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $notification->sender->profile_img : 'assets/images/Avatar.png') }}"
                                                        class="avatar-40" alt="">
                                                    @if ($notification->sender->badge != 0 && $notification->sender->badge != null)
                                                        <img src="{{ asset('assets/images/Badge_' . $notification->sender->badge . '.png' ?? 'assets/images/Badge_0.png') }}"
                                                            class="badge-40" alt="">
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">
                                                        <span class="fw-bold text-lowercase">{{ $notification->sender->user_name }}</span>
                                                        <span class="ps-1 text-muted">{{ $notification->body }}</span>
                                                    </h6>
                                                    <h6 class="mb-0 text-muted" style="font-size: 10px;">{{ $notification->created_at->diffForHumans() }}</h6>
                                                </div>
                                            </div>
                                            @if ($notification->post->file_type == 'image')
                                                <img src="{{ asset($notification->post->file[0] ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $notification->post->file[0] : 'assets/images/NoImage.png') }}"
                                                    height="40px" width="40px" class="rounded-1 object-fit-cover" alt="">
                                            @elseif ($notification->post->file_type == 'video')
                                                <video height="40px" width="40px" class="rounded-1 object-fit-cover">
                                                    <source src="{{ asset($notification->post->file[0] ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $notification->post->file[0] : 'assets/images/NoImage.png') }}" type="video/mp4">
                                                </video>
                                            @endif
                                        </div>
                                        <hr>
                                    </a>
                                @elseif ($category == 'job')
                                    @if ($notification->title == 'Job Highlight Expired')
                                        <a href="{{ url('view-job-highlight/' . $notification->category_id) }}">
                                    @else
                                        <a href="{{ url('applied-profiles/' . $notification->category_id) }}">
                                    @endif
                                        <div class="d-flex align-items-center justify-content-between flex-wrap notify-link">
                                            <div class="d-flex align-items-center justify-content-start column-gap-2 flex-wrap">
                                                <img src="{{ asset('assets/images/img_job.png') }}" class="avatar-40" alt="">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <span class="fw-bold">{{ $notification->title }} </span>
                                                        <span class="ps-1 text-muted">{{ $notification->body }}</span>
                                                    </h6>
                                                    <h6 class="mb-0 text-muted" style="font-size: 10px;">
                                                        {{ $notification->created_at->diffForHumans() }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    </a>
                                @elseif ($category == 'product')
                                    @if ($notification->title == 'Product Highlight Expired')
                                        <a href="{{ url('view-product-highlight/' . $notification->category_id) }}">
                                    @else
                                        <a href="{{ url('individual-product/' . $notification->category_id) }}">
                                    @endif
                                        <div class="d-flex align-items-center justify-content-between flex-wrap notify-link">
                                            <div class="d-flex align-items-center justify-content-start column-gap-2 flex-wrap">
                                                <img src="{{ asset('assets/images/img_product.png') }}" class="avatar-40" alt="">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <span class="fw-bold">{{ $notification->title }} </span>
                                                        <span class="ps-1 text-muted">{{ $notification->body }}</span>
                                                    </h6>
                                                    <h6 class="mb-0 text-muted" style="font-size: 10px;">
                                                        {{ $notification->created_at->diffForHumans() }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    </a>
                                @elseif ($category == 'service')
                                    @if ($notification->title == 'Service Highlight Expired')
                                        <a href="{{ url('view-service-highlight/' . $notification->category_id) }}">
                                    @else
                                        <a href="{{ url('individual-service/' . $notification->category_id) }}">
                                    @endif
                                        <div class="d-flex align-items-center justify-content-between flex-wrap notify-link">
                                            <div class="d-flex align-items-center justify-content-start column-gap-2 flex-wrap">
                                                <img src="{{ asset('assets/images/img_service.png') }}" class="avatar-40" alt="">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <span class="fw-bold">{{ $notification->title }} </span>
                                                        <span class="ps-1 text-muted">{{ $notification->body }}</span>
                                                    </h6>
                                                    <h6 class="mb-0 text-muted" style="font-size: 10px;">
                                                        {{ $notification->created_at->diffForHumans() }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    </a>
                                @elseif ($category == 'lead')
                                    <a href="{{ url('profile') }}">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap notify-link">
                                            <div class="d-flex align-items-center justify-content-start column-gap-2 flex-wrap">
                                                <img src="{{ asset('assets/images/img_lead.png') }}" class="avatar-40" alt="">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <span class="fw-bold">{{ $notification->title }} </span>
                                                        <span class="ps-1 text-muted">{{ $notification->body }}</span>
                                                    </h6>
                                                    <h6 class="mb-0 text-muted" style="font-size: 10px;">
                                                        {{ $notification->created_at->diffForHumans() }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    </a>
                                @elseif ($category == 'order')
                                    @if ($notification->order?->user_id != auth()->user()->id)
                                        <a href="{{ url('order-accept/' . ($notification->order?->order_id ?? 1)) }}">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap notify-link">
                                                <div class="d-flex align-items-center justify-content-start column-gap-2 flex-wrap">
                                                    <img src="{{ asset('assets/images/img_order.png') }}" class="avatar-40" alt="">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <span class="fw-bold">{{ $notification->title }} </span>
                                                            <span class="ps-1 text-muted">{{ $notification->body }}</span>
                                                        </h6>
                                                        <h6 class="mb-0 text-muted" style="font-size: 10px;">
                                                            {{ $notification->created_at->diffForHumans() }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        </a>
                                    @else
                                        <a href="{{ url('tracking/' . ($notification->order?->order_id ?? 1)) }}">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap notify-link">
                                                <div class="d-flex align-items-center justify-content-start column-gap-2 flex-wrap">
                                                    <img src="{{ asset('assets/images/img_order.png') }}" class="avatar-40" alt="">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <span class="fw-bold">{{ $notification->title }} </span>
                                                            <span class="ps-1 text-muted">{{ $notification->body }}</span>
                                                        </h6>
                                                        <h6 class="mb-0 text-muted" style="font-size: 10px;">
                                                            {{ $notification->created_at->diffForHumans() }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        </a>
                                    @endif
                                @elseif ($category == 'ready')
                                    <a href="{{ url('profile/' . $notification->reciver) }}">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap notify-link">
                                            <div class="d-flex align-items-center justify-content-start column-gap-2 flex-wrap">
                                                <img src="{{ asset('assets/images/img_ready.png') }}" class="avatar-40" alt="">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <span class="fw-bold">{{ $notification->title }} </span>
                                                        <span class="ps-1 text-muted">{{ $notification->body }}</span>
                                                    </h6>
                                                    <h6 class="mb-0 text-muted" style="font-size: 10px;">
                                                        {{ $notification->created_at->diffForHumans() }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

@endsection