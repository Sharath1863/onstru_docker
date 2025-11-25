<div class="body-head mb-3">
    <h5>Jobs</h5>
    <div class="d-flex align-items-center column-gap-2">
        @if ($gstverified == 'yes')
            <a>
                <button class="removebtn" id="boostedJobsBtn">Boosted Jobs</button>
            </a>
            <a href="{{ route('job.post') }}">
                <button class="listbtn">+ Add Job</button>
            </a>
        @elseif ($gstverified == 'no')
            <a href="{{ url('my-profile') }}">
                <button class="removebtn">Verify GST</button>
            </a>
        @endif
    </div>
</div>

<!-- Search -->
<div class="form-div">
    <div class="inpleftflex mb-3">
        <i class="fas fa-search"></i>
        <input type="text" name="keyword" id="jobsSearch" class="form-control border-0" placeholder="Search"
            value="{{ request('jobsKeyword') }}">
    </div>
</div>

<!-- Empty State -->
<div class="side-cards shadow-none border-0" id="noJobs"
    style="{{ count($jobs) > 0 ? 'display: none;' : 'display: block;' }}">
    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
        <img src="{{ asset('assets/images/Empty/NoJobs.png') }}" height="200px" class="d-flex mx-auto mb-2" alt="">
        <h5 class="text-center mb-0">No Jobs Found</h5>
        <h6 class="text-center bio">No jobs match your search right now - try refining your criteria or check back for
            new
            listings soon.</h6>
    </div>
</div>

<!-- Job Cards -->
@foreach ($jobs as $job)
    <div class="side-cards job-card filter-jobs mb-2 position-relative" data-id="{{ $job->id }}"
        data-category="{{ $job->categoryRelation->value }}" data-type="{{ $job->shift }}" data-exp="{{ $job->experience }}"
        data-salary="{{ $job->salary }}" data-location="{{ $job->location }}" data-boosted="{{ $job->highlighted }}">
        <div class="cards-head d-flex align-items-center justify-content-between">
            <h5 class="mb-3">{{ $job->title ?? '-' }}</h5>
            <h6 class="mb-0 {{ $job->approvalstatus == 'approved' ? 'green-label' : 'yellow-label' }}">
                {{ $job->approvalstatus }}
            </h6>
        </div>
        <div class="cards-content">
            <div class="d-flex align-items-center justify-content-start flex-wrap column-gap-4 mb-2">
                <h6 class="mb-2 d-flex align-items-center gap-2">
                    <i class="fas fa-clipboard-list"></i>
                    {{ $job->categoryRelation->value ?? '-' }}
                </h6>
                <h6 class="mb-2 d-flex align-items-center gap-2">
                    <i class="fas fa-briefcase"></i> Exp :
                    {{ $job->experience == 0 ? 'Fresher' : ($job->experience ? $job->experience . '+ Years' : '-') }}
                </h6>
                <h6 class="mb-2 d-flex align-items-center gap-2">
                    <i class="fas fa-indian-rupee-sign"></i>
                    {{ $job->salary ?? 'Not disclosed' }}
                </h6>
                <h6 class="mb-2 d-flex align-items-center gap-2">
                    <i class="fas fa-location-dot"></i>
                    {{ $job->locationRelation->value ?? '-' }}
                </h6>
                <h6 class="mb-2 d-flex align-items-center gap-2">
                    <i class="fas fa-location"></i>
                    {{ $job->sublocality ?? '-' }}
                </h6>
            </div>
            <h6 class="mb-2"><span class="text-muted">Required Skills :</span> {{ $job->skills ?? 'N/A' }}</h6>
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <h6 class="bio mb-0">{{ $job->created_at->diffForHumans() ?? '-' }}</h6>
                <div class="d-flex align-items-center flex-wrap column-gap-3">
                    @if ($job->approvalstatus == 'approved')
                        @if ($job->boosts->isNotEmpty() && $job->highlighted == 1)
                            <a href="{{ url('view-job-highlight', $job->id) }}">
                                <button class="followersbtn">Boosted</button>
                            </a>
                        @else
                            <a data-bs-toggle="modal" data-bs-target="#boostJob">
                                <button class="followingbtn">Boost Job</button>
                            </a>
                        @endif
                    @endif
                    <a href="{{ url('applied-profiles/' . $job->id) }}">
                        <button class="removebtn">View Job</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Boost Modal -->
<div class="modal fade" id="boostJob" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('job.boost') }}" method="POST" enctype="multipart/form-data" class="jobBoostForm">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Boost Job</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row mt-2">
                    <input type="hidden" name="job_id" id="boost_job_id">
                    <div class="d-flex align-items-center justify-content-between">
                        <label class="my-2">Boost / day : <span class="text-muted">₹
                                {{ $job_charge }} (Included Tax)</span></label>
                        <label class="my-2">Wallet : <span class="text-muted">₹
                                {{ auth()->user()->balance }}</span></label>
                    </div>
                    <div class="col-sm-12 mb-2">
                        <label>Job Title</label>
                        <h6 id="boost_job_title">Surveyor</h6>
                    </div>
                    <div class="col-sm-12 col-md-6 mb-2">
                        <label for="job_from">From Date <span>*</span></label>
                        <input type="date" class="form-control" name="from" id="job_from"
                            min="{{ \Carbon\Carbon::today()->toDateString() }}" value="{{ old('from') }}" required>
                    </div>

                    <div class="col-sm-12 col-md-6 mb-2">
                        <label for="job_end">End Date <span>*</span></label>
                        <input type="date" class="form-control" name="to" id="job_end" value="{{ old('to') }}" required>
                    </div>
                    <div class="col-sm-12 mb-2">
                        <label>Total</label>
                        <h4 id="total_amount">₹ 0.00</h4>
                        <small id="balance_message" style="display:none;">Insufficient
                            Balance</small>
                    </div>
                    <div class="col-sm-12 mb-2">
                        <label>Notes <span>*</span></label>
                        <h6>Amount will be deducted from the wallet</h6>
                    </div>
                    <div class="col-sm-12 mb-2">
                        <div class="d-flex align-items-center column-gap-2">
                            <input type="checkbox" id="jobBoostCheck" required>
                            <label for="jobBoostCheck" class="mb-0">Agree To Pay</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-center column-gap-2 mb-2">
                    <button type="submit" class="formbtn jobBoostbtn">Boost Job</button>
                    <a href="{{ url('wallet') }}" target="_blank">
                        <button type="button" class="removebtn job_boost_btn" id="recharge_button"
                            style="display: none;">
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
        const boostJobs = document.getElementById('boostedJobsBtn');
        const jobSearch = document.getElementById('jobsSearch');
        const noJobs = document.getElementById('noJobs');
        const jobCards = document.querySelectorAll('.filter-jobs');
        let showBoosted = false;
        jobSearch.addEventListener('input', function () {
            let jobsMatch = false;
            const jobsKeyword = this.value.toLowerCase();
            jobCards.forEach(card => {
                const cardText = card.textContent.toLowerCase();
                if (cardText.includes(jobsKeyword)) {
                    card.style.display = 'block';
                    jobsMatch = true;
                } else {
                    card.style.display = 'none';
                }
            });
            if (jobsMatch) {
                noJobs.style.display = 'none';
            } else {
                noJobs.style.display = 'block';
            }
        });

        boostJobs.addEventListener('click', function () {
            showBoosted = !showBoosted;
            let jobsMatch = false;
            jobCards.forEach(card => {
                if (!showBoosted || card.dataset.boosted === "1") {
                    card.style.display = 'block';
                    jobsMatch = true;
                } else {
                    card.style.display = 'none';
                }
            });
            noJobs.style.display = jobsMatch ? 'none' : 'block';
            boostJobs.textContent = showBoosted ? "All Jobs" : "Boosted Jobs";
        });
    });
</script>

<!-- Modal JS -->
<script>
    const boostButtons = document.querySelectorAll('.followingbtn');
    boostButtons.forEach(button => {
        button.addEventListener('click', function () {
            const jobCard = this.closest('.job-card');
            const jobId = jobCard.dataset.id;
            const jobTitle = jobCard.querySelector('h5').innerText;

            document.getElementById('boost_job_id').value = jobId;
            document.getElementById('boost_job_title').innerText = jobTitle;
        });
    });
</script>

<!-- Recharge -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const boostJobModal = document.getElementById('boostJob');
        const jobFrom = boostJobModal.querySelector('#job_from');
        const jobEnd = boostJobModal.querySelector('#job_end');
        const totalAmount = boostJobModal.querySelector('#total_amount');
        const balanceMessage = boostJobModal.querySelector('#balance_message');
        const rechargeButton = boostJobModal.querySelector('#recharge_button');
        const boostBtn = boostJobModal.querySelector('.formbtn');

        // Get charge per day and user balance from Blade
        const chargePerDay = {{ $job_charge }};
        const userBalance = {{ auth()->user()->balance }};

        function calculateDays(from, to) {
            const start = new Date(from);
            const end = new Date(to);
            const diffTime = end - start;
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
            return diffDays > 0 ? diffDays : 0;
        }

        function updateTotalAndCheckBalance() {
            const fromDate = jobFrom.value;
            const toDate = jobEnd.value;

            if (fromDate && toDate) {
                const days = calculateDays(fromDate, toDate);
                const total = days * chargePerDay;
                totalAmount.innerText = `₹ ${total.toFixed(2)}`;

                if (total > userBalance) {
                    balanceMessage.style.display = 'block';
                    rechargeButton.style.display = 'inline-block';
                    boostBtn.disabled = true;
                } else {
                    balanceMessage.style.display = 'none';
                    rechargeButton.style.display = 'none';
                    boostBtn.disabled = false;
                }
            } else {
                totalAmount.innerText = `₹ 0.00`;
                balanceMessage.style.display = 'none';
                rechargeButton.style.display = 'none';
                boostBtn.disabled = false;
            }
        }

        jobFrom.addEventListener('change', updateTotalAndCheckBalance);
        jobEnd.addEventListener('change', updateTotalAndCheckBalance);
    });
</script>

<!-- Form Submissions -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const boostForm = document.querySelector('.jobBoostForm');
        const submitBtn = boostForm.querySelector('.jobBoostbtn');

        boostForm.addEventListener('submit', function (e) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Boosting...';
        });
    });
</script>