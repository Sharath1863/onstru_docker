{{-- @dd($totalEarnings) --}}
<div class="flex-left mb-3">
    <div class="flex-cards">
        <div class="side-cards mb-2">
            <div class="cards-content">
                <!-- Profile Card -->
                @include('flexleft.profile-card')
                <hr class="w-75 mx-auto my-3">
                <!-- Tabs -->
                @include('flexleft.tabs')
            </div>
        </div>

        <!-- <div class="side-cards mb-2">
            <div class="body-head mb-3">
                <h5>Activities</h5>
            </div>
        </div> -->
        <!-- Badge Purchase Card -->
        @if ($totalEarnings >= 500000 && $totalEarnings < 1000000 && auth()->user()->badge != 10 && auth()->user()->badge != 15 && auth()->user()->badge != 5)
            <div class="side-cards">
                <div class="cards-content d-flex align-items-center justify-content-center flex-column">
                    <img src="{{ asset('assets/images/img_badge.png') }}" height="50px" class="mb-2" alt="">
                    <h5 class="mb-2 text-center">Congratulations</h5>
                    <h6 class="mb-2 text-center bio">You've crossed ₹5,00,000 in sales this month.</h6>
                    <button class="formbtn rounded-4" data-bs-toggle="modal" data-bs-target="#badgePlan">
                        View Badge Details
                    </button>
                </div>
            </div>
        @elseif ($totalEarnings >= 1000000 && $totalEarnings < 1500000 && auth()->user()->badge != 10 && auth()->user()->badge != 15)
            <div class="side-cards">
                <div class="cards-content d-flex align-items-center justify-content-center flex-column">
                    <img src="{{ asset('assets/images/img_badge.png') }}" height="50px" class="mb-2" alt="">
                    <h5 class="mb-2 text-center">Congratulations</h5>
                    <h6 class="mb-2 text-center bio">You've crossed ₹10,00,000 in sales this month.</h6>
                    <button class="formbtn rounded-4" data-bs-toggle="modal" data-bs-target="#badgePlan">
                        View Badge Details
                    </button>
                </div>
            </div>
        @elseif ($totalEarnings >= 1500000 && auth()->user()->badge != 15)
            <div class="side-cards">
                <div class="cards-content d-flex align-items-center justify-content-center flex-column">
                    <img src="{{ asset('assets/images/img_badge.png') }}" height="50px" class="mb-2" alt="">
                    <h5 class="mb-2 text-center">Congratulations</h5>
                    <h6 class="mb-2 text-center bio">You've crossed ₹15,00,000 in sales this month.</h6>
                    <button class="formbtn rounded-4" data-bs-toggle="modal" data-bs-target="#badgePlan">
                        View Badge Details
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Badge Plan Modal -->
<div class="modal modal-md fade" id="badgePlan" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            @php
                $wallet = auth()->user()->balance ?? 0;
                $badgePrice = 0;
                $eligibleBadge = null;

                if ($totalEarnings >= 500000 && $totalEarnings < 1000000 && auth()->user()->badge != 5) {
                    $badgePrice = $badge_5L;
                    $eligibleBadge = 5;
                } elseif ($totalEarnings >= 1000000 && $totalEarnings < 1500000 && auth()->user()->badge != 10) {
                    $badgePrice = $badge_10L;
                    $eligibleBadge = 10;
                } elseif ($totalEarnings >= 1500000 && auth()->user()->badge != 15) {
                    $badgePrice = $badge_15L;
                    $eligibleBadge = 15;
                }
            @endphp

            <form action="{{ route('buy.badge') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="m-0">Activate My Badge</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body mt-2">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        @if ($eligibleBadge)
                            <label class="my-2">Badge : <span class="text-muted"><i
                                        class="fas fa-indian-rupee-sign pe-1"></i>{{ $badgePrice }}</span></label>
                        @endif
                        <label class="my-2">Wallet : <span class="text-muted"><i
                                    class="fas fa-indian-rupee-sign pe-1"></i>{{ $wallet }}</span></label>
                    </div>

                    <!-- Eligible Badge UI  -->
                    <div class="d-flex align-items-center justify-content-center flex-column flex-wrap mb-3">
                        @if ($eligibleBadge == 5)
                            <div class="avatar-div-100 position-relative mb-2">
                                <img src="{{ asset(auth()->user()->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . auth()->user()->profile_img : 'assets/images/Avatar.png') }}"
                                    class="user-img avatar-100" alt="">
                                <img src="{{ asset('assets/images/Badge_5.png') }}" class="badge-100" alt="">
                            </div>
                            <a data-bs-toggle="modal" data-bs-target="#productBadges" class="mb-2">
                                <button class="iconbtn"><i class="fas fa-info-circle" data-bs-toggle="tooltip"
                                        data-bs-title="About"></i></button>
                            </a>
                            <h5 class="mb-2 text-center">Congratulations !</h5>
                            <h6 class="mb-2 text-center fst-italic">You've crossed ₹ 5,00,000 in sales this month. Activate
                                your <span class="fw-bold text-dark">Titan Seller</span> badge to start enjoying the
                                benefits.</h6>
                            <div class="d-flex align-items-center column-gap-2">
                                <input type="radio" name="badge" value="5" id="agree" required>
                                <label for="agree" class="mb-0">Amount will be deducted from the wallet
                                    <span>*</span></label>
                            </div>
                        @elseif($eligibleBadge == 10)
                            <div class="avatar-div-100 position-relative mb-2">
                                <img src="{{ asset(auth()->user()->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . auth()->user()->profile_img : 'assets/images/Avatar.png') }}"
                                    class="user-img avatar-100" alt="">
                                <img src="{{ asset('assets/images/Badge_10.png') }}" class="badge-100" alt="">
                            </div>
                            <a data-bs-toggle="modal" data-bs-target="#productBadges" class="mb-2">
                                <button class="iconbtn"><i class="fas fa-info-circle" data-bs-toggle="tooltip"
                                        data-bs-title="About"></i></button>
                            </a>
                            <h5 class="mb-2 text-center">Congratulations !</h5>
                            <h6 class="mb-2 text-center fst-italic">You've crossed ₹ 10,00,000 in sales this month. Activate
                                your <span class="fw-bold text-dark">Crown Seller</span> badge to start enjoying the
                                benefits.</h6>
                            <div class="d-flex align-items-center column-gap-2">
                                <input type="radio" name="badge" value="10" id="agree" required>
                                <label for="agree" class="mb-0">Amount will be deducted from the wallet
                                    <span>*</span></label>
                            </div>
                        @elseif($eligibleBadge == 15)
                            <div class="avatar-div-100 position-relative mb-2">
                                <img src="{{ asset(auth()->user()->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . auth()->user()->profile_img : 'assets/images/Avatar.png') }}"
                                    class="user-img avatar-100" alt="">
                                <img src="{{ asset('assets/images/Badge_15.png') }}" class="badge-100" alt="">
                            </div>
                            <a data-bs-toggle="modal" data-bs-target="#productBadges" class="mb-2">
                                <button class="iconbtn"><i class="fas fa-info-circle" data-bs-toggle="tooltip"
                                        data-bs-title="About"></i></button>
                            </a>
                            <h5 class="mb-2 text-center">Congratulations !</h5>
                            <h6 class="mb-2 text-center fst-italic">You've crossed ₹ 15,00,000 in sales this month. Activate
                                your <span class="fw-bold text-dark">Empire Seller</span> badge to start enjoying the
                                benefits.</h6>
                            <div class="d-flex align-items-center column-gap-2">
                                <input type="radio" name="badge" value="15" id="agree" required>
                                <label for="agree" class="mb-0">Amount will be deducted from the wallet
                                    <span>*</span></label>
                            </div>
                        @endif
                    </div>

                    <div class="col-sm-12 d-flex align-items-center justify-content-between gap-2 mt-3">
                        <a href="{{ url('wallet') }}" class="w-50">
                            <button type="button" class="w-100 removebtn">Recharge</button>
                        </a>
                        <button type="submit" class="w-50 listbtn" {{ $wallet < $badgePrice ? 'disabled' : '' }}>Buy
                            Now</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>