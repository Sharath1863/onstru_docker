{{-- <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAmDotOMSahWwA7WAjmtremPvNNRN1Nye0&callback=initOrderMap"></script> --}}

<link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">

<style>
    .gm-style .gm-style-iw-c {
        padding: 5px !important;
    }

    .gm-ui-hover-effect {
        height: 18px !important;
        width: 18px !important;
    }

    .gm-ui-hover-effect span {
        height: 12px !important;
        width: 12px !important;
        margin: 0px !important;
    }

    .gm-style-iw-d {
        overflow: hidden !important;
        text-align: center;
    }
</style>

<div class="modal fade" id="trackOrder" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Track My Order</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row py-2 px-3">
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-2">
                        <label>Type</label>
                        <h6 id="driverType"></h6>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-2">
                        <label>Driver Name</label>
                        <h6 id="driverName"></h6>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-2">
                        <label>Driver Contact Number</label>
                        <h6 id="driverNo"></h6>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-2">
                        <label>Vehicle Number</label>
                        <h6 id="driverVehicle"></h6>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-2">
                        <label>Estimated Delivery Date</label>
                        <h6 id="deliveryDate"></h6>
                    </div>
                    
                </div>
                <div id="orderMap" style="height: 400px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>




<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let orderMap, directionsService, directionsRenderer;
    let restaurantMarker, driverMarker, customerMarker;

    // Initialize map only once
    function initOrderMap() {
        orderMap = new google.maps.Map(document.getElementById("orderMap"), {
            zoom: 7,
            center: {
                lat: 13.0827,
                lng: 80.2707
            },
        });

        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: orderMap,
            suppressMarkers: true,
            polylineOptions: {
                strokeColor: "#FF0000",
                strokeWeight: 4,
            }
        });

        restaurantMarker = new google.maps.Marker({
            map: orderMap,
            icon: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
        });

        driverMarker = new google.maps.Marker({
            map: orderMap,
            icon: "http://maps.google.com/mapfiles/ms/icons/yellow-dot.png"
        });

        customerMarker = new google.maps.Marker({
            map: orderMap,
            icon: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"
        });
    }

    // AJAX request on button click
    $(document).ready(function() {
        $('.trackbtn').on('click', function() {
            const trackId = $(this).data('track_id');

            $.ajax({
                url: "{{ route('track.location') }}",
                type: 'POST',
                data: {
                    trackId: trackId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    document.getElementById('driverName').innerHTML = response.detail.driver_name;
                    document.getElementById('driverNo').innerHTML = "+91 " + response.detail.driver_number;
                    document.getElementById('driverType').innerHTML = response.detail.type;
                    document.getElementById('driverVehicle').innerHTML = response.detail.vehicle_number;
                    document.getElementById('deliveryDate').innerHTML = response.detail.estimated_delivery_date;
                    
                    const {
                        hub_lat,
                        hub_lng,
                        driver_lat,
                        driver_lng,
                        buyer_lat,
                        buyer_lng
                    } = response;

                    // Ensure map initialized
                    if (!orderMap) initOrderMap();

                    const hub = {
                        lat: parseFloat(hub_lat),
                        lng: parseFloat(hub_lng)
                    };
                    const driver = {
                        lat: parseFloat(driver_lat),
                        lng: parseFloat(driver_lng)
                    };
                    const buyer = {
                        lat: parseFloat(buyer_lat),
                        lng: parseFloat(buyer_lng)
                    };
                    
                    // Update markers
                    restaurantMarker.setPosition(hub);
                    driverMarker.setPosition(driver);
                    customerMarker.setPosition(buyer);

                    orderMap.setCenter(hub);

                    // Draw route
                    directionsService.route({
                        origin: hub,
                        destination: buyer,
                        travelMode: google.maps.TravelMode.DRIVING
                    }, (result, status) => {
                        if (status === "OK") {
                            directionsRenderer.setDirections(result);
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });
    });

    // Fix map rendering when modal is shown
    document.addEventListener("shown.bs.modal", function(event) {
        if (event.target.id === "trackOrder" && orderMap) {
            google.maps.event.trigger(orderMap, "resize");
        }
    });
</script>

<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAmDotOMSahWwA7WAjmtremPvNNRN1Nye0&callback=initOrderMap">
</script>
