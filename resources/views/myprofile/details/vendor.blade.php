@include('myprofile.details.bank')

<div class="profile-cards mb-2">
    <div class="container-xl px-sm-0 px-md-2">
        <div class="body-head mb-3">
            <h5>Additional Information</h5>
        </div>
        <div class="cards-content row">
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Your Purpose</h4>
                <h6>{{ $profile->purposeRelation->value ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Delivery Timeline</h4>
                <h6>{{ $profile->delivery_timeline ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Location Catered</h4>
                <h6>{{ $profile->location_catered ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Strength</h4>
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