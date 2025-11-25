<li class="mb-2">
    <a href="{{ url('dashboard_admin') }}">
        <button class="asidebtn mx-auto collapsed {{ Request::routeIs('dashboard_*') ? 'active' : '' }}">
            <div class="btnname">
                <img src="{{ asset('assets/images/admin/icon_dashboard.png') }}" height="20px" alt="">
                <span>Dashboard</span>
            </div>
            <div class="righticon d-flex ms-auto">
                <i class="fa-solid fa-angle-right toggle-icon"></i>
            </div>
        </button>
    </a>
</li>
<li class="mb-2">
    <button class="asidebtn mx-auto collapsed {{ Request::routeIs('user_*') ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#collapseUser">
        <div class="btnname">
            <img src="{{ asset('assets/images/admin/icon_user.png') }}" height="20px" alt="">
            <span>Users</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapseUser">
        <ul class="btn-toggle-nav list-unstyled text-start ps-5 pe-0 pb-3">
            <li>
                <a href="{{ url('user_vendor') }}" class="d-inline-flex text-decoration-none mt-3">Vendor</a>
            </li>
            <li>
                <a href="{{ url('user_contractor') }}" class="d-inline-flex text-decoration-none">Contractor</a>
            </li>
            <li>
                <a href="{{ url('user_consultant') }}" class="d-inline-flex text-decoration-none">Consultant</a>
            </li>
            <li>
                <a href="{{ url('user_consumer') }}" class="d-inline-flex text-decoration-none">Consumer</a>
            </li>
            <li>
                <a href="{{ url('user_professional') }}" class="d-inline-flex text-decoration-none">Professional</a>
            </li>
        </ul>
    </div>
</li>
<li class="mb-2">
    <button class="asidebtn mx-auto collapsed {{ Request::routeIs('product_*') ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#collapseProduct">
        <div class="btnname">
            <img src="{{ asset('assets/images/admin/icon_products.png') }}" height="20px" alt="">
            <span>Products</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapseProduct">
        <ul class="btn-toggle-nav list-unstyled text-start ps-5 pe-0 pb-3">
            <li>
                <a href="{{ url('product_list') }}" class="d-inline-flex text-decoration-none mt-3">Products</a>
            </li>
            <li>
                <a href="{{ url('commission_list') }}" class="d-inline-flex text-decoration-none">Commission</a>
            </li>
        </ul>
    </div>
</li>
<li class="mb-2">
    <a href="{{ url('job_list') }}">
        <button class="asidebtn mx-auto collapsed {{ Request::routeIs('job_*') ? 'active' : '' }}">
            <div class="btnname">
                <img src="{{ asset('assets/images/admin/icon_jobs.png') }}" height="20px" alt=""> <span>Jobs</span>
            </div>
            <div class="righticon d-flex ms-auto">
                <i class="fa-solid fa-angle-right"></i>
            </div>
        </button>
    </a>
</li>
<li class="mb-2">
    <a href="{{ url('service_list') }}">
        <button class="asidebtn mx-auto collapsed {{ Request::routeIs('service_*') ? 'active' : '' }}">
            <div class="btnname">
                <img src="{{ asset('assets/images/admin/icon_service.png') }}" height="20px" alt="">
                <span>Services</span>
            </div>
            <div class="righticon d-flex ms-auto">
                <i class="fa-solid fa-angle-right"></i>
            </div>
        </button>
    </a>
</li>
<li class="mb-2">
    <a href="{{ url('leads_list') }}">
        <button class="asidebtn mx-auto collapsed {{ Request::routeIs('leads_*') ? 'active' : '' }}">
            <div class="btnname">
                <img src="{{ asset('assets/images/admin/icon_leads.png') }}" height="20px" alt=""> <span>Leads</span>
            </div>
            <div class="righticon d-flex ms-auto">
                <i class="fa-solid fa-angle-right"></i>
            </div>
        </button>
    </a>
</li>
<li class="mb-2">
    <a href="{{ url('dropdown_list') }}">
        <button class="asidebtn mx-auto collapsed {{ Request::routeIs('dropdown_*') ? 'active' : '' }}">
            <div class="btnname">
                <img src="{{ asset('assets/images/admin/icon_dropdown.png') }}" height="20px" alt="">
                <span>Dropdowns</span>
            </div>
            <div class="righticon d-flex ms-auto">
                <i class="fa-solid fa-angle-right toggle-icon"></i>
            </div>
        </button>
    </a>
</li>
<li class="mb-2">
    <a href="{{ url('charges_list') }}">
        <button class="asidebtn mx-auto collapsed {{ Request::routeIs('charges_*') ? 'active' : '' }}">
            <div class="btnname">
                <img src="{{ asset('assets/images/admin/icon_charges.png') }}" height="20px" alt="">
                <span>Charges</span>
            </div>
            <div class="righticon d-flex ms-auto">
                <i class="fa-solid fa-angle-right toggle-icon"></i>
            </div>
        </button>
    </a>
</li>
<li class="mb-2">
    <button class="asidebtn mx-auto collapsed {{ Request::routeIs('orders_*') ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#collapseOrders">
        <div class="btnname">
            <img src="{{ asset('assets/images/admin/icon_orders.png') }}" height="20px" alt="">
            <span>Orders</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapseOrders">
        <ul class="btn-toggle-nav list-unstyled text-start ps-5 pe-0 pb-3">
            <li>
                <a href="{{ url('orders_list') }}" class="d-inline-flex text-decoration-none mt-3">Orders</a>
            </li>
            <li>
                <a href="{{ url('orders_settlement') }}" class="d-inline-flex text-decoration-none">Settlement</a>
            </li>
        </ul>
    </div>
</li>
<li class="mb-2">
    <a href="{{ url('cashback_list') }}">
        <button class="asidebtn mx-auto collapsed {{ Request::routeIs('cashback_*') ? 'active' : '' }}">
            <div class="btnname">
                <img src="{{ asset('assets/images/admin/icon_cashback.png') }}" height="20px" alt="">
                <span>Cashback</span>
            </div>
            <div class="righticon d-flex ms-auto">
                <i class="fa-solid fa-angle-right"></i>
            </div>
        </button>
    </a>
</li>
<li class="mb-2">
    <button class="asidebtn mx-auto collapsed {{ Request::routeIs('premium_*') ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#collapsePremium">
        <div class="btnname">
            <img src="{{ asset('assets/images/admin/icon_premium.png') }}" height="20px" alt="">
            <span>Premium</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapsePremium">
        <ul class="btn-toggle-nav list-unstyled text-start ps-5 pe-0 pb-3">
            <li>
                <a href="{{ url('premium_list') }}" class="d-inline-flex text-decoration-none mt-3">Content</a>
            </li>
            <li>
                <a href="{{ url('premium_users') }}" class="d-inline-flex text-decoration-none">Users</a>
            </li>
        </ul>
    </div>
</li>
<li class="mb-2">
    <button class="asidebtn mx-auto collapsed {{ Request::routeIs('highlight_*') ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#collapseHighlight">
        <div class="btnname">
            <img src="{{ asset('assets/images/admin/icon_highlights.png') }}" height="20px" alt="">
            <span>Highlights</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapseHighlight">
        <ul class="btn-toggle-nav list-unstyled text-start ps-5 pe-0 pb-3">
            <li>
                <a href="{{ url('highlight_products_list') }}"
                    class="d-inline-flex text-decoration-none mt-3">Products</a>
            </li>
            <li>
                <a href="{{ url('highlight_services_list') }}" class="d-inline-flex text-decoration-none">Services</a>
            </li>
            <li>
                <a href="{{ url('highlight_jobs_list') }}" class="d-inline-flex text-decoration-none">Jobs</a>
            </li>
        </ul>
    </div>
</li>
<li class="mb-2">
    <a href="{{ url('insight_list') }}">
        <button class="asidebtn mx-auto collapsed {{ Request::routeIs('insight_*') ? 'active' : '' }}">
            <div class="btnname">
                <img src="{{ asset('assets/images/admin/icon_insight.png') }}" height="20px" alt="">
                <span>Insights</span>
            </div>
            <div class="righticon d-flex ms-auto">
                <i class="fa-solid fa-angle-right toggle-icon"></i>
            </div>
        </button>
    </a>
</li>
<li class="mb-2">
    <button class="asidebtn mx-auto collapsed {{ Request::routeIs('report_*') ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#collapseReports">
        <div class="btnname">
            <img src="{{ asset('assets/images/admin/icon_report.png') }}" height="20px" alt="">
            <span>Reports</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapseReports">
        <ul class="btn-toggle-nav list-unstyled text-start ps-5 pe-0 pb-3">
            <li>
                <a href="{{ url('report_products') }}" class="d-inline-flex text-decoration-none mt-3">Products</a>
            </li>
            <li>
                <a href="{{ url('report_services') }}" class="d-inline-flex text-decoration-none">Services</a>
            </li>
            <li>
                <a href="{{ url('report_jobs') }}" class="d-inline-flex text-decoration-none">Jobs</a>
            </li>
            <li>
                <a href="{{ url('report_projects') }}" class="d-inline-flex text-decoration-none">Projects</a>
            </li>
            <li>
                <a href="{{ url('report_leads') }}" class="d-inline-flex text-decoration-none">Leads</a>
            </li>
            <li>
                <a href="{{ url('report_premium') }}" class="d-inline-flex text-decoration-none">Premium</a>
            </li>
            <li>
                <a href="{{ url('report_readytowork') }}" class="d-inline-flex text-decoration-none">Ready To Work</a>
            </li>
            <li>
                <a href="{{ url('report_badges') }}" class="d-inline-flex text-decoration-none">Badges</a>
            </li>
            <li>
                <a href="{{ url('report_chatbot') }}" class="d-inline-flex text-decoration-none">Chatbot</a>
            </li>
        </ul>
    </div>
</li>
<li class="mb-2">
    <button class="asidebtn mx-auto collapsed {{ Request::routeIs('franchise_*') ? 'active' : '' }}"
        data-bs-toggle="collapse" data-bs-target="#collapseFranchise">
        <div class="btnname">
            <img src="{{ asset('assets/images/admin/icon_franchise.png') }}" height="20px" alt="">
            <span>Franchise</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapseFranchise">
        <ul class="btn-toggle-nav list-unstyled text-start ps-5 pe-0 pb-3">
            <li>
                <a href="{{ url('franchise_dashboard') }}" class="d-inline-flex text-decoration-none mt-3">Dashboard</a>
            </li>
            <li>
                <a href="{{ url('franchise_list') }}" class="d-inline-flex text-decoration-none">List</a>
            </li>
            <li>
                <a href="{{ url('franchise_settlement') }}" class="d-inline-flex text-decoration-none">Settlement</a>
            </li>
        </ul>
    </div>
</li>
<li>
    <form action="{{ route('admin_logout') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="asidebtn mx-auto">
            <div class="btnname">
                <img src="{{ asset('assets/images/admin/icon_logout.png') }}" height="20px" alt="">
                <span>Logout</span>
            </div>
            <div class="righticon d-flex ms-auto">
                <i class="fa-solid fa-angle-right toggle-icon"></i>
            </div>
        </button>
    </form>
</li>