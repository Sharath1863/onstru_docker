<div class="flex-right">
    <div class="flex-cards">
        <div class="side-cards mb-2">
            <div class="body-head mb-3">
                <h5>Quick Access</h5>
            </div>
            <div
                class="cards-content d-flex align-items-start justify-content-between row-gap-3 column-gap-2 flex-wrap">
                @if (auth()->user()->you_are == 'Consumer' || auth()->user()->you_are == 'Professional')
                    <div class="quick-div">
                        <a href="{{ url('applied-jobs') }}"
                            class="d-flex align-items-center justify-content-center flex-column gap-2">
                            <img src="{{ asset('assets/images/icon_briefcase.png') }}" height="20px" alt="">
                            <label class="mb-0 text-center">Applied Jobs</label>
                        </a>
                    </div>
                @endif
                @if (auth()->user()->you_are == 'Business')
                    <div class="quick-div">
                        <a href="{{ url('services-request') }}"
                            class="d-flex align-items-center justify-content-center flex-column gap-2">
                            <img src="{{ asset('assets/images/icon_bill.png') }}" height="20px" alt="">
                            <label class="mb-0 text-center">My Services</label>
                        </a>
                    </div>
                    <div class="quick-div">
                        <a href="{{ url('orders-status') }}"
                            class="d-flex align-items-center justify-content-center flex-column gap-2">
                            <img src="{{ asset('assets/images/icon_orders.png') }}" height="20px" alt="">
                            <label class="mb-0 text-center">My Orders</label>
                        </a>
                    </div>
                @endif
                <div class="quick-div">
                    <a href="{{ url('owned-leads') }}"
                        class="d-flex align-items-center justify-content-center flex-column gap-2">
                        <img src="{{ asset('assets/images/icon_send.png') }}" height="20px" alt="">
                        <label class="mb-0 text-center">Owned Leads</label>
                    </a>
                </div>
                <div class="quick-div">
                    <a href="{{ url('cart') }}"
                        class="d-flex align-items-center justify-content-center flex-column gap-2">
                        <img src="{{ asset('assets/images/icon_cart.png') }}" height="20px" alt="">
                        <label class="mb-0 text-center">My Cart</label>
                    </a>
                </div>
            </div>
        </div>

        <div class="side-cards mb-2">
            <div class="body-head mb-3 position-sticky top-0 bg-white pb-2">
                <h5>Suggested For You</h5>
            </div>
            <div class="suggested">
                @foreach ($suggest_users as $user)
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                        <a href="{{ url('user-profile', ['id' => $user->id]) }}">
                            <div class="d-flex align-items-center justify-content-start gap-2">
                                <div class="avatar-div-40 position-relative">
                                    <img src="{{ asset($user['profile_img'] ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $user['profile_img'] : 'assets/images/Avatar.png') }}"
                                        class="avatar-40" alt="">
                                    @if ($user->badge != 0 && $user->badge != null)
                                        <img src="{{ asset('assets/images/Badge_' . $user->badge . '.png') }}" class="badge-40"
                                            alt="">
                                    @endif
                                </div>
                                <div class="cards-content">
                                    <h6 class="bio mb-1">{{ $user->name }}</h6>
                                    <h5 class="mb-0">
                                        <span class="label">{{ $user->as_a ?? 'Consumer' }}</span>
                                    </h5>
                                </div>
                            </div>
                        </a>
                        @php
                            $isFollowing = auth()->check() ? auth()->user()->isFollowing($user) : false;
                        @endphp
                        @if($isFollowing)
                            <button class="followingbtn follow-btn" data-user-id="{{ $user->id }}" data-following="1">
                                <span class="label">Following</span>
                            </button>
                        @else
                            @if (auth()->id() != $user->id)
                                <button class="followersbtn follow-btn" data-user-id="{{ $user->id }}" data-following="0">
                                    <span class="label">Follow</span>
                                </button>
                            @endif
                        @endif
                        {{-- <button class="listtdbtn">Follow</button> --}}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>