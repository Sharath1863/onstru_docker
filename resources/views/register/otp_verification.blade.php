@extends('layouts.app')

@section('title', 'Onstru | OTP Verification')

@section('content')
    @php
        $otp = Session::get('otp');
        $phone = Session::get('phone');
    @endphp
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">

    <style>
        .login-div .form-control:focus {
            border: 1px solid var(--main);
        }
    </style>

    <div class="login-main">
        <div class="login-div">
            <div class="login-left">
                <img src="{{ asset('assets/images/Portal_OTP_Verify.png') }}" class="d-flex mx-auto" width="90%" alt="">
            </div>
            <div class="login-right mx-auto">
                <div class="login-head">
                    <h5 class="text-center">OTP Verification</h5>
                    <h6 class="text-center">Please enter the 4-digit code sent to your number +91 {{ $phone }} for
                        Verification
                    </h6>
                </div>
                <div class="login-form">
                    <form action="{{ route('register.otp-success') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-xl-12 mb-3">
                                <label for="otp">OTP <span>*</span></label>
                                <div class="otp-boxes d-flex align-items-center justify-content-between gap-3">
                                    <input type="text" maxlength="1" name="otp_1" id="otp_1" class="otp-input form-control"
                                        autofocus />
                                    <input type="text" maxlength="1" name="otp_2" id="otp_2"
                                        class="otp-input form-control" />
                                    <input type="text" maxlength="1" name="otp_3" id="otp_3"
                                        class="otp-input form-control" />
                                    <input type="text" maxlength="1" name="otp_4" id="otp_4"
                                        class="otp-input form-control" />
                                </div>
                                <small id="otpmessage" style="font-size: 10px; text-align: center;"></small>
                            </div>
                            <div class="col-sm-12 col-md-12 col-xl-12 mb-3">
                                <button type="submit" class="loginbtn w-100">Verify</button>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-xl-12">
                            <h6 class="text-center mb-3" id="resend" style="display: none;">
                                Didn't recive any code ? <a id="resendOtpBtn">Resend Code</a>
                            </h6>
                            <h6 class="text-center">
                                Request new code in <span class="text-danger" id="timer">00:60</span>
                            </h6>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

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
        let countdown; // global reference
        let time = 60;
        const timerEl = document.getElementById('timer');
        const resend = document.getElementById('resend');
        const verifyBtn = document.querySelector('.loginbtn');

        function startTimer() {
            clearInterval(countdown); // clear previous timer if running
            time = 120;

            resend.style.display = "none";
            verifyBtn.style.display = "block";

            countdown = setInterval(() => {
                if (time <= 0) {
                    clearInterval(countdown);
                    timerEl.textContent = "00:00";
                    resend.style.display = "block";
                    verifyBtn.style.display = "none";
                } else {
                    let minutes = Math.floor(time / 60);
                    let seconds = time % 60;
                    timerEl.textContent = `0${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                    time--;
                }
            }, 1000);
        }
        startTimer();
    </script>

    <script>
        $(document).ready(function () {
            $(".loginbtn").on("click", function (e) {
                e.preventDefault();
                let sessionOtp = "{{ $otp ?? '' }}";
                let otp = "";
                document.querySelectorAll(".otp-input").forEach(input => {
                    otp += input.value;
                });
               // $('.loginbtn').prop('disabled', true);
                const message = document.getElementById('otpmessage');
                let yes = "yes"
                if (otp.length !== 4 || isNaN(otp)) {
                    showToast("Please enter a valid 4-digit OTP");
                    return;
                }
                if (otp === sessionOtp) {
                    $.ajax({
                        url: "{{ route('register.otp-success') }}",
                        type: "POST",

                        data: {
                            yes: yes,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                window.location.href = "{{ route('register.otp-view') }}";
                            } else {
                                alert(response.message || "Verification failed.");
                            }
                        },
                        error: function (xhr) {
                            console.error("AJAX Error:", xhr.responseText);
                            showToast("Server error. Please try again.");
                        }
                    });
                } else {
                    showToast("OTP Mismatched");
                }
            });
        });
    </script>

    <script>
        $("#resendOtpBtn").on("click", function (e) {
            e.preventDefault();
            $("#resendOtpBtn").hide();
            $.ajax({
                url: "{{ route('login.otp-verify', ['type' => 'resend']) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    alert(response.message || "OTP resent successfully!");
                    window.location.reload();
                    startTimer();
                },
                error: function (xhr) {
                    alert("Error resending OTP. Try again.");
                }
            });
        });
    </script>

@endsection