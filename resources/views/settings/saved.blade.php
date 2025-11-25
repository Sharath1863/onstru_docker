<div class="container-fluid px-0">
    <div class="body-head mb-3">
        <h5>Saved</h5>
    </div>

    <div class="proftabs mb-3">
        <ul class="nav nav-tabs gap-2 flex-wrap border-0" id="savedTab" role="tablist">
            <li class="nav-item mb-2" role="presentation">
                <button class="profilebtn border active" data-bs-toggle="tab" type="button" data-bs-target="#posts">
                    <img src="{{ asset('assets/images/icon_images.png') }}" height="15px" class="mb-0"
                        alt="">
                    <span>Post</span>
                </button>
            </li>
            <li class="nav-item mb-2" role="presentation">
                <button class="profilebtn border" data-bs-toggle="tab" type="button" data-bs-target="#jobs">
                    <img src="{{ asset('assets/images/icon_briefcase.png') }}" height="15px" class="mb-0"
                        alt="">
                    <span>Jobs</span>
                </button>
            </li>
        </ul>
    </div>

    <div class="flex-cards">
        <div class="tab-content" id="savedTabContent">
            <div class="tab-pane fade show active" id="posts" role="tabpanel">
                @include('settings.saved-posts')
            </div>
            <div class="tab-pane fade" id="jobs" role="tabpanel">
                @include('settings.saved-jobs')
            </div>
        </div>
    </div>
</div>