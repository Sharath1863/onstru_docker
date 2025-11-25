@extends('layouts.app')

@section('title', 'Onstru | Settings')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">
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
        .flex-sidebar {
            display: block !important;
        }

        @media screen and (max-width: 767px) {
            .profile-head {
                display: grid !important;
                grid-template-columns: 30% 69%;
                align-items: center;
                justify-content: space-between;
            }

            .side-cards .cards-content h5.label {
                width: 100%;
            }

            .profile-content h6 {
                font-size: 14px !important;
            }

            .user-img {
                height: 75px !important;
            }
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

                            <hr class="w-75 mx-auto my-3">

                            <div class="profile-tabs">
                                <ul class="nav nav-tabs d-flex justify-content-sm-between justify-content-md-center align-items-start flex-sm-row flex-md-column border-0"
                                    id="myTab" role="tablist">
                                    @include('settings.tabs')
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flex Right -->
            <div class="flex-cards pt-2">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="password" role="tabpanel">
                        @include('settings.password')
                    </div>
                    <div class="tab-pane fade" id="saved" role="tabpanel">
                        @include('settings.saved', ['posts' => $post])
                    </div>
                    <div class="tab-pane fade" id="liked" role="tabpanel">
                        @include('settings.liked')
                    </div>
                    <div class="tab-pane fade" id="delete" role="tabpanel">
                        @include('settings.delete_account')
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('popups.follow')

    @include('profile.post-popup')

    @include('popups.popup')

    @include('profile.post-script')

    @include('popups.tabs')

@endsection
