<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Onstru | Highlighted Jobs</title>

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
                <div class="body-head">
                    <h4 class="m-0">Highlighted Job List</h4>
                </div>

                <div class="container-fluid listtable mt-2">
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
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Experience</th>
                                    <th>Type</th>
                                    <th>Salary</th>
                                    <th>Location</th>
                                    <th>Sublocation</th>
                                    <th>Days</th>
                                    <th>Click</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($highlightedJobs as $jobs)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $jobs->job->title ?? '-' }}</td>
                                        <td>{{ $jobs->job->categoryRelation->value ?? '-' }}</td>
                                        <td>{{ $jobs->job->experience ?? '-' }} years</td>
                                        <td>{{ $jobs->job->shift ?? '-' }}</td>
                                        <td>{{ $jobs->job->salary ?? '-' }}</td>
                                        <td>{{ $jobs->job->locationRelation->value ?? '-' }}</td>
                                        <td>{{ $jobs->job->sublocality ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($jobs->from)->diffInDays(\Carbon\Carbon::parse($jobs->to), false) + 1 }}</td>
                                        <td>{{ $jobs->job->click ?? '-' }}</td>
                                        <td>{{ $jobs->job->user->name ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center column-gap-2">
                                                <a href="{{ route('job_detail', ['id' => $jobs->job->id]) }}" data-bs-toggle="tooltip" data-bs-title="View">
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