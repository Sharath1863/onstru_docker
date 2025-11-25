<div class="profile-cards mb-2">
    <div class="container-xl form-div">
        <div class="body-head mb-3">
            <h5>Basic Information</h5>
        </div>
        <div class="row">
            <!-- Common Inputs -->
            @include('myprofile.update.common')

            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="type">Type Of</label>
                <select class="form-select" name="type_of[]" id="typeof" multiple="multiple">
                    @foreach ($vendor_type as $item)
                        <option value="{{ $item->id }}" {{ in_array($item->id, $selectedTypes) ? 'selected' : '' }}>
                            {{ $item->value }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

@include('myprofile.update.bank')

<div class="profile-cards mb-2">
    <div class="container-xl form-div">
        <div class="body-head mb-3">
            <h5>Additional Information</h5>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="purpose">Your Purpose</label>
                <select class="form-select" name="your_purpose" id="purpose">
                    <option value="" selected disabled>Select Purpose</option>
                    @foreach ($your_purpose as $purpose)
                        <option value="{{ $purpose->id }}" {{ ($profile->your_purpose ?? '') == $purpose->id ? 'selected' : '' }}>{{ $purpose->value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="deltimeline">Delivery Timeline</label>
                <input type="text" class="form-control" name="delivery_timeline" id="deltimeline"
                    value="{{ $profile->delivery_timeline ?? '' }}">
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="location">Location Catered</label>
                <input type="text" class="form-control" name="location_catered" id="location"
                    value="{{ $profile->location_catered ?? '' }}">
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="resstrngth">Resources Strength</label>
                <select class="form-select" name="strength" id="resstrngth">
                    <option value="" selected disabled>Select Resources Strength</option>
                    <option value="1" {{ optional($profile)->strength == '1' ? 'selected' : '' }}>1</option>
                    <option value="2" {{ optional($profile)->strength == '2' ? 'selected' : '' }}>2</option>
                </select>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="clienttele">Client Telephone</label>
                <input type="number" class="form-control" name="client_tele" id="clienttele" min="6000000000"
                    max="9999999999" oninput="validate_contact(this)" value="{{ $profile->client_tele ?? '' }}">
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="major">Major Customer</label>
                <input type="text" class="form-control" name="customer" id="major"
                    value="{{ $profile->customer ?? '' }}">
            </div>
        </div>
    </div>
</div>