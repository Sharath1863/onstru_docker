@php
    ($notificationCount = getNotify())
@endphp

<a class="nav-link" data-bs-toggle="dropdown" aria-expanded="false">
    <div class="avatar-40 position-relative">
        <img src="{{ asset(auth()->user()->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . auth()->user()->profile_img : 'assets/images/Avatar.png') }}"
            class="avatar-40 mb-0" alt="">
        @if ($notificationCount > 0 ?? '')
            <h6 class="m-0 notification-badge">
                @if ($notificationCount > 9 ?? '')
                    9+
                @else
                    {{ $notificationCount ?? ''}}
                @endif
            </h6>
        @endif
        @if (auth()->user()->badge != 0 && auth()->user()->badge != null)
            <img src="{{ asset('assets/images/Badge_' . auth()->user()->badge . '.png') }}" class="badge-40" alt="">
        @endif
    </div>
</a>
<div class="dropdown-nav dropdown-menu" data-bs-auto-close="outside">
    <div class="dropdown-div">
        <div class="dropdown-img">
            <div class="avatar-40 position-relative">
                <img src="{{ asset(auth()->user()->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . auth()->user()->profile_img : 'assets/images/Avatar.png') }}"
                    class="avatar-40 mb-0" alt="">
                @if (auth()->user()->badge != 0 && auth()->user()->badge != null)
                    <img src="{{ asset('assets/images/Badge_' . auth()->user()->badge . '.png') }}" class="badge-40" alt="">
                @endif
            </div>
            <div>
                <h6 class="mb-1 text-lowercase">{{ auth()->user()->user_name ?? '-' }}</h6>
                @if(auth()->user()->as_a === null)
                    <h5 class="yellow-label text-center mb-0">{{ auth()->user()->you_are }}</h5>
                @else
                    <h5 class="yellow-label text-center mb-0">{{ auth()->user()->as_a }}</h5>
                @endif
            </div>
        </div>
        <ul class="p-0 list-unstyled">
            <li>
                <a href="{{ url('profile') }}" class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_user.png') }}" class="mb-0" height="18px" alt="">
                    <span>View Profile</span>
                </a>
            </li>
            <li>
                <a href="{{ url('cashback') }}" class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_cashback.png') }}" class="mb-0" height="18px" alt="">
                    <span>Cashback</span>
                </a>
            </li>
            <li>
                <a href="{{ url('wallet') }}" class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_wallet.png') }}" class="mb-0" height="18px" alt="">
                    <span>Wallet</span>
                </a>
            </li>
            <li class="collapsed d-flex justify-content-between align-items-center" data-bs-toggle="collapse"
                data-bs-target="#highlights" aria-expanded="false">
                <a class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_highlights.png') }}" class="mb-0" height="18px" alt="">
                    <span>Highlights</span>
                </a>
                <div class="righticon d-flex ms-auto">
                    <i class="fa-solid fa-angle-right toggle-icon" style="font-size: 12px;"></i>
                </div>
            </li>
            <div class="collapse mt-3" id="highlights">
                <ul class="btn-toggle-nav list-unstyled text-start ps-4 pe-0 py-2">
                    <li class="mt-0 mb-3">
                        <a href="{{ url('products-highlight') }}" class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/images/icon_dashbars.png') }}" class="mb-0" height="18px" alt="">
                            <span>Products</span>
                        </a>
                    </li>
                    <li class="mt-0 mb-3">
                        <a href="{{ url('services-highlight') }}" class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/images/icon_bill.png') }}" class="mb-0" height="18px" alt="">
                            <span>Services</span>
                        </a>
                    </li>
                    <li class="mt-0">
                        <a href="{{ url('jobs-highlight') }}" class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/images/icon_briefcase.png') }}" class="mb-0" height="18px"
                                alt="">
                            <span>Jobs</span>
                        </a>
                    </li>
                </ul>
            </div>
            <li>
                <a href="{{ url('premium') }}" class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_premium.png') }}" class="mb-0" height="18px" alt="">
                    <span>Premium</span>
                </a>
            </li>
            <li>
                <a href="{{ url('requested-services') }}" class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_bill.png') }}" class="mb-0" height="18px" alt="">
                    <span>Requested Services</span>
                </a>
            </li>
            <li>
                <a href="{{ url('wishlist') }}" class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_heart.png') }}" class="mb-0" height="18px" alt="">
                    <span>Wishlist</span>
                </a>
            </li>
            <!-- <li>
                <a data-bs-toggle="modal" data-bs-target="#questions" class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_info.png') }}" class="mb-0" height="18px" alt="">
                    <span>Something About You</span>
                </a>
            </li> -->
            <li>
                <a href="{{ url('my-profile') }}" class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_edit.png') }}" class="mb-0" height="18px" alt="">
                    <span>Update Details</span>
                </a>
            </li>
            <li class="d-flex justify-content-between align-items-center">
                <a href="{{ url('notification') }}" class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_notify.png') }}" class="mb-0" height="18px" alt="">
                    <span>Notifications</span>
                </a>
                @if ($notificationCount > 0 ?? '')
                    <h6 class="m-0 notification-h6 text-center">
                        @if ($notificationCount > 9 ?? '')
                            9+
                        @else
                            {{ $notificationCount ?? ''}}
                        @endif
                    </h6>
                @endif
            </li>
            <li>
                <a href="{{ url('invoices') }}" class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_invoice.png') }}" class="mb-0" height="18px" alt="">
                    <span>Invoices</span>
                </a>
            </li>
            <li>
                <a href="{{ url('chatbot') }}" class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_bot.png') }}" class="mb-0" height="20px" alt="">
                    <span>Chatbot</span>
                </a>
            </li>
            <!-- <li>
                <a data-bs-toggle="modal" data-bs-target="#switchAcct" class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_switch.png') }}" class="mb-0" height="18px" alt="">
                    <span>Switch Account</span>
                </a>
            </li> -->
            <li>
                <a href="{{ url('settings') }}" class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_setting.png') }}" class="mb-0" height="18px" alt="">
                    <span>Settings</span>
                </a>
            </li>
            <li>
                <a data-bs-toggle="modal" data-bs-target="#logout" class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_logout.png') }}" class="mb-0" height="18px" alt="">
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<script>
    document.querySelectorAll('.dropdown-div [data-bs-toggle="collapse"]').forEach(item => {
        item.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    });
</script>