@php
    $role = $user->as_a;
@endphp

<li class="nav-item w-100 mb-2" role="presentation">
    <button class="profilebtn w-100 d-flex align-items-center justify-content-between active" data-bs-toggle="tab"
        type="button" data-bs-target="#posts">
        <span><i class="fas fa-table-cells pe-3"></i><span>Posts</span></span>
    </button>
</li>
@if($role === 'Vendor')
    <li class="nav-item w-100 mb-2" role="presentation">
        <button class="profilebtn w-100 d-flex align-items-center justify-content-between" data-bs-toggle="tab"
            type="button" data-bs-target="#products">
            <span><i class="fas fa-tag pe-3"></i><span>Products</span></span>
        </button>
    </li>
    <li class="nav-item w-100 mb-2" role="presentation">
        <button class="profilebtn w-100 d-flex align-items-center justify-content-between" data-bs-toggle="tab"
            type="button" data-bs-target="#jobs">
            <span><i class="fas fa-briefcase pe-3"></i><span>Jobs</span></span>
        </button>
    </li>
    <li class="nav-item w-100 mb-2" role="presentation">
        <button class="profilebtn w-100 d-flex align-items-center justify-content-between" role="tab" data-bs-toggle="tab"
            type="button" data-bs-target="#service">
            <span><i class="fas fa-tools pe-3"></i><span>Services</span></span>
        </button>
    </li>
@elseif($role === 'Contractor' || $role === 'Consultant')
    <li class="nav-item w-100 mb-2" role="presentation">
        <button class="profilebtn w-100 d-flex align-items-center justify-content-between" data-bs-toggle="tab"
            type="button" data-bs-target="#projects">
            <span><i class="fas fa-list-check pe-3"></i><span>Projects</span></span>
        </button>
    </li>
    <li class="nav-item w-100 mb-2" role="presentation">
        <button class="profilebtn w-100 d-flex align-items-center justify-content-between" data-bs-toggle="tab"
            type="button" data-bs-target="#products">
            <span><i class="fas fa-tag pe-3"></i><span>Products</span></span>
        </button>
    </li>
    <li class="nav-item w-100 mb-2" role="presentation">
        <button class="profilebtn w-100 d-flex align-items-center justify-content-between" data-bs-toggle="tab"
            type="button" data-bs-target="#jobs">
            <span><i class="fas fa-briefcase pe-3"></i><span>Jobs</span></span>
        </button>
    </li>
    <li class="nav-item w-100 mb-2" role="presentation">
        <button class="profilebtn w-100 d-flex align-items-center justify-content-between" role="tab" data-bs-toggle="tab"
            type="button" data-bs-target="#service">
            <span><i class="fas fa-tools pe-3"></i><span>Services</span></span>
        </button>
    </li>
@endif
<!--  -->