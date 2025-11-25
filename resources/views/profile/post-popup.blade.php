<!-- View Post Modal  -->
<div class="modal fade" id="ind_post" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="viewPostLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-0">
            <div class="modal-body p-0">
                <div class="viewpost-modal">
                    <!-- Image block -->
                    <div class="viewpost-left h-100">
                        <div id="post-cl" class="splide d-none h-100">
                            <div class="splide__track h-100">
                                <ul class="splide__list h-100"></ul>
                            </div>
                        </div>

                        <!-- Video block -->
                        <div id="video-container" class="video-div d-none h-100"></div>
                    </div>

                    <div class="viewpost-right">
                        <div class="modal-header py-2">
                            <h4 class="m-0">Post</h4>
                            <button type="button" class="btn-close pop_close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="viewpost-img px-3 mb-2" id="ind_post_div"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal 1 -->
<div class="modal modal-md fade" id="addPost_1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Create New Post</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <label class="custom-file-upload border-0 w-100" style="height: 300px;" for="addPostImage"
                    id="dropZone">
                    <div class="icon mb-2">
                        <img src="{{ asset('assets/images/Drag_Drop.png') }}" height="75px" alt="">
                    </div>
                    <div class="text mb-2">
                        <span id="add-img-text" class="text-center">Drag and Drop Images and Videos Here</span>
                    </div>
                    <!-- Hidden input; files will be sent inside form in Modal 3 -->
                    <input type="file" id="addPostImage" name="files[]" accept="image/*,video/*" multiple hidden>
                </label>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2 -->
<div class="modal modal-lg fade" id="addPost_2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" id="addPostForm">
                @csrf
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <a data-bs-toggle="modal" data-bs-target="#addPost_1">
                        <h5 class="m-0"><i class="fas fa-arrow-left"></i></h5>
                    </a>
                    <h4 class="m-0">Publish Post</h4>
                    <button type="submit" class="listbtn addPostbtn">Publish</button>
                </div>
                <div class="modal-body p-0">
                    <div class="viewpost-modal">

                        <div class="position-relative" style="height: 300px;">
                            <div class="carousel-wrapper h-100" id="previewCarousel"></div>
                            <div id="carouselDots" class="carousel-dots"></div>
                            <button type="button" id="addMoreBtn" class="position-absolute"
                                style="right:10px;bottom:10px;z-index:5;">
                                <i class="fas fa-plus"></i>
                            </button>
                            <input type="file" id="addMoreImages" accept="image/*" multiple hidden>
                        </div>

                        <div class="viewpost-ct p-2">
                            <div class="d-flex pt-2 pb-4 position-sticky sticky-top bg-white">
                                <div class="modal-user">
                                    <div class="d-flex align-items-center justify-content-start gap-2">
                                        <img src="{{ asset(auth()->user()->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . auth()->user()->profile_img : 'assets/images/Avatar.png') }}"
                                            class="avatar-30" alt="">
                                        <div class="user-content">
                                            <h6 class="m-0 text-lowercase">{{ auth()->user()->user_name ?? '-' }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label for="caption">Captions</label>
                                <textarea rows="5" class="form-control position-relative" name="caption" id="caption"
                                    placeholder="Add Caption"></textarea>
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label for="location">Location</label>
                                <div class="inpleftflex w-100">
                                    <i class="fas fa-location-dot text-center"></i>
                                    <input type="text" class="form-control border-0 w-100" name="location"
                                        id="location" placeholder="Add Location">
                                </div>
                            </div>

                            <!-- Hidden file input to store uploaded files -->
                            <div id="hiddenFileContainer" class="d-none"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Post Modal -->
<div class="modal fade" id="editPost" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="editPostLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('posts.store') }}" method="POST" class="editPostForm">
                @csrf
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="m-0">Edit Details</h4>
                    <button type="submit" class="listbtn editPostbtn">Update</button>
                </div>
                <div class="modal-body p-0">
                    <div class="viewpost-modal">
                        <div class="viewpost-img">
                            <div class="editPostCarousel splide">
                                <div class="splide__track">
                                    <ul class="splide__list"></ul>
                                </div>
                            </div>
                        </div>
                        <div class="viewpost-ct p-2">
                            <div class="d-flex pt-2 pb-4 position-sticky sticky-top bg-white">
                                <div class="modal-user">
                                    <div class="d-flex align-items-center justify-content-start gap-2">
                                        <img src="{{ asset('assets/images/Avatars/3.png') }}" height="30px"
                                            class="rounded-circle object-fit-cover object-top" alt="">
                                        <div class="user-content">
                                            <h6 class="m-0">{{ auth()->user()->name }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label for="caption">Captions</label>
                                <textarea rows="5" class="form-control" name="editcaption" id="editcaption" placeholder="Add Caption"></textarea>
                                <input type="hidden" name="post_id" id="editPostId">
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label for="location">Location</label>
                                <div class="inpleftflex">
                                    <i class="fas fa-location-dot text-center"></i>
                                    <input type="text" class="form-control border-0" name="editlocation"
                                        id="editlocation" placeholder="Add Location">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
    $(document).on('click', '.ind_post', function() {
        $('#ind_post_div').html(`
             <div class="text-center d-flex align-items-center justify-content-center flex-column gap-1 mt-3">
                <img src="{{ asset('assets/images/Favicon.png') }}" alt="Loading" width="40px" height="40px">
                <h6 class="text-muted" style="font-size: 12px;">Loading</h6>
            </div>
        `);
        $("#commentList").html('');

        var post_id = $(this).data('post-id');
        if (post_id) {
            $.ajax({
                url: "{{ route('ind_post') }}",
                method: "POST",
                data: {
                    post_id: post_id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#ind_post_div').html(response.html);
                    linkHashtags($('#ind_post_div'));
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        } else {
            alert("Post ID not found");
        }
    });
</script>

<script>
    let auth_user = "{{ auth()->user()->id }}";

    // Comment List
    $(document).on("click", ".comment-count-pop", function() {
        let postId = $(this).data("post-id");
        // alert(postId);
        $("#commentList").html(`
            <div class="text-center d-flex align-items-center justify-content-center flex-column gap-1 mt-3">
                <img src="{{ asset('assets/images/Favicon.png') }}" alt="Loading" width="30px" height="30px">
                <h6 class="text-muted" style="font-size: 10px;">Loading</h6>
            </div>
        `);
        $.ajax({
            url: "{{ route('toggle.getCommentList') }}",
            method: "POST",
            data: {
                post_id: postId,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                $("#commentList").html(response.html);
                $('#post_id').val(response.post_id);
                $('#post_cby').text(response.post_cby);
                $('#post_cby_img').attr('src',
                    'https://onstru-social.s3.ap-south-1.amazonaws.com/' + response.post_cby_img
                );
                $('#post_cby_badge').attr('src',
                    'https://onstru-social.s3.ap-south-1.amazonaws.com/' + response
                    .post_cby_badge
                );
                $('#comment_popup_post_id').data('post-id', response.post_id);

                $(".auth-dropdown, .user-dropdown").hide();
                if (auth_user == response.post_cby_id) {
                    $(".auth-dropdown").show();
                } else {
                    $(".user-dropdown").show();
                }
            },
            error: function() {
                $("#commentList").html(
                    '<h6 class="text-center">Failed to Load Comments</h6>');
            }
        });
    });

    // Like Button 
    $(document).on("click", ".like_icon,.like-btn", function() {

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
            success: function(response) {
                if (response.success) {
                    if (response.likes_count >= 1000) {
                        $('.view-like').text((response.likes_count / 1000).toFixed(1) + 'k Likes');
                    } else if (response.likes_count == 0) {
                        $('.view-like').text(' Likes');
                    } else {
                        $('.view-like').text((response.likes_count) + ' Likes');
                    }
                    if (response.action === "like") {
                        showToast("Post Liked");
                        $button.removeClass("fa-regular").addClass("fa-solid active");
                    } else {
                        showToast("Post Unliked");
                        $button.removeClass("fa-solid active").addClass("fa-regular");
                    }
                }
            },
            complete: function() {
                $button.data("loading", false);
            }
        });
    });

    // Save Button
    $(document).on("click", ".save-btn", function() {
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
            success: function(response) {
                if (response.success) {
                    if (response.action === "save") {
                        showToast("Post Saved");
                        $button.removeClass("fa-regular").addClass("fa-solid active");
                    } else {
                        showToast("Post Unsaved");
                        $button.removeClass("fa-solid active").addClass("fa-regular");
                    }
                }
            },
            complete: function() {
                $button.data("loading", false);
            }
        });
    });

    // Comment Button
    $(document).on("click", "#postCommentBtn", function() {
        let comment = $("#commentInput").val().trim();
        let $btn = $(this);
        if ($btn.hasClass('pop_up_php')) {
            var post_id = $(this).data("post-id");
        } else {
            var post_id = $("#post_id").val().trim();
        }
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
            success: function(response) {
                showToast("Comment Posted")
                $("#commentList").html(response.html);
                $("#commentInput").val("");
                $(".comment-count").text(response.com_cnt);
            },
            complete: function() {
                $btn.prop("disabled", false).text("Comment");
            }
        });
    });

    // Comment Dropdwon
    $(document).on('click', '.comment-action', function(e) {
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
            success: function(response) {
                $this.data('clicked', false);

                if (response.success) {
                    if (action === 'delete') {
                        showToast('Comment Deleted');

                        let targetDiv = $('#comment-' + commentId);
                        $('#comment-' + commentId).fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else if (action === 'report') {
                        showToast('Comment reported');
                    } else if (action === 'report post') {
                        showToast('Reel reported');
                    }
                } else {
                    // toastr.error('Something went wrong');
                }
            },
            error: function() {
                $this.data('clicked', false);
            }
        });
    });

    // Share Button
    $(document).on("click", ".share-btn", function() {
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
                // action: issaved ? "unsave" : "save",
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                $("#share_list").html(response.html);
                $("#indent_link").html(response.link);
                $("#share_list").data('share-url', response.share_url);
                $("#share_list").data('share-text', response.share_text);
            },
            error: function() {
                $("#share_list").html(
                    '<h6 class="text-center text-danger">Failed to load</h6>');
            },
            complete: function() {
                $button.data("loading", false);
            }
        });
    });

    // Likes Count
    $(document).on("click", ".likes-count", function() {
        let postId = $(this).data("post-id");
        $("#likesModalBody").html(`
            <div class="text-center d-flex align-items-center justify-content-center flex-column gap-1 mt-3">
                <img src="{{ asset('assets/images/Favicon.png') }}" alt="Loading" width="30px" height="30px">
                <h6 class="text-muted" style="font-size: 10px;">Loading</h6>
            </div>
        `);
        $.ajax({
            url: "{{ route('toggle.getLikesList') }}",
            method: "POST",
            data: {
                post_id: postId,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                $("#likesModalBody").html(response.html);
            },
            error: function() {
                $("#likesModalBody").html(
                    '<h6 class="text-center text-danger">Failed to load likes</h6>');
            }
        });
    });
</script>

<script>
    // Delete Post
    $(document).ready(function() {
        $(document).on("click", ".delete-post-btn", function() {
            const postId = this.getAttribute('data-post-id');
            $.ajax({
                url: "{{ route('post.delete') }}",
                type: "POST",
                data: {
                    id: postId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    console.error("AJAX Error:", xhr.responseText);
                }
            });
        });
    });
</script>

<!-- Multiple Form Submissions -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('addPostForm');
        const submitBtn = document.querySelector('.addPostbtn');
        let isSubmitting = false;

        form.addEventListener('submit', function(e) {
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
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.editPostForm');
        const submitBtn = document.querySelector('.editPostbtn');
        let isSubmitting = false;

        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return;
            }
            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>  Updating...`;
        });
    });
</script>
