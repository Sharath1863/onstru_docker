<!-- Student -->
@if (auth()->user()->type_of_names[0] === 'Student')
    <div class="profile-cards mb-2">
        <div class="container-xl form-div">
            <div class="body-head mb-3">
                <h5>Basic Information</h5>
            </div>
            <div class="row">
                <!-- Common Inputs -->
                @include('myprofile.update.common')

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="professional_status">Professional Status</label>
                    <div class="d-flex align-items-center gap-4 mt-2">
                        <div class="d-flex align-items-center gap-2">
                            <input type="radio" class="form-check-input my-auto" name="professional_status" value="Student"
                                id="student" checked>
                            <label for="student" class="m-0">Student</label>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="radio" class="form-check-input my-auto" name="professional_status" value="Working"
                                id="working">
                            <label for="working" class="m-0">Working</label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="eduqualify">Educational Qualification</label>
                    <select class="form-select" name="education" id="eduqualify">
                        <option value="" selected disabled>Select Educational Qualification</option>
                        <option value="B.Tech. IT" {{ optional($profile)->education == 'B.Tech. IT' ? 'selected' : '' }}>
                            B.Tech. IT
                        </option>
                        <option value="B.E. CSE" {{ optional($profile)->education == 'B.E. CSE' ? 'selected' : '' }}>
                            B.E. CSE
                        </option>
                        <option value="B.Sc. CS" {{ optional($profile)->education == 'B.Sc. CS' ? 'selected' : '' }}>
                            B.Sc. CS
                        </option>
                        <option value="ECE" {{ optional($profile)->education == 'ECE' ? 'selected' : '' }}>ECE
                        </option>
                        <option value="EEE" {{ optional($profile)->education == 'EEE' ? 'selected' : '' }}>EEE
                        </option>
                    </select>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="college">College Name</label>
                    <input type="text" class="form-control" name="college" id="college"
                        value="{{ $profile->college ?? '' }}">
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="aadhar">Aadhar Number</label>
                    <input type="number" class="form-control" name="aadhar_no" id="aadhar" min="100000000000"
                        max="999999999999" oninput="validate_aadhar(this)" value="{{ $profile->aadhar_no ?? '' }}">
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="pan">PAN Number</label>
                    <input type="text" class="form-control" name="pan_no" id="pan" value="{{ $profile->pan_no ?? '' }}"
                        oninput="validatePAN()">
                    <small id="pan-error"></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Working -->
@elseif (auth()->user()->type_of_names[0] === 'Working')
    <div class="profile-cards mb-2">
        <div class="container-xl form-div">
            <div class="body-head mb-3">
                <h5>Basic Information</h5>
            </div>
            <div class="row">
                <!-- Common Inputs -->
                @include('myprofile.update.common')

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="professional_status">Professional Status</label>
                    <div class="d-flex align-items-center gap-4 mt-2">
                        <div class="d-flex align-items-center gap-2">
                            <input type="radio" class="form-check-input my-auto" name="professional_status" value="Student"
                                id="student">
                            <label for="student" class="m-0">Student</label>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="radio" class="form-check-input my-auto" name="professional_status" value="Working"
                                id="working" checked>
                            <label for="working" class="m-0">Working</label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="eduqualify">Educational Qualification</label>
                    <select class="form-select" name="education" id="eduqualify">
                        <option value="" selected disabled>Select Educational Qualification</option>
                        <option value="B.Tech. IT" {{ optional($profile)->education == 'B.Tech. IT' ? 'selected' : '' }}>
                            B.Tech. IT
                        </option>
                        <option value="B.E. CSE" {{ optional($profile)->education == 'B.E. CSE' ? 'selected' : '' }}>
                            B.E. CSE
                        </option>
                        <option value="B.Sc. CS" {{ optional($profile)->education == 'B.Sc. CS' ? 'selected' : '' }}>
                            B.Sc. CS
                        </option>
                        <option value="ECE" {{ optional($profile)->education == 'ECE' ? 'selected' : '' }}>ECE
                        </option>
                        <option value="EEE" {{ optional($profile)->education == 'EEE' ? 'selected' : '' }}>EEE
                        </option>
                    </select>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="designation">Designation</label>
                    <select class="form-select" name="designation" id="designation">
                        <option value="" selected disabled>Select Designation</option>
                        @foreach ($designations as $designation)
                            <option value="{{ $designation->id }}" {{ ($profile->designation ?? '') == $designation->id ? 'selected' : '' }}>{{ $designation->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="emptype">Employment Type</label>
                    <select class="form-select" name="employment_type" id="emptype">
                        <option value="" selected disabled>Select Employment Type</option>
                        <option value="Part-Time" {{ optional($profile)->employment_type == 'Part-Time' ? 'selected' : '' }}>
                            Part-Time
                        </option>
                        <option value="Full-Time" {{ optional($profile)->employment_type == 'Full-Time' ? 'selected' : '' }}>
                            Full-Time
                        </option>
                        <option value="Intern" {{ optional($profile)->employment_type == 'Intern' ? 'selected' : '' }}>Intern
                        </option>
                        <option value="Contract" {{ optional($profile)->employment_type == 'Contract' ? 'selected' : '' }}>
                            Contract
                        </option>
                    </select>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="exp">Total Experience</label>
                    <select class="form-select" name="experience" id="exp">
                        <option value="" selected disabled>Select Experience</option>
                        <option value="Fresher" {{ optional($profile)->experience == 'Fresher' ? 'selected' : '' }}>
                            Fresher
                        </option>
                        <option value="1" {{ optional($profile)->experience == '1' ? 'selected' : '' }}>1+ years
                        </option>
                        <option value="2" {{ optional($profile)->experience == '2' ? 'selected' : '' }}>2+ years
                        </option>
                        <option value="3" {{ optional($profile)->experience == '3' ? 'selected' : '' }}>3+ years
                        </option>
                        <option value="4" {{ optional($profile)->experience == '4' ? 'selected' : '' }}>4+ years
                        </option>
                        <option value="5" {{ optional($profile)->experience == '5' ? 'selected' : '' }}>More than
                            5 years
                        </option>
                    </select>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="prjthandle">Projects Handled</label>
                    <input type="text" name="projects_handled" id="prjthandle" class="form-control"
                        placeholder="Enter Projects Handled" value="{{ $profile->projects_handled ?? '' }}">
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="expertise">Expertise In</label>
                    <input type="text" class="form-control" name="expertise" id="expertise"
                        value="{{ $profile->expertise ?? '' }}">
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="ctc">Current CTC (LPA)</label>
                    <input type="number" class="form-control" name="current_ctc" id="ctc"
                        value="{{ $profile->current_ctc ?? '' }}">
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="notice">Notice Period</label>
                    <input type="text" class="form-control" name="notice_period" id="notice"
                        value="{{ $profile->notice_period ?? '' }}">
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="aadhar">Aadhar Number</label>
                    <input type="number" class="form-control" name="aadhar_no" id="aadhar" min="100000000000"
                        max="999999999999" oninput="validate_aadhar(this)" value="{{ $profile->aadhar_no ?? '' }}">
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="pan">PAN Number</label>
                    <input type="text" class="form-control" name="pan_no" id="pan" value="{{ $profile->pan_no ?? '' }}">
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        $(document).ready(function () {
            var arr = ['#designation', '#eduqualify'];
            arr.forEach((item) => {
                $(item).select2({
                    placeholder: "Select Options",
                    allowClear: true,
                    width: "100%",
                }).prop("required", false);
            });
        });
    </script>

    <script>
        function validatePAN() {
            const panInput = document.getElementById('pan');
            const panError = document.getElementById('pan-error');
            const formBtn = document.querySelector('.formbtn');
            const panValue = panInput.value.trim().toUpperCase();
            const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
            panInput.value = panValue; // force uppercase

            // Case 1: Field is empty (allowed)
            if (panValue === "") {
                panError.textContent = "";
                panInput.style.borderColor = "";
                formBtn.disabled = false;
            }
            // Case 2: Invalid format
            else if (!panRegex.test(panValue)) {
                panError.textContent = "Invalid PAN format. Example: ABCDE1234F";
                panInput.style.borderColor = "red";
                formBtn.disabled = true;
            }
            // Case 3: Valid PAN
            else {
                panError.textContent = "";
                panInput.style.borderColor = "green";
                formBtn.disabled = false;
            }
        }
        // Run on page load (optional, if value prefilled)
        window.onload = validatePAN;
    </script>
@endif