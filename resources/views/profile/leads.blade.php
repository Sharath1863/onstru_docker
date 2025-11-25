<div class="body-head mb-3">
    <h5>Leads</h5>
    <a data-bs-toggle="modal" data-bs-target="#addLeads">
        <button class="listbtn">+ Add Leads</button>
    </a>
</div>

<!-- Search -->
<div class="form-div">
    <div class="inpleftflex mb-3">
        <i class="fas fa-search"></i>
        <input type="text" name="keyword" id="leadSearch" class="form-control border-0" placeholder="Search"
            value="{{ request('leadsKeyword') }}">
    </div>
</div>

<!-- Empty Search -->
<div class="side-cards shadow-none border-0" id="noLeads"
    style="{{ count($leads) > 0 ? 'display: none;' : 'display: block;' }}">
    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
        <img src="{{ asset('assets/images/Empty/NoLeads.png') }}" height="200px" class="d-flex mx-auto mb-2" alt="">
        <h5 class="text-center mb-0">No Leads Found</h5>
        <h6 class="text-center bio">Currently, no leads are available - try adjusting your filters or check back
            later for new opportunities.</h6>
    </div>
</div>

@foreach ($leads as $lead)
    <div class="side-cards job-card filter-leads h-100 mx-auto mb-2" data-type="{{ $lead->serviceRelation->value }}"
        data-price="{{ $lead->budget }}" data-buildup="{{ $lead->buildup_area }}"
        data-location="{{ $lead->locationRelation->value }}">
        <div class="cards-head d-flex align-items-center justify-content-between">
            <h5 class="mb-2">{{ $lead->title ?? '-' }}</h5>
            <h6 class="mb-0 {{ $lead->approval_status == 'approved' ? 'green-label' : 'yellow-label' }}">
                {{ $lead->approval_status ?? '-' }}
            </h6>
        </div>
        <div class="cards-content">
            <h6 class="mb-2 bio long-text">{{ $lead->description }}</h6>
            <div class="d-flex align-items-center justify-content-start flex-wrap column-gap-4 mb-2">
                <h6 class="mb-2 d-flex align-items-center gap-2">
                    <i class="fas fa-star text-warning"></i>
                    {{ number_format($lead->reviews_avg_stars, 1) }}
                    ({{ $lead->reviews_count ?? '-' }})
                </h6>
                <h6 class="mb-2 d-flex align-items-center gap-2">
                    <i class="fas fa-tools"></i>
                    {{ $lead->serviceRelation->value ?? '-' }}
                </h6>
                <h6 class="mb-2 d-flex align-items-center gap-2">
                    <i class="fas fa-maximize"></i>
                    {{ $lead->buildup_area ?? '-' }} sqft.
                </h6>
                <h6 class="mb-2 d-flex align-items-center gap-2">
                    ₹ {{ $lead->budget ?? '-' }}
                </h6>
                <h6 class="mb-2 d-flex align-items-center gap-2">
                    <i class="fas fa-location-dot"></i>
                    {{ $lead->locationRelation->value ?? '-' }}
                </h6>
                <h6 class="mb-2 d-flex align-items-center gap-2">
                    <i class="far fa-calendar"></i>
                    {{ $lead->start_date ?? '-' }}
                </h6>
            </div>
            <div class="d-flex align-items-center justify-content-end flex-wrap">
                <div class="d-flex align-items-center flex-wrap column-gap-3">
                    @php
                        $repostCount = 5 - $lead->repost;
                    @endphp
                    @if ($lead->status == 'inactive' && $lead->repost < 5)
                        <button class="followingbtn" data-bs-toggle="modal" data-bs-target="#repostLeads{{ $lead->id }}">Repost
                            <span class="count-sm ms-1">{{ $repostCount }}</span></button>
                    @endif
                    @if ($lead->approval_status == 'pending' && $lead->repost == 0)
                        <button class="followersbtn" data-bs-toggle="modal" data-bs-target="#editLeads{{ $lead->id }}">
                            Edit Lead
                        </button>
                    @endif
                    <button class="removebtn" data-bs-toggle="modal" data-bs-target="#viewLeads{{ $lead->id }}">
                        View Lead
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Leads Modal -->
    <div class="modal fade" id="viewLeads{{ $lead->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="viewLeadsLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0">View Leads</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewLeadContent">
                    <div class="modal-grid-card mt-3">
                        <div class="col-sm-12 mb-2">
                            <label>Title</label>
                            <h6>{{ $lead->title }}</h6>
                        </div>
                        <div class="col-sm-12 mb-2">
                            <label>Service Type</label>
                            <h6>{{ $lead->serviceRelation->value }}</h6>
                        </div>
                        <div class="col-sm-12 mb-2">
                            <label>Buildup Area (sqft)</label>
                            <h6>{{ $lead->buildup_area }}</h6>
                        </div>
                        <div class="col-sm-12 mb-2">
                            <label>Budget</label>
                            <h6>{{ $lead->budget }}</h6>
                        </div>
                        <div class="col-sm-12 mb-2">
                            <label>Start Date</label>
                            <h6>{{ $lead->start_date }}</h6>
                        </div>
                        <div class="col-sm-12 mb-2">
                            <label>Location</label>
                            <h6>{{ $lead->locationRelation->value }}</h6>
                        </div>
                        <div class="col-sm-12 mb-2">
                            <label>Rating</label>
                            <h6>
                                <i class="fas fa-star text-warning"></i>
                                {{ number_format($lead->reviews_avg_stars, 1) }}
                                ({{ $lead->reviews_count }})
                            </h6>
                        </div>
                        <div class="col-sm-12 mb-2">
                            <label>Description</label>
                            <h6>{{ $lead->description }}</h6>
                        </div>
                        @if (auth()->user())
                            <div class="col-sm-12">
                                <label class="mb-2">Admin Remarks</label>
                                <h6 class="mb-0">{{ $lead->remark ?? '-' }}</h6>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Leads Modal -->
    <div class="modal fade" id="editLeads{{ $lead->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="editLeadsLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0">Edit Leads</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('leads.update', $lead->id) }}" method="POST" class="editLeadForm"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label for="editLeadsTitle">Title <span>*</span></label>
                                <input type="text" class="form-control" name="title" id="editLeadsTitle"
                                    value="{{ $lead->title }}" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="editLeadService">Service Type <span>*</span></label>
                                <select class="form-select editLeadService" name="service_type" id="editLeadService"
                                    required>
                                    <option value="" disabled>Select Service Type</option>
                                    @foreach ($serviceTypes as $id => $type)
                                        <option value="{{ $id }}" {{ $id == $lead->service_type ? 'selected' : 'disabled' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="editLeadBuildup">Buildup Area (sqft) <span>*</span></label>
                                <input type="number" class="form-control" name="buildup_area" id="editLeadBuildup"
                                    value="{{ $lead->buildup_area }}" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="editLeadBudget">Budget <span>*</span></label>
                                <input type="number" class="form-control" name="budget" id="editLeadBudget"
                                    value="{{ $lead->budget }}" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="editLeadStartDate">Start Date <span>*</span></label>
                                <input type="date" class="form-control" name="start_date" id="editLeadStartDate"
                                    value="{{ $lead->start_date }}" min="{{ \Carbon\Carbon::today()->toDateString() }}"
                                    required>

                            </div>
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label for="editLeadLoc">Location <span>*</span></label>
                                <select class="form-select editLeadLoc" name="location" id="editLeadLoc" required>
                                    <option value="" disabled>Select Location</option>
                                    @foreach ($locations as $id => $location)
                                        <option value="{{ $id }}" {{ $id == $lead->leaction ? 'selected' : '' }}>
                                            {{ $location }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label for="editLeadDescp">Description <span>*</span></label>
                                <textarea rows="2" class="form-control" name="description" id="editLeadDescp"
                                    required>{{ $lead->description }}</textarea>
                            </div>
                            <div class="col-sm-12">
                                <label>Notes <span>*</span></label>
                                <h6 class="mb-0">Once a lead is approved, the edit option will be disabled, ensuring data
                                    accuracy and maintaining record integrity.</h6>
                            </div>

                            <div class="d-flex justify-content-center align-items-center mt-3">
                                <button type="submit" class="formbtn editLeadbtn">Update Leads</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Repost Leads Modal -->
    <div class="modal fade" id="repostLeads{{ $lead->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="repostLeadsLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0">Repost Lead</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('leads.repost', $lead->id) }}" method="POST" class="repostLeadForm"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label for="repostLeadsTitle">Title <span>*</span></label>
                                <input type="text" class="form-control" name="title" id="repostLeadsTitle"
                                    value="{{ $lead->title }}" required readonly>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="repostLeadService">Service Type <span>*</span></label>
                                <input type="text" class="form-control" value="{{ $lead->serviceRelation->value }}" readonly
                                    required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="repostLeadStartDate">Start Date <span>*</span></label>
                                <input type="date" class="form-control" name="start_date" id="editLeadStartDate"
                                    value="{{ $lead->start_date }}" min="{{ \Carbon\Carbon::today()->toDateString() }}"
                                    required>
                            </div>

                            <div class="col-sm-12 col-md-6 mb-2">
                                <label>Notes <span>*</span></label>
                                <h6>Change date if you want..!</h6>
                            </div>

                            <div class="d-flex justify-content-center align-items-center mt-3">
                                <button type="submit" class="formbtn repostLeadbtn">Repost Leads</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Add Leads Modal -->
<div class="modal fade" id="addLeads" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="addLeadsLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Add Leads</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('leads.store') }}" method="POST" id="addLeadForm">
                    @csrf
                    <input type="number" name="user_id" value="{{ auth()->user()->id }}" hidden>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="addTitle">Title <span>*</span></label>
                            <input type="text" class="form-control" name="title" id="addTitle" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addLeadService">Service Type <span>*</span></label>
                            <select class="form-select addLeadService" name="service_type" id="addLeadService" required>
                                <option value="" selected disabled>Select Service Type</option>
                                @foreach ($serviceTypes as $id => $type)
                                    <option value="{{ $id }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addBuildup">Buildup Area (sqft) <span>*</span></label>
                            <input type="number" class="form-control" name="buildup_area" id="addBuildup" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addBudget">Budget <span>*</span></label>
                            <input type="number" class="form-control" name="budget" id="addBudget" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addStartDate">Start Date <span>*</span></label>
                            <input type="date" class="form-control" name="start_date" id="addStartDate" required>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="addLeadLoc">Location <span>*</span></label>
                            <select class="form-select addLeadLoc" name="location" id="addLeadLoc" required>
                                <option value="" selected disabled>Select Location</option>
                                @foreach ($locations as $id => $location)
                                    <option value="{{ $id }}">{{ $location }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="addDescp">Description <span>*</span></label>
                            <textarea rows="2" class="form-control" name="description" id="addDescp"
                                required></textarea>
                        </div>
                        <div class="col-sm-12">
                            <label>Notes <span>*</span></label>
                            <h6 class="mb-0">Once a lead is approved, the edit option will be disabled, ensuring data
                                accuracy and maintaining record integrity.</h6>
                        </div>

                        <div class="d-flex justify-content-center align-items-center mt-3">
                            <button type="submit" class="formbtn addLeadbtn">Add Leads</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Search Filter -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const leadSearch = document.getElementById('leadSearch');
        const noLeads = document.getElementById('noLeads');
        const leadCards = document.querySelectorAll('.filter-leads');
        let showHighlighted = false;

        leadSearch.addEventListener('input', function () {
            let leadMatch = false;
            const leadsKeyword = this.value.toLowerCase();
            leadCards.forEach(card => {
                const cardText = card.textContent.toLowerCase();
                if (cardText.includes(leadsKeyword)) {
                    card.style.display = 'block';
                    leadMatch = true;
                } else {
                    card.style.display = 'none';
                }
            });
            noLeads.style.display = leadMatch ? 'none' : 'block';
        });
    });
</script>

<!-- Select 2 -->
<script>
    $(function () {
        function initSelect2(modal) {
            modal.find('.editLeadService, .editLeadLoc, .addLeadService, .addLeadLoc').each(function () {
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
        $('#addLeads').on('shown.bs.modal', function () {
            initSelect2($(this));
        });
        $(document).on('shown.bs.modal', '[id^="editLeads"]', function () {
            initSelect2($(this));
        });
    });
</script>

<!-- Form Submit Event Listeners -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('addLeadForm');
        const submitBtn = document.querySelector('.addLeadbtn');
        let isSubmitting = false;

        form.addEventListener('submit', function (e) {
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
        const form = document.querySelector('.editLeadForm');
        const submitBtn = document.querySelector('.editLeadbtn');
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
        const form = document.querySelector('.repostLeadForm');
        const submitBtn = document.querySelector('.repostLeadbtn');
        let isSubmitting = false;

        form.addEventListener('submit', function (e) {
            if (isSubmitting) {
                e.preventDefault();
                return;
            }
            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Reposting...`;
        });
    });
</script>