@include('myprofile.details.bank')

<div class="profile-cards mb-2">
    <div class="container-xl px-sm-0 px-md-2">
        <div class="body-head mb-3">
            <h5>Additional Information</h5>
        </div>
        <div class="cards-content row">
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Projects Category</h4>
                <h6>{{ $profile->projectCatRelation->value ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Income Tax Returns</h4>
                @if ($profile && $profile->income_tax)
                    <a href="{{ $profile->income_tax ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $profile->income_tax : '#' }}" target="_blank">
                        <h6>
                            <i class="fas fa-external-link" data-bs-toggle="tooltip" data-bs-title="View File"></i>
                        </h6>
                    </a>
                @else
                    <h6>-</h6>
                @endif
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Your Purpose</h4>
                <h6>{{ $profile->purposeRelation->value ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Services Offered</h4>
                <h6>{{ $profile->services_offered ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>No. Of Ongoing Projects</h4>
                <h6>{{ $profile->projects_ongoing ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Ongoing Projects Details</h4>
                <h6>{{ $profile->ongoing_details ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>No. Of Labours Available</h4>
                <h6>{{ $profile->labours ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Mobilization Capability</h4>
                <h6>{{ $profile->mobilization ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Resources Strength</h4>
                <h6>{{ $profile->strength ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Client Telephone</h4>
                <h6>+91 {{ $profile->client_tele ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Major Customer</h4>
                <h6>{{ $profile->customer ?? '-' }}</h6>
            </div>
        </div>
    </div>
</div>

<!-- GST Details -->
@include('myprofile.details.gst')