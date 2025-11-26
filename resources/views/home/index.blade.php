@extends('layouts.app')

@section('title', 'Onstru | Home')

@section('content')
<div class="container">
    <h1>Welcome home again!</h1>
    <p>This is updated via CI/CD!</p>
</div>

    <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">

    @php
        $my = auth()
            ->user()
            ->loadCount(['followers', 'following']);
    @endphp

    <div class="container-xl main-div">
        <div class="flex-side">
            <!-- Home Left -->
            @include('home.home-left')


            <!-- Home Center -->
            <div>
                @include('home.home-center', ['posts_data' => $merged])

                <button id="loadMoreBtn" class="loadbtn d-flex flex-column mx-auto gap-1 my-3">
                    <img src="{{ asset('assets/images/Favicon.png') }}" alt="Loading" width="50px" height="50px">
                    <h6 class="text-muted" style="font-size: 12px;">Loading</h6>
                </button>
                <input type="hidden" id="next-posts" value="{{ $next['posts'] ?? 'empty' }}">
                <input type="hidden" id="next-jobs" value="{{ $next['jobs'] ?? 'empty' }}">
                <input type="hidden" id="next-products" value="{{ $next['products'] ?? 'empty' }}">
                <input type="hidden" id="next-services" value="{{ $next['services'] ?? 'empty' }}">
            </div>

            <!-- Home Right -->
            @include('home.home-right')
        </div>

        @include('popups.popup')

        @include('popups.comment')

        @include('popups.follow')

        @include('home.script')

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const loadMoreBtn = document.getElementById("loadMoreBtn");

            function loadMoreContent() {
                loadMoreBtn.click();
            }
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        loadMoreContent();
                    }
                });
            }, {
                root: null,
                threshold: 1.0
            });
            observer.observe(loadMoreBtn);
        });
    </script>

    <!-- Post Loading -->
    <script>
        let isLoading = false;
        let hasMorePosts = true;

        function loadFeed() {
            if (isLoading || !hasMorePosts) return;
            isLoading = true;
            $("#loadMoreBtn").show();

            let nextCursors = {
                posts: $("#next-posts").val(),
                jobs: $("#next-jobs").val(),
                products: $("#next-products").val(),
                services: $("#next-services").val(),
            };
            // Check if all are empty/null
            let allEmpty = Object.values(nextCursors).every(v => !v);

            if (allEmpty) {
                $("#loadMoreBtn").hide();
                $("#loadMoreBtn").removeClass("d-flex");
                $("#loadMoreBtn").addClass("d-none");
            } else {
                $("#loadMoreBtn").show();
            }
            $.ajax({
                url: "{{ route('home.index') }}",
                method: "GET",
                data: {
                    posts_cursor: nextCursors.posts,
                    jobs_cursor: nextCursors.jobs,
                    products_cursor: nextCursors.products,
                    service_cursor: nextCursors.services,
                },
                success: function(res) {
                    const container = $("#feed-container .flex-cards");
                    $("#feed-container").append(res.feed);

                    // Update cursor
                    $("#next-posts").val(res.next.posts);
                    $("#next-jobs").val(res.next.jobs);
                    $("#next-products").val(res.next.products);
                    $("#next-services").val(res.next.services);
                    initFeedVideos();
                },
                complete: function() {
                    $("#loadMoreBtn").hide();
                    isLoading = false;
                }
            });
        }

        //loaed more button
        $(document).on("click", "#loadMoreBtn", function() {
            loadFeed();
        });
    </script>

    <!-- Post Like / Share / Save -->
    <script>
        // Post Like
        $(document).on("click", ".like-btn", function() {
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
                        if (response.likes_count === 0) {
                            $(".likes-count[data-post-id='" + postId + "']").text("");
                        } else if (response.likes_count >= 1000) {
                            $(".likes-count[data-post-id='" + postId + "']").text((response
                                .likes_count / 1000).toFixed(1) + "k Likes");
                        } else {
                            $(".likes-count[data-post-id='" + postId + "']").text(response.likes_count +
                                " Likes");
                        }
                        if (response.action === "like") {
                            showToast("Post Liked");
                            $button.removeClass("fa-regular text-dark").addClass("fa-solid active");
                        } else {
                            showToast("Post Unliked");
                            $button.removeClass("fa-solid active").addClass("fa-regular text-dark");
                        }
                    }
                },
                complete: function() {
                    $button.data("loading", false);
                }
            });
        });

        // Post Save
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

        // Post Share
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
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $("#share_list").html(response.html);
                    $("#indent_link").html(response.link);
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
    </script>

    <!-- Post Like/Comment Counts/Actions -->
    <script>
        // Post Like Count
        $(document).on("click", ".likes-count", function() {
            let postId = $(this).data("post-id");
            $("#likesModalBody").html('<h6 class="text-center">Loading</h6>');
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

        // Post Comment Count
        $(document).on("click", ".comment-count-pop", function() {
            let postId = $(this).data("post-id");
            $("#commentList").html('<h6 class="text-center">Loading</h6>');
            $.ajax({
                url: "{{ route('toggle.getCommentList') }}",
                method: "POST",
                data: {
                    post_id: postId,
                    _token: "{{ csrf_token() }}" // required for Laravel
                },
                success: function(response) {
                    $("#commentList").html(response.html);
                    $('#post_id').val(response.post_id);
                },
                error: function() {
                    $("#commentList").html(
                        '<h6 class="text-center">Failed to Load Comments</h6>');
                }
            });
        });

        // Post Comment Button
        $(document).on("click", "#postCommentBtn", function() {
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
                success: function(response) {
                    showToast("Comment Posted")
                    $("#commentList").html(response.html);
                    $("#commentInput").val("");
                    $(".comment-count[data-post-id='" + post_id + "']").text(response.com_cnt);
                },
                complete: function() {
                    $btn.prop("disabled", false).text("Comment");
                }
            });
        });

        // Post Comment Action
        $(document).on('click', '.comment-action', function(e) {
            e.preventDefault();
            let $this = $(this);
            if ($this.data('clicked')) return;
            $this.data('clicked', true);
            let commentId = $this.data('id');
            let action = $this.data('action'); // "delete" or "report"
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
                            showToast('Comment deleted');
                            let targetDiv = $('#comment-' + commentId);
                            $('#comment-' + commentId).fadeOut(300, function() {
                                $(this).remove();
                            });

                        } else if (action === 'report') {
                            showToast('Comment reported');
                        } else if (action === 'report post') {
                            showToast('Post reported');
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
    </script>

    <!-- Add To Cart -->
    <script>
        $(document).on('click', '.add-to-cart', function(e) {
            e.preventDefault();
            let button = $(this);
            let productId = button.data('product');
            let vendorId = button.data('vendor');
            let min_quantity = button.data('quantity');
            if (button.data('clicked')) return;
            button.data('clicked', true);
            let cartUrl = "{{ url('/individual-product') }}/" + productId;
            button.removeClass('add-to-cart removebtn')
                .addClass('listbtn w-100 go-to-cart')
                .html('<i class="fas fa-cart-shopping pe-1"></i> Go To Cart')
                .off('click')
                .on('click', function() {
                    window.location.href = cartUrl;
                });
            button.data('clicked', false);
            $.ajax({
                url: "{{ route('cart.store') }}",
                type: "POST",
                data: {
                    product_id: productId,
                    vendor_id: vendorId,
                    quantity: min_quantity,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        showToast('Added to cart');
                    } else {
                        showToast(response.message || 'Could not add to cart');
                    }
                },
                error: function() {
                    button.data('clicked', false);
                    showToast('Something went wrong');
                }
            });
        });
    </script>

    <!-- See More / See Less (Caption) -->
    <script>
        $(document).on('click', '.see-more', function() {
            var postId = $(this).attr('id').replace('see-more', ''); // Get the post ID dynamically
            var caption = $('#caption' + postId);
            var seeMoreLink = $(this);

            // Toggle caption visibility
            if (caption.hasClass('expanded')) {
                caption.removeClass('expanded');
                seeMoreLink.text('See more');
            } else {
                caption.addClass('expanded');
                seeMoreLink.text('See less');
            }
        });
    </script>

    <script>
        function initFeedVideos() {
            const videos = document.querySelectorAll(".feed-video");

            videos.forEach(video => {
                const wrapper = video.closest(".video-wrapper");
                const btn = wrapper.querySelector(".video-play-btn");

                wrapper.addEventListener("click", (e) => {
                    e.stopPropagation();
                    if (video.paused) {
                        videos.forEach(v => {
                            if (!v.paused) v.pause();
                        }); // stop others
                        video.muted = false;
                        video.play();
                    } else {
                        video.pause();
                    }
                });

                video.addEventListener("play", () => btn.innerHTML = '<i class="fas fa-pause"></i>');
                video.addEventListener("pause", () => btn.innerHTML = '<i class="fas fa-play"></i>');
            });

            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    const video = entry.target;
                    const btn = video.closest(".video-wrapper").querySelector(".video-play-btn");

                    if (entry.isIntersecting) {
                        videos.forEach(v => {
                            if (!v.paused) v.pause();
                        }); // pause others
                        video.muted = false; // autoplay muted
                        video.play().catch(() => {}); // prevent NotAllowedError
                        btn.innerHTML = '<i class="fas fa-pause"></i>';
                    } else {
                        video.pause();
                        btn.innerHTML = '<i class="fas fa-play"></i>';
                    }
                });
            }, {
                threshold: 0.6
            });

            videos.forEach(video => observer.observe(video));
        }

        document.addEventListener("DOMContentLoaded", initFeedVideos);
    </script>

@endsection
