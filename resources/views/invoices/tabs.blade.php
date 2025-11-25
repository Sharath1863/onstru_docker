<li class="nav-item w-100 mb-2" role="presentation">
    <button class="profilebtn w-100 text-start active" role="tab" data-bs-toggle="tab" type="button"
        data-bs-target="#premium">
        <i class="fas fa-crown pe-1"></i><span>Premium</span>
    </button>
</li>
@if (auth()->user()->as_a == 'Vendor')
    <li class="nav-item w-100 mb-2" role="presentation">
        <button class="profilebtn w-100 text-start" data-bs-toggle="tab" type="button" data-bs-target="#badges">
            <i class="fas fa-certificate pe-1"></i><span>Badges</span>
        </button>
    </li>
    <li class="nav-item w-100 mb-2" role="presentation">
        <button class="profilebtn w-100 text-start" data-bs-toggle="tab" type="button" data-bs-target="#products">
            <i class="fas fa-tag pe-1"></i><span>Products</span>
        </button>
    </li>
    <li class="nav-item w-100 mb-2" role="presentation">
        <button class="profilebtn w-100 text-start" data-bs-toggle="tab" type="button" data-bs-target="#jobs">
            <i class="fas fa-briefcase pe-1"></i><span>Jobs</span>
        </button>
    </li>
    <li class="nav-item w-100 mb-2" role="presentation">
        <button class="profilebtn w-100 text-start" role="tab" data-bs-toggle="tab" type="button"
            data-bs-target="#services">
            <i class="fas fa-tools pe-1"></i><span>Services</span>
        </button>
    </li>
@elseif (auth()->user()->as_a == 'Contractor' || auth()->user()->as_a == 'Consultant')
    <li class="nav-item w-100 mb-2" role="presentation">
        <button class="profilebtn w-100 text-start" data-bs-toggle="tab" type="button" data-bs-target="#projects">
            <i class="fas fa-list-check pe-1"></i><span>Projects</span>
        </button>
    </li>
    <li class="nav-item w-100 mb-2" role="presentation">
        <button class="profilebtn w-100 text-start" data-bs-toggle="tab" type="button" data-bs-target="#products">
            <i class="fas fa-tag pe-1"></i><span>Products</span>
        </button>
    </li>
    <li class="nav-item w-100 mb-2" role="presentation">
        <button class="profilebtn w-100 text-start" data-bs-toggle="tab" type="button" data-bs-target="#jobs">
            <i class="fas fa-briefcase pe-1"></i><span>Jobs</span>
        </button>
    </li>
    <li class="nav-item w-100 mb-2" role="presentation">
        <button class="profilebtn w-100 text-start" role="tab" data-bs-toggle="tab" type="button"
            data-bs-target="#services">
            <i class="fas fa-tools pe-1"></i><span>Services</span>
        </button>
    </li>
@elseif (auth()->user()->you_are == 'Professional' || auth()->user()->you_are == 'Consumer')
    <li class="nav-item w-100 mb-2" role="presentation">
        <button class="profilebtn w-100 text-start" role="tab" data-bs-toggle="tab" type="button"
            data-bs-target="#readytowork">
            <i class="fas fa-briefcase pe-1"></i><span>Ready To Work</span>
        </button>
    </li>
@endif
<!--  -->
<li class="nav-item w-100 mb-2" role="presentation">
    <button class="profilebtn w-100 text-start" role="tab" data-bs-toggle="tab" type="button" data-bs-target="#leads">
        <i class="fas fa-paper-plane pe-1"></i><span>Owned Leads</span>
    </button>
</li>
<li class="nav-item w-100" role="presentation">
    <button class="profilebtn w-100 text-start" role="tab" data-bs-toggle="tab" type="button" data-bs-target="#chatbot">
        <i class="fas fa-comment pe-1"></i><span>Chatbot</span>
    </button>
</li>