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
                    @if (auth()->user()->as_a === 'Contractor')
                        @foreach ($contractor_type as $item)
                            <option value="{{ $item->id }}" {{ in_array($item->id, $selectedTypes) ? 'selected' : '' }}>
                                {{ $item->value }}
                            </option>
                        @endforeach
                    @elseif (auth()->user()->as_a === 'Consultant')
                        @foreach ($consultant_type as $item)
                            <option value="{{ $item->id }}" {{ in_array($item->id, $selectedTypes) ? 'selected' : '' }}>
                                {{ $item->value }}
                            </option>
                        @endforeach
                    @endif
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
                <label for="project_category">Project Category</label>
                <select class="form-select" name="project_category" id="prjtCat">
                    <option value="" selected disabled>Select Project Category</option>
                    @foreach ($project_category as $prjtCat)
                        <option value="{{ $prjtCat->id }}" {{ ($profile->project_category ?? '') == $prjtCat->id ? 'selected' : '' }}>
                            {{ $prjtCat->value }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="increturns">Income Tax Returns</label>
                <input type="file" class="form-control" name="income_tax" id="increturns"
                    accept="image/*, .pdf, .docx, .doc, .csv" onchange="validateFile(this)">
                @if (!empty($profile->income_tax))
                    <small class="mt-1 text-muted">Current File: {{ basename($profile->income_tax) }}</small>
                @endif
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="your_purpose">Your Purpose</label>
                <select class="form-select" name="your_purpose" id="purpose">
                    <option value="" selected disabled>Select Purpose</option>
                    @foreach ($your_purpose as $purpose)
                        <option value="{{ $purpose->id }}" {{ ($profile->your_purpose ?? '') == $purpose->id ? 'selected' : '' }}>{{ $purpose->value }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="servicesoff">Services Offered</label>
                <!-- <select class="form-select" name="services_offered" id="servicesOff">
                    <option value="" selected disabled>Select Services Offered</option>
                    @foreach ($services_offered as $serviceOff)
                        <option value="{{ $serviceOff->id }}"
                            {{ ($profile->services_offered ?? '') == $serviceOff->id ? 'selected' : '' }}>
                            {{ $serviceOff->value }}</option>
                    @endforeach
                </select> -->
                <textarea rows="1" class="form-control" name="services_offered"
                    id="services_offered">{{ $profile->services_offered ?? '' }}</textarea>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="projects">No. Of Ongoing Projects</label>
                <input type="number" class="form-control" name="projects_ongoing" id="projects" min="0"
                    value="{{ $profile->projects_ongoing ?? '' }}">
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="projectdet">Ongoing Projects Details</label>
                <textarea rows="1" class="form-control" name="ongoing_details"
                    id="projectdet">{{ $profile->ongoing_details ?? '' }}</textarea>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="labours">No. Of Labours Available</label>
                <input type="number" class="form-control" name="labours" id="labours" min="0"
                    value="{{ $profile->labours ?? '' }}">
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="mobcapable">Mobilization Capability</label>
                <input type="text" class="form-control" name="mobilization" id="mobcapable"
                    value="{{ $profile->mobilization ?? '' }}">
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