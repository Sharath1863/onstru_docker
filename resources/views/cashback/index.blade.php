@extends('layouts.app')

@section('title', 'Onstru | Cashback')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">

    <div class="container-xl main-div">
        <div class="body-head align-items-start mb-3">
            <div class="mb-2">
                <h5 class="mb-2">My Cashback</h5>
                <h6>Track all cashback rewards from different vendors</h6>
            </div>

            <div class="side-cards mb-2">
                <div class="d-flex justify-content-start align-items-center flex-wrap column-gap-3">
                    <img src="{{ asset('assets/images/img_cashback.png') }}" height="40px" alt="">
                    <div class="cards-content">
                        <h5 class="text-decoration-none mb-2">Total Cashback Earned</h5>
                        <h6 class="text-muted">₹ {{ $cashbacks->sum('avail_cb') }}</h6>
                    </div>
                </div>
            </div>

            <div class="side-cards mb-2">
                <div class="d-flex justify-content-start align-items-center flex-wrap column-gap-3">
                    <img src="{{ asset('assets/images/img_vendors.png') }}" height="40px" alt="">
                    <div class="cards-content">
                        <h5 class="text-decoration-none mb-2">Active Vendors</h5>
                        <h6 class="text-muted">{{ count($cashbacks) }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="body-head mb-3">
            <h5>All Cashbacks ({{ count($cashbacks) }})</h5>
            <button type="button" id="sort-heading" class="followingbtn">
                Sort Cashback
            </button>
        </div>
        {{-- @dd($cashback) --}}
        <div class="product-cards" id="cashback-list">
            @foreach ($cashbacks as $cashback)
                <div class="side-cards cashback-card" data-amount="{{ $cashback['amount'] }}">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="d-flex justify-content-start align-items-center column-gap-3 flex-wrap mb-3">
                            <div class="avatar-div-60 position-relative">
                                <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $cashback->vendor->profile_img ?? 'assets/images/NoImage.png') }}" class="avatar-60" alt="">
                                <img src="{{ asset('assets/images/Badge_' . $cashback->vendor->badge . '.png') }}" class="badge-60" alt="">
                            </div>
                            <div class="cards-head">
                                <div>
                                    <h4 class="text-decoration-none mb-1">{{ $cashback->vendor->name ?? '-' }}</h4>
                                    <h6 class="mb-0">{{ $cashback->vendor->as_a ?? 'Consumer' }}</h6>
                                </div>
                            </div>
                        </div>
                        <a href="{{ url('user-profile/' . $cashback->vendor->id) }}">
                            <button type="button" class="formbtn">View Profile</button>
                        </a>
                    </div>

                    <div class="cards-content">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h6 class="text-muted mb-0">Cashback Amount</h6>
                            <!-- <h6 class="green-label mb-0">
                                <span><i class="fas fa-circle-check"></i> Credited</span>
                            </h6> -->
                            {{-- @if ($cashback['status'] == 'Credited')
                            @else
                                <h6 class="red-label">
                                    <span><i class="fas fa-circle-xmark"></i> Not Credited</span>
                                </h6>
                            @endif --}}
                        </div>
                        <h4 class="mt-1 mb-0">₹ {{ $cashback['avail_cb'] }}</h4>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const heading = document.getElementById("sort-heading");
            const list = document.getElementById("cashback-list");

            let sortOrder = "desc";

            heading.addEventListener("click", () => {
                const cards = Array.from(list.querySelectorAll(".cashback-card"));

                cards.sort((a, b) => {
                    const amountA = parseInt(a.dataset.amount);
                    const amountB = parseInt(b.dataset.amount);

                    return sortOrder === "desc"
                        ? amountB - amountA
                        : amountA - amountB;
                });
                cards.forEach(card => list.appendChild(card));
                if (sortOrder === "desc") {
                    sortOrder = "asc";
                    heading.innerText = "Highest Cashback";
                } else {
                    sortOrder = "desc";
                    heading.innerText = "Lowest Cashback";
                }
            });
        });
    </script>

@endsection