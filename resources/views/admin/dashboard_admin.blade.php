<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onstru | Admin Dashboard</title>

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
                    <h4 class="m-0">Dashboard</h4>
                </div>

                <div class="container-fluid dashboard-cards w-100 mx-0 px-0 mt-3">
                    <div class="row">

                        <!-- Products Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <a href="{{ url('product_list') }}">
                                <div class="cardsdiv">
                                    <div class="cardshead">
                                        <h6>Products</h6>
                                    </div>
                                    <div class="cardsct mb-3">
                                        <div class="cards_01 d-block me-auto">
                                            <h6>Approved</h6>
                                            <h5>{{ $productActive }}</h5>
                                        </div>
                                        <div class="brdr"></div>
                                        <div class="cards_01 d-block mx-auto">
                                            <h6>Pending</h6>
                                            <h5>{{ $productPending }}</h5>
                                        </div>
                                        <div class="brdr"></div>
                                        <div class="cards_01 d-block mx-auto">
                                            <h6>Rejected</h6>
                                            <h5>{{ $productRejected }}</h5>
                                        </div>
                                        <div class="cards_01 img-div">
                                            <img src="{{ asset('assets/images/admin/icon_products.png') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="cardstotal">
                                        <h6>Total - {{ $productTotal }}</h6>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Jobs Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <a href="{{ url('job_list') }}">
                                <div class="cardsdiv">
                                    <div class="cardshead">
                                        <h6>Jobs</h6>
                                    </div>
                                    <div class="cardsct mb-3">
                                        <div class="cards_01 d-block me-auto">
                                            <h6>Approved</h6>
                                            <h5>{{ $jobActive }}</h5>
                                        </div>
                                        <div class="brdr"></div>
                                        <div class="cards_01 d-block mx-auto">
                                            <h6>Pending</h6>
                                            <h5>{{ $jobPending }}</h5>
                                        </div>
                                        <div class="brdr"></div>
                                        <div class="cards_01 d-block mx-auto">
                                            <h6>Rejected</h6>
                                            <h5>{{ $jobRejected }}</h5>
                                        </div>
                                        <div class="cards_01 img-div">
                                            <img src="{{ asset('assets/images/admin/icon_jobs.png') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="cardstotal">
                                        <h6>Total - {{ $jobTotal }}</h6>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Services Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <a href="{{ url('service_list') }}">
                                <div class="cardsdiv">
                                    <div class="cardshead">
                                        <h6>Services</h6>
                                    </div>
                                    <div class="cardsct mb-3">
                                        <div class="cards_01 d-block me-auto">
                                            <h6>Approved</h6>
                                            <h5>{{ $serviceActive }}</h5>
                                        </div>
                                        <div class="brdr"></div>
                                        <div class="cards_01 d-block mx-auto">
                                            <h6>Pending</h6>
                                            <h5>{{ $servicePending }}</h5>
                                        </div>
                                        <div class="brdr"></div>
                                        <div class="cards_01 d-block mx-auto">
                                            <h6>Rejected</h6>
                                            <h5>{{ $serviceRejected }}</h5>
                                        </div>
                                        <div class="cards_01 img-div">
                                            <img src="{{ asset('assets/images/admin/icon_service.png') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="cardstotal">
                                        <h6>Total - {{ $serviceTotal }}</h6>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Leads Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <a href="{{ url('leads_list') }}">
                                <div class="cardsdiv">
                                    <div class="cardshead">
                                        <h6>Leads</h6>
                                    </div>
                                    <div class="cardsct mb-3">
                                        <div class="cards_01 d-block me-auto">
                                            <h6>Approved</h6>
                                            <h5>{{ $leadsActive }}</h5>
                                        </div>
                                        <div class="brdr"></div>
                                        <div class="cards_01 d-block mx-auto">
                                            <h6>Pending</h6>
                                            <h5>{{ $leadsPending }}</h5>
                                        </div>
                                        <div class="brdr"></div>
                                        <div class="cards_01 d-block mx-auto">
                                            <h6>Rejected</h6>
                                            <h5>{{ $leadsRejected }}</h5>
                                        </div>
                                        <div class="cards_01 img-div">
                                            <img src="{{ asset('assets/images/admin/icon_leads.png') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="cardstotal">
                                        <h6>Total - {{ $leadsTotal }}</h6>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Business Providers Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <div class="cardsdiv">
                                <div class="cardshead">
                                    <h6>Business Providers</h6>
                                </div>
                                <div class="cardsct mb-3">
                                    <a href="{{ url('user_vendor') }}" class="d-block me-auto">
                                        <div class="cards_01">
                                            <h6>Vendor</h6>
                                            <h5>{{ $vendorCount }}</h5>
                                        </div>
                                    </a>
                                    <div class="brdr"></div>
                                    <a href="{{ url('user_contractor') }}" class="d-block mx-auto">
                                        <div class="cards_01">
                                            <h6>Contractor</h6>
                                            <h5>{{ $contractorCount }}</h5>
                                        </div>
                                    </a>
                                    <div class="brdr"></div>
                                    <a href="{{ url('user_consultant') }}" class="d-block mx-auto">
                                        <div class="cards_01">
                                            <h6>Consultant</h6>
                                            <h5>{{ $consultantCount }}</h5>
                                        </div>
                                    </a>
                                    <div class="cards_01 img-div">
                                        <img src="{{ asset('assets/images/admin/icon_business.png') }}" alt="">
                                    </div>
                                </div>
                                <div class="cardstotal">
                                    <h6>Total - {{ $business_total }}</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Users Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <div class="cardsdiv">
                                <div class="cardshead">
                                    <h6>Other Users</h6>
                                </div>
                                <div class="cardsct mb-3">
                                    <a href="{{ url('user_professional') }}" class="d-block me-auto">
                                        <div class="cards_01">
                                            <h6>Technical</h6>
                                            <h5>{{ $technicalCount }}</h5>
                                        </div>
                                    </a>
                                    <div class="brdr"></div>
                                    <a href="{{ url('user_professional') }}" class="d-block mx-auto">
                                        <div class="cards_01">
                                            <h6>Non-Tech</h6>
                                            <h5>{{ $nonTechnicalCount }}</h5>
                                        </div>
                                    </a>
                                    <div class="brdr"></div>
                                    <a href="{{ url('user_consumer') }}" class="d-block mx-auto">
                                        <div class="cards_01">
                                            <h6>Consumer</h6>
                                            <h5>{{ $consumerCount }}</h5>
                                        </div>
                                    </a>
                                    <div class="cards_01 img-div">
                                        <img src="{{ asset('assets/images/admin/icon_user.png') }}" alt="">
                                    </div>
                                </div>
                                <div class="cardstotal">
                                    <h6>Total - {{ $userTotal }}</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Orders Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <a href="{{ url('orders_list') }}">
                                <div class="cardsdiv">
                                    <div class="cardshead">
                                        <h6>Today Orders</h6>
                                    </div>
                                    <div class="cardsct mb-3">
                                        <div class="cards_01 d-block me-auto">
                                            <h6>Created</h6>
                                            <h5>{{ $ordersCreated }}</h5>
                                        </div>
                                        <div class="brdr"></div>
                                        <div class="cards_01 d-block mx-auto">
                                            <h6>Delivered</h6>
                                            <h5>{{ $deliveredOrders }}</h5>
                                        </div>
                                        <div class="brdr"></div>
                                        <div class="cards_01 d-block mx-auto">
                                            <h6></h6>
                                            <h5></h5>
                                        </div>
                                        <div class="cards_01 img-div">
                                            <img src="{{ asset('assets/images/admin/icon_orders.png') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="cardstotal">
                                        <h6>Total - {{ $ordersTotal }}</h6>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Highlights Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <div class="cardsdiv">
                                <div class="cardshead">
                                    <h6>Highlights</h6>
                                </div>
                                <div class="cardsct mb-3">
                                    <a href="{{ url('highlight_products_list') }}" class="d-block me-auto">
                                        <div class="cards_01">
                                            <h6>Products</h6>
                                            <h5>{{ $productHighlighted }}</h5>
                                        </div>
                                    </a>
                                    <div class="brdr"></div>
                                    <a href="{{ url('highlight_services_list') }}" class="d-block mx-auto">
                                        <div class="cards_01">
                                            <h6>Services</h6>
                                            <h5>{{ $serviceHighlighted }}</h5>
                                        </div>
                                    </a>
                                    <div class="brdr"></div>
                                    <a href="{{ url('highlight_jobs_list') }}" class="d-block mx-auto">
                                        <div class="cards_01">
                                            <h6>Jobs</h6>
                                            <h5>{{ $jobHighlighted }}</h5>
                                        </div>
                                    </a>
                                    <div class="cards_01 img-div">
                                        <img src="{{ asset('assets/images/admin/icon_highlights.png') }}" alt="">
                                    </div>
                                </div>
                                <div class="cardstotal">
                                    <h6>Total - {{ $highlightedTotal }}</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Report Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <a href="{{ url('insight_list') }}">
                                <div class="cardsdiv">
                                    <div class="cardshead">
                                        <h6>Insights</h6>
                                    </div>
                                    <div class="cardsct mb-3">
                                        <div class="cards_01 d-block me-auto">
                                            <h6>User</h6>
                                            <h5>{{ $reportUser }}</h5>
                                        </div>
                                        <div class="brdr"></div>
                                        <div class="cards_01 d-block mx-auto">
                                            <h6>Post</h6>
                                            <h5>{{ $reportPost }}</h5>
                                        </div>
                                        <div class="brdr"></div>
                                        <div class="cards_01 d-block mx-auto">
                                            <h6></h6>
                                            <h5></h5>
                                        </div>
                                        <div class="cards_01 img-div">
                                            <img src="{{ asset('assets/images/admin/icon_insight.png') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="cardstotal">
                                        <h6>Total - {{ $reportTotal }}</h6>
                                    </div>
                                </div>
                            </a>
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