<!-- Followers Modal -->
<div class="modal fade" id="followers" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="followersLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="max-height: 60vh;">
            <div class="modal-header">
                <h4 class="m-0">Followers</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="overflow-y: auto;">
                <div class="position-sticky sticky-top bg-white py-3">
                    <div class="inpleftflex">
                        <i class="fas fa-search text-muted text-center"></i>
                        <input type="text" name="search" id="followerSearch" class="form-control border-0"
                            placeholder="Search">
                    </div>
                </div>

                <!-- Empty State -->
                <div class="side-cards shadow-none border-0" id="noCard"
                    style="{{ count($followers) > 0 ? 'display: none;' : 'display: block;' }}">
                    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                        <img src="{{ asset('assets/images/Empty/NoPeoples.png') }}" height="150px"
                            class="d-flex mx-auto mb-2" alt="">
                        <h5 class="text-center mb-0">No Followers Yet</h5>
                        <h6 class="text-center bio">You don't have any followers yet — start connecting today.</h6>
                    </div>
                </div>

                <!-- Users Card -->
                @foreach ($followers as $reel)
                    <div class="modal-user follower-modal mb-2">
                        <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                            <a href="{{ url('user-profile/' . $reel->id) }}">
                                <div class="d-flex align-items-center justify-content-start gap-2">
                                    <img src="{{ asset($reel['profile_img'] ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $reel['profile_img'] : 'assets/images/Avatar.png') }}"
                                        class="avatar-30" alt="">
                                    <div class="user-content">
                                        <h5 class="mb-1">{{ $reel['name'] }}</h5>
                                        <h6 class="text-lowercase">{{ $reel['user_name'] }}</h6>
                                    </div>
                                </div>
                            </a>
                            @php
                                $isFollowing = auth()->check() ? auth()->user()->isFollowing($reel) : false;
                            @endphp
                            @if ($isFollowing)
                                {{-- <button class="removebtn" data-bs-toggle="offcanvas" id="chat_ind" data-user-id={{
                                    $reel->id }} data-bs-target="#chat"
                                    data-user-name="{{ $reel->user_name }}" data-user-img="{{ $reel->profile_img }}"
                                    onclick="openChat()">Message</button> --}}
                                <button class="followingbtn follow-btn" data-user-id="{{ $reel->id }}" data-following="1">
                                    <span class="label">Following</span>
                                </button>
                            @else
                                @if (auth()->id() != $reel->id)
                                    <button class="followersbtn follow-btn" data-user-id="{{ $reel->id }}" data-following="0">
                                        <span class="label">Follow</span>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Following Modal -->
<div class="modal fade" id="following" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="followingLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="max-height: 60vh;">
            <div class="modal-header">
                <h4 class="m-0">Following</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="overflow-y: auto;">
                <div class="position-sticky sticky-top bg-white py-3">
                    <div class="inpleftflex">
                        <i class="fas fa-search text-muted text-center"></i>
                        <input type="text" name="search" id="followingSearch" class="form-control border-0"
                            placeholder="Search">
                    </div>
                </div>

                <!-- Empty State -->
                <div class="side-cards shadow-none border-0" id="noCard"
                    style="{{ count($following) > 0 ? 'display: none;' : 'display: block;' }}">
                    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                        <img src="{{ asset('assets/images/Empty/NoPeoples.png') }}" height="150px"
                            class="d-flex mx-auto mb-2" alt="">
                        <h5 class="text-center mb-0">No Followings Yet</h5>
                        <h6 class="text-center bio">You don't have any followings yet — start connecting today.</h6>
                    </div>
                </div>

                <!-- Users Card -->
                @foreach ($following as $reel)
                    <div class="modal-user following-modal mb-2">
                        <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                            <a href="{{ url('user-profile/' . $reel->id) }}">
                                <div class="d-flex align-items-center justify-content-start gap-2">
                                    <img src="{{ asset($reel['profile_img'] ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $reel['profile_img'] : 'assets/images/Avatar.png') }}"
                                        height="30px" class="avatar-30" alt="">
                                    <div class="user-content">
                                        <h5 class="mb-1">{{ $reel['name'] }}</h5>
                                        <h6 class="text-lowercase">{{ $reel['user_name'] }}</h6>
                                    </div>
                                </div>
                            </a>
                            @php
                                $isFollowing = auth()->check() ? auth()->user()->isFollowing($reel) : false;
                            @endphp
                            @if ($isFollowing)
                                <button class="followingbtn follow-btn" data-user-id="{{ $reel->id }}" data-following="1">
                                    <span class="label">Following</span>
                                </button>
                            @else
                                @if (auth()->id() != $reel->id)
                                    <button class="followersbtn follow-btn" data-user-id="{{ $reel->id }}" data-following="0">
                                        <span class="label">Follow</span>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Popup Search -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function setupSearch(inputId, cardClass) {
            const searchInput = document.getElementById(inputId);
            if (!searchInput) return;

            searchInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase();
                document.querySelectorAll(cardClass).forEach(card => {
                    card.style.display = card.textContent.toLowerCase().includes(keyword) ?
                        'block' : 'none';
                });
            });
        }

        setupSearch('followerSearch', '.follower-modal');
        setupSearch('followingSearch', '.following-modal');
    });

    function openChat() {
        const followersModal = document.getElementById('followers'); // replace with your followers modal ID
        if (followersModal) {
            const bsModal = bootstrap.Modal.getInstance(followersModal);
            if (bsModal) bsModal.hide();
        }
    }
</script>