@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/register.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.css') }}">

    <div class="login-main">
        <div class="login-div">
            <div class="login-left">
                <img src="{{ asset('assets/images/Portal_Register.png') }}" class="d-flex mx-auto" width="100%" alt="">
            </div>
            <div class="login-right mx-auto">
                <div class="login-head">
                    <h3 class="text-center">Welcome to Onstru</h3>
                    <h5 class="text-center">Register</h5>
                </div>
                <div class="login-form">
                    <form action="{{ route('register-post') }}" method="POST" enctype="multipart/form-data"
                        id="registerForm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-xl-12 mb-2 px-0">
                                <label for="youre">You're <span>*</span></label>
                                <select class="form-select" name="you_are" id="youre" required autofocus>
                                    <option value="" disabled selected>Select Who You Are</option>
                                    <option value="Business">Business Provider</option>
                                    <option value="Professional">Professional</option>
                                    <option value="Consumer">Consumer</option>
                                </select>
                            </div>

                            <div class="asa px-0" style="display: none;">
                                <div class="col-sm-12 col-md-12 col-xl-12 mb-2 px-0">
                                    <label for="asa">As a <span>*</span></label>
                                    <select class="form-select" name="as_a" id="asa">
                                        <option value="" selected disabled>Select As A</option>
                                        <!-- Dynamic Script -->
                                    </select>
                                </div>
                            </div>

                            <div class="typeof px-0" style="display:none;">
                                <div class="col-sm-12 col-md-12 col-xl-12 mb-2 px-0">
                                    <label for="typeof">Type Of <span>*</span></label>
                                    <select class="form-select border-0" name="type_of[]" id="typeof" multiple="multiple">
                                        <option value="" selected disabled>Select Type Of</option>
                                        <!-- Dynamic Script -->
                                    </select>
                                </div>
                            </div>

                            <div class="name px-0">
                                <div class="col-sm-12 col-md-12 col-xl-12 mb-2 px-0">
                                    <label for="username">User Name <span>*</span></label>
                                    <input type="text" name="user_name" id="username" class="form-control" minlength="3"
                                        maxlength="15" pattern="^(?!.*\.\.)(?!\.)(?!.*\.$)[a-z0-9_.]+$" oninput="checkUser(this)"
                                        title="Only lowercase letters, numbers, underscore (_) and dot (.) are allowed. Consecutive dots (..) not allowed"
                                        placeholder="Enter Your User Name" required>
                                    <small id="username-error"></small>
                                </div>

                                <div class="col-sm-12 col-md-12 col-xl-12 mb-2 px-0">
                                    <label for="name">Name <span>*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" minlength="3"
                                        maxlength="20" placeholder="Enter Your Name" pattern="^[A-Za-z\s]+$"
                                        title="Only letters and spaces are allowed" required>
                                </div>

                                <div class="col-sm-12 col-md-12 col-xl-12 mb-2 px-0">
                                    <label for="gender">Gender <span>*</span></label>
                                    <select name="gender" id="gender" class="form-select" required>
                                        <option value="" selected required>Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Others</option>
                                    </select>
                                </div>

                                <div class="col-sm-12 col-md-12 col-xl-12 mb-2 px-0">
                                    <label for="location">Location <span>*</span></label>
                                    <select name="location" id="location" class="form-select" required>
                                        <option value="" selected required>Select Location</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->value }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-12 col-md-12 col-xl-12 mb-2 px-0">
                                    <label for="phone">Contact Number <span>*</span></label>
                                    <input type="number" name="phone" id="phone" class="form-control"
                                        placeholder="Enter Contact Number" onkeyup="checkPhone()"
                                        oninput="validate_contact(this)" required maxlength="10">
                                    <small id="phone-error"></small>
                                </div>

                            </div>

                            <div class="grid-content align-items-start px-0">
                                <div class="col-sm-12 col-md-12 col-xl-12 mb-2 px-0">
                                    <label for="password">Password <span>*</span></label>
                                    <div class="inptripleflex">
                                        <i class="fas fa-lock"></i>
                                        <input type="password" name="password" class="form-control border-0"
                                            placeholder="Password" id="password" minlength="6"
                                            pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&amp;*(),.?&quot;:{}|&lt;&gt;_\-\/\\]).{6,}$"
                                            title="Password must contain atleast 6 characters, Includes 1 Uppercase letter, 1 Special Character and 1 Number"
                                            required>
                                        <i class="fa-solid fa-eye-slash" id="passHide"
                                            onclick="togglePasswordVisibility('password', 'passShow', 'passHide')"
                                            style="display:none; cursor:pointer;"></i>
                                        <i class="fa-solid fa-eye" id="passShow"
                                            onclick="togglePasswordVisibility('password', 'passShow', 'passHide')"
                                            style="cursor:pointer;"></i>
                                    </div>
                                    <small id="passwordHint" class="text-muted" style="display:none;">
                                        Password must contain atleast 6 characters, Includes 1 Uppercase letter, 1 Special Character and 1 Number
                                    </small>
                                </div>
                                <div class="col-sm-12 col-md-12 col-xl-12 mb-2 px-0">
                                    <label for="password_1">Confirm Password <span>*</span></label>
                                    <div class="inptripleflex">
                                        <i class="fas fa-lock"></i>
                                        <input type="password" name="password" class="form-control border-0"
                                            placeholder="Confirm Password" id="password_1" minlength="6"
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
                            </div>
                            <div class="col-sm-12 p-0">
                                <small id="error"></small>
                            </div>

                            <div class="col-sm-12 col-md-12 col-xl-12 mb-3 mt-2 px-0">
                                <button type="submit" class="loginbtn w-100">Register</button>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-12 col-xl-12 px-0">
                            <h6 class="text-center mb-2">Already a member? <a href="{{ route('login') }}">Login</a></h6>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select 2 -->
    <script>
        $(document).ready(function () {
            let select2 = ['typeof', 'location']
            select2.forEach(ele => {
                $(`#${ele}`).select2({
                    width: "100%",
                    placeholder: "Select Options",
                    allowClear: true,
                });
            });
        });
    </script>

    <!-- Password Validation -->
    <script>
        const inppassword = document.getElementById('password');
        const hint = document.getElementById('passwordHint');
        inppassword.addEventListener('focus', () => hint.style.display = 'block');
        inppassword.addEventListener('blur', () => hint.style.display = 'none');
        // inppassword.addEventListener('input', () => {
        //     showToast('Password must contain atleast 6 characters, Includes 1 Uppercase letter and 1 Number')
        // });
    </script>


    <script>
        $(document).ready(function () {
            const $asa = $('#asa');
            const $typeof = $('#typeof');

            const templates = {
                businessOptions: `
                                <option value="" selected disabled>Select As A</option>
                                <option value="Vendor">Vendor</option>
                                <option value="Contractor">Contractor</option>
                                <option value="Consultant">Consultant</option>
                            `,
                professionalOptions: `
                                <option value="" selected disabled>Select As A  </option>
                                <option value="Technical">Technical</option>
                                <option value="Non-Technical">Non-Technical</option>
                            `,
                professionaltypeof: `
                                <option value="" selected disabled>Select Type Of</option>
                                @foreach ($professionaltypeof as $type)
                                    <option value="{{ $type->id }}">{{ $type->value }}</option>
                                @endforeach
                            `,
                vendortypeof: `
                                @foreach ($vendortypeof as $type)
                                    <option value="{{ $type->id }}">{{ $type->value }}</option>
                                @endforeach
                            `,
                contractortypeof: `
                                @foreach ($contractortypeof as $type)
                                    <option value="{{ $type->id }}">{{ $type->value }}</option>
                                @endforeach
                            `,
                consultanttypeof: `
                                @foreach ($consultanttypeof as $type)
                                    <option value="{{ $type->id }}">{{ $type->value }}</option>
                                @endforeach
                            `
            };

            $("#youre").change(function () {
                const role = $(this).val();
                $(".asa, .typeof, .business, .professional").hide();
                $asa.prop('required', false);
                $typeof.prop('required', false);

                if (role === "Business") {
                    $asa.html(templates.businessOptions).prop('required', true);
                    $typeof.prop('required', true);
                    $(".asa, .typeof, .business").show();
                } else if (role === "Professional") {
                    $asa.html(templates.professionalOptions).prop('required', true);
                    $typeof.html(templates.professionaltypeof).prop('required', true);
                    $(".asa, .typeof, .professional").show();
                    $('#typeof').removeAttr('multiple');
                } else if (role === "Consumer") {
                    $(".asa, .typeof").hide();
                }
            });

            $asa.change(function () {
                const type = $(this).val();
                let typeTemplate = '';

                if (type === 'Vendor') typeTemplate = templates.vendortypeof;
                else if (type === 'Contractor') typeTemplate = templates.contractortypeof;
                else if (type === 'Consultant') typeTemplate = templates.consultanttypeof;

                if (typeTemplate) $typeof.html(typeTemplate).prop('required', true);
            });
        });

        // Form Submit
        const form = document.querySelector('form');
        form.addEventListener('submit', function () {
            document.querySelectorAll('.business, .professional, .name').forEach(section => {
                if (section.style.display === "none") {
                    section.querySelectorAll("input, select, textarea").forEach(input => {
                        input.disabled = true;
                    });
                } else {
                    section.querySelectorAll("input, select, textarea").forEach(input => {
                        input.disabled = false;
                    });
                }
            });
        });

        // Password / Confirm Password
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_1');
        const message = document.getElementById('passwordMatchMessage');

        function checkPasswordMatch() {
            if (confirmPassword.value === '') {
                message.textContent = '';
                return;
            }
            if (password.value === confirmPassword.value) {
                message.style.color = 'green';
                message.textContent = 'Passwords Match';
            } else {
                message.style.color = 'red';
                message.textContent = 'Passwords do not Match';
            }
        }
        password.addEventListener('input', checkPasswordMatch);
        confirmPassword.addEventListener('input', checkPasswordMatch);

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
        function getCSRFToken() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        function checkUser(input) {
            input.value = input.value.replace(/\s+/g, '');
            const regex = /^[a-z._]*$/;
            const errorEl = document.getElementById('username-error');
            if (!regex.test(input.value)) {
                errorEl.textContent = "Only lowercase letters, underscore (_) and dot (.) are allowed";
            } else {
                errorEl.textContent = "";
            }
        }

        function checkUsername() {
            const username = document.getElementById('username').value.trim();
            const errorTag = document.getElementById('username-error');
            const submitBtn = document.querySelector('.loginbtn');
            checkUser(username);
            // Clear previous error
            errorTag.textContent = '';

            if (username === '') {
                if (submitBtn) submitBtn.disabled = true;
                return;
            }

            fetch('/validate-username', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken()
                },
                body: JSON.stringify({
                    user_name: username
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        errorTag.textContent = 'Username already exists.';
                        if (submitBtn) submitBtn.disabled = true;
                    }
                    if (submitBtn) submitBtn.disabled = false;
                })
                .catch(error => {
                    console.error('Username check error:', error);
                });
        }

        function checkPhone() {
            const phoneInput = document.getElementById('phone');
            const phone = phoneInput.value.trim();
            const errorTag = document.getElementById('phone-error');
            const submitBtn = document.querySelector('.loginbtn');
            errorTag.textContent = '';

            if (phone.length == 10) {
                if (submitBtn) submitBtn.disabled = false;
                // return;
            }

            if (!/^\d{10}$/.test(phone)) {
                errorTag.textContent = 'Please enter a valid 10-digit phone number.';
                if (submitBtn) submitBtn.disabled = true;
                return;
            }

            const phoneNumber = parseInt(phone, 10);
            if (phoneNumber < 6000000000) {
                errorTag.textContent = 'Please enter a valid phone number starting with 6 or above.';
                if (submitBtn) submitBtn.disabled = true;
                return;
            }

            fetch('/validate-phone', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken()
                },
                body: JSON.stringify({
                    phone: phone
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (submitBtn) submitBtn.disabled = false;
                    if (data.exists) {
                        errorTag.textContent = 'Contact Number already registered.';
                        if (submitBtn) submitBtn.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Phone check error:', error);
                });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('registerForm');
            const submitBtn = document.querySelector('.loginbtn');
            let isSubmitting = false;

            form.addEventListener('submit', function (e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }
                isSubmitting = true;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Registering...`;
            });
        });
    </script>

@endsection