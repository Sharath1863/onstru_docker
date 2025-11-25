@php
    $profileCompletion = getProfileCompletion();
@endphp
<div class="profile-head">
    <div class="cards-image">
        <div class="avatar-progress" data-progress="{{ getProfileCompletion() }}">
            <svg class="progress-ring" width="120" height="120">
                <circle class="progress-ring__background" cx="60" cy="60" r="54" />
                <circle class="progress-ring__circle" cx="60" cy="60" r="54" />
            </svg>
            <div class="avatar-div-90 position-relative mb-2">
                <img src="{{ asset(auth()->user()->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . auth()->user()->profile_img : 'assets/images/Avatar.png') }}"
                    class="user-img avatar-90" alt="">
                @if (auth()->user()->badge != 0 && auth()->user()->badge != null)
                    <img src="{{ asset('assets/images/Badge_' . auth()->user()->badge . '.png') }}" class="badge-90" alt="">
                @endif
            </div>
            <div class="progress-text" data-bs-toggle="tooltip" data-bs-title="Profile Completion">0%</div>
        </div>
        <div>
            <h5 class="text-center mb-2">
                <span>{{ auth()->user()->name }}</span>
                @if (auth()->user()->as_a === null)
                    <span class="label">{{ auth()->user()->you_are }}</span>
                @else
                    <span class="label">{{ auth()->user()->as_a }}</span>
                @endif
            </h5>
            <h6 class="text-center mb-2 text-muted text-lowercase"><em>{{ auth()->user()->user_name }}</em></h6>

            <div class="verification-badges">
                @if (auth()->user()->you_are == 'Business')
                    @if ($gstverified == 'yes')
                        <img src="{{ asset('assets/images/GST_Verify.png') }}" height="20px" data-bs-toggle="tooltip"
                            data-bs-title="GST Verified" alt="">
                    @endif
                @endif
                <div class="dropdown">
                    <a data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis text-dark"></i>
                    </a>
                    <ul class="dropdown-menu">
                        @php
                            $id = auth()->user()->id ?? null;
                            $shareUrl = url('user-profile/' . $id);
                            $shareText = 'Profile Shared' ?? 'Check this out!';
                        @endphp
                        <li>
                            <a class="dropdown-item share-btn" data-bs-toggle="modal" data-share-url="{{ $shareUrl }}"
                                data-share-text="{{ $shareText }}" data-bs-target="#sharePopup"
                                data-post-id="{{ auth()->user()->id }}" data-share-type="{{ 'profile' }}">
                                <i class="fas fa-share-nodes pe-1"></i>
                                Share
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="profile-content">
                <div class="d-flex align-items-center justify-content-evenly mt-2">
                    <a href="{{ url('profile') }}">
                        <div style="cursor: pointer">
                            <h6 class="text-center fw-bold mb-0">{{ $post_count ?? 0 }}</h6>
                            <label class="text-center my-0">Posts</label>
                        </div>
                    </a>
                    <div style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#followers">
                        <h6 class="text-center fw-bold mb-0">
                            <span class="my-followers-count">{{ $my->followers_count ?? '0' }}</span>
                        </h6>
                        <label class="text-center my-0">Followers</label>
                    </div>
                    <div style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#following">
                        <h6 class="text-center fw-bold mb-0">
                            <span class="my-following-count">{{ $my->following_count ?? '0' }}</span>
                        </h6>
                        <label class="text-center my-0">Following</label>
                    </div>
                </div>

                @if (getProfileCompletion() != 100)
                    <div class="d-flex align-items-center justify-content-center mt-2">
                        <a href="{{ url('my-profile') }}">
                            <button class="removebtn">Update Details</button>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- @php
$rawFiles = auth()->user()->profile_img;
if (is_array($rawFiles)) {
$files = $rawFiles;
} elseif ($rawFiles) {
$files = json_decode($rawFiles, true) ?? [];
} else {
$files = [];
}
$fileText = auth()->user()->bio ?? '-';
$fileUrls = array_map(fn($f) => 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $f, $files);
@endphp
<li>
    <a class="dropdown-item share-btn" data-bs-toggle="modal" data-bs-target="#sharePopup"
        data-post-id="{{ auth()->user()->id }}" data-type="profile"
        data-share-title="{{ auth()->user()->name ?? 'Profile' }}" data-share-url="{{ $fileUrls[0] ?? '' }}"
        data-share-text="{{ $fileText }}">
        <i class="fas fa-share-nodes pe-1"></i> Share Profile
    </a>
</li> --}}

<script>
    document.querySelectorAll('.avatar-progress').forEach(el => {
        const percent = parseInt(el.dataset.progress);
        const circle = el.querySelector('.progress-ring__circle');
        const text = el.querySelector('.progress-text');
        const radius = 54;
        const circumference = 2 * Math.PI * radius;

        circle.style.strokeDasharray = circumference;
        circle.style.strokeDashoffset = circumference;

        circle.style.stroke = percent === 100 ? "#38CB89" : "#FF0000";

        let progress = 0;
        const duration = 1000;
        const interval = 15;
        const step = (percent / (duration / interval));

        const animate = setInterval(() => {
            progress += step;
            if (progress >= percent) {
                progress = percent;
                clearInterval(animate);
            }
            const offset = circumference - (progress / 100) * circumference;
            circle.style.strokeDashoffset = offset;
            text.textContent = Math.round(progress) + "%";
        }, interval);
    });
</script>