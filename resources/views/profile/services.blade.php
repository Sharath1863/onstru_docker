<style>
    #addService,
    #editService {
        .error-message {
            font-size: 14px;
            margin-top: 5px;
            padding: 8px 12px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            color: #721c24;
            display: block;
        }
    }
</style>

<div class="body-head mb-3">
    <h5>Services</h5>
    <div class="d-flex align-items-center column-gap-2">
        <a>
            <button class="removebtn" id="highlightServices">Highlighted Services</button>
        </a>
        <a data-bs-toggle="modal" data-bs-target="#addService">
            <button class="listbtn">+ Add Service</button>
        </a>
    </div>
</div>

<!-- Search -->
<div class="form-div">
    <div class="inpleftflex mb-3">
        <i class="fas fa-search"></i>
        <input type="text" name="keyword" id="serviceSearch" class="form-control border-0" placeholder="Search"
            value="{{ request('servicesKeyword') }}">
    </div>
</div>

<!-- Empty State -->
<div class="side-cards shadow-none border-0" id="noServices"
    style="{{ count($services) > 0 ? 'display: none;' : 'display: block;' }}">
    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
        <img src="{{ asset('assets/images/Empty/NoServices.png') }}" height="200px" class="d-flex mx-auto mb-2" alt="">
        <h5 class="text-center mb-0">No Services Found</h5>
        <h6 class="text-center bio">No services are available right now - try adjusting your search or browse through
            other
            options.</h6>
    </div>
</div>

<!-- Service Cards -->
<div class="service-cards">
    @foreach ($services as $service)
        <div class="side-cards service-card filter-services w-100 mx-auto row position-relative"
            data-id="{{ $service->id }}" data-type="{{ $service->serviceType->value }}"
            data-budget="{{ $service->price_per_sq_ft }}" data-location="{{ $service->locationRelation->value ?? '' }}"
            data-highlighted="{{ $service->highlighted }}">
            <div class="col-sm-12 col-md-5 m-auto">
                <img src="{{ asset($service->image ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $service->image : 'assets/images/NoImage.png') }}"
                    class="object-fit-cover object-center w-100 rounded-3" height="175px" alt="{{ $service->title }}">
            </div>
            <div class="col-sm-12 col-md-7 m-auto">
                @if ($service->highlighted == '1')
                    <a href="{{ url('view-service-highlight', $service->id) }}" class="badge d-flex align-items-center">
                        <img src="{{ asset('assets/images/icon_highlights.png') }}" height="15px" class="pe-1" alt="">
                        <span>Highlighted</span>
                    </a>
                @endif
                <div class="cards-head">
                    <h5 class="mb-2 long-text">{{ $service->title ?? '-' }}</h5>
                    <h6 class="mb-2 long-text">
                        <i class="fas fa-tools pe-1"></i> {{ $service->serviceType->value ?? '-' }}
                    </h6>
                    <h6 class="mb-2 long-text">{{ $service->description ?? '-' }}</h6>
                </div>
                <div class="cards-content d-flex align-items-start justify-content-between flex-wrap">
                    <div>
                        <h6 class="mb-2">
                            <i class="fas fa-star text-warning"></i>
                            {{ number_format($service->reviews_avg_stars, 1) }}
                            ({{ $service->reviews_count }})
                        </h6>
                        <h6 class="mb-2">
                            <i class="fas fa-location-dot pe-1"></i>{{ $service->locationRelation->value ?? '-' }}
                        </h6>
                        <h6 class="mb-2">
                            <i class="fas fa-indian-rupee-sign pe-1"></i> {{ $service->price_per_sq_ft ?? '-' }} per sqft.
                        </h6>
                    </div>
                    <h6
                        class="{{ $service->approvalstatus == 'approved' ? 'green-label' : 'yellow-label' }} mb-2 text-capitalize">
                        {{ $service->approvalstatus }}
                    </h6>
                </div>
                <div class="row align-items-center justify-content-between">
                    <div class="col-6">
                        <button class="removebtn w-100" data-bs-toggle="modal"
                            data-bs-target="#editService{{ $service->id }}">
                            Edit
                        </button>
                    </div>
                    <div class="col-3 ps-0">
                        <a href="{{ url('view-services/' . $service->id) }}">
                            <button class="iconbtn w-100" data-bs-toggle="tooltip" data-bs-title="View Service">
                                <i class="fas fa-external-link"></i>
                            </button>
                        </a>
                    </div>
                    <div class="col-3 ps-0">
                        @if ($service->approvalstatus == 'approved')
                            @if ($service->highlighted == 0)
                                <a data-bs-toggle="modal" data-bs-target="#boostService"
                                    onclick="highlightService({{ $service->id }})">
                                    <button type="button" class="iconbtn w-100" data-bs-toggle="tooltip" data-bs-title="Highlight">
                                        <i class="far fa-star"></i>
                                    </button>
                                </a>
                            @elseif ($service->highlighted == 1)
                                <a href="{{ url('view-service-highlight', $service->id) }}">
                                    <button type="button" class="iconbtn w-100 text-warning" data-bs-toggle="tooltip"
                                        data-bs-title="Highlight">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </a>
                            @endif
                        @else
                            <button type="button" class="iconbtn w-100" data-bs-toggle="tooltip"
                                data-bs-title="Wait for Approval">
                                <i class="far fa-star"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Service Modal -->
        <div class="modal fade" id="editService{{ $service->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="editServiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="m-0">Update Service</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('services.update', $service->id) }}" class="editServiceForm" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-sm-12 col-md-12 mb-2">
                                    <label for="editTitle">Title <span>*</span></label>
                                    <input type="text" class="form-control" name="title" id="editTitle"
                                        value="{{ $service->title }}" required>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="editServicetype">Service Type <span>*</span></label>
                                    <select class="form-select editServicetype" name="servicetype" required>
                                        <option value="" disabled>Select Service Type</option>
                                        @foreach ($serviceTypes as $id => $serviceType)
                                            <option value="{{ $id }}" {{ $id == $service->service_type ? 'selected' : '' }}>
                                                {{ $serviceType }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="editPrice">Price per sqft <span>*</span></label>
                                    <input type="number" step="0.01" class="form-control" name="price" id="editPrice"
                                        value="{{ $service->price_per_sq_ft }}" required>
                                </div>
                                <div class="col-sm-12 col-md-12 mb-2">
                                    <label for="editServiceLoc">Location <span>*</span></label>
                                    <select class="form-select editServicetype" name="location" required>
                                        <option value="" disabled>Select Location</option>
                                        @foreach ($locations as $id => $location)
                                            <option value="{{ $id }}" {{ $id == $service->location ? 'selected' : '' }}>
                                                {{ $location }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-12 mb-2">
                                    <label for="editDescp">Description <span>*</span></label>
                                    <textarea rows="2" class="form-control" name="description" id="editDescp"
                                        required>{{ $service->description }}</textarea>
                                </div>
                                @php
                                    $images = $service->sub_images ? json_decode($service->sub_images, true) : [];
                                    $s3BaseUrl = 'https://onstru-social.s3.ap-south-1.amazonaws.com/';
                                @endphp
                                <div class="col-sm-12 col-md-12 mb-3">
                                    <label for="images">Upload Project Images <span>*</span></label>
                                    <div class="col-sm-12 col-md-12" id="imgContainer">
                                        <div class="mb-2">
                                            <label class="custom-file-upload w-100"
                                                for="service-edit-img-1-{{ $service->id }}">
                                                <div class="icon mb-2">
                                                    <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="25px"
                                                        alt="">
                                                </div>
                                                <input type="file" id="service-edit-img-1-{{ $service->id }}"
                                                    name="service-image-1" accept="image/*,video/mp4"
                                                    onchange="previewImage(this, 'service-edit-preview-1-{{ $service->id }}')">
                                            </label>
                                            <img src="{{ isset($images[0]) ? $s3BaseUrl . $images[0] : '' }}"
                                                class="rounded-2" width="100%" height="75px"
                                                id="service-edit-preview-1-{{ $service->id }}"
                                                style="{{ isset($images[0]) ? '' : 'display: none;' }}" alt="">
                                        </div>
                                        <div class="mb-2">
                                            <label class="custom-file-upload w-100"
                                                for="service-edit-img-2-{{ $service->id }}">
                                                <div class="icon mb-2">
                                                    <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="25px"
                                                        alt="">
                                                </div>
                                                <input type="file" id="service-edit-img-2-{{ $service->id }}"
                                                    name="service-image-2" accept="image/*,video/mp4"
                                                    onchange="previewImage(this, 'service-edit-preview-2-{{ $service->id }}')">
                                            </label>
                                            <img src="{{ isset($images[1]) ? $s3BaseUrl . $images[1] : '' }}"
                                                class="rounded-2" width="100%" height="75px"
                                                id="service-edit-preview-2-{{ $service->id }}"
                                                style="{{ isset($images[1]) ? '' : 'display: none;' }}" alt="">
                                        </div>
                                        <div class="mb-2">
                                            <label class="custom-file-upload w-100"
                                                for="service-edit-img-3-{{ $service->id }}">
                                                <div class="icon mb-2">
                                                    <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="25px"
                                                        alt="">
                                                </div>
                                                <input type="file" id="service-edit-img-3-{{ $service->id }}"
                                                    name="service-image-3" accept="image/*,video/mp4"
                                                    onchange="previewImage(this, 'service-edit-preview-3-{{ $service->id }}')">
                                            </label>
                                            <img src="{{ isset($images[2]) ? $s3BaseUrl . $images[2] : '' }}"
                                                class="rounded-2" width="100%" height="75px"
                                                id="service-edit-preview-3-{{ $service->id }}"
                                                style="{{ isset($images[2]) ? '' : 'display: none;' }}" alt="">
                                        </div>
                                        <div class="mb-2">
                                            <label class="custom-file-upload w-100"
                                                for="service-edit-img-4-{{ $service->id }}">
                                                <div class="icon mb-2">
                                                    <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="25px"
                                                        alt="">
                                                </div>
                                                <input type="file" id="service-edit-img-4-{{ $service->id }}"
                                                    name="service-image-4" accept="image/*,video/mp4"
                                                    onchange="previewImage(this, 'service-edit-preview-4-{{ $service->id }}')">
                                            </label>
                                            <img src="{{ isset($images[3]) ? $s3BaseUrl . $images[3] : '' }}"
                                                class="rounded-2" width="100%" height="75px"
                                                id="service-edit-preview-4-{{ $service->id }}"
                                                style="{{ isset($images[3]) ? '' : 'display: none;' }}" alt="">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center align-items-center mt-3">
                                    <button type="submit" class="formbtn editServicebtn">Update Service</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Add Service Modal -->
<div class="modal fade" id="addService" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="addServiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Add Service</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addServiceForm" action="{{ route('services.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="d-flex align-items-center justify-content-between">
                            <label class="my-2">Listing : <span class="text-muted">₹
                                    {{ $service_list_pay }} (Included Tax)</span></label>
                            <label class="my-2">Wallet : <span class="text-muted">₹
                                    {{ auth()->user()->balance ?? '0' }}</span></label>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="addTitle">Title <span>*</span></label>
                            <input type="text" class="form-control" name="addTitle" id="addTitle"
                                value="{{ old('addTitle') }}" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addServicetype">Service Type <span>*</span></label>
                            <select class="form-select" name="addServicetype" id="addServicetype" required>
                                <option value="" selected disabled>Select Service Type</option>
                                @foreach ($serviceTypes as $id => $serviceType)
                                    <option value="{{ $id }}" {{ old('addServicetype') == $id ? 'selected' : '' }}>
                                        {{ $serviceType }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addPrice">Price per sqft <span>*</span></label>
                            <input type="number" step="0.01" class="form-control" name="addPrice" id="addPrice"
                                value="{{ old('addPrice') }}" required>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="addServiceLoc">Location <span>*</span></label>
                            <select class="form-select" name="addLoc" id="addServiceLoc" required>
                                <option value="" selected disabled>Select Location</option>
                                @foreach ($locations as $id => $location)
                                    <option value="{{ $id }}" {{ old('addLoc') == $id ? 'selected' : '' }}>
                                        {{ $location }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="addDescp">Description <span>*</span></label>
                            <textarea rows="2" class="form-control" name="addDescp" id="addDescp"
                                required>{{ old('addDescp') }}</textarea>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-3">
                            <label for="images">Upload Project Images <span>*</span></label>
                            <div class="col-sm-12 col-md-12" id="imgContainer">
                                <div class="mb-2">
                                    <label class="custom-file-upload w-100" for="service-image-1">
                                        <div class="icon mb-2">
                                            <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="25px"
                                                alt="">
                                        </div>
                                        <input type="file" id="service-image-1" name="service-image-1"
                                            accept="image/*,video/mp4"
                                            onchange="previewImage(this, 'service-add-preview-1')">
                                    </label>
                                    <img src="" class="rounded-2" width="100%" id="service-add-preview-1"
                                        style="display: none;" alt="">
                                </div>
                                <div class="mb-2">
                                    <label class="custom-file-upload w-100" for="service-image-2">
                                        <div class="icon mb-2">
                                            <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="25px"
                                                alt="">
                                        </div>
                                        <input type="file" id="service-image-2" name="service-image-2"
                                            accept="image/*,video/mp4"
                                            onchange="previewImage(this, 'service-add-preview-2')">
                                    </label>
                                    <img src="" class="rounded-2" width="100%" id="service-add-preview-2"
                                        style="display: none;" alt="">
                                </div>
                                <div class="mb-2">
                                    <label class="custom-file-upload w-100" for="service-image-3">
                                        <div class="icon mb-2">
                                            <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="25px"
                                                alt="">
                                        </div>
                                        <input type="file" id="service-image-3" name="service-image-3"
                                            accept="image/*,video/mp4"
                                            onchange="previewImage(this, 'service-add-preview-3')">
                                    </label>
                                    <img src="" class="rounded-2" width="100%" id="service-add-preview-3"
                                        style="display: none;" alt="">
                                </div>
                                <div class="mb-2">
                                    <label class="custom-file-upload w-100" for="service-image-4">
                                        <div class="icon mb-2">
                                            <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="25px"
                                                alt="">
                                        </div>
                                        <input type="file" id="service-image-4" name="service-image-4"
                                            accept="image/*,video/mp4"
                                            onchange="previewImage(this, 'service-add-preview-4')">
                                    </label>
                                    <img src="" class="rounded-2" width="100%" id="service-add-preview-4"
                                        style="display: none;" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 mb-2">
                            <label>Notes <span>*</span></label>
                            <h6>Amount will be deducted from the wallet</h6>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2 d-flex align-items-center column-gap-2">
                            <input type="checkbox" id="addPay" name="addPay" required>
                            <label class="mb-0" for="addPay">Agree to Pay</label>
                        </div>
                        <small id="service_balance_message" style="display:none; color:red;">Insufficient
                            Balance</small>
                        <div class="d-flex justify-content-center align-items-center column-gap-2 mt-3">
                            <button type="submit" class="formbtn addServicebtn">Add Service</button>
                            <a href="{{ url('wallet') }}" target="_blank" id="rechargeBtn" style="display:none;">
                                <button type="button" class="removebtn">Recharge</button>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Highlight Modal -->
<div class="modal fade" id="boostService" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('services.highlight') }}" method="POST" class="highlightServiceForm"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Highlight Service</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row mt-2">
                    <!-- Hidden service id -->
                    <input type="hidden" name="service_id" id="boost_service_id">
                    <div class="d-flex align-items-center justify-content-between">
                        <label class="my-2">Highlight / Click : <span class="text-muted">₹
                                {{ $service_click_charge }} (Included Tax)</span></label>
                        <label class="my-2">Wallet : <span class="text-muted">₹
                                {{ auth()->user()->balance ?? '0' }}</span></label>
                    </div>

                    <div class="col-sm-12 mb-2">
                        <label>Service Title</label>
                        <h6 id="viewServiceTitle"></h6>
                    </div>

                    <div class="col-sm-12 mb-2">
                        <label for="service_click">Highlight / Clicks <span>*</span></label>
                        <input type="number" min="5" class="form-control" name="clicks" id="service_click" value=""
                            oninput="checkServiceBoost()" required>
                    </div>

                    <div class="col-sm-12 mb-2">
                        <label>Total</label>
                        <h4 id="total_amount">₹ 0.00</h4>
                        <small id="boost_balance_message" style="display:none; color:red;"></small>
                    </div>

                    <div class="col-sm-12 mb-2">
                        <label for="addVideo">Video for Reels (Optional)</label>
                        <input type="file" class="form-control" id="addVideo" name="servicevideo" accept="video/*"
                            onchange="validateVideo(this)">
                    </div>

                    <div class="col-sm-12 mb-2">
                        <label>Notes <span>*</span></label>
                        <h6>Amount will be deducted from the wallet</h6>
                    </div>

                    <div class="col-sm-12 mb-2">
                        <div class="d-flex align-items-center column-gap-2">
                            <input type="checkbox" id="serviceHighlightCheck" required>
                            <label for="serviceHighlightCheck" class="mb-0">Agree To Pay</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-center column-gap-2 mb-2">
                    <button type="submit" class="formbtn highlightServicebtn">Highlight Service</button>
                    <a href="{{ url('wallet') }}" target="_blank">
                        <button type="button" class="removebtn" id="recharge_button" style="display: none;">
                            Recharge
                        </button>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Search Filter -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const highlightServices = document.getElementById('highlightServices');
        const serviceSearch = document.getElementById('serviceSearch');
        const noServices = document.getElementById('noServices');
        const serviceCards = document.querySelectorAll('.filter-services');
        let showHighlighted = false;

        serviceSearch.addEventListener('input', function () {
            let serviceMatch = false;
            const servicesKeyword = this.value.toLowerCase();
            serviceCards.forEach(card => {
                const cardText = card.textContent.toLowerCase();
                if (cardText.includes(servicesKeyword)) {
                    card.style.display = 'flex';
                    serviceMatch = true;
                } else {
                    card.style.display = 'none';
                }
            });
            noServices.style.display = serviceMatch ? 'none' : 'block';
        });

        highlightServices.addEventListener('click', function () {
            showHighlighted = !showHighlighted;
            let serviceMatch = false;
            serviceCards.forEach(card => {
                if (!showHighlighted || card.dataset.highlighted === "1") {
                    card.style.display = 'flex';
                    serviceMatch = true;
                } else {
                    card.style.display = 'none';
                }
            });
            noServices.style.display = serviceMatch ? 'none' : 'block';
            highlightServices.textContent = showHighlighted ? "All Services" : "Highlighted Services";
        });
    });
</script>

<!-- Select 2 -->
<script>
    $(function () {
        function initSelect2(modal) {
            modal.find('.editServicetype, .editServiceLoc, #addServicetype, #addServiceLoc').each(function () {
                let $select = $(this);
                if ($select.hasClass('select2-hidden-accessible')) return;
                $select.select2({
                    width: "100%",
                    placeholder: "Select Options",
                    allowClear: true,
                    dropdownParent: modal
                });
            });
        }
        $('#addService').on('shown.bs.modal', function () {
            initSelect2($(this));
        });
        $(document).on('shown.bs.modal', '[id^="editService"]', function () {
            initSelect2($(this));
        });
    });
</script>

<script>
    function highlightService(id) {
        fetch(`{{ url('services') }}/${id}/edit`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                document.getElementById('boost_service_id').value = data.id;
                document.getElementById('viewServiceTitle').textContent = data.title || "-";
                document.getElementById('service_click').value = "";
                document.getElementById('total_amount').textContent = "₹ 0.00";
                document.getElementById('boost_balance_message').style.display = "none";
                document.getElementById('recharge_button').style.display = "none";
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading service data: ' + error.message);
            });
    }

    function showToast(message, type = 'info') {
        if (type === 'error') {
            alert(message);
        }
    }
</script>

<!-- Recharge -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const agreeCheckbox = document.getElementById("addPay");
        const balanceMessage = document.getElementById("service_balance_message");
        const submitBtn = document.querySelector("#addServiceForm button[type='submit']");
        const rechargeBtn = document.getElementById("rechargeBtn");

        // Get values from Blade (rendered server-side into JS correctly)
        const serviceCost = {{ $service_list_pay ?? 0 }};
        const userBalance = {{ auth()->user()->balance ?? 0 }};

        // Reset initial states
        balanceMessage.style.display = "none";
        rechargeBtn.style.display = "none";
        submitBtn.disabled = false;

        agreeCheckbox.addEventListener("change", function () {
            if (this.checked) {
                if (userBalance < serviceCost) {
                    balanceMessage.style.display = "block";
                    rechargeBtn.style.display = "inline-block";
                    submitBtn.disabled = true;
                } else {
                    balanceMessage.style.display = "none";
                    rechargeBtn.style.display = "none";
                    submitBtn.disabled = false;
                }
            } else {
                balanceMessage.style.display = "none";
                rechargeBtn.style.display = "none";
                submitBtn.disabled = false;
            }
        });
    });
</script>

<!-- Highlight Service -->
<script>
    function checkServiceBoost() {
        const modal = document.getElementById('boostService');

        const perClickCharge = Number("{{ $service_click_charge ?? 0 }}");
        const walletBalance = Number("{{ auth()->user()->balance ?? 0 }}");

        const clicksInput = modal.querySelector('#service_click');
        const totalEl = modal.querySelector('#total_amount');
        const msgEl = modal.querySelector('#boost_balance_message');
        const submitBtn = modal.querySelector('.formbtn');
        const rechargeBtn = modal.querySelector('#recharge_button');

        const clicks = parseInt(clicksInput.value, 10) || 0;
        const total = clicks * perClickCharge;

        // Show total live
        totalEl.textContent = '₹ ' + total.toFixed(2);

        // Reset UI
        msgEl.style.display = 'none';
        rechargeBtn.style.display = 'none';
        submitBtn.disabled = false;

        // Rules
        if (clicks > 0 && clicks < 5) {
            msgEl.textContent = 'Minimum 5 clicks required';
            msgEl.style.display = 'block';
            submitBtn.disabled = true;
            return;
        }

        if (total > walletBalance) {
            msgEl.textContent = 'Insufficient Balance';
            msgEl.style.display = 'block';
            rechargeBtn.style.display = 'inline-block';
            submitBtn.disabled = true;
        }
    }

    // auto-run when modal is opened
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('boostService');
        if (modalEl) {
            modalEl.addEventListener('shown.bs.modal', checkServiceBoost);
        }
    });
</script>

<!-- Prevent Multiple Submissions -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('addServiceForm');
        const submitBtn = document.querySelector('.addServicebtn');
        let isSubmitting = false;

        form.addEventListener('submit', function (e) {
            const requiredImageInput = document.getElementById('service-image-1');
            if (!requiredImageInput.files.length) {
                e.preventDefault();
                showToast("Service first Image is Mandatory. Please Fill all the fields");
                requiredImageInput.focus();
                return;
            }

            if (isSubmitting) {
                e.preventDefault();
                return;
            }
            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Submitting...`;
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('.editServiceForm');
        const submitBtn = document.querySelector('.editServicebtn');
        let isSubmitting = false;

        form.addEventListener('submit', function (e) {
            if (isSubmitting) {
                e.preventDefault();
                return;
            }
            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Updating...`;
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('boostService');
        const submitBtn = document.querySelector('.highlightServicebtn');
        let isSubmitting = false;

        form.addEventListener('submit', function (e) {
            if (isSubmitting) {
                e.preventDefault();
                return;
            }
            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Highlighting...`;
        });
    });
</script>