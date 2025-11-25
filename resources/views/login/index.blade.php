@extends('layouts.app')

@section('title', 'Onstru | Login')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">

    <div class="login-main flex-column">
        <div class="login-div mt-4">
            <div class="login-left">
                <img src="{{ asset('assets/images/Portal_Login.png') }}" class="d-flex mx-auto" width="90%" alt="">
            </div>
            <div class="login-right mx-auto">
                <div class="login-head">
                    <h3 class="text-center">Welcome to Onstru</h3>
                    <h5 class="text-center">Login</h5>
                </div>
                <div class="login-form">
                    <form action="{{ route('user_login') }}" method="POST" id="loginForm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-xl-12 mb-3">
                                <label for="phone">Contact Number <span>*</span></label>
                                <div class="inpflex">
                                    <i class="fas fa-phone"></i>
                                    <input type="number" name="phone" id="phone" class="form-control border-0"
                                        value="{{ old('phone') }}" placeholder="Enter Contact Number"
                                        oninput="validate_contact(this)" min="6000000000" max="9999999999" required
                                        autofocus>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-xl-12 mb-3">
                                <label for="password">Password <span>*</span></label>
                                <div class="inpflex">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" name="password" class="form-control border-0"
                                        placeholder="Enter your Password" id="password" required>
                                    <i class="fa-solid fa-eye-slash" id="passHide"
                                        onclick="togglePasswordVisibility('password', 'passShow', 'passHide')"
                                        style="display:none; cursor:pointer;"></i>
                                    <i class="fa-solid fa-eye" id="passShow"
                                        onclick="togglePasswordVisibility('password', 'passShow', 'passHide')"
                                        style="cursor:pointer;"></i>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-xl-12 mb-3">
                                <div class="row justify-content-between">
                                    <div class="d-flex align-items-center column-gap-2 col-6">
                                        <!-- <input type="checkbox" id="remember"><label for="remember" class="m-0">Remember Me</label> -->
                                    </div>
                                    <div class="col-6 text-end">
                                        <label class="mb-0">
                                            <a href="{{ route('forgot-password') }}">
                                                Forgot Password ?
                                            </a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="web_token" id="web_token">
                            <div class="col-sm-12 col-md-12 col-xl-12 mb-3">
                                <button type="submit" class="loginbtn w-100">Login</button>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-12 col-xl-12">
                            <h6 class="text-center mb-2">Don't have account yet? <a
                                    href="{{ route('register') }}">Register</a></h6>

                        </div>
                    </form>
                </div>
            </div>
        </div>
        <h6 class="mb-0" style="font-size: 12px;">&copy; Copyrights TECHNOMERATES PRIVATE LIMITED
            <span class="mx-2">|</span>
            <a href="{{ url('terms-and-condition') }}">Terms & Conditions</a>
            <span class="mx-2">|</span>
            <a href="{{ url('privacy-policy') }}">Privacy Policy</a>
            <span class="mx-2">|</span>
            <a href="{{ url('contact-us') }}">Contact Us</a>
        </h6>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js"></script>

    <script>
        // Contact Number Validation
        function validate_contact(input) {
            let value = input.value.replace(/\D/g, "");
            if (value.length > 10) {
                value = value.slice(0, 10);
            }
            input.value = value;
        }

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

        document.addEventListener("DOMContentLoaded", () => {
            // Firebase config
            const firebaseConfig = {
                apiKey: "AIzaSyAJIkS79WQPd5gx9Ke4i0Gr_2dQhrPG7os",
                authDomain: "onstru-super-app.firebaseapp.com",
                projectId: "onstru-super-app",
                storageBucket: "onstru-super-app.firebasestorage.app",
                messagingSenderId: "623687488765",
                appId: "1:623687488765:web:7d2e2dee0c87c1001fda94"
            };

            // Initialize Firebase
            firebase.initializeApp(firebaseConfig);
            const messaging = firebase.messaging();

            // Function to get token (no extra permission request here)
            async function getFcmToken() {
                try {
                    const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                    const token = await messaging.getToken({
                        vapidKey: "BGF40c00tA9HY9tgsPG_XCBsIGt84wzwZtz7MY0x4DAjsqYtEKXKuAiFGO3rqjG4gPkb-Kyr4rdB4tBed6_n15A",
                        serviceWorkerRegistration: registration
                    });

                    if (token) {
                        document.getElementById("web_token").value = token;
                        $('.loginbtn').prop('disabled', false);
                    } else {
                        $('.loginbtn').prop('disabled', false);
                    }
                } catch (err) {
                    return null;
                }
            }

            // Handle notification permission states
            if (Notification.permission === "granted") {
                $('.loginbtn').prop('disabled', false);
                getFcmToken();
            } else if (Notification.permission === "default") {
                const wantsPermission = confirm("🔔 Would you like to enable notifications to stay updated?");
                $('.loginbtn').prop('disabled', false);
                if (wantsPermission) {
                    Notification.requestPermission().then(permission => {
                        if (permission === "granted") {
                            showToast("Notifications allowed!");
                            getFcmToken();
                        } else {
                            showToast("Notifications denied!");
                        }
                    });
                } else {
                    showToast("User denied to enable notifications.");
                }
            } else {
                // Permission already denied
                showToast("Notifications were denied earlier. If you need to enable it manually.");
                $('.loginbtn').prop('disabled', false);
            }
        });

        async function testNotification() {
            try {
                // Make sure the service worker is registered
                const registration = await navigator.serviceWorker.getRegistration();
                if (!registration) {
                    return;
                }

                // Show a notification manually
                registration.showNotification("Test Notification", {
                    body: "Click to open profile",
                    icon: "https://onstru.com/assets/images/Logo_Admin.png",
                    data: {
                        link: "https://onstru.com/profile"
                    }, // this link will be used in notificationclick
                });
            } catch (err) {}
        }
    </script>

    <!-- Multiple Submissions -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
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
                    `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Logging In...`;
            });
        });
    </script>

@endsection
