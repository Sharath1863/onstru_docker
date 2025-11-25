@foreach ($jobs as $job)
    <div class="side-cards job-card mb-2 position-relative" data-category="{{ $job->categoryRelation->value }}"
        data-type="{{ $job->shift }}" data-exp="{{ $job->experience }}" data-salary="{{ $job->salary }}"
        data-location="{{ $job->location }}" data-sublocation="{{ $job->sublocality }}"
        data-highlight="{{ $job->highlighted }}">
        <div class="cards-head">
            <h5 class="mb-2">{{ $job->title ?? '-' }}</h5>
            <h6 class="mb-2">
                {{ $job->user->gst->business_legal ?? $job->user->name }} | {{ $job->user->you_are ?? '-' }}
            </h6>
            @if ($job->highlighted == '1')
                <a class="badge d-flex align-items-center">
                    <img src="{{ asset('assets/images/icon_fire.png') }}" height="15px" class="pe-1" alt="">
                    <span>Boosted</span>
                </a>
            @endif
        </div>
        <div class="cards-content">
            <div class="d-flex align-items-center justify-content-start flex-wrap column-gap-4">
                <h6 class="my-2 d-flex align-items-center gap-2">
                    <i class="fas fa-clipboard-list"></i>
                    {{ $job->categoryRelation->value ?? '-' }}
                </h6>
                <h6 class="my-2 d-flex align-items-center gap-2">
                    <i class="fas fa-briefcase"></i> Exp :
                    {{ $job->experience == 0 ? 'Fresher' : ($job->experience ? $job->experience . '+ Years' : '-') }}
                </h6>
                <h6 class="my-2 d-flex align-items-center gap-2">
                    ₹ {{ $job->salary ?? 'Not Disclosed' }}
                </h6>
                <h6 class="my-2 d-flex align-items-center gap-2">
                    <i class="fas fa-location-dot"></i>
                    {{ $job->locationRelation->value ?? '-' }}
                </h6>
                <h6 class="my-2 d-flex align-items-center gap-2">
                    <i class="fas fa-location"></i>
                    {{ $job->sublocality ?? '-' }}
                </h6>
            </div>
            <h6 class="my-2"><span class="text-muted">Required Skills :</span> {{ $job->skills ?? 'N/A' }}</h6>
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <h6 class="bio mb-0">{{ $job->created_at->diffForHumans() }}</h6>
                <div class="d-flex align-items-center flex-wrap column-gap-3">
                    <h6 class="mb-0" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#sharePopup"
                        data-share-title='{{ $job->title }}'
                        data-share-url='{{ env('BASE_URL') . 'job-details/' . $job->id }}'
                        data-share-text='{{ $job->categoryRelation->value }}'>
                        <i class="fas fa-share-nodes pe-1 share-btn" data-bs-toggle="tooltip" data-bs-title="Share"
                            data-job-id={{ $job->id }} data-share-type="job"></i>
                    </h6>
                    <h6 class="save-job mb-0" data-id="{{ $job->id }}" style="cursor: pointer;">
                        @if (in_array($job->id, $savedJobIds))
                            <i class="fas fa-bookmark pe-1 text-warning"></i>
                        @else
                            <i class="far fa-bookmark pe-1"></i>
                        @endif
                    </h6>
                    <a href="{{ url('job-details/' . $job->id) }}" class="mb-0">
                        <button class="followingbtn">View Job</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach

@if ($jobs->hasMorePages())
    <div class="load-more-trigger text-center my-4" data-next-url="{{ $jobs->nextPageUrl() }}">
        <span class="loading-text">Loading</span>
    </div>
@endif

<!-- jQuery from CDN -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- Pass Laravel data to JS -->
<script>
    window.appData = {
        shareUrl: "{{ route('toggle.getShareList') }}",
        csrf: "{{ csrf_token() }}"
    };
</script>
<script src="{{ asset('assets/js/share_job.js') }}"></script>