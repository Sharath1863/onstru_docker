<li class="nav-item w-100 mb-2" role="presentation">
    <button class="profilebtn w-100 text-start active" data-bs-toggle="tab" type="button" data-bs-target="#password">
        <i class="fas fa-lock pe-1"></i><span>Change Password</span>
    </button>
</li>
{{-- <li class="nav-item w-100 mb-2" role="presentation">
    <button class="profilebtn justify-content-between w-100 text-start">
        <div class="d-flex gap-2">
            <i class="fas fa-bell pe-1"></i><span>Notifications</span>
        </div>
        <div class="form-check form-switch mb-0">
            <input class="form-check-input py-2 px-3" type="checkbox" value="" id="toggleSwitch" switch>
        </div>
    </button>
</li> --}}
<li class="nav-item w-100 mb-2" role="presentation">
    <a href="{{ url('orders') }}">
        <button class="profilebtn w-100 text-start">
            <i class="fas fa-box-open pe-1"></i><span>My Orders</span>
        </button>
    </a>
</li>
<li class="nav-item w-100 mb-2" role="presentation">
    <button class="profilebtn w-100 text-start" role="tab" data-bs-toggle="tab" type="button" data-bs-target="#saved">
        <i class="fas fa-bookmark pe-1"></i><span>Saved</span>
    </button>
</li>
<li class="nav-item w-100 mb-2" role="presentation">
    <button class="profilebtn w-100 text-start" role="tab" data-bs-toggle="tab" type="button" data-bs-target="#liked">
        <i class="fas fa-heart pe-1"></i><span>Liked</span>
    </button>
</li>
<li class="nav-item w-100 mb-2" role="presentation">
    <a href="{{ url('delete-my-account') }}" target="_blank">
        <button class="profilebtn w-100 text-start" type="button">
            <i class="fas fa-trash pe-1"></i><span>Delete My Account</span>
        </button>
    </a>
</li>
<li class="nav-item w-100 mb-2" role="presentation">
    <a href="{{ url('terms-and-condition') }}" target="_blank">
        <button class="profilebtn w-100 text-start" type="button">
            <i class="fas fa-shield pe-1"></i><span>Terms And Condition</span>
        </button>
    </a>
</li>
<li class="nav-item w-100 mb-2" role="presentation">
    <a href="{{ url('privacy-policy') }}" target="_blank">
        <button class="profilebtn w-100 text-start" type="button">
            <i class="fas fa-file-shield pe-1"></i><span>Privacy Policy</span>
        </button>
    </a>
</li>
<li class="nav-item w-100 mb-2" role="presentation">
    <a href="{{ url('refund-and-cancellation') }}" target="_blank">
        <button class="profilebtn w-100 text-start" type="button">
            <i class="fas fa-money-bill-transfer pe-1"></i><span>Refund and Cancellation Policy</span>
        </button>
    </a>
</li>
<li class="nav-item w-100 mb-2" role="presentation">
    <a href="{{ url('contact-us') }}" target="_blank">
        <button class="profilebtn w-100 text-start" type="button">
            <i class="fas fa-phone pe-1"></i><span>Contact Us</span>
        </button>
    </a>
</li>