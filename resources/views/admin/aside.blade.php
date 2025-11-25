<!-- Web Sidebar -->
<aside>
    <div class="flex-shrink-0 sidebar">
        <div class="nav col-md-12">
            <a href="admin_login" class="mx-auto">
                <img src="{{ asset('assets/images/Logo_Admin.png') }}" alt="" height="50px" class="mx-auto lightLogo">
            </a>
        </div>
        <ul class="main-ul list-unstyled ps-0" style="margin-top: 20px">
            @include('admin.common_menu')
        </ul>
    </div>
</aside>

<!-- Responsive Sidebar -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <img src="{{ asset('assets/images/Logo_Admin.png') }}" height="40px" alt="">
        <button type="button" class="btn-close bg-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="flex-shrink-0 sidebar">
            <ul class="list-unstyled mt-2 ps-0">
                @include('admin.common_menu')
            </ul>
        </div>
    </div>
</div>