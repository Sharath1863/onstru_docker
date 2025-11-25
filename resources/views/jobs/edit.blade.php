@extends('layouts.app')

@section('title', 'Onstru | Edit Job')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.css') }}">

    <style>
        .form-div {
            border: 1px solid var(--border);
            border-radius: 8px;
        }
    </style>

    <div class="container main-div">
        <div class="body-head mt-4">
            <h5>Edit Job</h5>
        </div>

        <div class="form-div py-2 px-3 mt-4" id="">
            <form action="{{ url('update-job/' . $job->id) }}" method="POST" enctype="multipart/form-data" id="jobEdit">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-sm-12 col-md-4 mb-3">
                        <label for="jobtitle">Job Title <span>*</span></label>
                        <input type="text" name="title" id="jobtitle" class="form-control" value="{{ $job->title }}" autofocus>
                    </div>

                    <div class="col-sm-12 col-md-4 mb-3">
                        <label for="skills">Required Skills  <span>*</span></label>
                        <textarea rows="1" name="skills" id="skills" class="form-control" required>{{ $job->skills }}</textarea>
                    </div>

                    <div class="col-sm-12 col-md-4 mb-3">
                        <label for="description">Job Description <span>*</span></label>
                        <textarea rows="1" name="description" id="description"
                            class="form-control" required>{{ $job->description }}</textarea>
                    </div>

                    <div class="col-sm-12 col-md-4 mb-3">
                        <label for="qualification">Minimum Qualification  <span>*</span></label>
                        <select name="qualification" class="form-select" id="qualification" required>
                            <option value="" disabled {{ old('qualification') ? '' : 'selected' }}>Select
                                Qualification</option>
                            <option value="8th" {{ $job->qualification == '8th' ? 'selected' : '' }}>8th
                            </option>
                            <option value="10th" {{ $job->qualification == '10th' ? 'selected' : '' }}>10th
                                (SSLC/Matriculation)</option>
                            <option value="12th" {{ $job->qualification == '12th' ? 'selected' : '' }}>12th
                                (HSC/PUC/Intermediate)</option>
                            <option value="Diploma" {{ $job->qualification == 'Diploma' ? 'selected' : '' }}>
                                Diploma</option>
                            <option value="BE_Civil" {{ $job->qualification == 'BE_Civil' ? 'selected' : '' }}>B.E.
                                (Civil)
                            </option>
                            <option value="BE" {{ $job->qualification == 'BE' ? 'selected' : '' }}>B.E.
                                (Bachelor of Engineering)</option>
                            <option value="BTech" {{ $job->qualification == 'BTech' ? 'selected' : '' }}>
                                B.Tech (Bachelor of Technology)</option>
                            <option value="BCA" {{ $job->qualification == 'BCA' ? 'selected' : '' }}>BCA
                                (Bachelor of Computer Application)</option>
                            <option value="BSC" {{ $job->qualification == 'BSC' ? 'selected' : '' }}>B.Sc
                            </option>
                            <option value="BCom" {{ $job->qualification == 'BCom' ? 'selected' : '' }}>B.Com
                            </option>
                            <option value="BA" {{ $job->qualification == 'BA' ? 'selected' : '' }}>BA
                            </option>
                            <option value="BBA" {{ $job->qualification == 'BBA' ? 'selected' : '' }}>BBA
                            </option>
                            <option value="ME" {{ $job->qualification == 'ME' ? 'selected' : '' }}>M.E.
                            </option>
                            <option value="MTech" {{ $job->qualification == 'MTech' ? 'selected' : '' }}>
                                M.Tech</option>
                            <option value="MCA" {{ $job->qualification == 'MCA' ? 'selected' : '' }}>MCA
                            </option>
                            <option value="MSC" {{ $job->qualification == 'MSC' ? 'selected' : '' }}>M.Sc
                            </option>
                            <option value="MCom" {{ $job->qualification == 'MCom' ? 'selected' : '' }}>M.Com
                            </option>
                            <option value="MA" {{ $job->qualification == 'MA' ? 'selected' : '' }}>MA
                            </option>
                            <option value="MBA" {{ $job->qualification == 'MBA' ? 'selected' : '' }}>MBA
                            </option>
                            <option value="PhD" {{ $job->qualification == 'PhD' ? 'selected' : '' }}>PhD
                            </option>
                        </select>
                    </div>

                    <div class="col-sm-12 col-md-4 mb-3">
                        <label for="job_type">Job Type <span>*</span></label>
                        <select name="job_type" id="job_type" class="form-select" required>
                            <option value="" selected disabled>Select Job Type</option>
                            @foreach (['Full-Time', 'Part-Time', 'Intern', 'Contract', 'freelancer'] as $type)
                                <option value="{{ $type }}" {{ $job->shift == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-12 col-md-4 mb-3">
                        <label for="experience">Work Experience</label>
                        <select name="experience" id="experience" class="form-select" required>
                            <option value="" selected disabled>Select Experience</option>
                            <option value="0" {{ $job->experience == '0' ? 'selected' : '' }}>Fresher</option>
                            <option value="1" {{ $job->experience == '1' ? 'selected' : '' }}>1+ year</option>
                            <option value="2" {{ $job->experience == '2' ? 'selected' : '' }}>2+ years</option>
                            <option value="3" {{ $job->experience == '3' ? 'selected' : '' }}>3+ years</option>
                            <option value="4" {{ $job->experience == '4' ? 'selected' : '' }}>4+ years</option>
                            <option value="5" {{ $job->experience == '5' ? 'selected' : '' }}>5+ years</option>
                        </select>
                    </div>

                    <div class="col-sm-12 col-md-4 mb-3">
                        <label for="category">Job Category <span>*</span></label>
                        <select name="category" id="category" class="form-select" required>
                            <option value="" selected disabled>Select Job Category</option>
                            @foreach ($category as $cat)
                                <option value="{{ $cat->id }}" {{ $job->category == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-12 col-md-4 mb-3">
                        <label for="salary">Pay Range <span>*</span></label>
                        <input type="text" name="salary" id="salary" class="form-control" value="{{ $job->salary }}"
                            required>
                    </div>

                    <div class="col-sm-12 col-md-4 mb-3">
                        <label for="no_of_openings">No. of Openings <span>*</span></label>
                        <input type="number" name="no_of_openings" id="no_of_openings" class="form-control"
                            value="{{ $job->no_of_openings }}" required>
                    </div>

                    <div class="col-sm-12 col-md-4 mb-3">
                        <label for="location">Location <span>*</span></label>
                        <select name="location" class="form-select" id="location">
                            <option value="" selected disabled>Select Location</option>
                            @foreach ($locations as $loc)
                                <option value="{{ $loc->id }}" {{ $job->location == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-12 col-md-4 mb-3">
                        <label for="address">Sub Location <span>*</span></label>
                        <input type="text" name="address" id="address" class="form-control" value="{{ $job->sublocality }}"
                            required>
                        <input type="hidden" id="coordinates">
                        <input type="hidden" id="sublocality" name="sublocality" value="{{ $job->sublocality }}">
                    </div>

                    <div class="col-sm-12 col-md-4 mb-3">
                        <label for="benefits">Benefits/Perks</label>
                        <textarea rows="1" name="benefits" id="benefits" class="form-control">{{ $job->benfit }}</textarea>
                    </div>

                    <div
                        class="col-sm-12 col-md-12 d-flex align-items-center justify-content-sm-start justify-content-sm-end my-3">
                        <button type="submit" class="formbtn">Update Job</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select 2 -->
    <script>
        $(document).ready(function () {
            let select2 = ['qualification', 'experience', 'category', 'location']
            select2.forEach(ele => {
                $(`#${ele}`).select2({
                    width: "100%",
                    placeholder: "Select Options",
                    allowClear: true,
                });
            });
        });
    </script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAmDotOMSahWwA7WAjmtremPvNNRN1Nye0&libraries=places"></script>

    <script>
        let fromAutocomplete;

        document.addEventListener("DOMContentLoaded", function () {
            // Restrict to India initially
            const options = {
                componentRestrictions: { country: "in" }
            };
            const fromInput = document.getElementById('address');
            fromAutocomplete = new google.maps.places.Autocomplete(fromInput, options);

            // Change autocomplete city bias when dropdown changes
            document.getElementById('location').addEventListener('change', function () {
                const selectedCity = this.options[this.selectedIndex].text; // e.g. Chennai
                if (selectedCity && selectedCity !== 'Select Location') {
                    const geocoder = new google.maps.Geocoder();
                    geocoder.geocode({ address: selectedCity + ', India' }, function (results, status) {
                        if (status === 'OK' && results[0].geometry) {
                            const cityBounds = results[0].geometry.viewport;
                            // Bias autocomplete to this city
                            fromAutocomplete.setBounds(cityBounds);
                            // Restrict strictly inside the city
                            fromAutocomplete.setOptions({
                                strictBounds: true,
                                componentRestrictions: { country: "in" }
                            });
                        }
                    });
                }
            });

            // On place select
            fromAutocomplete.addListener('place_changed', function () {
                const place = fromAutocomplete.getPlace();
                if (!place.geometry) return;
                const location = place.geometry.location;
                document.getElementById('coordinates').value = location.lat() + ',' + location.lng();
                let sublocality = '';
                if (place.address_components) {
                    for (const component of place.address_components) {
                        const types = component.types;
                        if (
                            types.includes('sublocality') ||
                            types.includes('sublocality_level_1') ||
                            types.includes('sublocality_level_2')
                        ) {
                            sublocality = component.long_name;
                            break;
                        }
                        if (!sublocality && types.includes('locality')) {
                            sublocality = component.long_name;
                        }
                        if (!sublocality && types.includes('administrative_area_level_2')) {
                            sublocality = component.long_name;
                        }
                    }
                }
                document.getElementById('sublocality').value = sublocality;
                document.getElementById('address').value = sublocality;
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('jobEdit');
            const submitBtn = document.querySelector('.formbtn');
            const sublocality = document.getElementById("sublocality");
            const addressInput = document.getElementById("address");
            let isSubmitting = false;

            form.addEventListener('submit', function (e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }
                if (sublocality.value.trim() === '') {
                    e.preventDefault();
                    addressInput.classList.add("is-invalid");
                    showToast("Please select the Sub-location from suggestions");
                    addressInput.focus();
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Update Job';
                    isSubmitting = false;
                    return;
                }
                isSubmitting = true;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Updating...`;
            });

            // Remove invalid highlight when user types again
            addressInput.addEventListener("input", function () {
                if (addressInput.classList.contains("is-invalid")) {
                    addressInput.classList.remove("is-invalid");
                }
            });
        });
    </script>

@endsection