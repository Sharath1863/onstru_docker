<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onstru | Franchise Settlement</title>

    <!-- Stylesheets CDN -->
    @include('admin.cdn_style')

</head>

<body>

    <div class="main">

        <!-- aside -->
        @include('admin.aside')

        <div class="body-main">

            <!-- Navbar -->
            @include('admin.navbar')

            <div class="main-div px-4 py-1">
                <div class="body-head mb-3">
                    <h4 class="m-0">Franchise Settlement List</h4>
                </div>

                <form action="{{ route('franchise_amount_settle') }}" method="POST">
                    @csrf
                    <div class="container-fluid form-div">
                        <div class="row">
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <label for="users">User <span>*</span></label>
                                <select class="form-select" name="users" id="users" required>
                                    <option value="" disabled selected>Select User</option>
                                    @foreach($franchises_dropdown as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <label for="from_date">From Date <span>*</span></label>
                                <input type="date" class="form-control" name="from_date" id="from_date" required>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <label for="to_date">To Date <span>*</span></label>
                                <input type="date" class="form-control" name="to_date" id="to_date" required>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mt-auto mb-3">
                                <button type="submit" class="formbtn">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
                @if(!empty($fromDate) && $franchise)
                <div class="container-fluid listtable mt-3">
                    <form action="{{ route('franchise_amount_store') }}" method="POST">
                        @csrf
                        <div class="filter-container">
                            <div class="filter-container-start">
                                <select class="headerDropdown form-select filter-option">
                                    <option value="All" selected>All</option>
                                </select>
                                <input type="text" class="form-control filterInput" placeholder=" Search">
                            </div>
                            <div class="filter-container-end ms-auto">
                                <h6 class="mb-0">From: <span class="text-dark fw-bold">{{$fromDate}}</span></h6>
                                <h6 class="mb-0">To: <span class="text-dark fw-bold">{{$toDate}}</span></h6>
                                <h6 class="mb-0">Total: <span class="text-dark fw-bold">₹{{ $totalCommissionAmount + $lead_total_amount + $service_total_amount + $job_total_amount + $ready_total_amount + $premium_total_amount + $badge_total_amount + $product_total_amount + $chatbot_total_amount + $project_total_amount }}</span></h6>
                                <button type="submit" class="listbtn {{   $totalCommissionAmount == 0 &&
                                        $lead_total_amount == 0 &&
                                        $service_total_amount == 0 &&
                                        $job_total_amount == 0 &&
                                        $ready_total_amount == 0 &&
                                        $premium_total_amount == 0 &&
                                        $badge_total_amount == 0  &&
                                        $product_total_amount == 0 &&
                                        $chatbot_total_amount == 0 &&
                                        $project_total_amount == 0
                                            ? 'd-none'
                                            : '' }}">Settle Payment</button>
                            </div>
                        </div>

                        <div class="table-wrapper">

                            <!-- 🔹 Hidden fields to submit franchise and date info -->
                            <input type="hidden" name="franchise_id" value="{{ $franchise->id }}">
                            <input type="hidden" name="franchise_name" value="{{ $franchise->name }}">
                            <input type="hidden" name="from_date" value="{{ $fromDate }}">
                            <input type="hidden" name="to_date" value="{{ $toDate }}">
                            <input type="hidden" name="total_product_commission" value="{{ $totalCommissionAmount }}">
                            <input type="hidden" name="total_lead_commission" value="{{ $lead_total_amount }}">
                            <input type="hidden" name="total_service_commission" value="{{ $service_total_amount }}">
                            <input type="hidden" name="total_job_commission" value="{{ $job_total_amount }}">
                            <input type="hidden" name="total_ready_commission" value="{{ $ready_total_amount }}">
                            <input type="hidden" name="total_premium_commission" value="{{ $premium_total_amount }}">
                            <input type="hidden" name="total_badge_commission" value="{{ $badge_total_amount }}">
                            <input type="hidden" name="total_product_commission" value="{{ $product_total_amount }}">
                            <input type="hidden" name="total_chatbot_commission" value="{{ $chatbot_total_amount }}">
                            <input type="hidden" name="total_project_commission" value="{{ $project_total_amount }}">




                              <!-- 🔹 Hidden input for results array (encoded as JSON) -->
                              <input type="hidden" name="results" value='@json($results)'>
                              <input type="hidden" name="leads" value='@json($Leads)'>
                              <input type="hidden" name="service" value='@json($Services)'>
                              <input type="hidden" name="job" value='@json($Jobs)'>
                              <input type="hidden" name="ready" value='@json($Ready)'>
                              <input type="hidden" name="premium" value='@json($Premium)'>
                              <input type="hidden" name="badge" value='@json($Badge)'>
                              <input type="hidden" name="product" value='@json($Product)'>
                              <input type="hidden" name="chatbot" value='@json($Chatbot)'>
                              <input type="hidden" name="project" value='@json($Project)'>
                            <table class="example table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>type</th>
                                        <th>FranchiseName</th>
                                        <th>Amount</th>
                                        <th>FromDate</th>
                                        <th>ToDate</th>
                                       

                                    </tr>
                                </thead>
                                <tbody>
                                    @php $count = 1; @endphp
                                    <tr>
                                        <td>{{ $count++ }}</td>
                                        <td>Sales</td>
                                        <td>{{$franchise->name}}</td>
                                        <td>{{$totalCommissionAmount}}</td>
                                        <td>{{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $count++ }}</td>
                                        <td>Lead</td>
                                        <td>{{$franchise->name}}</td>
                                        <td>{{ $lead_total_amount }}</td>
                                        <td>{{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }}</td>  
                                    </tr>
                                    <tr>
                                        <td>{{ $count++ }}</td>
                                        <td>Service</td>
                                        <td>{{$franchise->name}}</td>
                                        <td>{{ $service_total_amount }}</td>
                                        <td>{{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }}</td>  
                                    </tr>
                                    <tr>
                                        <td>{{ $count++ }}</td>
                                        <td>Jobs</td>
                                        <td>{{$franchise->name}}</td>
                                        <td>{{ $job_total_amount }}</td>
                                        <td>{{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }}</td>  
                                    </tr>
                                    <tr>
                                        <td>{{ $count++ }}</td>
                                        <td>ReadyToWork</td>
                                        <td>{{$franchise->name}}</td>
                                        <td>{{ $ready_total_amount }}</td>
                                        <td>{{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }}</td>  
                                    </tr>
                                    <tr>
                                        <td>{{ $count++ }}</td>
                                        <td>Premium</td>
                                        <td>{{$franchise->name}}</td>
                                        <td>{{ $premium_total_amount }}</td>
                                        <td>{{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }}</td>  
                                    </tr>
                                    <tr>
                                        <td>{{ $count++ }}</td>
                                        <td>Badge</td>
                                        <td>{{$franchise->name}}</td>
                                        <td>{{ $badge_total_amount }}</td>
                                        <td>{{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }}</td>  
                                    </tr>
                                    <tr>
                                        <td>{{ $count++ }}</td>
                                        <td>Product</td>
                                        <td>{{$franchise->name}}</td>
                                        <td>{{ $product_total_amount }}</td>
                                        <td>{{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }}</td>  
                                    </tr>
                                    <tr>
                                        <td>{{ $count++ }}</td>
                                        <td>Chatbot</td>
                                        <td>{{$franchise->name}}</td>
                                        <td>{{ $chatbot_total_amount }}</td>
                                        <td>{{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }}</td>  
                                    </tr>
                                    <tr>
                                        <td>{{ $count++ }}</td>
                                        <td>Project</td>
                                        <td>{{$franchise->name}}</td>
                                        <td>{{ $project_total_amount }}</td>
                                        <td>{{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }}</td>  
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    @include('admin.toaster')

    <!-- script -->
    @include('admin.cdn_script')

</body>

<!-- DataTables List -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const table = $('.example').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            bDestroy: true,
            info: false,
            responsive: true,
            pageLength: 15,
            dom: '<"top"f>rt<"bottom"lp><"clear">'
        });
        $('.example thead th').each(function(index) {
            const headerText = $(this).text();
            if (headerText && headerText.toLowerCase() !== "action" && headerText.toLowerCase() !==
                "progress") {
                $('.headerDropdown').append('<option value="' + index + '">' + headerText +
                    '</option>');
            }
        });
        $('.filterInput').on('keyup', function() {
            const selectedColumn = $('.headerDropdown').val();
            if (selectedColumn !== 'All') {
                table.column(selectedColumn).search($(this).val()).draw();
            } else {
                table.search($(this).val()).draw();
            }
        });
        $('.headerDropdown').on('change', function() {
            $('.filterInput').val('');
            table.search('').columns().search('').draw();
        });
    });
</script>

<script>
    $(document).ready(function() {
        let select2 = ['users']
        select2.forEach(ele => {
            $(`#${ele}`).select2({
                width: "100%",
                placeholder: "Select Options",
                allowClear: true,
            });
        });
    });
</script>

</html>