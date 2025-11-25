<!-- Switch Account Modal -->
<div class="modal fade" id="switchAcct" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Switch Account</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-2 px-3">
                <div class="switch-div mb-4">
                    <a href="">
                        <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                            <div class="d-flex align-items-center justify-content-start gap-2">
                                <img src="{{ asset('assets/images/Avatar.png') }}" height="40px"
                                    class="avatar" alt="">
                                <div class="user-content">
                                    <h6 class="mb-1">_.sheikkk._</h6>
                                    <h6 class="m-0 text-center label">Contractor</h6>
                                </div>
                            </div>
                            <h4 class="mb-0">
                                <i class="fas fa-circle-check text-success"></i>
                            </h4>
                        </div>
                    </a>
                </div>

                <div class="d-block">
                    <button class="editbtn w-100">Add New Account</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Logout Modal -->
<div class="modal modal-md fade" id="logout" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-4">
                <img src="{{ asset('assets/images/img_logout.png') }}" height="100px" class="d-flex mx-auto mb-3"
                    alt="">
                <h5 class="text-center mb-2">Are You Logging Out?</h5>
                <h6 class="text-center mb-2">
                    Once logged out, you'll be redirected to the login page. It's easy to sign back in anytime.
                </h6>
                <div class="col-sm-12 d-flex align-items-center justify-content-between gap-2 mt-3">
                    <a class="w-50" data-bs-dismiss="modal">
                        <button type="button" class="w-100 removebtn">Stay Logged In</button>
                    </a>
                    <a class="w-50" href="{{ route('logout') }}">
                        <button type="submit" class="w-100 listbtn">Yes, Logout</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>