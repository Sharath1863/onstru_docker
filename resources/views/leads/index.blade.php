@extends('layouts.app')

@section('title', 'Onstru | Leads')

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
            @include('leads.aside')
            <!-- Cards -->
            <div class="flex-cards pt-2">
                <div class="body-head mb-3">
                    <h5>Leads</h5>
                    <button class="border-0 m-0 p-0 filter-responsive" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvas-filter" aria-controls="offcanvas-filter">
                        <i class="fas fa-filter fs-4"></i>
                    </button>
                </div>

                <!-- Empty State -->
                <div class="side-cards shadow-none border-0" id="noCard"
                    style="{{ count($leads) > 0 ? 'display: none;' : 'display: block;' }}">
                    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                        <img src="{{ asset('assets/images/Empty/NoLeads.png') }}" height="200px" class="d-flex mx-auto mb-2"
                            alt="">
                        <h5 class="text-center mb-0">No Leads Found</h5>
                        <h6 class="text-center bio">Currently, no leads are available - try adjusting your filters or check
                            back later for new opportunities.</h6>
                    </div>
                </div>

                <!-- Lead Cards -->
                @foreach ($leads as $lead)
                    <div class="side-cards service-card mb-2" data-type="{{ $lead->serviceRelation->value }}"
                        data-price="{{ $lead->budget }}" data-buildup="{{ $lead->buildup_area }}"
                        data-location="{{ $lead->locationRelation->value }}">
                        <div class="cards-content">
                            <div class="row">
                                <div
                                    class="col-sm-12 col-md-4 d-flex align-items-start justify-content-start column-gap-3 mb-2">
                                    <img src="{{ asset($lead->image ?? 'assets/images/Favicon.png') }}" height="40px" alt="">
                                    <div>
                                        <h5 class="mb-1">{{ $lead->title ?? '-' }}</h5>
                                        <label class="m-0">{{ Str::limit($lead->description, 100) ?? '-' }}</label>
                                    </div>
                                </div>
                                <div
                                    class="col-sm-12 col-md-8 d-flex align-items-start justify-content-between flex-wrap column-gap-3">
                                    <div class="mb-2">
                                        <label class="mb-2">
                                            <i class="fas fa-cogs pe-2"></i> Service Type
                                        </label>
                                        <h6 class="mb-0">{{ $lead->serviceRelation->value ?? '-' }}</h6>
                                    </div>
                                    <div class="mb-2">
                                        <label class="mb-2">
                                            <i class="fas fa-street-view pe-2"></i> Build Up Area
                                        </label>
                                        <h6 class="mb-0">{{ $lead->buildup_area ?? '-' }} sqft</h6>
                                    </div>
                                    <div class="mb-2">
                                        <label class="mb-2">
                                            <i class="fas fa-chart-bar pe-2"></i>Budget
                                        </label>
                                        <h6 class="mb-0">₹ {{ number_format($lead->budget) ?? '-' }}
                                        </h6>
                                    </div>
                                    <div class="mb-2">
                                        <label class="mb-2">
                                            <i class="fas fa-location-dot pe-2"></i> Location
                                        </label>
                                        <h6 class="mb-0">{{ $lead->locationRelation->value ?? '-' }}</h6>
                                    </div>
                                    <div class="mb-2">
                                        <label class="mb-2">
                                            <i class="fas fa-chart-column pe-2"></i>Lead price
                                        </label>
                                        <h6 class="mb-0">₹ {{ number_format($lead->admin_charge * 1.18 ?? '-') }} (With Tax)
                                        </h6>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4 mb-2">
                                    <label>
                                        <i class="far fa-clock pe-1"></i> Posted
                                        {{ $lead->created_at->diffForHumans() ?? '-' }}
                                    </label>
                                </div>
                                <div
                                    class="col-sm-12 col-md-8 d-flex align-items-start justify-content-between flex-wrap column-gap-3">
                                    <div class="mb-2">
                                        <label class="mb-2">
                                            <i class="far fa-calendar pe-2"></i>Expected Start Date
                                        </label>
                                        <h6 class="mb-0">
                                            {{ \Carbon\Carbon::parse($lead->start_date)->format('d-m-Y') ?? '-' }}
                                        </h6>
                                    </div>
                                    <div class="d-block ms-auto my-auto mb-2">
                                        <button class="followersbtn" data-bs-toggle="modal"
                                            data-bs-target="#buyLeads{{ $lead->id }}">
                                            Pay ₹ {{ $lead->admin_charge * 1.18 ?? '-' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lead Owned Modal -->
                    <div class="modal fade" id="buyLeads{{ $lead->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="buyLeadsLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="m-0">Buy Leads</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('buy.lead', $lead->id) }}" method="POST">
                                        @csrf
                                        <div class="row mt-2">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <label class="my-2">Wallet : <span class="text-muted">₹
                                                        {{ auth()->user()->balance }}</span></label>
                                            </div>
                                            <div class="col-sm-12 col-md-6 mb-2">
                                                <label>Title</label>
                                                <h6>{{ $lead->title ?? '-' }}</h6>
                                            </div>
                                            <div class="col-sm-12 col-md-6 mb-2">
                                                <label>Service Type</label>
                                                <h6>{{ $lead->serviceRelation->value ?? '-' }}</h6>
                                            </div>
                                            <div class="col-sm-12 col-md-6 mb-2">
                                                <label>Buildup Area</label>
                                                <h6>{{ $lead->buildup_area ?? '-' }} sq.ft</h6>
                                            </div>
                                            <div class="col-sm-12 col-md-6 mb-2">
                                                <label>Budget</label>
                                                <h6>₹ {{ number_format($lead->budget) ?? '-' }}</h6>
                                            </div>
                                            <div class="col-sm-12 col-md-6 mb-2">
                                                <label>Location</label>
                                                <h6>{{ $lead->locationRelation->value ?? '-' }}</h6>
                                            </div>
                                            <div class="col-sm-12 col-md-6 mb-2">
                                                <label>Expected Start Date</label>
                                                <h6>{{ \Carbon\Carbon::parse($lead->start_date)->format('d-m-Y') ?? '-' }}
                                                </h6>
                                            </div>
                                            <div class="col-sm-12 col-md-12 mb-2">
                                                <label>Description</label>
                                                <h6>{{ $lead->description ?? '-' }}</h6>
                                            </div>
                                            <div class="col-sm-12 col-md-12 mb-2">
                                                <label>Notes <span>*</span></label>
                                                <h6>Amount will be deducted from the wallet. After successful payment,
                                                    you will get your leads in the Owned Leads Screen, which is in your
                                                    Quick Access (Home Screen).</h6>
                                            </div>
                                            <div class="col-sm-12 col-md-12 mb-2 d-flex align-items-center column-gap-2">
                                                <input type="checkbox" id="leadPay{{ $lead->id }}" name="leadPay" required>
                                                <label class="mb-0" for="leadPay{{ $lead->id }}">Agree to
                                                    Pay</label>
                                                <small class="balance-message" style="display:none;">Insufficient
                                                    Balance</small>
                                            </div>
                                        </div>
                                        @php
                                            $totalCharge = $lead->admin_charge * 1.18;
                                            $userBalance = auth()->user()->balance;
                                        @endphp

                                        <div class="d-flex align-items-center justify-content-center mt-2 column-gap-2">
                                            @if ($userBalance >= $totalCharge)
                                                <button class="formbtn w-100">Buy Now - ₹ {{ $totalCharge }}</button>
                                            @else
                                                <button class="formbtn w-100" disabled>Buy Now -
                                                    ₹ {{ $totalCharge }}</button>
                                            @endif

                                            @if ($userBalance < $totalCharge)
                                                <a href="{{ url('wallet') }}" class="w-100" target="_blank" id="recharge_button">
                                                    <button type="button" class="removebtn w-100">
                                                        Recharge
                                                    </button>
                                                </a>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const agreeCheckbox = document.getElementById('leadPay');
            const rechargeButton = document.getElementById('recharge_button');
            const insufficientMessage = document.querySelector('.balance-message');
            const buyButton = document.querySelector('.formbtn');

            // Replace these with actual values from Blade
            const walletBalance = parseFloat(`{{ auth()->user()->balance }}`);
            const adminCharge = parseFloat(`{{ $lead->admin_charge ?? 0 }}`);

            agreeCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    if (walletBalance < adminCharge) {
                        // Insufficient balance
                        rechargeButton.style.display = 'inline-block';
                        insufficientMessage.style.display = 'inline';
                        buyButton.disabled = true;
                        buyButton.classList.add('disabled');
                    } else {
                        // Sufficient balance
                        rechargeButton.style.display = 'none';
                        insufficientMessage.style.display = 'none';
                        buyButton.disabled = false;
                        buyButton.classList.remove('disabled');
                    }
                } else {
                    // If checkbox is unchecked, reset everything
                    rechargeButton.style.display = 'none';
                    insufficientMessage.style.display = 'none';
                    buyButton.disabled = true;
                    buyButton.classList.add('disabled');
                }
            });

            // Optional: disable buy button on page load
            buyButton.disabled = true;
            buyButton.classList.add('disabled');
        });
    </script>

@endsection