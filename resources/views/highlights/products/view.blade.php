@extends('layouts.app')

@section('title', 'Onstru | Highlighted Products')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list.css') }}">

    <div class="container-xl main-div">
        <div class="body-head mb-3">
            <a href="javascript:history.back()">
                <h5><i class="fas fa-angle-left pe-1"></i> Highlighted Product</h5>
            </a>
        </div>

        <!-- Header Cards -->
        <div class="service-cards">
            <div class="side-cards filter-products shadow-none row mx-0 mb-2">
                <div class="col-sm-12 col-md-4 m-auto">
                    <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $product->cover_img ?? 'assets/images/NoImage.png') }}"
                        class="object-fit-cover object-center w-100 rounded-3" height="125px" alt="">
                </div>
                <div class="col-sm-12 col-md-8 mx-auto mt-2">
                    <div class="cards-head">
                        <h5 class="mb-2 long-text">{{ $product['name'] ?? '-' }}</h5>
                        <h6 class="mb-3 long-text">{{ $product->categoryRelation->value ?? '-' }}</h6>
                    </div>
                    <div
                        class="cards-content d-flex align-items-start justify-content-start column-gap-md-5 column-gap-sm-3 flex-wrap">
                        <div>
                            <h6 class="mb-2">Price : <span class="text-muted">₹ {{ $product['sp'] ?? '-' }}</span>
                            </h6>
                            @if ($product['highlighted'] == 1)
                                <button class="green-label border-0">
                                    Highlighted
                                </button>
                            @elseif ($product['highlighted'] == 0)
                                <button class="grey-label border-0">
                                    Not-Highlighted
                                </button>
                            @endif
                        </div>
                        <div>
                            @if ($product['availability'] == 'In Stock')
                                <h6 class="mb-2">Availability : <span
                                        class="text-success">{{ $product['availability'] ?? '-' }}</span></h6>
                            @else
                                <h6 class="mb-2">Availability : <span class="text-danger">{{ $product['stock'] ?? '-' }}</span>
                                </h6>
                            @endif
                            <a href="{{ url('individual-product/' . $product->id) }}">
                                <button class="followersbtn">View Product</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="body-head my-3 heading-hide">
            <h5>Highlight Details</h5>
            <div class="d-flex align-items-center column-gap-2">
                @if ($product['highlighted'] == 1)
                    <button class="followingbtn">Total Clicks <span
                            class="count-sm ms-1">{{ $product->boosts->sum('click') - $product->click }}</span></button>
                    <button class="followingbtn">Available <span class="count-sm ms-1">{{ $product->click }}</span></button>
                @endif
            </div>
        </div>

        <div class="listtable">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Clicks Bought</th>
                            <!-- <th>Available Click</th> -->
                            <th>Highlight Cost</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product->boosts as $boost)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $boost->click }}</td>
                                <!-- <td>{{ $product->click }}</td> -->
                                <td>₹ {{ $boost->amount }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a class="view-users" data-boost-id="{{ $boost->id }}" data-bs-toggle="modal"
                                            data-bs-target="#viewUsers">
                                            <i class="fas fa-external-link"></i>
                                        </a>
                                        <a href="{{ url('product-click-bill', ['id' => $boost->id]) }}" target="_blank">
                                            <i class="fas fa-print" data-bs-toggle="tooltip" data-bs-title="Print Invoice"></i>
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

    <!-- User Modal -->
    <div class="modal fade" id="viewUsers" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="viewUserLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0">View User</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="position-sticky sticky-top bg-white py-3">
                        <div class="inpleftflex">
                            <i class="fas fa-search text-muted text-center"></i>
                            <input type="text" name="search" id="userSearch" class="form-control border-0"
                                placeholder="Search">
                        </div>
                    </div>
                    <div id="userListContainer"></div>
                    {{-- @foreach ($product->clicks as $click)
                    <div class="modal-user user-modal mb-2">
                        <a href="{{ url('user-profile') }}">
                            <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                                <div class="d-flex align-items-center justify-content-start gap-2">
                                    <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $click->user->profile_img ?? 'assets/images/Avatar.png') }}"
                                        height="30px" class="avatar" alt="">
                                    <div class="user-content">
                                        <h5 class="mb-1">{{ $click->user['name'] }}</h5>
                                        <h6>{{ $click->user['user_name'] }}</h6>
                                    </div>
                                </div>
                                <button type="button" class="yellow-label border-0">{{ $click->user['as_a'] }}</button>
                            </div>
                        </a>
                    </div>
                    @endforeach --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Popup Search -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function setupSearch(inputId, cardClass) {
                const searchInput = document.getElementById(inputId);
                if (!searchInput) return;

                searchInput.addEventListener('input', function () {
                    const keyword = this.value.toLowerCase();
                    document.querySelectorAll(cardClass).forEach(card => {
                        card.style.display = card.textContent.toLowerCase().includes(keyword) ?
                            'block' : 'none';
                    });
                });
            }
            setupSearch('userSearch', '.user-modal');
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productClicks = @json($product->clicks); // Can be passed similarly for service/job

            // Attach event listeners to the view buttons
            document.querySelectorAll('.view-users').forEach(link => {
                link.addEventListener('click', function () {
                    const boostId = this.getAttribute('data-boost-id');
                    const userListContainer = document.getElementById('userListContainer');
                    userListContainer.innerHTML = ''; // Clear existing content

                    // Filter the clicks for this boost
                    const filteredClicks = productClicks.filter(click => click.boost_id == boostId);

                    if (filteredClicks.length === 0) {
                        userListContainer.innerHTML =
                            '<h6 class="text-muted text-center mt-3">No Users Found.</h6>';
                        return;
                    }

                    // Generate HTML for each user
                    filteredClicks.forEach(click => {
                        const user = click.user;
                        const profileImg = user?.profile_img ?
                            `https://onstru-social.s3.ap-south-1.amazonaws.com/${user.profile_img}` :
                            "{{ asset('assets/images/Avatar.png') }}";

                        const userHtml = `
                            <div class="modal-user user-modal mb-2">
                                <a href="{{ url('user-profile/${user.id}') }}">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                                        <div class="d-flex align-items-center justify-content-start gap-2">
                                            <img src="${profileImg}" class="avatar-30" alt="">
                                            <div class="user-content">
                                                <h5 class="mb-1">${user.name}</h5>
                                                <h6>${user.user_name}</h6>
                                            </div>
                                        </div>
                                        <button type="button" class="yellow-label border-0">${user.as_a ?? 'Consumer'}</button>
                                    </div>
                                </a>
                            </div>
                        `;
                        userListContainer.insertAdjacentHTML('beforeend', userHtml);
                    });
                });
            });
        });
    </script>

@endsection