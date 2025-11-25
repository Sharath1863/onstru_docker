@extends('layouts.app')

@section('title', content: 'Onstru | View Details')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">

    <style>
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backdrop-filter: blur(5px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        #loader h6 {
            font-size: 14px;
            font-weight: var(--fw-md);
            color: var(--text-primary);
        }

        #loader h6 span {
            font-size: 16px !important;
        }
    </style>

    <div class="container-xl main-div">
        <div class="body-head mb-3">
            <h5>Details</h5>
        </div>

        <div class="profile-cards mb-2">
            <div class="container-xl px-sm-0 px-md-2">
                <div class="profile-grid">
                    <a href="{{ asset(auth()->user()->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . auth()->user()->profile_img : 'assets/images/Avatar.png') }}"
                        data-fancybox="profileImage">
                        <div class="avatar-150 position-relative d-flex mx-auto">
                            <img src="{{ asset(auth()->user()->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . auth()->user()->profile_img : 'assets/images/Avatar.png') }}"
                                class="avatar-150 d-flex mx-auto" alt="">
                            @if (auth()->user()->badge != 0 && auth()->user()->badge != null)
                                <img src="{{ asset('assets/images/Badge_' . auth()->user()->badge . '.png') }}"
                                    class="badge-150" alt="">
                            @endif
                        </div>
                    </a>
                    <div>
                        <div class="body-head mb-2">
                            <h5 class="text-decoration-none">{{ auth()->user()->name }}</h5>
                            <div>
                                @if ($gstverified == 'no')
                                    @if (auth()->user()->as_a === 'Contractor' || auth()->user()->as_a === 'Vendor' || auth()->user()->as_a === 'Consultant')
                                        <a data-bs-toggle="modal" data-bs-target="#addGST">
                                            <button class="removebtn">+ Add GST</button>
                                        </a>
                                    @endif
                                @endif
                                <a href="{{ url('edit-profile') }}">
                                    <button class="listbtn">
                                        <i class="fas fa-pen-to-square pe-1"></i> Update Details
                                    </button>
                                </a>
                            </div>
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
                </div>
            </div>
        </div>

        <!-- Contractor -->
        @if (auth()->user()->as_a === 'Contractor' || auth()->user()->as_a === 'Consultant')
            @include('myprofile.details.contractor')

            <!-- Vendor -->
        @elseif (auth()->user()->as_a === 'Vendor')
            @include('myprofile.details.vendor')

            <!-- Professional -->
        @elseif (auth()->user()->you_are === 'Professional')
            @include('myprofile.details.professional')
        @endif

        <!-- Verify GST -->
        <div class="modal modal-sm fade" id="addGST" tabindex="-1" aria-labelledby="addGSTLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="m-0">Add GST</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex align-items-center justify-content-center my-2">
                            <img src="{{ asset('assets/images/Image_GST.png') }}" height="175px" class="d-flex mx-auto"
                                alt="">
                        </div>
                        <form action="{{ route('gst.add') }}" method="POST" id="gstForm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12 col-md-12 mb-2">
                                    <label for="addgst">GST Number</label>
                                    <input type="text" class="form-control text-uppercase" name="gst_no" id="gst_no">
                                </div>
                                <div class="col-sm-12">
                                    <label>Notes <span>*</span></label>
                                    <h6 class="mb-0">Once your GST details are verified and submitted, the edit option will
                                        no longer be available — ensuring data authenticity and compliance.</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-center mt-2">
                                <button type="submit" class="formbtn w-100" data-bs-toggle="modal"
                                    data-bs-target="#addGSTDetails">Verify</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="loader" style="display:none;">
            <img src="{{ asset('assets/images/Favicon.png') }}" height="50px" class="mb-2" alt="">
            <h6 class="mb-0">Fetching your GST Details. Please wait...</h6>
        </div>

        <!-- Add GST Details -->
        <div class="modal fade" id="addGSTDetails" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="addGSTDetailsLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="m-0">Add GST Details</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('gst_store') }}" method="POST" id="gstDetailsForm">
                            @csrf
                            <div class="row mt-2">
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="addgstno">GST Number</label>
                                    <input type="text" class="form-control" name="gst_number" id="gst_number" readonly>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="addname">Name <span>*</span></label>
                                    <select class="form-select" name="name" id="name" required>
                                        <option value="" selected disabled>Select Name</option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="addbusinessname">Business Legal Name <span>*</span></label>
                                    <input type="text" class="form-control" name="business_legal" id="business_legal"
                                        readonly required>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="addcontact">Contact Number <span>*</span></label>
                                    <input type="number" class="form-control" name="contact_no" id="contact_no"
                                        oninput="validate_contact(this)" readonly required>
                                    <!-- min="6000000000" max="9999999999"-->
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="addemail">Email ID <span>*</span></label>
                                    <input type="email" class="form-control" name="email_id" id="email_id" readonly
                                        required>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="addpan">PAN Number <span>*</span></label>
                                    <input type="text" class="form-control" name="pan_no" id="pan_no" readonly required>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="adddor">Date Of Registration <span>*</span></label>
                                    <input type="date" class="form-control" name="register_date" id="register_date" readonly
                                        required>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="addaddress">Address <span>*</span></label>
                                    <select class="form-select" name="gstaddress" id="gstaddress" required>
                                        <option value="" selected disabled>Select Name</option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="addnature">Nature Of Business <span>*</span></label>
                                    <input type="text" class="form-control" name="nature_business" id="nature_business"
                                        readonly required>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="addturnover">Annual Turnover <span>*</span></label>
                                    <input type="text" class="form-control" name="annual_turnover" id="annual_turnover"
                                        readonly required>
                                </div>
                                <div class="col-sm-12">
                                    <label>Notes <span>*</span></label>
                                    <h6 class="mb-0">Once your GST details are verified and submitted, the edit option will
                                        no longer be available — ensuring data authenticity and compliance.</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-center mt-2">
                                <button type="submit" class="formbtn">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#gstForm').on('submit', function (e) {
                e.preventDefault();

                let gstNumber = $('#gst_no').val();
                // let token = $('input[name="_token"]').val();

                $.ajax({
                    url: "{{ route('gst.add') }}",
                    type: 'POST',
                    data: {
                        gst_no: gstNumber,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function () {
                        $('#loader').show();
                        $('#loader').addClass('d-flex');
                    },
                    success: function (response) {
                        if (response.success) {
                            const details = response.data[0]?.result?.details ?? {};
                            const principal = details.contact_details?.principal ?? {};
                            const promoters = details.promoters ?? [];
                            const address = details.promoters ?? [];
                            const result = response.data[0]?.result;
                            const email = result?.email ?? '';
                            const mobile = result?.mobile ?? '';
                            // Extract values safely
                            const gstin = details.gstin ?? '';
                            const legalName = details.legal_name ?? '';
                            const panNumber = details.pan_number ?? '';
                            const turnover = details.annual_turnover ?? '';
                            const regdate = details.date_of_registration ?? '';
                            const natureOfBusiness = principal.nature_of_business ?? '';
                            // Populate fields
                            $('#gst_number').val(gstin);
                            $('#business_legal').val(legalName);
                            $('#pan_no').val(panNumber);
                            $('#annual_turnover').val(turnover);
                            $('#register_date').val(regdate);
                            $('#nature_business').val(natureOfBusiness);
                            $('#email_id').val(email);
                            $('#contact_no').val(mobile);

                            // Populate promoter names in dropdown
                            const $nameDropdown = $('#name');
                            $nameDropdown.empty().append(
                                '<option value="" disabled selected>Select Name</option>');
                            promoters.forEach(name => {
                                $nameDropdown.append(
                                    `<option value="${name.trim()}">${name.trim()}</option>`
                                );
                            });

                            const contactDetails = details.contact_details ?? {};
                            const principaladd = contactDetails.principal ? [contactDetails
                                .principal
                            ] : [];
                            const additional = contactDetails.additional ?? [];
                            const allAddresses = [...principaladd, ...additional];
                            const $addressDropdown = $('#gstaddress');

                            $addressDropdown.empty().append(
                                '<option value="" disabled selected>Select Address</option>'
                            );

                            allAddresses.forEach((entry, index) => {
                                const formatted =
                                    `${entry.address}, ${entry.nature_of_business}`;
                                $addressDropdown.append(
                                    `<option value="${formatted}">${formatted}</option>`
                                );
                            });

                            // Switch modals
                            $('#addGST').modal('hide');
                            $('#addGSTDetails').modal('show');

                        } else {
                            alert('Failed: ' + response.message);
                        }
                    },

                    error: function (xhr) {
                        showToast('Something went wrong! Please try again.');
                    },
                    complete: function () {
                        $('#loader').hide();
                        $('#loader').removeClass('d-flex');
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const boostForm = document.querySelector('#gstDetailsForm form');
            const submitBtn = boostForm.querySelector('.formbtn');
            boostForm.addEventListener('submit', function (e) {
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    'Submiting... <span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>';
            });
        });
    </script>

@endsection