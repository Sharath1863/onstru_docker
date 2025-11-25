<div class="body-head mb-3">
    <h5>Jobs</h5>
    <a>
        <button class="removebtn" id="boostedJobsBtn">Boosted Jobs</button>
    </a>
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
        <div class="cards-head">
            <h5 class="mb-2">{{ $job->title ?? '-' }}</h5>
            <h6 class="mb-2">
                {{ $job->user->gst->business_legal ?? '-' }} | {{ $job->user->you_are ?? '-' }}
            </h6>
        </div>
        @if ($job->highlighted == '1')
            <a class="badge d-flex align-items-center">
                <img src="{{ asset('assets/images/icon_fire.png') }}" height="15px" class="pe-1" alt="">
                <span>Boosted</span>
            </a>
        @endif
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
                    {{ $job->salary ?? 'Not Disclosed' }}
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
                    <a href="{{ url('job-details/' . $job->id) }}">
                        <button class="followingbtn">View Job</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Job Save -->
<script>
    $(document).ready(function () {
        $(document).on('click', '.save-job', function (e) {
            e.preventDefault();
            let jobId = $(this).data('id');
            let btn = $(this);
            $.ajax({
                url: "{{ route('jobs.toggleSave', ['id' => '__jobId__']) }}".replace('__jobId__', jobId),
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    if (response.saved) {
                        btn.find("i").removeClass("far").addClass("fas text-warning");
                        showToast('Job Saved!');
                    } else {
                        btn.find("i").removeClass("fas text-warning").addClass("far");
                        showToast('Job Unsaved!');
                    }
                },
                error: function () {
                    showToast("Something went wrong!");
                }
            });
        });
    });
</script>

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