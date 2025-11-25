@extends('layouts.app')

@section('title', 'Onstru | Checkout')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">

    <style>
        @media screen and (min-width: 767px) {
            .header-section {
                width: 60%;
                display: grid;
                grid-template-columns: repeat(3, 32%);
                align-items: center;
                justify-content: space-between;
                margin-inline: auto;
            }
        }

        @media screen and (max-width: 767px) {
            .header-section {
                width: 100%;
                display: grid;
                grid-template-columns: repeat(1, 1fr);
                align-items: center;
                justify-content: start;
            }
        }

        .pac-container {
            z-index: 1099 !important;
        }
    </style>

    <div class="container-xl main-div">
        <div class="body-head">
            <a href="javascript:history.back()">
                <h5><i class="fas fa-angle-left pe-1"></i> Back</h5>
            </a>
        </div>
        <div class="body-head header-section my-4">
            <div class="mb-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_1.png') }}" height="30px" alt="">
                    <h6 class="mb-0">Address</h6>
                </div>
                <div class="line bg-warning mt-2"></div>
            </div>
            <div class="mb-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_2.png') }}" height="30px" alt="">
                    <h6 class="mb-0">Order Summary</h6>
                </div>
                <div class="line bg-secondary mt-2"></div>
            </div>
            <div class="mb-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/icon_3.png') }}" height="30px" alt="">
                    <h6 class="mb-0">Payment</h6>
                </div>
                <div class="line bg-secondary mt-2"></div>
            </div>
        </div>

        <form method="POST" action="{{ route('address.store') }}" id="form_input">
            @csrf
            <section class="row my-3">
                <div class="col-sm-12 col-md-6 mb-2">
                    <div class="border form-div p-3 rounded-2">
                        <div class="body-head mb-3">
                            <h5>Contact Information</h5>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="first_name">First Name <span>*</span></label>
                                <input type="text" id="first_name" name="first_name" class="form-control"
                                    value="{{ auth()->user()->name }}" required autofocus>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" class="form-control">
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="primary_phone">Primary Phone Number <span>*</span></label>
                                <input type="number" id="primary_phone" name="primary_phone" class="form-control"
                                    min="6000000000" max="9999999999" value="{{ auth()->user()->number }}"
                                    oninput="validate_contact(this)" required readonly>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="secondary_phone">Secondary Phone Number</label>
                                <input type="number" id="secondary_phone" name="secondary_phone" class="form-control"
                                    min="6000000000" max="9999999999" oninput="validate_contact(this)">
                            </div>
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label for="gst_billing">Want GST Billing <span>*</span></label>
                                <select id="gst_billing" name="gst_billing" class="form-select" required>
                                    <option value="" selected disabled>Select Yes or No</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-12 mb-2 d-none" id="gst_div">
                                <label for="gst_no">Billing GST Number <span>*</span></label>
                                <div class="input-group">
                                    <input type="text" id="gst_no" class="form-control" name="billing_gst"
                                        placeholder="Enter GST Number">
                                    <button type="button" id="verifyGstBtn" class="formbtn">Verify</button>
                                </div>
                                <small id="gstStatus" class="mt-2 d-block"></small>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-12 d-md-flex align-items-center justify-content-between gap-2 mt-3">
                        <button type="button" class="locbtn w-100" id="useMyLocation">
                            <i class="fas fa-location pe-1"></i> Use My Location
                        </button>
                        <button type="button" class="darkbtn w-100" data-bs-toggle="offcanvas" data-bs-target="#mapModal">
                            <i class="fas fa-location-dot pe-1"></i> Pin My Location
                        </button>
                    </div>
                </div>

                <div class="col-sm-12 col-md-6">
                    <div class="border form-div p-3 rounded-2">

                        <div class="body-head mb-3">
                            <h5>Shipping Address</h5>
                            <button type="button" class="listbtn" data-bs-toggle="modal"
                                data-bs-target="#addressModal">Previous Location</button>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="ship_gst_no">Shipping GST Number (Optional)</label>
                                <input type="text" name="shipping_gst" id="shipping_gst" class="form-control">
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="shipping_address">Address <span>*</span></label>
                                <input type="text" name="shipping_address" id="shipping_address" class="form-control"
                                    required>
                            </div>
                            <div class="col-sm-12 col-md-4 mb-3">
                                <label for="shipping_city">Town/City <span>*</span></label>
                                <input type="text" name="shipping_city" id="shipping_city" class="form-control" required
                                    readonly>
                            </div>
                            <div class="col-sm-12 col-md-4 mb-3">
                                <label for="shipping_state">State <span>*</span></label>
                                <input type="text" name="shipping_state" id="shipping_state" class="form-control" required
                                    readonly>
                            </div>
                            <div class="col-sm-12 col-md-4 mb-3">
                                <label for="shipping_pincode">Pin Code <span>*</span></label>
                                <input type="text" name="shipping_pincode" id="shipping_pincode" class="form-control"
                                    oninput="validate_pincode(this)" required readonly onchange="check_pincode(this)">
                                <input type="hidden" id="session_pincode" value="{{ session('pincode') }}">
                            </div>
                            <div class="col-sm-12">
                                <small id="pincodeError" class="text-danger d-none">Your pincode doesn't match with the
                                    one used on the cart page. Please use the correct pincode or update it in the
                                    cart.</small>
                            </div>

                        </div>


                        <div class="body-head mb-3">
                            <h5>Billing Address</h5>
                            <div class="d-flex align-items-center gap-1">
                                <input type="checkbox" name="same_as_shipping" id="same_as_shipping" value="1">
                                <label for="same_as_shipping" class="mb-0">Same as Billing address</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label for="billing_address">Address <span>*</span></label>
                                <input type="text" name="billing_address" id="billing_address" class="form-control"
                                    required>
                            </div>
                            <div class="col-sm-12 col-md-4 mb-3">
                                <label for="billing_city">Town/City <span>*</span></label>
                                <input type="text" name="billing_city" id="billing_city" class="form-control" required>
                            </div>
                            <div class="col-sm-12 col-md-4 mb-3">
                                <label for="billing_state">State <span>*</span></label>
                                <input type="text" name="billing_state" id="billing_state" class="form-control" required>
                            </div>
                            <div class="col-sm-12 col-md-4 mb-3">
                                <label for="billing_pincode">Pin Code <span>*</span></label>
                                <input type="text" name="billing_pincode" id="billing_pincode" class="form-control"
                                    oninput="validate_pincode(this)" required>
                            </div>
                        </div>

                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">

                        <div class="col-sm-12 d-flex justify-content-sm-start justify-content-md-end">
                            <button type="submit" class="formbtn" id="checkoutBtn">Continue</button>
                        </div>
                    </div>
                </div>
            </section>
        </form>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addressModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="height: 50vh;">
                <div class="modal-header">
                    <h4 class="m-0">Choose Address</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body mt-2">
                    <div class="saved-addresses">
                        @if ($address->isEmpty())
                            <p>No addresses found.</p>
                        @else
                            @foreach ($address as $myadrs)
                            <div class="card modal-user mb-2 p-3">
                                <h4 class="mb-2">{{ $myadrs->first_name }} {{ $myadrs->last_name }}</h4>
                                <h5 class="mb-1">{{ $myadrs->primary_phone }}</h5>
                                <h5 class="mb-1">{{ $myadrs->billing_address }}, {{ $myadrs->billing_city }} -
                                    {{ $myadrs->billing_pincode }}
                                </h5>
                                <h5 class="mb-2"><em>Shipping:</em> {{ $myadrs->shipping_address }},
                                    {{ $myadrs->shipping_city }} -
                                    {{ $myadrs->shipping_pincode }}
                                </h5>
                                <button type="button" class="formbtn use-address-btn" data-bs-dismiss="modal"
                                    data-id="{{ $myadrs->id }}">
                                    Use this address
                                </button>
                            </div>
                        @endforeach
                        @endif
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" style="width: 50%;" tabindex="-1" id="mapModal" aria-labelledby="offcanvasLabel">
        <div class="offcanvas-body position-relative">
            <div class="offcanvas-header d-flex align-items-center justify-content-between p-0 mb-2">
                <h4>Pin Your Location</h4>
                <button type="button" id="confirmLocation" class="formbtn" data-bs-dismiss="offcanvas">
                    Confirm Location
                </button>
            </div>
            <input type="text" class="form-control" id="locInput"
                style="position: absolute; top: 110px; left: 25px; z-index: 1099; width: 70%;"
                placeholder="Search for a location">
            <div id="map" style="width: 100%; height: 90vh;"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function check_pincode(input) {
            const enteredPincode = input.value;
            const sessionPincode = document.getElementById("session_pincode").value;

            // Simple validation example
            if (enteredPincode !== sessionPincode) {
                input.classList.remove("is-valid");
                input.classList.add("is-invalid");
                showToast("Shipping pincode does not match the pincode which you entered in the cart page.");
            } else {
                // If valid, remove any error classes
                input.classList.remove("is-invalid");
                input.classList.add("is-valid");
            }
        }
    </script>

    <script>
        $(document).ready(function () {
            $('#gst_billing').on('change', function () {
                $('#gst_no').prop('required', false);
                if ($(this).val() === 'yes') {
                    $('#gst_div').removeClass('d-none');
                    $('#gst_no').prop('required', true);
                } else {
                    $('#gst_div').addClass('d-none');
                    $('#gst_no').prop('required', false);
                }
            });
        });
    </script>

    <script>
        document.getElementById('same_as_shipping').addEventListener('change', function () {
            const isChecked = this.checked;
            const shipping = {
                address: document.getElementById('shipping_address').value,
                pincode: document.getElementById('shipping_pincode').value,
                city: document.getElementById('shipping_city').value,
                state: document.getElementById('shipping_state').value
            };
            const billing = {
                address: document.getElementById('billing_address'),
                pincode: document.getElementById('billing_pincode'),
                city: document.getElementById('billing_city'),
                state: document.getElementById('billing_state')
            };
            if (isChecked) {
                billing.address.value = shipping.address;
                billing.pincode.value = shipping.pincode;
                billing.city.value = shipping.city;
                billing.state.value = shipping.state;
            } else {
                billing.address.value = '';
                billing.pincode.value = '';
                billing.city.value = '';
                billing.state.value = '';
            }
        });

        document.getElementById('useMyLocation').addEventListener('click', function (e) {
            e.preventDefault();
            if (!navigator.geolocation) {
                return showToast('Geolocation is not supported by this browser.');
            }
            navigator.geolocation.getCurrentPosition(success, error);
            function success(position) {
                const {
                    latitude,
                    longitude
                } = position.coords;
                const apiKey = "AIzaSyAmDotOMSahWwA7WAjmtremPvNNRN1Nye0";
                const url =
                    `https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&key=${apiKey}`;
                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === "OK" && data.results.length > 0) {
                            const result = data.results[0];
                            const addressComponents = result.address_components;
                            const formattedAddress = result.formatted_address;
                            const getComponent = (types) => {
                                const component = addressComponents.find(comp =>
                                    types.every(type => comp.types.includes(type))
                                );
                                return component ? component.long_name : '';
                            };
                            const pincode = getComponent(['postal_code']);
                            const city = getComponent(['locality']) ||
                                getComponent(['administrative_area_level_2']) ||
                                getComponent(['sublocality']) ||
                                getComponent(['postal_town']);
                            console.log(formattedAddress);
                            const state = getComponent(['administrative_area_level_1']);
                            document.getElementById('shipping_address').value = formattedAddress || '';
                            document.getElementById('shipping_pincode').value = pincode;
                            document.getElementById('shipping_city').value = city;
                            document.getElementById('shipping_state').value = state;

                            console.log("City:", city, "State:", state, "Pincode:", pincode);
                        } else {
                            showToast('No results found for your location.');
                        }
                    })
                    .catch(err => {
                        console.error('Error during reverse geocoding:', err);
                        showToast('Failed to get address from location.');
                    });
            }
            function error(err) {
                console.warn(`ERROR(${err.code}): ${err.message}`);
                showToast('Unable to retrieve your location.');
            }
        });

        document.getElementById('confirmLocation').addEventListener('click', function () {
            if (!selectedLatLng) {
                showToast('Please pin your location on the map.');
                return;
            }
            const {
                lat,
                lng
            } = selectedLatLng;
            const apiKey = "AIzaSyAmDotOMSahWwA7WAjmtremPvNNRN1Nye0";
            const url = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${apiKey}`;
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data.status === "OK" && data.results.length > 0) {
                        const result = data.results[0];
                        const addressComponents = result.address_components;
                        const formattedAddress = result.formatted_address;
                        const getComponent = (types) => {
                            const component = addressComponents.find(comp =>
                                types.every(type => comp.types.includes(type))
                            );
                            return component ? component.long_name : '';
                        };
                        const pincode = getComponent(['postal_code']);
                        const city = getComponent(['locality']) ||
                            getComponent(['administrative_area_level_2']) ||
                            getComponent(['sublocality']) ||
                            getComponent(['postal_town']);
                        console.log();
                        const state = getComponent(['administrative_area_level_1']);
                        // Update form fields
                        document.getElementById('shipping_address').value = formattedAddress || '';
                        document.getElementById('shipping_pincode').value = pincode;
                        document.getElementById('shipping_pincode').dispatchEvent(new Event('change'));
                        document.getElementById('shipping_city').value = city;
                        document.getElementById('shipping_state').value = state;
                        // Update hidden latitude/longitude inputs if you added them
                        if (document.getElementById('latitude')) {
                            document.getElementById('latitude').value = lat;
                        }
                        if (document.getElementById('longitude')) {
                            document.getElementById('longitude').value = lng;
                        }
                    } else {
                        showToast('No results found for the pinned location.');
                    }
                })
                .catch(err => {
                    console.error('Error during reverse geocoding:', err);
                    showToast('Failed to get address from pinned location.');
                });
        });

        document.querySelectorAll('.use-address-btn').forEach(button => {
            button.addEventListener('click', function () {
                let addressId = this.dataset.id;
                //  fetch(`/address/${addressId}`)
                const url = `{{ route('address.get', ['id' => '__id__']) }}`.replace('__id__', addressId);

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        // Fill contact fields
                        document.querySelector('[name="first_name"]').value = data.first_name || '';
                        document.querySelector('[name="last_name"]').value = data.last_name || '';
                        document.querySelector('[name="primary_phone"]').value = data.primary_phone ||
                            '';
                        document.querySelector('[name="secondary_phone"]').value = data
                            .secondary_phone || '';
                        document.querySelector('[name="gst_billing"]').value = data.gst_billing || '';
                        // Fill billing fields
                        document.querySelector('[name="billing_address"]').value = data
                            .billing_address || '';
                        document.querySelector('[name="billing_pincode"]').value = data
                            .billing_pincode || '';
                        document.querySelector('[name="billing_city"]').value = data.billing_city || '';
                        document.querySelector('[name="billing_state"]').value = data.billing_state ||
                            '';
                        // Fill shipping fields
                        document.querySelector('[name="shipping_address"]').value = data
                            .shipping_address || '';
                        document.querySelector('[name="shipping_pincode"]').value = data
                            .shipping_pincode || '';
                        document.querySelector('[name="shipping_city"]').value = data.shipping_city ||
                            '';
                        document.querySelector('[name="shipping_state"]').value = data.shipping_state ||
                            '';
                        document.querySelector('[name="latitude"]').value = data.latitude ||
                            '';
                        document.querySelector('[name="longitude"]').value = data.longitude ||
                            '';
                        // Optional: Scroll to form
                        document.querySelector('form').scrollIntoView({
                            behavior: 'smooth'
                        });
                    })
                    .catch(err => {
                        console.error('Error fetching address:', err);
                        showToast('Unable to load address.');
                    });
            });
        });
    </script>

    <script>
        document.getElementById('verifyGstBtn').addEventListener('click', function () {
            const gstNo = document.getElementById('gst_no').value.trim();
            const msg = document.getElementById('gstStatus');
            const verifyBtn = document.getElementById('verifyGstBtn');
            verifyBtn.textContent = 'Verifying...';
            verifyBtn.disabled = true;
            if (!gstNo) {
                msg.textContent = 'Please enter GST number';
                verifyBtn.disabled = false;
                return;
            }
            fetch("{{ route('gst.verify') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    gst_no: gstNo
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data && data.data.details) {
                        verifyBtn.textContent = 'Verified';
                        verifyBtn.style.backgroundColor = '#28a745';
                        localStorage.setItem('verifiedGstNo', gstNo);
                    } else {
                        msg.textContent = 'Verification failed. Please try again.';
                        verifyBtn.textContent = 'Verify';
                        verifyBtn.style.backgroundColor = '';
                        verifyBtn.disabled = false;
                        localStorage.removeItem('verifiedGstNo');
                    }
                })
                .catch(error => {
                    msg.textContent = 'Something went wrong! Please try again later.';
                    verifyBtn.textContent = 'Verify';
                    verifyBtn.style.backgroundColor = '';
                    verifyBtn.disabled = false;
                    localStorage.removeItem('verifiedGstNo');
                });
        });

        // Form submit logic with pincode validation and GST check
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('form_input');
            const submitBtn = document.querySelector('#checkoutBtn');
            const sessionPincode = document.getElementById('session_pincode')?.value;
            const shippingPincode = document.getElementById('shipping_pincode');
            const billingPincode = document.getElementById('billing_pincode');
            const gstBillingSelect = document.getElementById('gst_billing');
            const gstStatus = document.getElementById('gstStatus');
            let isSubmitting = false;
            if (!form || !submitBtn || !shippingPincode || !billingPincode || !gstBillingSelect) return;
            form.addEventListener('submit', function (e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }
                const shippingPincodeVal = shippingPincode.value;
                // Validate pincode
                if (sessionPincode && shippingPincodeVal && sessionPincode !== shippingPincodeVal) {
                    e.preventDefault();
                    showToast(
                        "Your pincode doesn't match with the one used on the cart page. Please use the correct pincode or update it in the cart."
                    );
                    shippingPincode.classList.add("is-invalid");
                    shippingPincode.focus();
                    return;
                }
                // Check if GST Billing is selected as 'Yes'
                if (gstBillingSelect.value === "yes") {
                    const enteredGst = document.getElementById('gst_no').value.trim();
                    const verifiedGst = localStorage.getItem('verifiedGstNo');

                    if (!enteredGst) {
                        e.preventDefault();
                        showToast("Please enter a GST number.");
                        return;
                    }

                    if (enteredGst !== verifiedGst) {
                        e.preventDefault();
                        showToast("Please verify the GST number before submitting.");
                        return;
                    }
                }
                // Proceed with the form submission
                isSubmitting = true;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Submitting...`;
            });
            // Reset shipping pincode validation on input
            shippingPincode.addEventListener("input", function () {
                shippingPincode.classList.remove("is-invalid");
            });
        });
    </script>

    @section('scripts')

        <script>
            let map, marker, selectedLatLng;
            window.initMap = function () {
                const defaultLocation = {
                    lat: 13.0827,
                    lng: 80.2707
                };
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const userLocation = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            };
                            initializeMap(userLocation);
                        },
                        (error) => {
                            console.warn("Geolocation failed or denied. Using default.");
                            initializeMap(defaultLocation);
                        }
                    );
                } else {
                    console.warn("Geolocation not supported. Using default.");
                    initializeMap(defaultLocation);
                }
            };
            function initializeMap(location) {
                selectedLatLng = null;
                map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 15,
                    center: location,
                });
                marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    draggable: true,
                });
                // Autocomplete search
                const input = document.getElementById("locInput");
                const autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.bindTo("bounds", map);
                autocomplete.addListener("place_changed", function () {
                    const place = autocomplete.getPlace();
                    if (!place.geometry || !place.geometry.location) {
                        showToast("No details available for this location.");
                        return;
                    }
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                    marker.setPosition(place.geometry.location);
                    selectedLatLng = {
                        lat: place.geometry.location.lat(),
                        lng: place.geometry.location.lng()
                    };
                });
                // Update on marker drag
                google.maps.event.addListener(marker, "dragend", function (event) {
                    selectedLatLng = {
                        lat: event.latLng.lat(),
                        lng: event.latLng.lng()
                    };
                });
                // Update on map click
                map.addListener("click", function (event) {
                    marker.setPosition(event.latLng);
                    selectedLatLng = {
                        lat: event.latLng.lat(),
                        lng: event.latLng.lng()
                    };
                });
            }
        </script>

        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAmDotOMSahWwA7WAjmtremPvNNRN1Nye0&libraries=places&callback=initMap"></script>

    @endsection

@endsection