@auth
    @if (!Request::is('register-otp-view'))
        <!-- After Authentication -->
        <nav class="navbar navbar-expand-lg" aria-label="OnstruNavbar">
            <div class="container-xl w-100">
                <div class="responsive-button">
                    <div>
                        <a href="{{ url('home') }}">
                            <img src="{{ asset('assets/images/Logo_Admin.png') }}" height="50px" title="" alt="">
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <ul class="list-unstyled m-0">
                            <li class="nav-item nav-user dropstart p-0">
                                @include('layouts.user')
                            </li>
                        </ul>
                        <div>
                            <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#navbarcontent" aria-controls="navbarcontent" aria-expanded="false"
                                aria-label="Toggle navigation">
                                <i class="fa-solid fa-bars toggler-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="navbar-collapse d-lg-flex justify-content-between align-items-center collapse" id="navbarcontent">
                    <div class="navbar-brand col-lg-2 me-0">
                        <a href="{{ url('home') }}">
                            <img src="{{ asset('assets/images/Logo_Admin.png') }}" height="50px" alt="">
                        </a>
                    </div>
                    <ul class="navbar-nav col-lg-7 align-items-lg-center justify-content-lg-between" id="navbarNav">
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center justify-content-center flex-column"
                                href="{{ url('home') }}">
                                <img src="{{ asset('assets/images/icon_home.png') }}" height="20px" class="d-flex mx-auto">
                                <h6 class="mb-0 text-center">Home</h6>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center justify-content-center flex-column"
                                href="{{ url('reels') }}">
                                <img src="{{ asset('assets/images/icon_reels.png') }}" height="20px" class="d-flex mx-auto">
                                <h6 class="mb-0 text-center">Reels</h6>
                            </a>
                        </li>
                        @if (auth()->user()->you_are == 'Consumer' || auth()->user()->you_are == 'Professional')
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center justify-content-center flex-column"
                                    href="{{ url('jobs') }}">
                                    <img src="{{ asset('assets/images/icon_briefcase.png') }}" height="20px" class="d-flex mx-auto">
                                    <h6 class="mb-0 text-center">Jobs</h6>
                                </a>
                            </li>
                        @elseif (auth()->user()->you_are != 'Consumer' || auth()->user()->you_are != 'Professional')
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center justify-content-center flex-column"
                                    href="{{ url('hire') }}">
                                    <img src="{{ asset('assets/images/icon_hire.png') }}" height="20px" class="d-flex mx-auto">
                                    <h6 class="mb-0 text-center">Hire</h6>
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center justify-content-center flex-column"
                                href="{{ url('people') }}">
                                <img src="{{ asset('assets/images/icon_people.png') }}" height="20px" class="d-flex mx-auto">
                                <h6 class="mb-0 text-center">People</h6>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center justify-content-center flex-column"
                                href="{{ url('products') }}">
                                <img src="{{ asset('assets/images/icon_dashbars.png') }}" height="20px" class="d-flex mx-auto">
                                <h6 class="mb-0 text-center">Products</h6>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center justify-content-center flex-column"
                                href="{{ url('services') }}">
                                <img src="{{ asset('assets/images/icon_bill.png') }}" height="20px" class="d-flex mx-auto">
                                <h6 class="mb-0 text-center">Services</h6>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center justify-content-center flex-column"
                                href="{{ url('leads') }}">
                                <img src="{{ asset('assets/images/icon_send.png') }}" height="20px" class="d-flex mx-auto">
                                <h6 class="mb-0 text-center">Leads</h6>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center justify-content-center flex-column"
                                href="{{ url('cart') }}">
                                <img src="{{ asset('assets/images/icon_cart.png') }}" height="20px" class="d-flex mx-auto">
                                <h6 class="mb-0 text-center">Cart</h6>
                            </a>
                        </li>
                        <li class="nav-item nav-user dropstart p-0 d-none d-lg-block">
                            @include ('layouts.user')
                        </li>
                    </ul>

                </div>
            </div>
        </nav>
    @else
        <!-- Before Authentication -->
        <nav class="navbar navbar-expand-lg bg-light py-md-0">
            <div class="container-xl w-100">
                <!-- Responsive -->
                <div class="responsive-button">
                    <div>
                        <a href="{{ url('login') }}">
                            <img src="{{ asset('assets/images/Logo_Admin.png') }}" height="50px" title="" alt="">
                        </a>
                    </div>
                    <div>
                        <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarcontent" aria-controls="navbarcontent" aria-expanded="false"
                            aria-label="Toggle navigation">
                            <i class="fa-solid fa-bars toggler-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="navbar-collapse bg-light d-lg-flex justify-content-between align-items-center collapse"
                    id="navbarcontent">
                    <div class="navbar-brand col-lg-2 me-0">
                        <a href="{{ url('home') }}">
                            <img src="{{ asset('assets/images/Logo_Admin.png') }}" height="50px" alt="">
                        </a>
                    </div>
                    <ul class="navbar-nav col-lg-7 align-items-lg-center justify-content-lg-end gap-xl-5" id="navbarNav">
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center justify-content-center flex-column" href="">
                                <img src="{{ asset('assets/images/icon_dashboard.png') }}" height="20px" class="d-flex mx-auto">
                                <h6 class="mb-0 text-center">Get the App</h6>
                            </a>
                        </li>
                    </ul>

                </div>
            </div>
        </nav>
    @endif
@else
    <!-- Before Authentication -->
    <nav class="navbar navbar-expand-lg bg-light py-md-0">
        <div class="container-xl w-100">
            <!-- Responsive -->
            <div class="responsive-button">
                <div>
                    <a href="{{ url('login') }}">
                        <img src="{{ asset('assets/images/Logo_Admin.png') }}" height="50px" title="" alt="">
                    </a>
                </div>
                <div>
                    <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarcontent" aria-controls="navbarcontent" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <i class="fa-solid fa-bars toggler-icon"></i>
                    </button>
                </div>
            </div>

            <div class="navbar-collapse bg-light d-lg-flex justify-content-between align-items-center collapse"
                id="navbarcontent">
                <div class="navbar-brand col-lg-2 me-0">
                    <a href="{{ url('home') }}">
                        <img src="{{ asset('assets/images/Logo_Admin.png') }}" height="50px" alt="">
                    </a>
                </div>
                <ul class="navbar-nav col-lg-7 align-items-lg-center justify-content-lg-end gap-xl-5" id="navbarNav">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center justify-content-center flex-column" href="">
                            <img src="{{ asset('assets/images/icon_dashboard.png') }}" height="20px" class="d-flex mx-auto">
                            <h6 class="mb-0 text-center">Get the App</h6>
                        </a>
                    </li>
                </ul>

            </div>
        </div>
    </nav>
@endauth