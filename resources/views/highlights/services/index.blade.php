@extends('layouts.app')

@section('title', 'Onstru | Highlighted Services')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">

    <style>
        @media screen and (min-width: 767px) {
            .leftflex {
                width: 40% !important;
            }

            .service-cards {
                grid-template-columns: repeat(2, 48%);
            }
        }
    </style>

    <div class="container-xl main-div">
        <div class="body-head mb-3">
            <a href="javascript:history.back()">
                <h5><i class="fas fa-angle-left pe-1"></i> Highlighted Services</h5>
            </a>
        </div>

        <!-- Search -->
        <div class="form-div mt-4 mb-3">
            <div class="inpleftflex leftflex">
                <i class="fas fa-search"></i>
                <input type="text" name="keyword" id="serviceSearch" class="form-control border-0" placeholder="Search"
                    value="{{ request('servicesKeyword') }}">
            </div>
        </div>

        <!-- Header Cards -->
        <div class="post-cards">
            <div class="side-cards shadow-none mb-3">
                <div class="cards-head">
                    <h5 class="mb-2">Available Amount</h5>
                    <div class="d-flex align-items-center column-gap-2">
                        <img src="{{ asset('assets/images/img_availamt.png') }}" height="25px" alt="">
                        <h6 class="mb-0">₹ {{ auth()->user()->balance }}</h6>
                    </div>
                </div>
            </div>

            <div class="side-cards shadow-none mb-3">
                <div class="cards-head">
                    <h5 class="mb-2">Spend Amount</h5>
                    <div class="d-flex align-items-center column-gap-2">
                        <img src="{{ asset('assets/images/img_spend.png') }}" height="25px" alt="">
                        <h6 class="mb-0">₹ {{ $totalSpend }}</h6>
                    </div>
                </div>
            </div>

            <div class="side-cards shadow-none mb-3">
                <div class="cards-head">
                    <h5 class="mb-2">No. Of Clicks</h5>
                    <div class="d-flex align-items-center column-gap-2">
                        <img src="{{ asset('assets/images/img_click.png') }}" height="25px" alt="">
                        <h6 class="mb-0">{{ $timesClicked }}</h6>
                    </div>
                </div>
            </div>

            <div class="side-cards shadow-none mb-3">
                <div class="cards-head">
                    <h5 class="mb-2">No. Of Purchase</h5>
                    <div class="d-flex align-items-center column-gap-2">
                        <img src="{{ asset('assets/images/img_purchase.png') }}" height="25px" alt="">
                        <h6 class="mb-0">{{ $totalHighlights }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="body-head my-3">
            <h5>Highlighted Services List</h5>
            <a href="">
                <button class="removebtn">Recharge Now</button>
            </a>
        </div>

        <!-- Empty State -->
        <div class="side-cards shadow-none border-0" id="noServices"
            style="{{ count($boostedService) > 0 ? 'display: none;' : 'display: block;' }}">
            <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                <img src="{{ asset('assets/images/Empty/NoServices.png') }}" height="200px" class="d-flex mx-auto mb-2"
                    alt="">
                <h5 class="text-center mb-0">No Highlighted Services Found</h5>
                <h6 class="text-center bio">No highlighted services are available at the moment - try a different search or
                    explore other categories.</h6>
            </div>
        </div>

        <div class="post-cards">
            @foreach ($boostedService as $service)
                <div class="side-cards filter-services shadow-none mx-0 mb-2">
                    <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $service->image ?? 'assets/images/NoImage.png') }}"
                        class="object-fit-cover w-100 rounded-3" height="150px" alt="">
                    <div class="cards-head mt-3">
                        <h5 class="mb-2 long-text">{{ $service['title'] }}</h5>
                        <h6 class="mb-3 long-text">{{ $service['description'] }}</h6>
                    </div>
                    <div class="cards-content d-flex align-items-start justify-content-between flex-wrap">
                        <div>
                            <h6 class="mb-2">
                                <i class="fas fa-location-dot pe-1"></i>
                                <span class="text-muted">{{ $service->locationRelation->value }}</span>
                            </h6>
                            @if ($service['highlighted'] == 1)
                                <button class="green-label border-0">
                                    Highlighted
                                </button>
                            @elseif ($service['highlighted'] == 0)
                                <button class="grey-label border-0">
                                    Not-Highlighted
                                </button>
                            @endif
                        </div>
                        <div>
                            <h6 class="mb-2">
                                <i class="fas fa-maximize pe-1"></i>
                                <span class="text-muted">{{ $service['price_per_sq_ft'] }} per sq.ft</span>
                            </h6>
                            <a href="{{ url('view-service-highlight', $service->id) }}">
                                <button class="followersbtn">View More</button>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Highlight Modal -->
        <div class="modal fade" id="highlight" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-4">
                        <img src="{{ asset('assets/images/img_highlight.png') }}" height="50px" class="d-flex mx-auto mb-3"
                            alt="">
                        <h5 class="text-center mb-2">Are you sure you want to highlight this service?</h5>
                        <h6 class="text-center mb-2">
                            Highlighted Services will be shown with priority on the listing page.
                        </h6>
                        <div class="col-sm-12 d-flex align-items-center justify-content-between gap-2 mt-4">
                            <a class="w-50">
                                <button type="button" class="w-100 listbtn">Confirm Highlight</button>
                            </a>
                            <a class="w-50" data-bs-dismiss="modal">
                                <button type="submit" class="w-100 removebtn">Cancel</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Not Highlight Modal -->
        <div class="modal fade" id="nothighlight" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-4">
                        <img src="{{ asset('assets/images/img_nothighlight.png') }}" height="50px"
                            class="d-flex mx-auto mb-3" alt="">
                        <h5 class="text-center mb-2">Are you sure you want to put this service on Not Highlight?</h5>
                        <h6 class="text-center mb-2">
                            This Service will not be highlighted anymore and will move to hold status.
                        </h6>
                        <div class="col-sm-12 d-flex align-items-center justify-content-between gap-2 mt-4">
                            <a class="w-50">
                                <button type="button" class="w-100 listbtn">Confirm Not Highlight</button>
                            </a>
                            <a class="w-50" data-bs-dismiss="modal">
                                <button type="submit" class="w-100 removebtn">Cancel</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Search Filter -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const serviceSearch = document.getElementById('serviceSearch');
            const noServices = document.getElementById('noServices');
            const serviceCards = document.querySelectorAll('.filter-services');
            serviceSearch.addEventListener('input', function () {
                let serviceMatch = false;
                const servicesKeyword = this.value.toLowerCase();
                serviceCards.forEach(card => {
                    const cardText = card.textContent.toLowerCase();
                    if (cardText.includes(servicesKeyword)) {
                        card.style.display = 'block';
                        serviceMatch = true;
                    } else {
                        card.style.display = 'none';
                    }
                });
                if (serviceMatch) {
                    noServices.style.display = 'none';
                } else {
                    noServices.style.display = 'block';
                }
            });
        });
    </script>

@endsection