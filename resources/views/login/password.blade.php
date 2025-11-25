@extends('layouts.app')

@section('title', 'Onstru | Change Password')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">

    <div class="login-main">
        <div class="login-div">
            <div class="login-left">
                <img src="{{ asset('assets/images/Portal_New_Password.png') }}" class="d-flex mx-auto" width="90%" alt="">
            </div>
            <div class="login-right mx-auto">
                <div class="login-head">
                    <h5 class="text-center">Change Password</h5>
                    <h6 class="text-center">Your OTP has been verified successfully, change your password here.</h6>
                </div>
                <div class="login-form">
                    <form action="{{ route('login.password-success') }}" method="POST" id="passwordForm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-xl-12 mb-3">
                                <label for="password">New Password <span>*</span></label>
                                <div class="inpflex">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" name="password" class="form-control border-0"
                                        placeholder="Password" id="password" 
                                        pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&amp;*(),.?&quot;:{}|&lt;&gt;_\-\/\\]).{6,}$"
                                        title="Password must contain atleast 6 characters, Includes 1 Uppercase letter, 1 Special Character and 1 Number"
                                        autofocus required>
                                    <i class="fa-solid fa-eye-slash" id="passHide"
                                        onclick="togglePasswordVisibility('password', 'passShow', 'passHide')"
                                        style="display:none; cursor:pointer;"></i>
                                    <i class="fa-solid fa-eye" id="passShow"
                                        onclick="togglePasswordVisibility('password', 'passShow', 'passHide')"
                                        style="cursor:pointer;"></i>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-xl-12 mb-3">
                                <label for="password_1">Confirm Password <span>*</span></label>
                                <div class="inpflex">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" name="password_1" class="form-control border-0"
                                        placeholder="Confirm Password" id="password_1" 
                                        pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&amp;*(),.?&quot;:{}|&lt;&gt;_\-\/\\]).{6,}$"
                                        title="Password must contain atleast 6 characters, Includes 1 Uppercase letter, 1 Special Character and 1 Number"
                                        required>
                                    <i class="fa-solid fa-eye-slash" id="passHide_1"
                                        onclick="togglePasswordVisibility('password_1', 'passShow_1', 'passHide_1')"
                                        style="display:none; cursor:pointer;"></i>
                                    <i class="fa-solid fa-eye" id="passShow_1"
                                        onclick="togglePasswordVisibility('password_1', 'passShow_1', 'passHide_1')"
                                        style="cursor:pointer;"></i>
                                </div>
                                <small id="passwordMatchMessage" style="font-size: 10px">
                                    @if ($errors->has('password'))
                                        {{ $errors->first('password') }}
                                    @endif
                                </small>
                            </div>
                            <div class="col-sm-12 col-md-12 col-xl-12">
                                <button type="submit" class="loginbtn w-100">Change Password</button>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password Toggle
        function togglePasswordVisibility(inputId, showId, hideId) {
            let $input = $('#' + inputId);
            let $passShow = $('#' + showId);
            let $passHide = $('#' + hideId);
            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $passShow.hide();
                $passHide.show();
            } else {
                $input.attr('type', 'password');
                $passShow.show();
                $passHide.hide();
            }
        }
    </script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('passwordForm');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_1');
        const message = document.getElementById('passwordMatchMessage');
        const submitBtn = document.querySelector('.loginbtn');

        function validatePasswords() {
            // Don’t show message until both fields have some value
            if (password.value.length === 0 || confirmPassword.value.length === 0) {
                message.textContent = "";
                submitBtn.disabled = true; // prevent submission until both filled
                return;
            }

            // Now check if they match
            if (password.value !== confirmPassword.value) {
                message.textContent = "Passwords do not match!";
                message.style.color = "red";
                submitBtn.disabled = true;
            } else {
                message.textContent = "";
                submitBtn.disabled = false;
            }
        }

        // Run validation while typing
        confirmPassword.addEventListener('input', validatePasswords);
        password.addEventListener('input', validatePasswords);

        // Prevent submit if they still don’t match
        form.addEventListener('submit', function(e) {
            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                message.textContent = "Passwords do not match!";
                message.style.color = "red";
            }
        });
    });
</script>

    
    <!-- Multiple Submissions -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('passwordForm');
            const submitBtn = document.querySelector('.loginbtn');
            let isSubmitting = false;

            form.addEventListener('submit', function(e) {
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