<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Track Your Package</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- Script -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <link rel="stylesheet" href="{{ asset('assets/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">

</head>

<style>
    * {
        margin: 0;
        padding: 0;
        font-family: "Outfit", sans-serif;
    }

    body {
        background-color: #f4f4f4;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .container {
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        text-align: center;
        max-width: 400px;
    }

    img {
        margin-bottom: 20px;
    }

    h1 {
        color: #091E42;
        font-size: 24px;
        font-weight: 700;
    }

    h2 {
        color: #1f1f1f;
        font-size: 20px;
        font-weight: 600;
    }

    h6 {
        font-size: 16px;
        font-weight: 500;
        color: #5a6272;
    }

    .trackbtn {
        margin-top: 20px;
        padding: 6px 20px;
        background-color: #FFE000;
        color: #000;
        border: 2px solid #FFE000;
        border-radius: 5px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: 0.5s ease-in-out;
    }

    .trackbtn:hover {
        background-color: #000;
        color: #FFE000;
    }
</style>

<body>
    <!-- Container -->
    <div class="container">
        @if ($track->status == 'shipped')
            <img src="{{ asset('assets/images/Logo_Login.png') }}" height="20px" alt="">
            <h2>#{{ $track->order_id }}</h2>
            <img src="{{ asset('assets/images/Tracking.gif') }}" height="250px" alt="Tracking Package" />
            <h1 class="mb-2">Package Tracking</h1>
            <h6 class="mb-2">Driver Name : {{ $track->drivername }}</h6>
            <div id="tracking-info" class="mb-3">
                <h6>Getting location and tracking info...</h6>
            </div>
            <div class="form-div">
                <form action="{{ route('order-otp-update') }}" method="POST">
                    @csrf
                    <input type="text" name="tracking_id" value="{{ $track->tracking_id }}" hidden>
                    <input type="text" name="order_id" value="{{ $track->order_id }}" hidden>
                    <label class="mb-2">Get the Customer's OTP and Enter Below</label>
                    <div class="otp-boxes d-flex align-items-center justify-content-between gap-3">
                        <input type="text" maxlength="1" name="otp_1" id="otp_1" class="otp-input form-control"
                            oninput="checkOtp()" autofocus required />
                        <input type="text" maxlength="1" name="otp_2" id="otp_2" class="otp-input form-control"
                            oninput="checkOtp()" required />
                        <input type="text" maxlength="1" name="otp_3" id="otp_3" class="otp-input form-control"
                            oninput="checkOtp()" required />
                        <input type="text" maxlength="1" name="otp_4" id="otp_4" class="otp-input form-control"
                            oninput="checkOtp()" required />
                    </div>
                    <input type="hidden" name="otp" id="hiddenOtp">
                    <!-- Error Message & Submit Button -->
                    <div id="otp-error" style="display: none; color: red; font-size: 14px; margin-top: 10px;">
                        OTP Mismatched. Please try again.
                    </div>

                    <button type="submit" id="submit-btn" class="trackbtn" disabled>Submit</button>
                </form>
            </div>
        @elseif ($track->status == 'delivered')
            <img src="{{ asset('assets/images/Logo_Login.png') }}" height="20px" alt="">
            <h2>#{{ $track->order_id }}</h2>
            <img src="{{ asset('assets/images/Received.gif') }}" height="350px" alt="Tracking Package" />
            <h1 class="mb-2">Package Status</h1>
            <h6 class="mb-2">Your package has been delivered successfully</h6>
            <a href="{{ url('home') }}">
                <button type="submit" id="submit-btn" class="trackbtn">Go to Home</button>
            </a>
        @endif
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>

<script>
    const inputs = document.querySelectorAll(".otp-input");
    inputs.forEach((input, index) => {
        input.addEventListener("input", () => {
            // Move to next input if one digit entered
            if (input.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });
        input.addEventListener("keydown", (e) => {
            // Backspace to go to previous input
            if (e.key === "Backspace" && input.value === "" && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        var ord = '{{ $track->tracking_id }}';

        // console.log(ord);

        // console.log('Script loaded at', new Date().toLocaleTimeString());

        function fetchTrackingStatus() {
            // console.log('⏱️ Running fetchTrackingStatus at', new Date().toLocaleTimeString());

            // Try to access geolocation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // console.log("✅ Location received:", position.coords.latitude, position.coords
                        //     .longitude);

                        // Use the lat/lng in your request
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        // Example AJAX request (you can modify as per your need)
                        $.ajax({
                            url: "{{ route('update.track') }}",
                            type: 'POST',
                            dataType: 'JSON',
                            data: {
                                ord_lat: lat,
                                ord_lng: lng,
                                order_id: ord,
                                '_token': '{{ csrf_token() }}'

                            },
                            success: function(response) {
                                // console.log("✅ Location updated on server:", response);
                                $('#tracking-info').text(response.message + ' at ' + new Date()
                                    .toLocaleTimeString());
                            },
                            error: function() {
                                $('#tracking-info').html('Error fetching tracking info.');
                            }
                        });
                    },
                    function(error) {
                        console.log("❌ Geolocation error:", error);

                        // Show alert every time geolocation is denied
                        alert(
                            '⚠️ Location access denied. Please enable it in your device or browser settings.'
                        );

                        $('#tracking-info').html('Geolocation permission denied.');
                    }
                );
            } else {
                alert('❌ Geolocation is not supported by your browser.');
                $('#tracking-info').html('Geolocation is not supported.');
            }
        }

        // Call immediately once when the page loads
        fetchTrackingStatus();

        // Run the function every 3 seconds thereafter
        setInterval(fetchTrackingStatus, 5000); // 3000 ms = 3 seconds
    });
</script>
<script>
    const correctOtp = "{{ $track->otp }}";
    document.getElementById('submit-btn').classList.add('d-none');
    document.querySelectorAll('.otp-input').forEach(input => {
        input.addEventListener('input', function() {
            // Collect OTP values
            const otpEntered = document.getElementById('otp_1').value +
                document.getElementById('otp_2').value +
                document.getElementById('otp_3').value +
                document.getElementById('otp_4').value;

            const submitButton = document.getElementById('submit-btn');
            const hiddenOtpField = document.getElementById('hiddenOtp');

            // Set the hidden OTP field value
            hiddenOtpField.value = otpEntered;

            // Check OTP validity
            if (otpEntered.length === 4) {
                if (otpEntered === correctOtp) {
                    submitButton.disabled = false;
                    document.getElementById('otp-error').style.display = 'none';
                    document.getElementById('submit-btn').classList.remove('d-none');
                } else {
                    submitButton.disabled = true;
                    document.getElementById('otp-error').style.display = 'block';
                    document.getElementById('submit-btn').classList.add('d-none');
                }
            } else {
                submitButton.disabled = true;
                document.getElementById('otp-error').style.display = 'none';
            }
        });
    });
</script>

<script>
    document.querySelector("form").addEventListener("submit", function(e) {
        const submitButton = document.getElementById('submit-btn');
        submitButton.disabled = true;
        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Submitting...`;
    });
</script>

</html>
