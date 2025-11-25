@extends('layouts.app')

@section('title', 'Onstru | Reels')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/reels.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">

    <style>
        @media screen and (max-width: 767px) {
            .container-xl {
                padding: 0px !important;
                margin-block-start: 0px !important;
            }
        }
    </style>

    <div class="container-xl main-div my-1">

        <div class="reels">
            <div class="reels-div">
                @include('reels.reel-div', ['posts' => $posts, 'posts_type' => $posts_type])
            </div>
        </div>

        <input type="hidden" id="next-reels-cursor" name="reels_cursor" value="{{ $posts->nextCursor()?->encode() }}">
        {{-- <div id="loader" style="display:none;">Loading</div> --}}

    </div>

    @include('popups.popup')

    @include('popups.comment')

    @include('reels.script')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        let isLoading = false;
        let hasMoreReels = true;

        function bindReelEvents() {
            document.querySelectorAll(".reel-item").forEach(item => {
                if (item.dataset.bound === "true") return;
                item.dataset.bound = "true";

                const video = item.querySelector("video");
                const playIcon = item.querySelector(".play-btn");
                const pauseIcon = item.querySelector(".pause-btn");

                if (!video || !playIcon || !pauseIcon) return;

                const videoDiv = item.querySelector(".video-div");

                videoDiv.addEventListener("click", (e) => {
                    if (e.target.tagName.toLowerCase() === "video") {
                        video.paused ? video.play() : video.pause();
                    }
                });

                video.addEventListener("play", () => {
                    playIcon.style.display = "none";
                    pauseIcon.style.display = "block";
                });

                video.addEventListener("pause", () => {
                    pauseIcon.style.display = "none";
                    playIcon.style.display = "block";
                });

                const observer = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            video.play().catch(() => { });
                        } else {
                            video.pause();
                        }
                    });
                }, {
                    threshold: 0.6
                });

                observer.observe(video);
            });
        }
        bindReelEvents();

        function loadReels() {
            if (isLoading || !hasMoreReels) return;
            isLoading = true;
            $.ajax({
                url: "{{ route('reels') }}",
                method: "GET",
                data: {
                    reels_cursor: $("#next-reels-cursor").val()
                },
                success: function (res) {
                    $(".reels-div").append(res.html);
                    $("#next-reels-cursor").val(res.next_cursor);
                    bindReelEvents();
                    let lastPost = res.posts_type;
                    if (lastPost) {
                        $(".act_asset_1").hide();
                    } else {
                        $(".act_" + lastPost.id).show();
                    }
                },
                complete: function () {
                    isLoading = false;
                }
            });
        }

        $(".reels-div").on("scroll", function () {
            let container = $(this);
            if (container.scrollTop() + container.innerHeight() >= container[0].scrollHeight - 50) {
                loadReels();
            }
        });
    </script>

@endsection