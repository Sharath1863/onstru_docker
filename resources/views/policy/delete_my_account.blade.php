<!DOCTYPE html>
<html>

<head>
    <title>Onstru | Delete My Account</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/Favicon.png') }}" sizes="32*32" type="image/png">

    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Font / Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <!-- jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/delete.css') }}">

</head>

<body>

    <div class="main">
        <div class="delete-main">
            <div class="delete-header">
                <h4 class="m-0">Delete My Account</h4>
                <img src="{{ asset('assets/images/Logo_Login.png') }}" height="25px" alt="">
            </div>
            <hr class="my-3">
            <div class="delete-content mt-3">
                <h4 class="mb-2">We're sorry to see you here! </h4>
                <h6 class="mb-2">
                    If you wish to delete your account, please follow the instructions below. Please note that deleting
                    your
                    account will permanently remove all your data associated with it, including your profile
                    information,
                    preferences, and any content you've created. This action cannot be undone.
                </h6>
                <form action="" class="my-3">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="phoneNumber">Contact Number <span>*</span></label>
                            <input type="text" class="form-control" name="" id="phoneNumber" min="0" maxlength="10"
                                required autofocus>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="password">Password <span>*</span></label>
                            <input type="password" class="form-control" id="password" required>
                        </div>
                    </div>
                    <div class="col-sm-12 d-flex align-items-center justify-content-center mt-3">
                        <button type="button" class="removebtn" id="deleteAccountBtn">Delete My Account</button>
                    </div>
                </form>
                <h5 class="mb-2">Important Notes</h5>
                <ul>
                    <li>Deleting your account is irreversible. Make sure you have backed up any important data or
                        information
                        before proceeding.</li>
                    <li>Some information may be retained for legal or regulatory purposes even after your account is
                        deleted.
                    </li>
                    <li>If you've subscribed to any services or have ongoing transactions, make sure to cancel them
                        before
                        deleting your account to avoid any unexpected charges.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="toast-main" style="z-index: 1100">
        <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body">
                <!-- Message goes here -->
                <img src="{{ asset('assets/images/Favicon_2.png') }}" height="30px" alt="">
                <span id="toast-message"></span>
            </div>
        </div>
    </div>

    <!-- Toast Data -->
    <div id="toast-data" data-success="{{ session('success') }}"
        data-error="{{ $errors->any() ? $errors->first() : '' }}">
    </div>

    <!-- Toast Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toastData = document.getElementById('toast-data');
            const success = toastData.getAttribute('data-success');
            const error = toastData.getAttribute('data-error');
            if (success) {
                showToast(success, "success");
            } else if (error) {
                showToast(error, "danger");
            }
        });

        function showToast(message, type = 'success') {
            const toastElement = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            toastMessage.textContent = message;

            // Show toast
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            $("#deleteAccountBtn").click(function () {
                const phoneNumber = $("#phoneNumber").val().trim();
                const password = $("#password").val().trim();
                if (!phoneNumber || !password) {
                    showToast("Please fill in all the required fields!");
                    return;
                }
                if (!/^\d{10}$/.test(phoneNumber)) {
                    showToast("Please enter a valid 10-digit phone number!");
                    return;
                }
                showToast("Your account deletion request will be processed within 24 hours after verification.");
            });
        });
    </script>

</body>

</html>