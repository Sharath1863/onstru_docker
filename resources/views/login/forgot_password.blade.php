@extends('layouts.app')

@section('title', 'Onstru | Forgot Password')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">

    <div class="login-main">
        <div class="login-div">
            <div class="login-left">
                <img src="{{ asset('assets/images/Portal_Forgot.png') }}" class="d-flex mx-auto" width="90%" alt="">
            </div>
            <div class="login-right mx-auto">
                <div class="login-head">
                    <h5 class="text-center">Forgot Password</h5>
                    <h6 class="text-center">To reset your password, you need your mobile number that can be authenticated
                    </h6>
                </div>
                <div class="login-form">
                    <form action="{{ route('login.otp-verify') }}" method="POST" id="forgotForm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-xl-12 mb-3">
                                <label for="phone">Phone Number <span>*</span></label>
                                <div class="inpflex">
                                    <i class="fas fa-phone"></i>
                                    <input type="number" name="phone" id="phone" class="form-control border-0"
                                        placeholder="Enter Phone Number" value="{{ old('phone') }}"
                                        oninput="validate_contact(this)" required autofocus>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-xl-12 mb-4">
                                <button type="submit" class="loginbtn w-100">Send OTP</button>
                                </a>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-12 col-xl-12">
                            <h6 class="text-center mb-2">Back to <a href="{{ route('login') }}">Login</a></h6>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Contact Number Validation
        function validate_contact(input) {
            let value = input.value.replace(/\D/g, "");
            if (value.length > 10) {
                value = value.slice(0, 10);
            }
            input.value = value;
        }
    </script>

    <!-- Multiple Submissions -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('forgotForm');
            const submitBtn = document.querySelector('.loginbtn');
            let isSubmitting = false;

            form.addEventListener('submit', function (e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }
                isSubmitting = true;
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sending...`;
            });
        });
    </script>

@endsection