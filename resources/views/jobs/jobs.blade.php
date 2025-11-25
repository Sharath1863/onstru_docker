@extends('layouts.app')

@section('title', 'Onstru | Jobs')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">

    <style>
        .side-cards:hover {
            transform: translate(0px, -5px);
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }
    </style>

    <div class="container-xl main-div">
        <div class="flex-side">
            <!-- Filter Sidebar -->
            @include('jobs.aside')
            <!-- Cards -->
            <div class="flex-cards pt-2" id="job-container">
                <div class="body-head mb-3">
                    <h5>Recommended Jobs For You!</h5>
                    <button class="border-0 m-0 p-0 filter-responsive" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvas-filter" aria-controls="offcanvas-filter">
                        <i class="fas fa-filter fs-4"></i>
                    </button>
                </div>

                <!-- Empty State -->
                <div class="side-cards shadow-none border-0" id="noCard"
                    style="{{ count($jobs) > 0 ? 'display: none;' : 'display: block;' }}">
                    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                        <img src="{{ asset('assets/images/Empty/NoJobs.png') }}" height="200px" class="d-flex mx-auto mb-2"
                            alt="">
                        <h5 class="text-center mb-0">No Jobs Found</h5>
                        <h6 class="text-center bio">No jobs match your search right now - try refining your criteria or check
                            back for new listings soon.</h6>
                    </div>
                </div>

                <!-- Job Cards -->
                @include('jobs.listing')

                <button id="loadMoreBtn" class="btn btn-primary" data-next-url="{{ $next_page_url ?? '' }}"
                    @if (!$next_page_url) style="display:none;" @endif>
                    Load More
                </button>
                <div id="loader" style="display:none; text-align:center; margin:10px;">
                    <img src="{{ url('assets/images/icon_1.png') }}" alt="Loading" width="50">
                </div>
            </div>
        </div>
    </div>

    @include('popups.popup')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let isLoading = false;

            const observer = new IntersectionObserver(async (entries) => {
                entries.forEach(async (entry) => {
                    if (entry.isIntersecting && !isLoading) {
                        const trigger = entry.target;
                        const nextUrl = trigger.dataset.nextUrl;

                        if (!nextUrl) return;

                        isLoading = true;
                        trigger.querySelector('.loading-text').classList.remove('d-none');
                        trigger.querySelector('.loading-text').textContent = 'Loading';

                        try {
                            const response = await fetch(nextUrl, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            if (!response.ok) throw new Error('Network error');

                            const html = await response.text();
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = html;

                            // Append jobs
                            const newJobs = tempDiv.querySelectorAll('.job-card');
                            newJobs.forEach(job => document.getElementById('job-container')
                                .appendChild(job));

                            // Remove old trigger
                            trigger.remove();

                            // Add new trigger (if exists)
                            const newTrigger = tempDiv.querySelector('.load-more-trigger');
                            if (newTrigger) {
                                document.getElementById('job-container').appendChild(
                                    newTrigger);
                                observer.observe(newTrigger);
                            }
                        } catch (error) {
                            console.error(error);
                            trigger.querySelector('.loading-text').textContent =
                                'Failed, retry scroll...';
                        } finally {
                            isLoading = false;
                        }
                    }
                });
            }, {
                rootMargin: '200px'
            }); // loads early (before user fully reaches bottom)

            // Start observing the first trigger
            const firstTrigger = document.querySelector('.load-more-trigger');
            if (firstTrigger) {
                observer.observe(firstTrigger);
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $(document).on('click', '.save-job', function(e) {
                e.preventDefault();
                let jobId = $(this).data('id');
                let btn = $(this);
                $.ajax({
                    url: "{{ route('jobs.toggleSave', ['id' => '__jobId__']) }}".replace('__jobId__', jobId);
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            btn.find("i").removeClass("far").addClass("fas text-warning");
                            showToast('Job Saved!');
                        } else {
                            btn.find("i").removeClass("fas text-warning").addClass("far");
                            showToast('Job Unsaved!');
                        }
                    },
                    error: function() {
                        showToast("Something went wrong!");
                    }
                });
            });

            let nextUrl = "/job_location";
            let loading = false;
            // let debounceTimer; // for keyword input

            // Collect all filters into params
            function getFilters() {
                return {
                    loc: $(".location-filter:checked").map((_, el) => $(el).val()).get(),
                };
            }

            // Load products
            function loadProducts(url, reset = false) {
                // $('#loader').show();
                // if (!url || loading) return;
                // loading = true;

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        ...getFilters(),
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    beforeSend: function() {},
                    success: function(res) {
                        let html = "";
                        res.data.forEach(sub_loc => {
                            html += `
                            <li>
                                <input type="checkbox" class="filter-checkbox sub-location-filter" id="${sub_loc.sublocality}"
                                    value="${sub_loc.sublocality}">
                                <label for="${sub_loc.sublocality}">${sub_loc.sublocality}</label>
                            </li>`;
                        });

                        if (res.data) {
                            $("#sub_loc").html(html);
                        }
                        nextUrl = res.next_page_url;
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                    },
                    complete: function() {}
                });
            }

            $(document).on('change', '.location-filter', function() {
                nextUrl = "/job_location"; // reset cursor
                loadProducts(nextUrl, true);
            });

        });
    </script>
    
    <script>
        document.querySelectorAll('.followingbtn').forEach(button => {
            button.addEventListener('click', function(e) {
                // Disable all buttons on the page
                document.querySelectorAll('button').forEach(btn => {
                    btn.disabled = true;
                });

                // Optionally: Change the clicked button text to show it's loading
                this.innerText = 'Loading';

                // If you want to allow default behavior (like navigation), let it continue.
                // Otherwise, use e.preventDefault();
            });
        });
    </script>
@endsection
