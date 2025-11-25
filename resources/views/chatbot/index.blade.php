@extends('layouts.app')

@section('title', 'Onstru | Chatbot')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list.css') }}">

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
        }
    </style>

    <div class="container-xl main-div">
        <div class="flex-side">
            <!-- Flex Left -->
            <div class="flex-sidebar border-0">
                <div class="flex-cards">

                    <div class="side-cards mb-3">
                        <div class="cards-content">
                            <!-- Profile Card -->
                            @include('flexleft.profile-card')
                            <hr class="w-75 mx-auto my-3">
                            <!-- Tabs -->
                            @include('flexleft.tabs')
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flex Right -->
            <div class="flex-cards pt-2">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="chatbot" role="tabpanel">
                        @include('chatbot.chatbot')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chatbot Plan Modal -->
    <div class="modal fade" id="chatbotModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <form action="{{ route('chatbot.subscribe') }}" method="POST" class="w-100">
                        @csrf
                        <img src="{{ asset('assets/images/img_bot.png') }}" height="75px" class="d-flex mx-auto mb-3"
                            alt="">
                        <h5 class="text-center mb-2">Onstru Chatbot</h5>
                        <h6 class="text-center mb-2">
                            Subscribe to our chatbot for exclusive, expert insights on construction trends, best practices,
                            innovations, and industry updates. Stay informed and ahead in the world of construction and
                            Onstru solutions.
                        </h6>
                        <hr>
                        <h6 class="text-center mb-2">1-Month Chatbot Plan</h6>
                        <h5 class="text-center fw-bold mb-1">₹ {{ $charge }}</h5>
                        <h6 class="text-center mb-2" style="font-size: 10px">Wallet : ₹ {{ auth()->user()->balance }}</h6>
                        <div class="d-flex align-items-center justify-content-center gap-1 mb-3">
                            <input type="checkbox" name="agree" id="agree" required>
                            <label class="mb-0" for="agree" style="font-size: 10px;">Agree To Pay</label>
                        </div>
                        <div class="col-sm-12 d-flex align-items-center justify-content-center gap-2 mt-2">
                            @if (auth()->user()->balance > $charge)
                                <button type="submit" class="w-100 listbtn">Subscribe Now</button>
                            @else
                                <a href="{{ route('wallet') }}" target="_blank" class="w-100">
                                    <button type="button" class="removebtn w-100">Recharge Now</button>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (!$hasSubscription)
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var chatbotModal = new bootstrap.Modal(document.getElementById('chatbotModal'));
                var modalElement = document.getElementById('chatbotModal');

                // Show modal on load
                chatbotModal.show();

                // Remove blur class when modal is closed
                modalElement.addEventListener('hidden.bs.modal', function () {
                    document.querySelectorAll('.blurred').forEach(el => {
                        el.classList.remove('blurred');
                    });
                });
            });
        </script>
    @endif


@endsection