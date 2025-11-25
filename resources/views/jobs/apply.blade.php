@extends('layouts.app')

@section('title', 'Onstru | Job Apply')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.css') }}">

    <style>
        .flex-sidebar {
            display: block !important;
        }

        @media screen and (min-width: 1024px) {
            .flex-side {
                grid-template-columns: 35% 64% !important;
            }
        }
    </style>

    <div class="container main-div">
        <div class="flex-side">
            <!-- Flex Left -->
            <div class="flex-sidebar border-0">
                <div class="side-cards job-card mb-3 position-relative">
                    <div class="cards-head">
                        <a href="" class="text-decoration-none text-dark">
                            <h5 class="mb-2">{{ $job->title ?? 'Job Title' }}</h5>
                            <h6 class="mb-3">
                                {{ $job->categoryRelation->value ?? 'Company' }}
                            </h6>
                            @if ($job->highlighted == 1 && $job->created_by == auth()->id())
                                <a href="{{ route('view-job-highlight', $job->id) }}" class="badge" data-bs-toggle="tooltip"
                                    data-bs-title="View Boosted">
                                    <img src="{{ asset('assets/images/icon_fire.png') }}" height="15px" class="pe-1" alt="">
                                    Boosted
                                </a>
                            @elseif ($job->highlighted == 1)
                                <a class="badge " data-bs-toggle="tooltip" data-bs-title="Boosted Job">
                                    <img src="{{ asset('assets/images/icon_fire.png') }}" height="15px" class="pe-1" alt="">
                                    Boosted
                                </a>
                            @endif
                        </a>
                    </div>
                    <div class="cards-content">
                        <div class="d-flex align-items-center justify-content-start flex-wrap gap-5">
                            <h6 class="mb-3 d-flex align-items-center gap-2">
                                <i class="fas fa-briefcase"></i>
                                {{ $job->experience ?? '0-1 Yrs' }}
                            </h6>
                            <h6 class="mb-3 d-flex align-items-center gap-2">
                                <i class="fas fa-indian-rupee-sign"></i>
                                {{ $job->salary ?? 'Not disclosed' }}
                            </h6>
                            <h6 class="mb-3 d-flex align-items-center gap-2">
                                <i class="fas fa-location-dot"></i>
                                {{ $job->locationRelation->value ?? 1 }}
                            </h6>
                        </div>
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex align-items-center justify-content-start gap-2">
                                <h6 class="mb-0">{{ optional($job->created_at)->diffForHumans() }}</h6>
                                <h6 class="mb-0">|</h6>
                                <h6 class="mb-0">Opening: {{ $job->no_of_openings ?? 'N/A' }}</h6>
                            </div>
                            <a href="">
                                <h6 class="mb-0">
                                    <i class="fas fa-share-nodes pe-1"></i> Share
                                </h6>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="side-cards job-card mb-3">
                    <div class="cards-content row">
                        <div class="col-sm-12 mb-3">
                            <h5 class="mb-2">Job Category</h5>
                            <h6 class="mb-0 text-muted">{{ $job->categoryRelation->value }}</h6>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <h5 class="mb-2">Job Location</h5>
                            <h6 class="mb-0 text-muted">
                                {{ $job->locationRelation->value ?? 'Multiple locations, depending on project sites.' }}
                            </h6>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <h5 class="mb-2">Job Type</h5>
                            <h6 class="mb-0 text-muted">{{ $job->shift ?? 'Fresher' }}</h6>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <h5 class="mb-2">Qualification</h5>
                            <h6 class="mb-0 text-muted">
                                {{ $job->qualification ?? "Diploma or Bachelor's degree in a related field." }}
                            </h6>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <h5 class="mb-2">Work Experience</h5>
                            <h6 class="mb-0 text-muted">
                                {{ $job->experience ?? '1-3 years of experience preferred (freshers may also apply).' }}
                            </h6>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <h5 class="mb-2">Salary Range</h5>
                            <h6 class="mb-0 text-muted">₹ {{ $job->salary ?? '15,000-25,000' }} per month</h6>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <h5 class="mb-2">Required Skills</h5>
                            <h6 class="mb-0 text-muted">{{ $job->skills ?? 'No Skills Required' }}</h6>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <h5 class="mb-2">Description</h5>
                            <h6 class="mb-0 text-muted">{{ $job->description ?? 'No description available.' }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flex Right -->
            <div class="flex-cards">
                <div class="side-cards job-card mb-3">
                    <div class="form-div">
                        <form action="{{ route('job.apply', ['id' => $job->id]) }}" method="POST" id="form_input"
                            enctype="multipart/form-data">
                            @csrf

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0 ps-3 list-unstyled">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-sm-12 mb-2">
                                    <label for="skills">Your Skills <span>*</span></label>
                                    <textarea rows="1" name="skills" id="skills" class="form-control"
                                        required>{{ old('skills') }}</textarea>
                                </div>
                                <div class="col-sm-12 mb-2">
                                    <label for="qualification">Qualification <span>*</span></label>
                                    <select name="qualification" id="qualification" class="form-select" required>
                                        <option value="" disabled selected>Select Qualification</option>
                                        <option value="8th" {{ old('qualification') == '8th' ? 'selected' : '' }}>8th
                                        </option>
                                        <option value="10th" {{ old('qualification') == '10th' ? 'selected' : '' }}>10th
                                            (SSLC/Matriculation)</option>
                                        <option value="12th" {{ old('qualification') == '12th' ? 'selected' : '' }}>12th
                                            (HSC/PUC/Intermediate)</option>
                                        <option value="Diploma" {{ old('qualification') == 'Diploma' ? 'selected' : '' }}>
                                            Diploma</option>
                                        <option value="BE_Civil" {{ old('qualification') == 'BE_Civil' ? 'selected' : '' }}>
                                            B.E. (Civil)
                                        </option>
                                        <option value="BE" {{ old('qualification') == 'BE' ? 'selected' : '' }}>B.E.
                                            (Bachelor of Engineering)</option>
                                        <option value="BTech" {{ old('qualification') == 'BTech' ? 'selected' : '' }}>
                                            B.Tech (Bachelor of Technology)</option>
                                        <option value="BCA" {{ old('qualification') == 'BCA' ? 'selected' : '' }}>BCA
                                            (Bachelor of Computer Application)</option>
                                        <option value="BSC" {{ old('qualification') == 'BSC' ? 'selected' : '' }}>B.Sc
                                        </option>
                                        <option value="BCom" {{ old('qualification') == 'BCom' ? 'selected' : '' }}>B.Com
                                        </option>
                                        <option value="BA" {{ old('qualification') == 'BA' ? 'selected' : '' }}>BA
                                        </option>
                                        <option value="BBA" {{ old('qualification') == 'BBA' ? 'selected' : '' }}>BBA
                                        </option>
                                        <option value="ME" {{ old('qualification') == 'ME' ? 'selected' : '' }}>M.E.
                                        </option>
                                        <option value="MTech" {{ old('qualification') == 'MTech' ? 'selected' : '' }}>
                                            M.Tech</option>
                                        <option value="MCA" {{ old('qualification') == 'MCA' ? 'selected' : '' }}>MCA
                                        </option>
                                        <option value="MSC" {{ old('qualification') == 'MSC' ? 'selected' : '' }}>M.Sc
                                        </option>
                                        <option value="MCom" {{ old('qualification') == 'MCom' ? 'selected' : '' }}>M.Com
                                        </option>
                                        <option value="MA" {{ old('qualification') == 'MA' ? 'selected' : '' }}>MA
                                        </option>
                                        <option value="MBA" {{ old('qualification') == 'MBA' ? 'selected' : '' }}>MBA
                                        </option>
                                        <option value="PhD" {{ old('qualification') == 'PhD' ? 'selected' : '' }}>PhD
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="experience">Work Experience <span>*</span></label>
                                    <select name="experience" id="experience" class="form-select" required>
                                        <option value="" selected disabled>Select Work Experience</option>
                                        <option value="0" {{ old('experience') == '0' ? 'selected' : '' }}>Fresher
                                        </option>
                                        <option value="1" {{ old('experience') == '1' ? 'selected' : '' }}>1+ year
                                        </option>
                                        <option value="2" {{ old('experience') == '2' ? 'selected' : '' }}>2+ years
                                        </option>
                                        <option value="3" {{ old('experience') == '3' ? 'selected' : '' }}>3+ years
                                        </option>
                                        <option value="4" {{ old('experience') == '4' ? 'selected' : '' }}>4+ years
                                        </option>
                                        <option value="5" {{ old('experience') == '5' ? 'selected' : '' }}>5+ years
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="location">Current Location</label>
                                    <select name="location" id="location" class="form-select" required>
                                        <option value="" selected disabled>Select Location</option>
                                        @foreach ($location as $loc)
                                            <option value="{{ $loc->id }}" {{ old('location') == $loc->value ? 'selected' : '' }}>
                                                {{ $loc->value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {{-- <input type="text" name="location" id="location" class="form-control"
                                        value="{{ old('location') }}" required> --}}
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="current_salary">Current Salary <span>*</span></label>
                                    <input type="number" name="current_salary" id="current_salary" class="form-control"
                                        value="{{ old('current_salary') }}" min="0" required>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="expected_salary">Expected Salary <span>*</span></label>
                                    <input type="number" name="expected_salary" id="expected_salary" class="form-control"
                                        value="{{ old('expected_salary') }}" min="0" required>
                                </div>
                                <div class="col-sm-12 mb-2">
                                    <label for="notes">Notes For Recruitment Team</label>
                                    <input type="text" name="notes" id="notes" class="form-control"
                                        value="{{ old('notes') }}">
                                </div>
                                <div class="col-sm-12 mb-2">
                                    <label for="resume">Resume <span>*</span></label>
                                    <label class="custom-file-upload w-100" for="resume">
                                        <div class="icon mb-4">
                                            <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="60px" alt="">
                                        </div>
                                        <div class="text">
                                            <span id="file-text" class="text-center">Choose a file (DOC, PDF formats, upto
                                                15 MB)</span>
                                        </div>
                                        <input type="file" id="resume" name="resume"
                                            onchange="updateFileName('resume', 'file-text')" accept=".pdf, .doc, .docx">
                                    </label>
                                </div>
                                <div
                                    class="col-sm-12 d-flex align-items-center justify-content-sm-start justify-content-sm-end">
                                    <button type="submit" class="formbtn">Apply Job</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select 2 -->
    <script>
        $(document).ready(function () {
            let select2 = ['qualification', 'experience', 'location']
            select2.forEach(ele => {
                $(`#${ele}`).select2({
                    width: "100%",
                    placeholder: "Select Options",
                    allowClear: true,
                });
            });
        });
    </script>

    <script>
        function updateFileName(inputId, textId) {
            const input = document.getElementById(inputId);
            const textSpan = document.getElementById(textId);
            const file = input.files[0];

            // Reset default text
            textSpan.textContent = "Choose a file (DOC, PDF formats, upto 15 MB)";

            if (!file) return;

            // Validate size (15 MB)
            const maxSize = 15 * 1024 * 1024;
            if (file.size > maxSize) {
                input.value = "";
                showToast("File size must be less than 15 MB.");
                return;
            }

            // Validate file type
            const allowedExtensions = [".pdf", ".doc", ".docx"];
            const fileName = file.name.toLowerCase();
            const isValid = allowedExtensions.some(ext => fileName.endsWith(ext));

            if (!isValid) {
                input.value = "";
                showToast("Only PDF or Word documents are allowed.");
                return;
            }
            textSpan.textContent = file.name;
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('form_input');
            const submitBtn = document.querySelector('.formbtn');
            let isSubmitting = false;

            form.addEventListener('submit', function (e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }
                isSubmitting = true;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Applying...`;
            });
        });
    </script>
@endsection