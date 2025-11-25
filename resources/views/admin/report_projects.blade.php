<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Onstru | Projects Report</title>

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
                    <h4 class="m-0">Projects Report</h4>
                </div>

                <div class="profile-tabs">
                    <ul class="nav nav-tabs d-flex align-items-center gap-md-3 gap-sm-2 flex-row border-0" id="myTab"
                        role="tablist">
                        <li class="nav-item mb-1" role="presentation">
                            <button class="exportbtn rounded-5 active" data-bs-toggle="tab" type="button"
                                data-bs-target="#listing">
                                Listing
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="myTabContent">
                    <!-- Listing -->
                    <div class="tab-pane fade show active" id="listing" role="tabpanel">
                        <div class="container-fluid listtable mt-2">
                            <div class="filter-container">
                                <div class="filter-container-start">
                                    <select class="form-select filter-option" id="listingDropdown">
                                        <option value="All" selected>All</option>
                                    </select>
                                    <input type="text" class="form-control" id="listingInput" placeholder=" Search">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table" id="listingTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Location</th>
                                            <th>Created By</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($projectListing as $project)
                                            <tr id="{{ $project->id }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $project->title ?? '-' }}</td>
                                                <td>{{ $project->locationDetails->value ?? '-'}}</td>
                                                <td>{{ $project->creator->name ?? '-' }}</td>
                                                <td>₹ {{ $project->amount ?? '-' }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center column-gap-2">
                                                        <a href="{{ route('project_detail', ['id' => $project->id]) }}"
                                                            data-bs-toggle="tooltip" data-bs-title="View Project">
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

    @include('admin.toaster')

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
            initTable('#listingTable', '#listingDropdown', '#listingInput');
        });
    </script>

</body>

</html>