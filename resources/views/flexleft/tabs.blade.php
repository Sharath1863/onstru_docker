<div class="profile-tabs">
    <ul class="nav nav-tabs d-flex justify-content-sm-between justify-content-md-center align-items-start flex-sm-row flex-md-column border-0"
        id="myTab" role="tablist">
        <li class="nav-item w-100 mb-2" role="presentation">
            <a href="{{ url('explore') }}">
                <button class="profilebtn w-100">
                    <img src="{{ asset('assets/images/icon_explore.png') }}" class="mb-0" height="20px" alt="">
                    <span>Explore</span>
                </button>
            </a>
        </li>
        <li class="nav-item w-100 mb-2" role="presentation">
            <a href="{{ url('premium') }}">
                <button class="profilebtn w-100">
                    <img src="{{ asset('assets/images/icon_premium.png') }}" class="mb-0" height="17px" alt="">
                    <span>Premium</span>
                </button>
            </a>
        </li>
        <li class="nav-item w-100" role="presentation">
            <a href="{{ url('profile') }}">
                <button class="profilebtn w-100">
                    <img src="{{ asset('assets/images/icon_user.png') }}" class="mb-0" height="18px" alt="">
                    <span>Profile</span>
                </button>
            </a>
        </li>
    </ul>
</div>