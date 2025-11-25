@extends('layouts.app')

@section('title', 'Onstru | Explore')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/post.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">

    <!-- Tribute -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.css" />
    <script src="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.min.js"></script>

    <style>
        @media screen and (min-width: 767px) {
            .searchflex {
                width: 40% !important;
            }

            .post-bg {
                height: 300px;
            }
        }

        @media screen and (max-width: 767px) {
            .post-bg {
                height: 250px;
            }
        }

        .post-bg {
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* .suggestion-box {
                position: absolute;
                top: 35px;
                background-color: var(--secondary);
                border: 2px solid var(--border);
                border-radius: 5px;
                z-index: 1000;
                width: 100%;
                max-height: 200px;
                overflow-y: auto;
            }

            .suggestion-box::-webkit-scrollbar {
                display: none;
            }

            .suggestion-item {
                font-size: 12px;
                font-weight: var(--fw-md);
                color: var(--text-primary);
                padding: 5px 12px;
                cursor: pointer;
                margin-bottom: 5px;
            }

            .suggestion-item:hover {
                background-color: #f1f1f1;
            }

            .searchflex {
                position: relative;
            } */
    </style>

    <div class="container-xl main-div">
        <div class="flex-side d-block mb-3">
            <!-- Flex Cards -->
            <div class="flex-cards form-div">
                <div class="body-head mb-3">
                    <h5>Explore</h5>
                </div>
                <div class="inpleftflex searchflex mb-3">
                    <i class="fas fa-search"></i>
                    <input type="text" name="keyword" id="searchInput" class="form-control border-0 position-relative"
                        placeholder="Search Hashtags (eg., #construction, #onstru,...)" value="{{ request('keyword') }}">
                    <!-- <div id="suggestionBox" class="suggestion-box" style="display: none;"></div> -->
                </div>

                <div class="explore-cards" id="feed-container">
                    <!-- Explore Cnt -->
                </div>
            </div>
        </div>
    </div>

    <!-- Load More -->
    <button id="loadMoreBtn" class="loadbtn">
        <!-- <img src="{{ url('assets/images/Favicon.png') }}" alt="Loading" width="50px" height="50px"> -->
        <input type="hidden" id="next-posts" value="{{ $next_posts_cursor ?? 'empty' }}">
        <!-- <h6 class="text-muted" style="font-size: 10px;">Loading</h6> -->
    </button>

    <!-- Loader -->
    <div class="loader d-flex align-items-center justify-content-center flex-column gap-1">
        <img src="{{ asset('assets/images/Favicon.png') }}" alt="Loading" width="40px" height="40px">
        <h6 class="text-muted" style="font-size: 12px;">Loading</h6>
    </div>

    @include('popups.popup')

    @include('profile.post-popup')

    @include('profile.post-script')

    <!-- Ajax -->
    <script>
        let isLoading = false;
        let hasMorePosts = true;

        function loadFeed(reset = false) {
            // if (isLoading || !hasMorePosts) return;
            if (!hasMorePosts && !reset) return;
            isLoading = true;
            $("#loader").show();
            let searchQuery = $("#searchInput").val()?.trim() || getRouteParameter(2) || 'empty';
            if (reset) {
                $("#feed-container").html('');
                nextCursor = '';
                hasMorePosts = true;
            }

            let nextCursors = {
                posts: reset ? '' : $("#next-posts").val()
            };
            let allEmpty = Object.values(nextCursors).every(v => !v);
            if (allEmpty) {
                $("#loadMoreBtn").hide();
                $('.loader').hide();
                $("#loadMoreBtn").removeClass("d-flex");
            } else {
                $("#loadMoreBtn").show(); // show button
            }
            $.ajax({
                url: "{{ route('explore') }}",
                method: "GET",
                data: {
                    posts_cursor: nextCursors.posts,
                    keyword: searchQuery,
                    _token: "{{ csrf_token() }}"
                },
                before: function () {
                    $(".loader").show();
                },
                success: function (res) {
                    if (res.all_html) {
                        $("#feed-container").removeClass('d-block');
                        $("#feed-container").append(res.all_html);
                    } else if (reset) {
                        $("#feed-container").addClass('d-block');
                        $('#feed-container').html(`
                                    <div class="side-cards shadow-none border-0 col-sm-12 w-100">
                                        <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                                            <img src="{{ asset('assets/images/Empty/NoPosts.png') }}" height="200px" class="d-flex mx-auto mb-2" alt="">
                                            <h5 class="text-center mb-0">No Posts Found</h5>
                                            <h6 class="text-center bio">No posts are available at the moment - try refreshing or check back later for new
                                                updates</h6>
                                        </div>
                                    </div>
                                `);
                        hasMorePosts = false;
                    }

                    $("#next-posts").val(res.next_posts_cursor);
                    if (!res.next_posts_cursor) {
                        hasMorePosts = false;
                        $("#loadMoreBtn").hide();
                        $('.loader').removeClass('d-flex')
                        $('.loader').hide();
                    } else {
                        hasMorePosts = true;
                        $("#loadMoreBtn").show();
                    }
                    // Update cursor
                    // $("#next-posts").val(res.next_posts_cursor);
                    // if (res.next_posts_cursor === null) {
                    //     hasMorePosts = false;
                    //     $("#loadMoreBtn").hide();
                    //     $("#loadMoreBtn").removeClass("d-flex");
                    // }
                    // $("#next-jobs").val(res.next.jobs);
                    // $("#next-products").val(res.next.products);
                    // $("#next-services").val(res.next.services);
                    // initFeedVideos();
                },
                complete: function () {
                    $(".loader").hide();
                    isLoading = false;
                }
            });
        }

        // Load more button
        $(document).on("click", "#loadMoreBtn", function () {
            loadFeed();
        });

        // Search listener
        $("#searchInput").on("keyup", function (e) {
            let query = $(this).val()?.trim();
            let routeParam = getRouteParameter(2);

            if (e.key === "Enter") {
                loadFeed(true);
            } else if (query === '') {
                loadFeed(true);
            }
        });

        function getRouteParameter(index) {
            let segments = window.location.pathname.split('/');
            return segments[index] || '';
        }

        // Load feed on page load
        $(document).ready(function () {
            loadFeed(true);
        });
    </script>

    <!-- Load More Tigger -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
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

    <!-- Suggestion Box -->
    <!-- <script>
        const searchInput = document.getElementById("searchInput");
        const suggestionBox = document.getElementById("suggestionBox");

        let debounceTimer;

        searchInput.addEventListener("input", function () {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            // Triggers only starts (#)
            if (query.length < 1 || !query.startsWith("#")) {
                suggestionBox.style.display = "none";
                return;
            }

            debounceTimer = setTimeout(() => {
                fetchSuggestions(query);
            }, 300);
        });

        function fetchSuggestions(query) {
            fetch(`{{ route('hashtags.suggest') }}?keyword=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) throw new Error("Network error");
                    return response.json();
                })
                .then(data => {
                    renderSuggestions(data);
                })
                .catch(error => {
                    console.error("Error fetching hashtags:", error);
                    suggestionBox.style.display = "none";
                });
        }

        function renderSuggestions(tags) {
            suggestionBox.innerHTML = "";

            if (!Array.isArray(tags) || tags.length === 0) {
                suggestionBox.style.display = "none";
                return;
            }

            tags.forEach(tag => {
                const div = document.createElement("div");
                div.classList.add("suggestion-item");
                div.textContent = tag;
                div.addEventListener("click", () => {
                    searchInput.value = tag;
                    suggestionBox.style.display = "none";
                });
                suggestionBox.appendChild(div);
            });

            suggestionBox.style.display = "block";
        }

        document.addEventListener("click", function (e) {
            if (!e.target.closest(".searchflex")) {
                suggestionBox.style.display = "none";
            }
        });
    </script> -->

@endsection