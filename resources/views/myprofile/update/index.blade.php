@extends('layouts.app')

@section('title', 'Onstru | View Details')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.css') }}">

    <style>
        @media screen and (min-width: 767px) {
            .profile-grid {
                grid-template-columns: 25% 50% 25%;
            }
        }

        @media screen and (max-width: 767px) {
            .col-sm-12 {
                padding-inline: 0px !important;
            }
        }
    </style>

    <div class="container-xl main-div">
        <div class="body-head mb-3">
            <h5>Edit Details</h5>
        </div>

        <div class="profile-cards mb-2">
            <div class="container-xl">
                <div class="profile-grid">
                    <div class="img-container">
                        <img src="{{ asset(auth()->user()->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . auth()->user()->profile_img : 'assets/images/Avatar.png') }}"
                            class="avatar-150 d-flex mx-auto" alt="Profile Image">
                        <label data-bs-toggle="modal" data-bs-target="#profileImg">
                            <span><i class="fas fa-camera"></i></span>
                        </label>
                    </div>
                    <div>
                        <div class="body-head mb-2">
                            <h5 class="text-decoration-none">{{ auth()->user()->name }}</h5>
                        </div>
                        <div class="cards-content">
                            <h5 class="mb-3 text-lowercase">{{ auth()->user()->user_name ?? '-' }}</h5>
                            <h6 class="mb-3">{{ auth()->user()->bio ?? '-' }}</h6>
                            <div class="cards-grid">
                                <h5 class="mb-3">
                                    <i class="text-muted fas fa-location-dot pe-1"></i>
                                    {{ auth()->user()->user_location->value ?? '-' }}
                                </h5>
                                <h5 class="mb-3">
                                    <i class="text-muted fas fa-phone pe-1"></i> +91 {{ auth()->user()->number ?? '-' }}
                                </h5>
                                <h5 class="mb-3 text-capitalize" style="line-height: 20px;">
                                    <i class="text-muted fas fa-id-card-clip pe-1"></i>
                                    @if (auth()->user()->you_are == 'Consumer')
                                        <span>Consumer</span>
                                    @else
                                        <span>{{ implode(', ', auth()->user()->type_of_names) ?: '-' }}</span>
                                    @endif
                                </h5>
                                <h5 class="mb-3">
                                    <i class="text-muted fas fa-envelope pe-1"></i> {{ auth()->user()->email ?? '-' }}
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="need-div cards-content">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5>Basic Informations</h5>
                            <h5 class="{{ $profileCompletion == 100 ? 'green-label' : 'yellow-label' }} rounded-5">
                                {{ $profileCompletion }}%
                            </h5>
                        </div>

                        @if (Auth::user()->as_a === "Contractor" || Auth::user()->as_a === "Consultant")
                            <div class="d-flex align-items-center justify-content-between">
                                <h5>Additional Informations</h5>
                                <h5
                                    class="{{ $additionalProfileCompletion == 100 ? 'green-label' : 'yellow-label' }} rounded-5">
                                    {{ $additionalProfileCompletion }}%
                                </h5>
                            </div>

                            <div class="d-flex align-items-center justify-content-between">
                                <h5>Verification</h5>
                                <h5 class="{{ $gst_details == 100 ? 'green-label' : 'yellow-label' }} rounded-5">
                                    {{$gst_details}}%
                                </h5>
                            </div>
                        @endif

                        @if (Auth::user()->as_a === "Vendor")
                            <div class="d-flex align-items-center justify-content-between">
                                <h5>Additional Informations</h5>
                                <h5 class="{{ $vendor_details == 100 ? 'green-label' : 'yellow-label' }} rounded-5">
                                    {{ $vendor_details }}%
                                </h5>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <h5>Verification</h5>
                                <h5 class="{{ $gst_details == 100 ? 'green-label' : 'yellow-label' }} rounded-5">
                                    {{$gst_details}}%
                                </h5>
                            </div>
                        @endif

                        @if (Auth::user()->you_are == 'Professional' && Auth::user()->type_of_names[0] === 'Student')
                            <div class="d-flex align-items-center justify-content-between">
                                <h5>Additional Informations</h5>
                                <h5 class="{{ $student_completion == 100 ? 'green-label' : 'yellow-label' }} rounded-5">
                                    {{ $student_completion }}%
                                </h5>
                            </div>
                        @endif

                        @if (Auth::user()->you_are == 'Professional' && Auth::user()->type_of_names[0] === 'Working')
                            <div class="d-flex align-items-center justify-content-between">
                                <h5>Additional Informations</h5>
                                <h5 class="{{ $working_completion == 100 ? 'green-label' : 'yellow-label' }} rounded-5">
                                    {{ $working_completion }}%
                                </h5>
                            </div>
                        @endif

                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">Profile Picture</h5>
                            <h5 class="{{ Auth::user()->profile_img ? 'green-label' : 'yellow-label' }} rounded-5 mb-0">
                                {{ Auth::user()->profile_img ? '100%' : '0%' }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('profile.store') }}" method="POST" enctype="multipart/form-data" id="detailsForm">
            @csrf
            <!-- Consumer -->
            @if (auth()->user()->you_are === 'Consumer')
                @include('myprofile.update.consumer')

                <!-- Contractor -->
            @elseif (auth()->user()->as_a === 'Contractor' || auth()->user()->as_a === 'Consultant')
                @include('myprofile.update.contractor')

                <!-- Vendor -->
            @elseif (auth()->user()->as_a === 'Vendor')
                @include('myprofile.update.vendor')

                <!-- Professional -->
            @elseif (auth()->user()->you_are === 'Professional')
                @include('myprofile.update.professional')
            @endif
            <div class="col-sm-12 d-flex align-items-center justify-content-sm-start justify-content-sm-end my-3">
                <button type="submit" class="formbtn updatebtn">Update Details</button>
            </div>
        </form>

        <!-- Update Profile Image Modal -->
        <div class="modal fade" id="profileImg" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="profileImgLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Update Profile Image</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body mt-2 pb-0">
                        <form action="{{ route('update-profile-image') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="d-flex align-items-center justify-content-center mx-auto mb-2">
                                <img src="{{ asset(auth()->user()->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . auth()->user()->profile_img : 'assets/images/Avatar.png') }}"
                                    class="avatar-150" id="preview-img" alt="Profile Image">
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label for="profile_img">Profile Image <span>*</span></label>
                                <input type="file" class="form-control" name="profile_img" id="profile_img" accept="image/*"
                                    onchange="validateFile(this)">
                            </div>

                            <div class="modal-footer d-flex align-items-center justify-content-center py-2">
                                <button type="submit" class="formbtn submitbtn">Update</button>
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
            let select2 = ['typeof', 'prjtCat', 'purpose', 'servicesOff', 'location']
            select2.forEach(ele => {
                $(`#${ele}`).select2({
                    width: "100%",
                    placeholder: "Select Options",
                    allowClear: true,
                });
            });
        });
    </script>

    <!-- File Size Validation -->
    <script>
        document.getElementById("profile_img").addEventListener("change", function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById("preview-img").src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        function validateFile(input) {
            if (input.files.length === 0) return true;
            const file = input.files[0];
            if (file.size > 15 * 1024 * 1024) {
                showToast("File size must be less than 15 MB.");
                input.value = "";
                return false;
            }
            return true;
        }
    </script>

    <!-- Prevent Multiple Submissions -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('detailsForm');
            const submitBtn = document.querySelector('.updatebtn');
            let isSubmitting = false;

            form.addEventListener('submit', function (e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }
                isSubmitting = true;
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Updating...`;
            });
        });
    </script>



@endsection