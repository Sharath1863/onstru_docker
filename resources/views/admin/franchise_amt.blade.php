<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onstru | Franchise Amount Details</title>

    <!-- Stylesheets CDN -->
    @include('admin.cdn_style')

    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/css/admin/profile.css') }}">

</head>

<body>
    <div class="main">

        <!-- aside -->
        @include('admin.aside')

        <div class="body-main">

            <!-- Navbar -->
            @include('admin.navbar')

            <div class="main-div px-4 py-1">
                <div class="profile-tabs">
                    <ul class="nav nav-tabs d-flex align-items-center gap-md-3 gap-sm-2 flex-row border-0" id="myTab"
                        role="tablist">
                        @include('admin.franchise_amt_tabs')
                    </ul>
                </div>

                <div class="tab-content" id="myTabContent">
                    <!-- Sales -->
                    <div class="tab-pane fade show active" id="sales" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="salesDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="salesInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="salesTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Order Id</th>
                                            <th>Amount</th>
                                            <!--<th>Product</th> -->
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                         @foreach($orderProducts as $sales)
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $sales['order_id'] }}</td>
                                            <td>{{ $sales['total_settle'] }}</td>
                                            <td>{{ $sales['frn_date'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Chatbot -->
                    <div class="tab-pane fade" id="chatbot" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="chatbotDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="chatbotInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="chatbotTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Settlement Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($chatbotincome as $chatbot)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $chatbot['settle_amount'] }}</td>
                                            <td>{{ $chatbot['frn_updated_date'] }}</td>
                                        </tr>
                                    </tbody>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Ready to Work -->
                    <div class="tab-pane fade" id="ready" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="readyDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="readyInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="readyTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Days</th>
                                            <th>Amount</th>
                                            <th>Settle Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                         @foreach($readyincome as $ready)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $ready->days }}</td>
                                            <td>{{ $ready->amount }}</td>
                                             <td>{{ $ready->settle_amount }}</td>
                                             <td>{{ $ready->frn_updated_date }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Premium -->
                    <div class="tab-pane fade" id="premium" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="premiumDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="premiumInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="premiumTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Price</th>
                                            <th>Settle Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($premiumincome as $premium)
                                        <tr>
                                            <td>{{ $loop->iteration}}</td>
                                            <td>{{ $premium->price }}</td>
                                            <td>{{ $premium->settle_amount}}</td>
                                            <td>{{ $premium->frn_updated_date }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Badge -->
                    <div class="tab-pane fade" id="badge" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="badgeDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="badgeInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="badgeTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>badge</th>
                                            <th>Amount</th>
                                            <th>Settle Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($badgeincome as $badge)
                                        <tr>
                                            <td>{{ $loop->iteration }}
                                            <td>{{ $badge->badge }}</td>
                                            <td>{{ $badge->amount }}</td>
                                            <td>{{ $badge->settle_amount }}</td>
                                            <td>{{ $badge->frn_updated_date }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Project -->
                    <div class="tab-pane fade" id="project" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="projectDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="projectInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="projectTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Budget</th>
                                            <th>Settle Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       @foreach($projectincome as $project)
                                        <tr>
                                            <td>{{ $loop->iteration}}</td>
                                            <td>{{ $project->title}}</td>
                                            <td>{{ $project->prjt_budget}}</td>
                                            <td>{{ $project->settle_amount}}</td>
                                            <td>{{ $project->frn_updated_date }}</td>
                                        </tr>
                                    </tbody>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Product -->
                    <div class="tab-pane fade" id="product" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="productDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="productInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="productTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Amount</th>
                                            <th>Settle Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($productincome as $product)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $product->amount }}</td>
                                            <td>{{ $product->settle_amount }}</td>
                                            <td>{{ $product->frn_updated_date }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Job -->
                    <div class="tab-pane fade" id="job" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="jobDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="jobInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="jobTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                             <th>Job Title</th>
                                            <th>Amount</th>
                                            <th>Settle Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       @foreach($jobincome as $job)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $job->job->title }}</td>
                                            <td>{{ $job->amount }}</td>
                                            <td>{{ $job->settle_amount }}</td>
                                            <td>{{ $job->frn_updated_date }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Service -->
                    <div class="tab-pane fade" id="service" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="serviceDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="serviceInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="serviceTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Settle Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       @foreach($serviceincome as $service)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $service->service->title }}</td>
                                            <td>{{ $service->type }}</td>
                                            <td>{{ $service->amount }}</td>
                                            <td>{{ $service->settle_amount }}</td>
                                            <td>{{ $service->frn_updated_date }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Lead -->
                    <div class="tab-pane fade" id="lead" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="leadDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="leadInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="leadTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>buildup Area</th>
                                            <th>Settle Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       @foreach($leadincome as $lead)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $lead->lead->title }}</td>
                                            <td>{{ $lead->lead->buildup_area }}</td>
                                             <td>{{ $lead->settle_amount }}</td>
                                            <td>10-10-2025</td>
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
            initTable('#salesTable', '#salesDropdown', '#salesInput');
            initTable('#chatbotTable', '#chatbotDropdown', '#chatbotInput');
            initTable('#readyTable', '#readyDropdown', '#readyInput');
            initTable('#premiumTable', '#premiumDropdown', '#premiumInput');
            initTable('#badgeTable', '#badgeDropdown', '#badgeInput');
            initTable('#projectTable', '#projectDropdown', '#projectInput');
            initTable('#productTable', '#productDropdown', '#productInput');
            initTable('#jobTable', '#jobDropdown', '#jobInput');
            initTable('#serviceTable', '#serviceDropdown', '#serviceInput');
            initTable('#leadTable', '#leadDropdown', '#leadInput');
        });
    </script>

</body>

</html>