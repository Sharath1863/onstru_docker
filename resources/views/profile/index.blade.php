@extends('layouts.app')

@section('title', 'Onstru | Profile')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.css') }}">
    <!-- Post Tab CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/post.css') }}">

    <!-- Tribute -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.css" />
    <script src="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.min.js"></script>

    @php
        $my = auth()
            ->user()
            ->loadCount(['followers', 'following']);
    @endphp

    <style>
        @media screen and (min-width: 767px) {

            #projects,
            #products,
            #leads,
            #jobs,
            #service {
                .inpleftflex {
                    width: 50% !important;
                }
            }
        }

        .flex-sidebar {
            display: block !important;
        }

        .carousel-indicators [data-bs-target] {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--border);
        }

        .carousel-indicators .active {
            background-color: var(--main);
        }

        /* Inactive Status */
        .readybtn.inactive {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
            border-color: #dc3545 !important;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        }

        .readybtn.inactive:hover {
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        }

        .tab-content .side-cards:hover {
            transform: translate(0px, -5px);
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }
    </style>

    <div class="container-xl main-div">
        <div class="flex-side">
            <!-- Flex Left -->
            <div class="flex-sidebar border-0">
                <div class="flex-cards">

                    <div class="side-cards mb-3">
                        <div class="cards-content">
                            @include('flexleft.profile-card')

                            @if (auth()->user()->you_are === 'Consumer' || auth()->user()->you_are === 'Professional')
                                @if ($readyToWork_count == 0 || $readyToWork->status === 'expired')
                                    {{-- @if ($readyToWork_count == 0) --}}
                                    <button class="addreadybtn d-flex mx-auto mt-2" data-bs-toggle="modal"
                                        data-bs-target="#addready">
                                        Add Ready to Work
                                    </button>
                                    {{-- @else
                                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editready">
                                        <i class="fas fa-pen-to-square pe-1"></i> Edit
                                    </a>
                                    @endif --}}
                                @else
                                    <!-- Show Ready to Work button with status -->
                                    <div class="dropdown mt-2">
                                        <button
                                            class="readybtn d-flex mx-auto {{ $readyToWork->status === 'inactive' ? 'inactive' : '' }}"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Ready to Work
                                            {{ $readyToWork->status === 'inactive' ? '(Inactive)' : '' }}
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li class="pb-1">
                                                <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editready">
                                                    <i class="fas fa-pen-to-square pe-1"></i> Edit
                                                </a>
                                            </li>
                                            <li class="pb-1">
                                                <form method="POST"
                                                    action="{{ route('ready-to-work.toggle-status', $readyToWork->id) }}"
                                                    style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item border-0 bg-transparent">
                                                        <i
                                                            class="fas fa-{{ $readyToWork->status === 'active' ? 'pause' : 'play' }} pe-1"></i>
                                                        {{ $readyToWork->status === 'active' ? 'Inactive' : 'Active' }}
                                                    </button>
                                                </form>
                                            </li>
                                            {{-- <li class="pb-1">
                                                <form method="POST" action="{{ route('ready-to-work.delete', $readyToWork->id) }}"
                                                    style="display: inline;"
                                                    onsubmit="return confirm('Are you sure you want to delete your Ready to Work entry?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item border-0 bg-transparent">
                                                        <i class="fas fa-trash pe-1"></i> Delete
                                                    </button>
                                                </form>
                                            </li> --}}
                                        </ul>
                                    </div>
                                @endif
                            @endif

                            <hr class="w-75 mx-auto my-3">

                            <div class="profile-tabs">
                                <ul class="nav nav-tabs d-flex justify-content-sm-between justify-content-md-center align-items-start flex-sm-row flex-md-column border-0"
                                    id="myTab" role="tablist">
                                    @include('profile.tabs')
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flex Right -->
            <div class="flex-cards pt-2">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="posts" role="tabpanel">
                        @include('profile.posts')
                    </div>
                    <div class="tab-pane fade" id="projects" role="tabpanel">
                        @include('profile.projects')
                    </div>
                    <div class="tab-pane fade" id="products" role="tabpanel">
                        @include('profile.products')
                    </div>
                    <div class="tab-pane fade" id="jobs" role="tabpanel">
                        @include('profile.jobs')
                    </div>
                    <div class="tab-pane fade" id="service" role="tabpanel">
                        @include('profile.services')
                    </div>
                    <div class="tab-pane fade" id="leads" role="tabpanel">
                        @include('profile.leads')
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('popups.tabs')

    @include('popups.popup')

    @include('profile.readytowork')

    <script>
        function validateVideo(input) {
            if (input.files.length === 0) return true;
            const file = input.files[0];
            const allowedExtensions = ['mp4', 'mkv', 'mov', 'webm', 'avi'];
            const ext = file.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(ext)) {
                showToast("Only MP4, MOV, MKV, AVI or WEBM files with 30 MB only allowed.");
                input.value = "";
                return false;
            }
            if (file.size > 30 * 1024 * 1024) {
                showToast("Video size must be less than 30 MB.");
                input.value = "";
                return false;
            }
            return true;
        }
    </script>

    <!-- Image Preview -->
    <script>
        function previewImage(input, previewId) {
            const file = input.files[0];
            const preview = document.getElementById(previewId);

            if (!file) {
                preview.src = "";
                preview.style.display = "none";
                return;
            }

            const maxSize = 15 * 1024 * 1024;
            if (file.size > maxSize) {
                showToast("Each image must be less than 15 MB.");
                input.value = ""; // Clear the invalid file
                preview.src = "";
                preview.style.display = "none";
                return;
            }

            // Check file type
            if (!file.type.startsWith("image/")) {
                showToast("Please upload a valid image file.");
                input.value = "";
                preview.src = "";
                preview.style.display = "none";
                return;
            }

            // Preview the image
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = "block";
                preview.style.height = "75px";
                preview.style.objectFit = "cover";
            };
            reader.readAsDataURL(file);
        }
    </script>

@endsection