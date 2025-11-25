@foreach ($posts as $index => $reel)
    @php
        $id = $reel->id ?? null;
        $shareUrl = url('user-profile/' . $reel->created_by . '/' . $id);
        $shareText = 'Post Shared' ?? 'Check this out!';
    @endphp

    <div class="reel-item my-2" id="reel_item">
        <!-- Reels -->
        <div class="video-div">
            <!-- Loader -->
            <!-- <div class="video-loader">
                        <div class="spinner"></div>
                    </div> -->

            @foreach ($reel->file as $file)
                @if ($reel->file_type === 'video' || $reel->file_type == 'premium')
                    <video class="videos {{ $reel->file_type == 'premium' ? 'blurred' : '' }}" loop>
                        <source
                            src="{{ $reel->file_type === 'premium' ? $file : 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $file }}"
                            type="video/mp4">
                    </video>
                @elseif ($reel->file_type === 'image')
                    <img class="img-fluid" src="{{ 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $file }}"
                        alt="reel image">
                @endif
            @endforeach

            <div class="play-pause-btn">
                <i class="fas fa-play play-btn"></i>
                <i class="fas fa-pause pause-btn" style="display: none;"></i>
            </div>

            <div class="user-details">
                @if (!is_null($reel->category))
                    @php
                        if ($reel->category === 'service' && $reel->created_by != auth()->id()) {
                            $route = url('individual-service', ['id' => $reel->category_id]);
                            $type = 'Apply Service';
                        } elseif ($reel->category === 'products') {
                            $route = url('individual-product', ['id' => $reel->category_id]);
                            $type = 'Buy Now';
                        } elseif ($reel->category === 0) {
                            $route = url('premium');
                            $type = 'Subscribe Now';
                        } else {
                            $route = null;
                            $type = null;
                        }
                    @endphp
                    <a href="{{ $route }}">
                        <button class="reelbtn mb-3 w-100">{{ $type }}<i class="fas fa-arrow-right ps-1"></i></button>
                    </a>
                @endif
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center justify-content-start gap-3 flex-wrap">
                        <a href="{{ route('user-profile', ['id' => $reel->created_by]) }}">
                            <div class="d-flex align-items-center justify-content-start gap-2">
                                <div class="avatar-30 position-relative">
                                    <img src="{{ $reel->file_type === 'premium' ? $reel->user->profile_img : 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $reel->user->profile_img }}"
                                        class="avatar-30" alt="">
                                    @if ($reel->user->badge != 0 && $reel->user->badge != null)
                                        <img src="{{ asset('assets/images/Badge_' . $reel->user->badge . '.png') }}"
                                            class="badge-30" alt="">
                                    @endif
                                </div>
                                <div class="user-content">
                                    <h5 class="mb-1">{{ $reel->user->name ?? '' }}</h5>
                                    <h6 class="text-lowercase">{{ $reel->user->user_name ?? '' }}</h6>
                                </div>
                            </div>
                        </a>

                        @if ($reel->is_followed)
                            <button class="followingbtn follow-btn" data-user-id="{{ $reel->created_by }}" data-following="1">
                                <span class="label">Following</span>
                            </button>
                        @else
                            @if ($reel->file_type != 'premium')
                                <button class="followersbtn follow-btn" data-user-id="{{ $reel->created_by }}" data-following="0">
                                    <span class="label">Follow</span>
                                </button>
                            @endif
                        @endif
                    </div>
                    {{-- <div class="caption-div m-0">
                        <h6 class="fw-semibold mb-0">
                            <i class="fas fa-location-dot pe-1"></i>
                            {{ $reel->location }}
                        </h6>
                    </div> --}}
                </div>
                <div class="caption-div">
                    <h6 class="caption" id="caption{{ $reel->id ?? '' }}">{{ $reel->caption ?? '' }}</h6>
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="see-more" id="see-more{{ $reel->id ?? '' }}" style="cursor: pointer;">See more</h6>
                        <h6 class="fw-medium mb-0">
                            <i class="fas fa-location-dot pe-1"></i>
                            {{ $reel->location ?? '' }}
                        </h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="actions act_{{ $reel->id }} {{ $reel->type == 'asset' ? 'd-none' : 'block' }}">
            <a>
                <button class="actionbtn like">
                    <i class="fa-{{ $reel->is_liked ? 'solid active' : 'regular' }} fa-heart like-btn"
                        data-post-id="{{ $reel->id }}"></i>
                </button>
                <h6 class="text-center mb-0 likes-count" data-bs-toggle="modal" data-bs-target="#likesPopup"
                    data-post-id="{{ $reel->id }}" id="likes_count_{{ $reel->id }}">
                    {{ $reel->like_cnt ? ($reel->like_cnt >= 1000 ? number_format($reel->like_cnt / 1000, 1) . 'k' : $reel->like_cnt) : 0 }}
                </h6>
            </a>
            <a class="comment-count-pop" data-bs-toggle="modal" data-bs-target="#commentPopup"
                data-post-id="{{ $reel->id }}">
                <button class="actionbtn">
                    <i class="fa-regular fa-message"></i>
                </button>
                <h6 class="text-center mb-0 comment-count">
                    {{ $reel->com_cnt ? ($reel->com_cnt >= 1000 ? number_format($reel->com_cnt / 1000, 1) . 'k' : $reel->com_cnt) : 0 }}
                </h6>
            </a>
            <a data-open-comments data-bs-toggle="modal" data-bs-target="#sharePopup" data-share-url="{{ $shareUrl }}"
                data-share-text="{{ $shareText }}">
                <button class="actionbtn">
                    <i class="far fa-paper-plane share-btn" data-post-id="{{ $reel->id }}"
                        data-share-type="{{ 'post' }}"></i>
                </button>
            </a>
            <a>
                <button class="actionbtn">
                    <i class="fa-{{ $reel->is_saved ? 'solid active' : 'regular' }} fa-bookmark save-btn"
                        data-post-id="{{ $reel->id }}"></i>
                </button>
            </a>
            <div class="dropdown">
                <button class="actionbtn" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-vertical"></i>
                </button>
                <ul class="dropdown-menu z-4">
                    @if ($reel->is_reported)
                        <li>
                            <a class="dropdown-item">
                                <i class="fas fa-circle-check text-success pe-1"></i>
                                Reported
                            </a>
                        </li>
                    @else
                        <li>
                            <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#postReport"
                                data-id="{{ $reel->id }}">
                                <i class="fas fa-triangle-exclamation text-danger pe-1"></i> Report
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Post Like / Share / Save -->
    <script>
        // Post Like
        $(document).on("click", ".like-btn", function () {
            let postId = $(this).data("post-id");
            let $button = $(this);
            if ($button.data("loading")) return;
            $button.data("loading", true);
            let isLiked = $button.hasClass("fa-solid");
            $.ajax({
                url: "{{ route('toggle.like') }}",
                method: "POST",
                data: {
                    post_id: postId,
                    action: isLiked ? "unlike" : "like",
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    if (response.success) {
                        let likeCount = response.likes_count ?? 0;
                        let formattedCount = likeCount >= 1000 ? (likeCount / 1000).toFixed(1) + 'k' :
                            likeCount;
                        $('#likes_count_' + postId).text(formattedCount);
                        $(".likes-count[data-post-id='" + postId + "']").text(formattedCount);

                        if (response.action === "like") {
                            showToast("Reel Liked");
                            $button.removeClass("fa-regular text-dark").addClass("fa-solid active");
                        } else {
                            showToast("Reel Unliked");
                            $button.removeClass("fa-solid active").addClass("fa-regular text-dark");
                        }
                    }
                },
                complete: function () {
                    $button.data("loading", false);
                }
            });
        });

        // Post Save
        $(document).on("click", ".save-btn", function () {
            let postId = $(this).data("post-id");
            let $button = $(this);
            if ($button.data("loading")) return;
            $button.data("loading", true);
            let issaved = $button.hasClass("fa-solid");

            $.ajax({
                url: "{{ route('toggle.save') }}",
                method: "POST",
                data: {
                    post_id: postId,
                    action: issaved ? "unsave" : "save",
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    if (response.success) {
                        if (response.action === "save") {
                            showToast("Reel Saved");
                            $button.removeClass("fa-regular").addClass("fa-solid active");
                        } else {
                            showToast("Reel Unsaved");
                            $button.removeClass("fa-solid active").addClass("fa-regular");
                        }
                    }
                },
                complete: function () {
                    $button.data("loading", false);
                }
            });
        });

        // Post Share
        $(document).on("click", ".share-btn", function () {
            let postId_share = $(this).data("post-id");
            let share_type = $(this).data("share-type");
            let $button = $(this);
            if ($button.data("loading")) return;
            $button.data("loading", true);
            $.ajax({
                url: "{{ route('toggle.getShareList') }}",
                method: "POST",
                data: {
                    post_id: postId_share,
                    share_type: share_type,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    $("#share_list").html(response.html);
                    $("#indent_link").html(response.link);
                },
                error: function () {
                    $("#share_list").html(
                        '<h6 class="text-center text-danger">Failed to load</h6>');
                },
                complete: function () {
                    $button.data("loading", false);
                }
            });
        });
    </script>

    <!-- Post Like/Comment Counts/Actions -->
    <script>
        // Post Like Count
        $(document).on("click", ".likes-count", function () {
            let postId = $(this).data("post-id");
            $("#likesModalBody").html('<h6 class="text-center">Loading</h6>');
            $.ajax({
                url: "{{ route('toggle.getLikesList') }}",
                method: "POST",
                data: {
                    post_id: postId,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    $("#likesModalBody").html(response.html);
                },
                error: function () {
                    $("#likesModalBody").html(
                        '<h6 class="text-center text-danger">Failed to load likes</h6>');
                }
            });
        });

        // Post Comment Count
        $(document).on("click", ".comment-count-pop", function () {
            let postId = $(this).data("post-id");
            $("#commentList").html('<h6 class="text-center">Loading</h6>');
            $.ajax({
                url: "{{ route('toggle.getCommentList') }}",
                method: "POST",
                data: {
                    post_id: postId,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    $("#commentList").html(response.html);
                    $('#post_id').val(response.post_id);
                },
                error: function () {
                    $("#commentList").html(
                        '<h6 class="text-center">Failed to Load Comments</h6>');
                }
            });
        });

        // Post Comment Button
        $(document).on("click", "#postCommentBtn", function () {
            let comment = $("#commentInput").val().trim();
            let post_id = $("#post_id").val().trim();
            let $btn = $(this);
            $btn.prop("disabled", true).text("Posting...");
            if (!comment) {
                alert("Comment cannot be empty");
                return;
            }
            $.ajax({
                url: "{{ route('toggle.storeComment') }}",
                method: "POST",
                data: {
                    post_id: post_id,
                    comment: comment,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    showToast("Comment Posted")
                    $("#commentList").html(response.html);
                    $("#commentInput").val("");
                    $(".comment-count").text(response.com_cnt);
                },
                complete: function () {
                    $btn.prop("disabled", false).text("Comment");
                }
            });
        });

        // Post Comment Action
        $(document).on('click', '.comment-action', function (e) {
            e.preventDefault();
            let $this = $(this);
            if ($this.data('clicked')) return;
            $this.data('clicked', true);

            let commentId = $this.data('id');
            let action = $this.data('action');
            $.ajax({
                url: "{{ route('toggle.report') }}",
                type: 'POST',
                data: {
                    comment_id: commentId,
                    action: action,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    $this.data('clicked', false);
                    if (response.success) {
                        if (action === 'delete') {
                            showToast('Comment Deleted');

                            let targetDiv = $('#comment-' + commentId);
                            $('#comment-' + commentId).fadeOut(300, function () {
                                $(this).remove();
                            });
                        } else if (action === 'report') {
                            showToast('Comment Reported');
                        } else if (action === 'report post') {
                            showToast('Reel Reported');
                        }
                    } else {
                        // toastr.error('Something went wrong');
                    }
                },
                error: function () {
                    $this.data('clicked', false);
                }
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.caption').forEach(captionEl => {
                let text = captionEl.textContent;

                // Replace hashtags with anchor tags
                let linkedText = text.replace(/#(\w+)/g, (match, tag) => {
                    let baseUrl = `{{ url('explore') }}`;
                    let url = `${baseUrl}/${tag}`;
                    return `<a href="${url}" class="hashtag-link">#${tag}</a>`;
                });

                captionEl.innerHTML = linkedText;
            });
        });
    </script>

@endforeach