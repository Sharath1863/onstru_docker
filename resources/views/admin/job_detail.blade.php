<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Onstru | Job Details</title>

    <!-- Stylesheets CDN -->
    @include('admin.cdn_style')

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
                <div class="body-head">
                    <h4 class="m-0">Job Details</h4>
                </div>

                <div class="mt-3 profile-card">
                    <div class="cards mb-2">
                        <h6 class="mb-1">Title</h6>
                        <h5 class="mb-0">{{ $job->title ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Category</h6>
                        <h5 class="mb-0">{{ $job->categoryRelation->value ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Shift</h6>
                        <h5 class="mb-0">{{ $job->shift ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Salary</h6>
                        <h5 class="mb-0">{{ $job->salary ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Location</h6>
                        <h5 class="mb-0">{{ $job->locationRelation->value ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Location</h6>
                        <h5 class="mb-0">{{ $job->sublocality ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Skills</h6>
                        <h5 class="mb-0">{{ $job->skills ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Qualification</h6>
                        <h5 class="mb-0">{{ $job->qualification ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Description</h6>
                        <h5 class="mb-0">{{ $job->description ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Experience</h6>
                        <h5 class="mb-0">{{ $job->experience ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Benefits</h6>
                        <h5 class="mb-0">{{ $job->benfit ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Vacancy Count</h6>
                        <h5 class="mb-0">{{ $job->no_of_openings ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Created By</h6>
                        <h5 class="mb-0">{{ $job->user->name ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Boosted</h6>
                        @if ($job->highlighted == 0)
                            <h5 class="mb-0">No</h5>
                        @elseif ($job->highlighted == 1)
                            <h5 class="mb-0">Yes</h5>
                        @endif
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Admin Approval</h6>
                        @if ($job->approvalstatus == 'pending')
                            <h5 class="mb-0 text-warning">{{ $job->approvalstatus }}</h5>
                        @elseif ($job->approvalstatus == 'approved')
                            <h5 class="mb-0 text-success">{{ $job->approvalstatus }}</h5>
                        @elseif ($job->approvalstatus == 'rejected')
                            <h5 class="mb-0 text-danger">{{ $job->approvalstatus }}</h5>
                        @endif
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Admin Remarks</h6>
                        <h5 class="mb-0">{{ $job->remarks ?? '-' }}</h5>
                    </div>
                </div>

                <div class="body-head my-3">
                    <h4 class="m-0">Boost Details</h4>
                </div>

                <!-- Boosting -->
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
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Amount</th>
                                    <th>Boosted On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jobBoosting as $jobs)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $jobs->from ?? '-' }}</td>
                                        <td>{{ $jobs->to ?? '-' }}</td>
                                        <td>₹ {{ $jobs->amount ?? '0' }}</td>
                                        <td>{{ $jobs->created_at->format('d-m-Y') ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center column-gap-2">
                                                <a href="{{ url('job-boost-bill', ['id' => $jobs->id]) }}" target="_blank"
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

    @include('admin.toaster')

    <!-- script -->
    @include('admin.cdn_script')

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

</body>

</html>