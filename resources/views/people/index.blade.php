@extends('layouts.app')

@section('title', 'Onstru | Peoples')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">

    <style>
        .side-cards:hover {
            transform: translate(0px, -5px);
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }
    </style>

    <div class="container-xl main-div">
        <div class="flex-side">
            <!-- Filter Sidebar -->
            @include('people.aside')
            <!-- Cards -->
            <div class="flex-cards pt-2">
                <div class="body-head mb-3">
                    <h5>Peoples</h5>
                    <button class="border-0 m-0 p-0 filter-responsive" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvas-filter" aria-controls="offcanvas-filter">
                        <i class="fas fa-filter fs-4"></i>
                    </button>
                </div>

                <!-- Empty State -->
                <div class="side-cards shadow-none border-0" id="noCard"
                    style="{{ count($users) > 0 ? 'display: none;' : 'display: block;' }}">
                    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                        <img src="{{ asset('assets/images/Empty/NoPeoples.png') }}" height="200px"
                            class="d-flex mx-auto mb-2" alt="">
                        <h5 class="text-center mb-0">No Peoples Found</h5>
                        <h6 class="text-center bio">Currently, no people matches your search - try refining your search or
                            explore other connections.</h6>
                    </div>
                </div>

                <!-- People Cards -->
                <div class="post-cards" id="initial_people">
                    @foreach ($users as $user)
                        <div class="side-cards people-card rounded-1">
                            <div class="cards-content d-flex align-items-center justify-content-center flex-column">
                                <div class="avatar-div-40 position-relative mb-2">
                                    <img src="{{ asset($user['profile_img'] ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $user['profile_img'] : 'assets/images/Avatar.png') }}"
                                        class="avatar-40" alt="">
                                    @if ($user->badge != 0 && $user->badge != null)
                                        <img src="{{ asset('assets/images/Badge_' . $user->badge . '.png') }}" class="badge-40"
                                            alt="">
                                    @endif
                                </div>
                                <div>
                                    <h5 class="mb-1 text-center">{{ $user['name'] }}</h5>
                                    <h6 class="bio mb-2 text-center text-lowercase"><em>{{ $user['user_name'] }}</em></h6>
                                </div>
                                <a href="{{ url('user-profile/' . $user->id) }}">
                                    <div class="d-flex align-items-center justify-content-center mx-auto">
                                        <button class="listbtn">View Profile</button>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button id="loadMoreBtn" class="loadbtn flex-column mx-auto gap-1 my-3"
                    data-next-url="{{ $next_page_url ?? '' }}" @if (!$next_page_url) style="display:none;" @else
                    style="display: flex;" @endif>
                    <img src="{{ asset('assets/images/Favicon.png') }}" alt="Loading" width="50px" height="50px">
                    <h6 class="text-muted" style="font-size: 12px;">Loading</h6>
                </button>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

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

    <script>
        let nextUrl = "/people";
        let loading = false;
        let debounceTimer;

        // Collect all filters into params
        function getFilters() {
            return {
                keyword: $("#keywordSearch").val(),
                categories: $(".category-filter:checked").map((_, el) => $(el).val()).get(),
                locations: $(".loc-filter:checked").map((_, el) => $(el).val()).get(),
            };
        }

        // Load products
        function loadProducts(url, reset = false) {
            $('#loader').show();
            if (!url || loading) return;
            loading = true;

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    ...getFilters(),
                    _token: "{{ csrf_token() }}"
                },
                dataType: 'json',
                beforeSend: function () {
                    loading = true;
                    $('#loader').show();
                },
                success: function (res) {
                    console.log(res.data);

                    if (res.data.length === 0) {
                        if (reset) {
                            $("#initial_people").html("");
                            $("#noCard").show();
                        }
                        $("#loadMoreBtn").hide();
                        return;
                    }

                    $("#noCard").hide();

                    let html = "";
                    res.data.forEach(people => {
                        html += `
                                        <div class="side-cards people-card rounded-1">

                                            <div class="cards-content d-flex align-items-center justify-content-center flex-column">
                                                <div class="avatar-div-40 position-relative mb-2">
                                                    <img src="${people.profile_img
                                ? `https://onstru-social.s3.ap-south-1.amazonaws.com/${people.profile_img}`
                                : 'assets/images/Avatar.png'}"
                                                        class="avatar-40" alt="">
                                                    ${people.badge && people.badge != 0
                                ? `<img src="assets/images/Badge_${people.badge}.png" class="badge-40" alt="">`
                                : ''}
                                                </div>

                                                <div>
                                                    <h5 class="mb-1 text-center">${people.name ?? ''}</h5>
                                                    <h6 class="bio mb-2 text-center text-lowercase"><em>${people.user_name ?? ''}</em></h6>
                                                </div>

                                                <a href="user-profile/${people.id}">
                                                    <div class="d-flex align-items-center justify-content-center mx-auto">
                                                        <button class="listbtn">View Profile</button>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>`;
                    });

                    if (reset) {
                        $("#initial_people").html(html);
                    } else {
                        $("#initial_people").append(html);
                    }

                    // Update Load More button
                    if (res.next_page_url) {
                        $("#loadMoreBtn").data("next-url", res.next_page_url).show();
                    } else {
                        $("#loadMoreBtn").hide();
                    }

                    nextUrl = res.next_page_url;
                },

                error: function (xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                },
                complete: function () {
                    loading = false;
                    $('#loader').hide();
                }
            });
        }

        // Load more button click
        $(document).on("click", "#loadMoreBtn", function () {
            const nextUrl = $(this).data("next-url");
            loadProducts(nextUrl, false);
        });

        // Debounce keyword input
        $('#keywordSearch').on('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                nextUrl = "/people";
                loadProducts(nextUrl, true);
            }, 500);
        });

        // On filter change → reset products
        $(document).on('change', '.filter-checkbox, .loc-filter', function () {
            nextUrl = "/people";
            loadProducts(nextUrl, true);
        });
    </script>

@endsection