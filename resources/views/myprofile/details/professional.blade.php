<!-- Student -->
@if (auth()->user()->type_of_names[0] === 'Student')
    <div class="profile-cards mb-2">
        <div class="container-xl px-sm-0 px-md-2">
            <div class="body-head mb-3">
                <h5>Basic Information</h5>
            </div>
            <div class="cards-content row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>Professional Status</h4>
                    <h6>{{ $profile->professional_status ?? '-' }}</h6>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>Educational Qualification</h4>
                    <h6>{{ $profile->education ?? '-' }}</h6>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>College Name</h4>
                    <h6>{{ $profile->college ?? '-' }}</h6>
                </div>
                {{-- <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>Address</h4>
                    <h6>{{ $profile->address ?? '-' }}</h6>
                </div> --}}
            </div>
        </div>
    </div>

    <div class="profile-cards mb-2">
        <div class="container-xl px-sm-0 px-md-2">
            <div class="body-head mb-3">
                <h5>Additional Information</h5>
            </div>
            <div class="cards-content row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>Aadhar Number</h4>
                    <h6>{{ $profile->aadhar_no ?? '-' }}</h6>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>PAN Number</h4>
                    <h6>{{ $profile->pan_no ?? '-' }}</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Working -->
@elseif (auth()->user()->type_of_names[0] === 'Working')
    <div class="profile-cards mb-2">
        <div class="container-xl px-sm-0 px-md-2">
            <div class="body-head mb-3">
                <h5>Basic Information</h5>
            </div>
            <div class="cards-content row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>Professional Status</h4>
                    <h6>{{ $profile->professional_status ?? '-' }}</h6>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>Educational Qualification</h4>
                    <h6>{{ $profile->education ?? '-' }}</h6>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>Designation</h4>
                    <h6>{{ $profile->designationRelation->value ?? '-' }}</h6>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>Employment Type</h4>
                    <h6>{{ $profile->employment_type ?? '-' }}</h6>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>Total Experience</h4>
                    <h6>{{ $profile->experience ?? '-' }} Years</h6>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>Type Of Project Handled</h4>
                    <h6>{{ $profile->projects_handled ?? '-' }}</h6>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>Expertise In</h4>
                    <h6>{{ $profile->expertise ?? '-' }}</h6>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>Current CTC</h4>
                    <h6>{{ $profile->current_ctc ?? '-' }} LPA</h6>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>Notice Period</h4>
                    <h6>{{ $profile->notice_period ?? '-' }}</h6>
                </div>
                {{-- <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>Address</h4>
                    <h6>{{ $profile->address ?? '-' }}</h6>
                </div> --}}
            </div>
        </div>
    </div>

    <div class="profile-cards mb-2">
        <div class="container-xl px-sm-0 px-md-2">
            <div class="body-head mb-3">
                <h5>Additional Information</h5>
            </div>
            <div class="cards-content row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>Aadhar Number</h4>
                    <h6>{{ $profile->aadhar_no ?? '-' }}</h6>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <h4>PAN Number</h4>
                    <h6>{{ $profile->pan_no ?? '-' }}</h6>
                </div>
            </div>
        </div>
    </div>
@endif