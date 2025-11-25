<div class="container-fluid px-0">
    <div class="body-head mb-3">
        <h5>Change Password</h5>
    </div>

    <div class="form-div side-cards">
        <form action="{{ route('password.change') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-sm-12 col-md-4 mb-3">
                    <label for="old_password">Old Password <span>*</span></label>
                    <div class="inptripleflex">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control border-0" name="old_password" id="old_password"
                            required autofocus>
                        <i class="fa-solid fa-eye-slash" id="old_passHide"
                            onclick="togglePasswordVisibility('old_password', 'old_passShow', 'old_passHide')"
                            style="display:none; cursor:pointer;"></i>
                        <i class="fa-solid fa-eye" id="old_passShow"
                            onclick="togglePasswordVisibility('old_password', 'old_passShow', 'old_passHide')"
                            style="cursor:pointer;"></i>
                    </div>
                </div>

                <div class="col-sm-12 col-md-4 mb-3">
                    <label for="new_password">New Password <span>*</span></label>
                    <div class="inptripleflex">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control border-0" name="new_password" id="new_password" required>
                        <i class="fa-solid fa-eye-slash" id="new_passHide"
                            onclick="togglePasswordVisibility('new_password', 'new_passShow', 'new_passHide')"
                            style="display:none; cursor:pointer;"></i>
                        <i class="fa-solid fa-eye" id="new_passShow"
                            onclick="togglePasswordVisibility('new_password', 'new_passShow', 'new_passHide')"
                            style="cursor:pointer;"></i>
                    </div>
                </div>

                <div class="col-sm-12 col-md-4 mb-3">
                    <label for="confirm_password">Confirm Password <span>*</span></label>
                    <div class="inptripleflex">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control border-0" name="confirm_password"
                            id="confirm_password" required>
                        <i class="fa-solid fa-eye-slash" id="confirm_passHide"
                            onclick="togglePasswordVisibility('confirm_password', 'confirm_passShow', 'confirm_passHide')"
                            style="display:none; cursor:pointer;"></i>
                        <i class="fa-solid fa-eye" id="confirm_passShow"
                            onclick="togglePasswordVisibility('confirm_password', 'confirm_passShow', 'confirm_passHide')"
                            style="cursor:pointer;"></i>
                    </div>
                </div>

                <div class="col-sm-12 mb-3">
                    <label>Password Requirements</label>
                    <div class="col-sm-12 col-md-4 mb-1" id="req-length">
                        <label class="text-muted"><i class="fas fa-circle-xmark pe-1"></i> At least 6 Characters</label>
                    </div>
                    <div class="col-sm-12 col-md-4 mb-1" id="req-case">
                        <label class="text-muted"><i class="fas fa-circle-xmark pe-1"></i> Includes Uppercase Letter</label>
                    </div>
                    <div class="col-sm-12 col-md-4 mb-1" id="req-special">
                        <label class="text-muted"><i class="fas fa-circle-xmark pe-1"></i> Includes Special Character</label>
                    </div>
                    <div class="col-sm-12 col-md-4 mb-1" id="req-number">
                        <label class="text-muted"><i class="fas fa-circle-xmark pe-1"></i> Includes Numbers</label>
                    </div>
                </div>

                <div
                    class="col-sm-12 col-md-12 d-flex align-items-center justify-content-sm-start justify-content-sm-end my-3">
                    <button type="submit" class="formbtn" id="submitBtn" disabled>Change Password</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
    // Toggle password visibility
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

    // Validate password strength
    $(document).ready(function() {
        $("#new_password").on("input", function() {
            let password = $(this).val();
            let isValid = true;

            // Length Check
            if (password.length >= 6) {
                $("#req-length i").removeClass("fa-circle-xmark text-danger").addClass(
                    "fa-circle-check text-success");
            } else {
                $("#req-length i").removeClass("fa-circle-check text-success").addClass(
                    "fa-circle-xmark text-danger");
                isValid = false;
            }

            // Number Check
            if (/\d/.test(password)) {
                $("#req-number i").removeClass("fa-circle-xmark text-danger").addClass(
                    "fa-circle-check text-success");
            } else {
                $("#req-number i").removeClass("fa-circle-check text-success").addClass(
                    "fa-circle-xmark text-danger");
                isValid = false;
            }

            // Uppercase Check
            if (/[A-Z]/.test(password)) {
                $("#req-case i").removeClass("fa-circle-xmark text-danger").addClass(
                    "fa-circle-check text-success");
            } else {
                $("#req-case i").removeClass("fa-circle-check text-success").addClass(
                    "fa-circle-xmark text-danger");
                isValid = false;
            }

            // Special Character Check
            if (/[!@#$%^&*(),.?":{}|<>_\-\/\\]/.test(password)) {
                $("#req-special i").removeClass("fa-circle-xmark text-danger").addClass("fa-circle-check text-success");
            } else {
                $("#req-special i").removeClass("fa-circle-check text-success").addClass("fa-circle-xmark text-danger");
                isValid = false;
            }

            // Enable/Disable Submit Button
            if (isValid && password === $('#confirm_password').val()) {
                $('#submitBtn').prop('disabled', false);
            } else {
                $('#submitBtn').prop('disabled', true);
            }
        });

        // Confirm password match
        $("#confirm_password").on("input", function() {
            if ($(this).val() === $('#new_password').val()) {
                $('#submitBtn').prop('disabled', false);
            } else {
                $('#submitBtn').prop('disabled', true);
            }
        });
    });
</script>
