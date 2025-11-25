<nav class="navbar px-4">
    <div class="icons login col-sm-12 col-md-12">
        <button class="border-0 m-0 p-0 responsive_button" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
            <i class="bx bx-menu-alt-left"></i>
        </button>
        <div class="navlogo">
            <a href="./index.php" class="mx-auto">
                <img src="{{ asset('assets/images/Logo_Admin.png') }}" alt="" height="40px" class="mx-auto lightLogo">
            </a>
        </div>
        @include('admin.common_user')
    </div>
</nav>

<!-- Change Password Modal -->
<!-- <div class="modal fade" id="password" tabindex="-1" aria-labelledby="passwordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h4 class="m-0">Change Password</h4>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="col-sm-12 col-md-12 mb-2">
                        <label for="oldpassword">Old Password</label>
                        <div class="inpflex">
                            <input type="password" class="form-control border-0" name="oldpassword" id="password_1">
                            <i class="fa-solid fa-eye-slash" id="passHide_1"
                                onclick="togglePasswordVisibility('password_1', 'passShow_1', 'passHide_1')"
                                style="display:none; cursor:pointer;"></i>
                            <i class="fa-solid fa-eye" id="passShow_1"
                                onclick="togglePasswordVisibility('password_1', 'passShow_1', 'passHide_1')"
                                style="cursor:pointer;"></i>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 mb-2">
                        <label for="newpassword">New Password</label>
                        <div class="inpflex">
                            <input type="password" class="form-control border-0" name="newpassword" id="password_2">
                            <i class="fa-solid fa-eye-slash" id="passHide_2"
                                onclick="togglePasswordVisibility('password_2', 'passShow_2', 'passHide_2')"
                                style="display:none; cursor:pointer;"></i>
                            <i class="fa-solid fa-eye" id="passShow_2"
                                onclick="togglePasswordVisibility('password_2', 'passShow_2', 'passHide_2')"
                                style="cursor:pointer;"></i>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 mb-2">
                        <label for="confirmpassword">Confirm Password</label>
                        <div class="inpflex">
                            <input type="password" class="form-control border-0" name="confirmpassword" id="password_3">
                            <i class="fa-solid fa-eye-slash" id="passHide_3"
                                onclick="togglePasswordVisibility('password_3', 'passShow_3', 'passHide_3')"
                                style="display:none; cursor:pointer;"></i>
                            <i class="fa-solid fa-eye" id="passShow_3"
                                onclick="togglePasswordVisibility('password_3', 'passShow_3', 'passHide_3')"
                                style="cursor:pointer;"></i>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                        <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                        <button type="submit" class="modalbtn w-50">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> -->