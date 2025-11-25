<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onstru | Franchise Profile</title>

    <!-- Stylesheets CDN -->
    @include('admin.cdn_style')

    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/css/admin/profile.css') }}">
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
                    <h4 class="m-0">User Details</h4>
                </div>

                <div class="mt-3 profile-card">
                    <div class="cards mb-2">
                        <h6 class="mb-1">Name</h6>
                        <div class="d-flex align-items-center column-gap-2">
                            <img src="{{ asset('assets/images/Avatar.png') }}" height="25px" width="25px" class="avatar"
                                alt="">
                            <h5 class="mb-0">{{$user->name}}</h5>
                        </div>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Role</h6>
                        <h5 class="mb-0">{{$user->type_of}}</h5>
                    </div>
                    <!--
                    <div class="cards mb-2">
                        <h6 class="mb-1">Type Of</h6>
                        <h5 class="mb-0">Flooring Contractor, Ceiling Contractor</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Gender</h6>
                        <h5 class="mb-0">Male</h5>
                    </div>-->
                    <div class="cards mb-2">
                        <h6 class="mb-1">Email ID</h6>
                        <h5 class="mb-0">{{$user->mail_id}}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Contact Number</h6>
                        <h5 class="mb-0">{{$user->mobile}}</h5>
                    </div>
                </div>

                <div class="conatiner-fluid dashboard-cards w-100 p-0 mt-3">
                    <div class="row">
                        <!-- Vendor Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <a href="{{ route('franchise_users', ['id' => $user->id, 'type' => 'Vendor']) }}">
                                <div class="cardsdiv">
                                    <div class="cardshead">
                                        <h6>Vendor</h6>
                                    </div>
                                    <div class="cardsct">
                                        <div class="cards_01 d-block me-auto">
                                            <h6>Total</h6>
                                            <h5>{{ $vendor_total ?? 0 }}</h5>
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
                            </a>
                        </div>

                        <!-- Contractor Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <a href="{{ route('franchise_users', ['id' => $user->id, 'type' => 'Contractor']) }}">
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
                            </a>
                        </div>

                        <!-- Consultant Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <a href="{{ route('franchise_users', ['id' => $user->id, 'type' => 'Consultant']) }}">
                                <div class="cardsdiv">
                                    <div class="cardshead">
                                        <h6>Consultant</h6>
                                    </div>
                                    <div class="cardsct">
                                        <div class="cards_01 d-block me-auto">
                                            <h6>Total</h6>
                                            <h5>{{ $consultant_total }}</h5>
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
                            </a>
                        </div>

                        <!-- Consumer Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <a href="{{ route('franchise_users', ['id' => $user->id, 'type' => 'Consumer']) }}">
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
                            </a>
                        </div>

                        <!-- Technical Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <a href="{{ route('franchise_users', ['id' => $user->id, 'type' => 'Technical']) }}">
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
                            </a>
                        </div>

                        <!-- Non-Technical Card -->
                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2 cards">
                            <a href="{{ route('franchise_users', ['id' => $user->id, 'type' => 'Non-Technical']) }}">
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
                            </a>
                        </div>
                    </div>
                </div>

                <div class="profile-tabs mt-2">
                    <ul class="nav nav-tabs d-flex align-items-center gap-md-3 gap-sm-2 flex-row border-0" id="myTab"
                        role="tablist">
                        @include('admin.franchise_detail_tabs')
                    </ul>
                </div>

                <div class="tab-content" id="myTabContent">
                    <!-- Vendor -->
                    <div class="tab-pane fade show active" id="vendor" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="vendorDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="vendorInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="vendorTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Contact Number</th>
                                            <th>Email Id</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                        @foreach($vendor_data as $vendor)
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ $vendor->name }}</td>
                                            <td>{{ $vendor->gender }}</td>
                                            <td>{{ $vendor->number }}</td>
                                            <td>{{ $vendor->email }}</td>
                                            <td>
                                                <div class="d-flex align-items-center column-gap-2">
                                                    <a href="{{ url('franchise_amt/' . $vendor->id . '/' . $user->id . '/Vendor') }}">
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
                    <!-- Contractor -->
                    <div class="tab-pane fade" id="contractor" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="contractorDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="contractorInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="contractorTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Contact Number</th>
                                            <th>Email Id</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                        @foreach($contractor_data as $contractor)
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ $contractor->name }}</td>
                                            <td>{{ $contractor->gender }}</td>
                                            <td>{{ $contractor->number }}</td>
                                            <td>{{ $contractor->email }}</td>
                                            <td>
                                                <div class="d-flex align-items-center column-gap-2">
                                                    <a href="{{ url('franchise_amt/' . $contractor->id . '/' . $user->id . '/Contractor') }}">
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
                    <!-- Consultant -->
                    <div class="tab-pane fade" id="consultant" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="consultantDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="consultantInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="consultantTable">
                                    <thead>
                                        <tr>
                                           <th>#</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Contact Number</th>
                                            <th>Email Id</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       @foreach($consultant_data as $consultant)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ $consultant->name }}</td>
                                            <td>{{ $consultant->gender }}</td>
                                            <td>{{ $consultant->number }}</td>
                                            <td>{{ $consultant->email }}</td>
                                            <td>
                                                <div class="d-flex align-items-center column-gap-2">
                                                   <a href="{{ url('franchise_amt/' . $consultant->id . '/' . $user->id . '/Consultant') }}">
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
                    <!-- Consumer -->
                    <div class="tab-pane fade" id="consumer" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="consumerDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="consumerInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="consumerTable">
                                    <thead>
                                        <tr>
                                            <!--
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Transaction ID</th>
                                            <th>Commission</th>
                                            <th>Date</th>
                                            <th>Action</th>-->
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Contact Number</th>
                                            <th>Email Id</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                        @foreach($consumer_data as $consumer)
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ $consumer->name }}</td>
                                            <td>{{ $consumer->gender }}</td>
                                            <td>{{ $consumer->number }}</td>
                                            <td>{{ $consumer->email }}</td>
                                            <td>
                                                <div class="d-flex align-items-center column-gap-2">
                                                    <a href="{{ url('franchise_amt/' . $consumer->id . '/' . $user->id . '/Consumer') }}">
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
                    <!-- Technical -->
                    <div class="tab-pane fade" id="technical" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="techDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="techInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="techTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Contact Number</th>
                                            <th>Email Id</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                        @foreach($technical_data as $technical)
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ $technical->name }}</td>
                                            <td>{{ $technical->gender }}</td>
                                            <td>{{ $technical->number }}</td>
                                            <td>{{ $technical->email }}</td>
                                            <td>
                                                <div class="d-flex align-items-center column-gap-2">
                                                    <a href="{{ url('franchise_amt/' . $technical->id . '/' . $user->id . '/Technical') }}">
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
                    <!-- Non-Technical -->
                    <div class="tab-pane fade" id="nontechnical" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="nonTechDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="nonTechInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="nonTechTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Contact Number</th>
                                            <th>Email Id</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                        @foreach($nontechnical_data as $nontech)
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ $nontech->name }}</td>
                                            <td>{{ $nontech->gender }}</td>
                                            <td>{{ $nontech->number }}</td>
                                            <td>{{ $nontech->email }}</td>
                                            <td>
                                                <div class="d-flex align-items-center column-gap-2">
                                                    <a href="{{ url('franchise_amt/' . $nontech->id . '/' . $user->id . '/Non-Technical') }}">
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
                    <!-- Payment -->
                    <div class="tab-pane fade" id="paymenttab" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="paymentDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="paymentInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="paymentTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Type</th>
                                            <th>PaymentType</th>
                                            <th>Amount</th>
                                            <th>Payment Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                        @foreach($user_data as $usr)
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{ $usr->type }}</td>
                                        <td>{{ $usr->payment_type }}</td>
                                        <td>{{ $usr->amount }}</td>
                                        <td>{{ $usr->payment_status }}</td>


                                            <td>
                                                <div class="d-flex align-items-center column-gap-2">
                                                    <a href="{{ url('franchise_amt') }}">
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
    </div>

    <!-- script -->
    @include('admin.cdn_script')


    <script>
        // DataTables List
        $(document).ready(function () {
            function initTable(tableId, dropdownId, filterInputId) {
                var table = $(tableId).DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "order": [0, "asc"],
                    "bDestroy": true,
                    "info": false,
                    "responsive": true,
                    "pageLength": 30,
                    "dom": '<"top"f>rt<"bottom"ilp><"clear">',
                });
                $(tableId + ' thead th').each(function (index) {
                    var headerText = $(this).text();
                    if (headerText != "" && headerText.toLowerCase() != "action") {
                        $(dropdownId).append('<option value="' + index + '">' + headerText + '</option>');
                    }
                });
                $(filterInputId).on('keyup', function () {
                    var selectedColumn = $(dropdownId).val();
                    if (selectedColumn !== 'All') {
                        table.column(selectedColumn).search($(this).val()).draw();
                    } else {
                        table.search($(this).val()).draw();
                    }
                });
                $(dropdownId).on('change', function () {
                    $(filterInputId).val('');
                    table.search('').columns().search('').draw();
                });
                $(filterInputId).on('keyup', function () {
                    table.search($(this).val()).draw();
                });
            }
            // Initialize each table
            initTable('#vendorTable', '#vendorDropdown', '#vendorInput');
            initTable('#contractorTable', '#contractorDropdown', '#contractorInput');
            initTable('#consultantTable', '#consultantDropdown', '#consultantInput');
            initTable('#consumerTable', '#consumerDropdown', '#consumerInput');
            initTable('#techTable', '#techDropdown', '#techInput');
            initTable('#nonTechTable', '#nonTechDropdown', '#nonTechInput');
            initTable('#paymentTable', '#paymentDropdown', '#paymentInput');
        });
    </script>

</body>

</html>