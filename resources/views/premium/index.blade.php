@extends('layouts.app')

@section('title', 'Onstru | Premium')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/premium.css') }}">

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
                    <div class="tab-pane fade show active" id="premium" role="tabpanel">
                        @include('premium.premium')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Plan Modal -->
    <div class="modal fade" id="premiumModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <form action="{{ route('premium.subscribe') }}" method="POST" class="w-100">
                        @csrf
                        <img src="{{ asset('assets/images/img_premium.png') }}" height="75px" class="d-flex mx-auto mb-3"
                            alt="">
                        <h5 class="text-center mb-2">This is Premium Content</h5>
                        <h6 class="text-center mb-2">
                            Subscribe now to unlock exclusive posts, reels, and blogs for Consumers, Professionals, Vendors
                            and
                            Contractors.
                        </h6>
                        <hr>
                        <h6 class="text-center mb-2">1-Month Premium Plan</h6>
                        <h5 class="d-flex align-items-center justify-content-center gap-2 fw-bold mb-1">
                            ₹ {{ $premium_charge }} 
                            <span class="text-muted fw-medium" style="font-size: 10px"> (Tax Included)</span>
                        </h5>
                        <h6 class="text-center mb-2" style="font-size: 10px">Wallet : ₹ {{ auth()->user()->balance }}</h6>
                        <div class="d-flex align-items-center justify-content-center gap-1 mb-3">
                            <input type="checkbox" name="agree" id="agree" required>
                            <label class="mb-0" for="agree" style="font-size: 10px;">Agree To Pay</label>
                        </div>
                        <div class="d-flex align-items-center justify-content-center">
                            <ul class="list-unstyled">
                                <li>
                                    <h6>
                                        <i class="fas fa-circle-check text-success pe-1"></i> Unlimited Premium Posts, Reels
                                        &
                                        Blogs
                                    </h6>
                                </li>
                                <li>
                                    <h6>
                                        <i class="fas fa-circle-check text-success pe-1"></i> Priority Industry Updates
                                    </h6>
                                </li>
                            </ul>
                        </div>
                        <div class="col-sm-12 d-flex align-items-center justify-content-center gap-2 mt-2">
                            @if (auth()->user()->balance >= $premium_charge)
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

    <!-- Premium Invoice Modal -->
    <div class="modal fade" id="premiumInv" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0">Premium Invoices</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Month</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($buy_list as $bl)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $bl->created_at->format('F') }}</td>
                                        <td>₹ {{ $bl->price }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="{{ url('premium-bill/' . $bl->id) }}" target="_blank"
                                                    data-bs-toggle="tooltip" data-bs-title="Print Invoice">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (!$hasSubscription)
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var premiumModal = new bootstrap.Modal(document.getElementById('premiumModal'));
                var modalElement = document.getElementById('premiumModal');

                // Show modal on load
                premiumModal.show();

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