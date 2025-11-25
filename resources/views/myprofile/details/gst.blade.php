<div class="profile-cards mb-2">
    <div class="container-xl px-sm-0 px-md-2">
        <div class="body-head mb-3">
            <h5>GST Information</h5>
        </div>
        <div class="cards-content row">
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>GST Number</h4>
                <h6>{{ $gst->gst_number ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Business Legal Name</h4>
                <h6>{{ $gst->business_legal ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Contact Number</h4>
                <h6>+91 {{ $gst->contact_no ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Email ID</h4>
                <h6>{{ $gst->email_id ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>PAN Number</h4>
                <h6>{{ $gst->pan_no ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Date Of Registration</h4>
                <h6>{{ $gst->register_date ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Address</h4>
                <h6>{{ $gst->gst_address ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Nature Of Business</h4>
                <h6>{{ $gst->nature_business ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Annual Turnover</h4>
                <h6>{{ $gst->annual_turnover ?? '-' }}</h6>
            </div>
        </div>

        <div class="cards-content">
            <h4>Notes <span class="mandatory">*</span></h4>
            <h6>Get the GST Verified icon <img src="{{ asset('assets/images/GST_Verify.png') }}" height="18px" alt="" class="px-1"> by successfully verifying your GST details — showcasing your business authenticity and trustworthiness on the platform.</h6>
        </div>
    </div>
</div>