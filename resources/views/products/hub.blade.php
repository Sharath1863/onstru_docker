<!-- Add Hub Modal -->
<div class="modal fade" id="addHub" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addHubLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Add Hub Location</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('hubs.store') }}" enctype="multipart/form-data" id="hubForm">
                    @csrf
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    {{-- <input type="hidden" name="place_id" id="place_id">
                    <input type="hidden" name="address_components" id="address_components"> --}}
                    <select name="locations" id="locations" class="d-none">
                        <option value="" disabled selected></option>
                        @foreach ($locations as $id => $loc)
                            <option value="{{ $loc->id }}">{{ $loc->value }}</option>
                        @endforeach
                    </select>

                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="add_hubname">Hub Name <span>*</span></label>
                            <input type="text" name="hubname" id="add_hubname" class="form-control" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="add_address">Address <span>*</span></label>
                            <textarea rows="1" name="address" id="add_address" cols="30" class="form-control"
                                required></textarea>
                            {{-- <input type="text" name="address" id="add_address" class="form-control" required> --}}
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="add_city">Town/City <span>*</span></label>
                            <input type="text" name="city" id="add_city" class="form-control" readonly>
                            <small id="mismatchedlocation" style="display: none">Reselect the location for
                                perfection</small>
                            <input type="hidden" name="location_id" id="location_id" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="add_state">State <span>*</span></label>
                            <input type="text" name="state" id="add_state" class="form-control" readonly>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="add_pincode">Pin Code <span>*</span></label>
                            <input type="text" name="pincode" id="add_pincode" class="form-control"
                                oninput="validate_pincode(this)" readonly>
                        </div>
                        <div class="col-sm-12 d-md-flex align-items-center justify-content-between gap-2 mt-2">
                            <button type="button" class="locbtn w-100 d-none" id="add_useMyLocation">
                                <i class="fas fa-location pe-1"></i> Use My Location
                            </button>
                            <button type="button" class="removebtn w-100" id="pinlocation" data-bs-toggle="offcanvas"
                                data-bs-target="#mapModal">
                                <i class="fas fa-location-dot pe-1"></i> Pin My Location
                            </button>
                            <button type="submit" class="formbtn w-100 addhub">Add Hub</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-start" tabindex="-1" id="mapModal" aria-labelledby="offcanvasLabel">
    <div class="offcanvas-body position-relative">
        <div class="body-head mb-2">
            <h5 class="mb-0">Pin Your Location</h5>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('hubForm');
        const submitBtn = document.querySelector('.addhub');
        let isSubmitting = false;
        form.addEventListener('submit', function (e) {
            const locationId = document.getElementById('location_id').value.trim();
            if (!locationId) {
                e.preventDefault();
                document.getElementById('mismatchedlocation').style.display = 'block';
                return;
            }
            if (isSubmitting) {
                e.preventDefault();
                return;
            }
            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Submitting...`;
        });
    });

    document.getElementById('pinlocation').addEventListener('click', function () {
        var addHubModal = bootstrap.Modal.getInstance(document.getElementById('addHub'))
            || new bootstrap.Modal(document.getElementById('addHub'));
        addHubModal.hide();
    });
</script>

<script>
    // GLOBAL VARIABLES
    let selectedLatLng = null;
    let map, marker, autocomplete;
    let currentPrefix = null;
    // GOOGLE MAP INIT
    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: {
                lat: 11.1271,
                lng: 78.6569
            }, // Tamil Nadu center
            zoom: 7,
        });
        // Initialize the Autocomplete
        const input = document.getElementById("locInput");
        autocomplete = new google.maps.places.Autocomplete(input, {
            types: ["geocode"], // Restrict results to addresses only
        });
        autocomplete.addListener("place_changed", onPlaceChanged);
        // Add listener for click events on the map
        map.addListener("click", (e) => {
            if (marker) marker.setMap(null); // Remove previous marker
            marker = new google.maps.Marker({
                position: e.latLng,
                map: map,
                draggable: true
            });
            selectedLatLng = {
                lat: e.latLng.lat(),
                lng: e.latLng.lng()
            };
            marker.addListener("dragend", (event) => {
                selectedLatLng = {
                    lat: event.latLng.lat(),
                    lng: event.latLng.lng()
                };
            });
        });
    }

    // When a place is selected from the Autocomplete dropdown
    function onPlaceChanged() {
        const place = autocomplete.getPlace();
        if (!place.geometry) {
            return;
        }
        // Set the map to the selected place
        map.setCenter(place.geometry.location);
        map.setZoom(15); // Zoom to the selected location
        // Add a marker at the selected location
        if (marker) marker.setMap(null); // Remove previous marker
        marker = new google.maps.Marker({
            position: place.geometry.location,
            map: map,
            draggable: true
        });
        selectedLatLng = {
            lat: place.geometry.location.lat(),
            lng: place.geometry.location.lng()
        };
        marker.addListener("dragend", (event) => {
            selectedLatLng = {
                lat: event.latLng.lat(),
                lng: event.latLng.lng()
            };
        });
        // Fill the address fields with the selected location
        const formattedAddress = place.formatted_address;
        const city = place.address_components ? place.address_components.find(comp => comp.types.includes("locality"))
            ?.long_name : "";
        const state = place.address_components ? place.address_components.find(comp => comp.types.includes(
            "administrative_area_level_1"))?.long_name : "";
        const pincode = place.address_components ? place.address_components.find(comp => comp.types.includes(
            "postal_code"))?.long_name : "";
        fillAddressFields(currentPrefix, formattedAddress, pincode, city, state, selectedLatLng.lat, selectedLatLng
            .lng);
    }

    // FILL FORM FIELDS
    function fillAddressFields(prefix, formattedAddress, pincode, city, state, lat = null, lng = null) {
        document.getElementById(prefix + '_address').value = formattedAddress || '';
        document.getElementById(prefix + '_pincode').value = pincode || '';
        document.getElementById(prefix + '_city').value = city || '';
        document.getElementById(prefix + '_state').value = state || '';
        if (document.getElementById('latitude')) {
            document.getElementById('latitude').value = lat || '';
        }
        if (document.getElementById('longitude')) {
            document.getElementById('longitude').value = lng || '';
        }

        const normalizedCity = city?.trim().toLowerCase();
        const locationSelect = document.getElementById('locations');
        let matched = false;
        if (locationSelect) {
            for (let i = 0; i < locationSelect.options.length; i++) {
                const opt = locationSelect.options[i];
                const optionText = opt.text.trim().toLowerCase();
                if (optionText === normalizedCity) {
                    opt.selected = true;
                    matched = true;
                    document.getElementById('location_id').value = opt.value; // Store the id here
                    break;
                }
            }
            if (!matched) {
                locationSelect.value = '';
                document.getElementById('mismatchedlocation').style.display = 'block';
                document.getElementById('location_id').value = ''; // Reset id if no match
            }
        }
    }

    // CONFIRM PINNED LOCATION
    document.getElementById('confirmLocation').addEventListener('click', function () {
        if (!selectedLatLng) {
            showToast('Please pin your location on the map.');
            return;
        }
        const {
            lat,
            lng
        } = selectedLatLng;
        reverseGeocode(lat, lng, currentPrefix);
        // Close map modal before showing target modal
        const mapModal = bootstrap.Modal.getInstance(document.getElementById('mapModal'));
        if (mapModal) mapModal.hide();
        // Show correct modal
        if (currentPrefix === 'add') {
            const addModal = new bootstrap.Modal(document.getElementById('addHub'));
            addModal.show();
        }
    });

    // REVERSE GEOCODING
    function reverseGeocode(lat, lng, prefix) {
        const apiKey = "AIzaSyAmDotOMSahWwA7WAjmtremPvNNRN1Nye0";
        const url = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${apiKey}&language=en`;
        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.status === "OK" && data.results.length > 0) {
                    document.getElementById('mismatchedlocation').style.display = 'none';
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
                    const district = getComponent(['administrative_area_level_2']) ||
                        getComponent(['administrative_area_level_3']);
                    const state = getComponent(['administrative_area_level_1']);
                    fillAddressFields(prefix, formattedAddress, pincode, district, state, lat, lng);
                    const normalizedDistrict = district?.trim().toLowerCase();
                    const locationSelect = document.getElementById('locations');
                    let matched = false;
                    if (locationSelect) {
                        for (let i = 0; i < locationSelect.options.length; i++) {
                            const opt = locationSelect.options[i];
                            const optionText = opt.text.trim().toLowerCase();
                            if (optionText === normalizedDistrict) {
                                opt.selected = true;
                                matched = true;
                                document.getElementById('location_id').value = opt.value;
                                break;
                            }
                        }
                        if (!matched) {
                            locationSelect.value = '';
                            document.getElementById('mismatchedlocation').style.display = 'block';
                            document.getElementById('location_id').value = '';
                        }
                    }
                } else {
                    showToast('No results found for this location.');
                }
            })
            .catch(err => {
                document.getElementById('mismatchedlocation').style.display = 'block';
            });
    }

    // USE MY LOCATION
    function useMyLocation(prefix) {
        if (!navigator.geolocation) {
            return showToast('Geolocation is not supported by this browser.');
        }
        navigator.geolocation.getCurrentPosition(success, error);
        function success(position) {
            const {
                latitude,
                longitude
            } = position.coords;
            reverseGeocode(latitude, longitude, prefix);
        }
        function error(err) {
            showToast('Unable to retrieve your location.');
        }
    }

    document.getElementById('add_useMyLocation').addEventListener('click', (e) => {
        e.preventDefault();
        useMyLocation('add');
    });

    // Trigger location search when the user presses Enter
    document.getElementById('locInput').addEventListener('keydown', function (event) {
        if (event.key === "Enter") {
            const place = autocomplete.getPlace();
            if (place) {
                onPlaceChanged();
            }
        }
    });

    // TRACK WHICH MODAL OPENED MAP
    document.querySelector('#addHub [data-bs-target="#mapModal"]').addEventListener('click', () => {
        currentPrefix = 'add';
    });
</script>

<!-- Load Google Maps JS -->
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAmDotOMSahWwA7WAjmtremPvNNRN1Nye0&libraries=places&callback=initMap"></script>