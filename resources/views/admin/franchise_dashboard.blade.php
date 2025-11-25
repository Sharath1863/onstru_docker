<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onstru | Franchise Dashboard</title>

    <!-- Stylesheets CDN -->
    @include('admin.cdn_style')

    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/css/admin/dashboard_main.css') }}">

</head>

<body>

    <div class="main">

        <!-- aside -->
        @include('admin.aside')

        <div class="body-main">

            <!-- Navbar -->
            @include('admin.navbar')

            <div class="main-div px-4 py-1">
                <div class="body-head">
                    <h4 class="m-0">Franchise Dashboard</h4>
                </div>

                <div class="container-fluid dashboard-cards w-100 mx-0 px-0 mt-3">
                    <div class="row">

                        <!-- Vendor Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <div class="cardsdiv">
                                <div class="cardshead">
                                    <h6>Vendor</h6>
                                </div>
                                <div class="cardsct">
                                    <div class="cards_01 d-block me-auto">
                                        <h6>Total</h6>
                                        <h5>{{ $vendor_total }}</h5>
                                    </div>
                                    <div class="brdr"></div>
                                    <div class="cards_01 d-block mx-auto">
                                        <h6>New</h6>
                                        <h5>{{ $today_vendor_count }}</h5>
                                    </div>
                                    <div class="brdr"></div>
                                    <div class="cards_01"></div>
                                    <div class="cards_01 img-div">
                                        <img src="{{ asset('assets/images/admin/icon_vendor.png') }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contractor Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <div class="cardsdiv">
                                <div class="cardshead">
                                    <h6>Contractor</h6>
                                </div>
                                <div class="cardsct">
                                    <div class="cards_01 d-block me-auto">
                                        <h6>Total</h6>
                                        <h5>{{ $contractor_total }}</h5>
                                    </div>
                                    <div class="brdr"></div>
                                    <div class="cards_01 d-block mx-auto">
                                        <h6>New</h6>
                                        <h5>{{ $today_contractor_count }}</h5>
                                    </div>
                                    <div class="brdr"></div>
                                    <div class="cards_01"></div>
                                    <div class="cards_01 img-div">
                                        <img src="{{ asset('assets/images/admin/icon_contractor.png') }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Consultant Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <div class="cardsdiv">
                                <div class="cardshead">
                                    <h6>Consultant</h6>
                                </div>
                                <div class="cardsct">
                                    <div class="cards_01 d-block me-auto">
                                        <h6>Total</h6>
                                        <h5>{{ $cosultant_total }}</h5>
                                    </div>
                                    <div class="brdr"></div>
                                    <div class="cards_01 d-block mx-auto">
                                        <h6>New</h6>
                                        <h5>{{ $today_consultant_count }}</h5>
                                    </div>
                                    <div class="brdr"></div>
                                    <div class="cards_01"></div>
                                    <div class="cards_01 img-div">
                                        <img src="{{ asset('assets/images/admin/icon_contractor.png') }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Consumer Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <div class="cardsdiv">
                                <div class="cardshead">
                                    <h6>Consumer</h6>
                                </div>
                                <div class="cardsct">
                                    <div class="cards_01 d-block me-auto">
                                        <h6>Total</h6>
                                        <h5>{{ $consumer_total }}</h5>
                                    </div>
                                    <div class="brdr"></div>
                                    <div class="cards_01 d-block mx-auto">
                                        <h6>New</h6>
                                        <h5>{{$today_consumer_count}}</h5>
                                    </div>
                                    <div class="brdr"></div>
                                    <div class="cards_01"></div>
                                    <div class="cards_01 img-div">
                                        <img src="{{ asset('assets/images/admin/icon_consumer.png') }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Technical Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <div class="cardsdiv">
                                <div class="cardshead">
                                    <h6>Technical</h6>
                                </div>
                                <div class="cardsct">
                                    <div class="cards_01 d-block me-auto">
                                        <h6>Total</h6>
                                        <h5>{{ $technical_total }}</h5>
                                    </div>
                                    <div class="brdr"></div>
                                    <div class="cards_01 d-block mx-auto">
                                        <h6>New</h6>
                                        <h5>{{ $today_tech_count }}</h5>
                                    </div>
                                    <div class="brdr"></div>
                                    <div class="cards_01"></div>
                                    <div class="cards_01 img-div">
                                        <img src="{{ asset('assets/images/admin/icon_tech.png') }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Non-Technical Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <div class="cardsdiv">
                                <div class="cardshead">
                                    <h6>Non-Technical</h6>
                                </div>
                                <div class="cardsct">
                                    <div class="cards_01 d-block me-auto">
                                        <h6>Total</h6>
                                        <h5>{{ $nontechnical_total }}</h5>
                                    </div>
                                    <div class="brdr"></div>
                                    <div class="cards_01 d-block mx-auto">
                                        <h6>New</h6>
                                        <h5>{{ $today_nontech_count }}</h5>
                                    </div>
                                    <div class="brdr"></div>
                                    <div class="cards_01"></div>
                                    <div class="cards_01 img-div">
                                        <img src="{{ asset('assets/images/admin/icon_nontech.png') }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="body-head my-3">
                        <h4>Today Registered List</h4>
                    </div>
                    <div class="container-fluid listtable">
                        <div class="filter-container">
                            <div class="filter-container-start">
                                <select class="headerDropdown form-select filter-option">
                                    <option value="All" selected>All</option>
                                </select>
                                <input type="text" class="form-control filterInput" placeholder=" Search">
                            </div>
                        </div>

                        <div class="table-wrapper">
                            <table class="example table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Gender</th>
                                        <th>Contact Number</th>
                                        <th>Email ID</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                     @foreach($today_list as $user)
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->as_a ?? 'Consumer' }}</td>
                                        <td>{{ $user->gender??'nil' }}</td>
                                        <td>{{ $user->number }}</td>
                                        <td>{{ $user->email??'nil' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center column-gap-2">
                                                <a href="{{ route('franchise_detail', ['id' => $user->franchise->id])  }}" data-bs-toggle="tooltip"
                                                    data-bs-title="View User">
                                                    <i class="fas fa-external-link"></i>
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
    </div>

    <!-- script -->
    @include('admin.cdn_script')
</body>

<script>
    // DataTables List
    $(document).ready(function () {
        var table = $('.example').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "bDestroy": true,
            "info": false,
            "responsive": true,
            "pageLength": 10,
            "dom": '<"top"f>rt<"bottom"lp><"clear">',
        });

    });

    // List Filter
    $(document).ready(function () {
        var table = $('.example').DataTable();
        $('.example thead th').each(function (index) {
            var headerText = $(this).text();
            if (headerText != "" && headerText.toLowerCase() != "action" && headerText.toLowerCase() !=
                "progress") {
                $('.headerDropdown').append('<option value="' + index + '">' + headerText +
                    '</option>');
            }
        });
        $('.filterInput').on('keyup', function () {
            var selectedColumn = $('.headerDropdown').val();
            if (selectedColumn !== 'All') {
                table.column(selectedColumn).search($(this).val()).draw();
            } else {
                table.search($(this).val()).draw();
            }
        });
        $('.headerDropdown').on('change', function () {
            $('.filterInput').val('');
            table.search('').columns().search('').draw();
        });
    });
</script>

</html>