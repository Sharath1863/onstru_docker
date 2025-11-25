<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Onstru | Jobs</title>

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
                    <h4 class="m-0">Job List</h4>
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.jobs.refreshHighlights') }}">
                            @csrf
                            <button type="submit" class="listbtn">Refresh Job Boost</button>
                        </form>
                        <form method="POST" action="{{ route('admin.refreshreadytowork') }}">
                            @csrf
                            <button type="submit" class="listbtn">Refresh ReadyToWork</button>
                        </form>
                    </div>
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
                                    <th>Job Title</th>
                                    <th>Category</th>
                                    <th>Experience</th>
                                    <th>Salary</th>
                                    <th>Location</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jobs as $job)
                                    <tr id="jobRow{{ $job->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $job->title ?? '-' }}</td>
                                        <td>{{ $job->categoryRelation->value ?? '-' }}</td>
                                        <td>{{ $job->experience ?? '-' }} Year</td>
                                        <td>{{ $job->salary ?? 'Not disclosed' }}</td>
                                        <td>{{ $job->locationRelation->value ?? '-' }}</td>
                                        <td>{{ $job->user->name ?? '-' }}</td>
                                        <td>
                                            <span id="statusText{{ $job->id }}"
                                                class="text-capitalize {{ $job->approvalstatus == 'approved' ? 'text-success' : '' }} {{ $job->approvalstatus == 'rejected' ? 'text-danger' : '' }} {{ $job->approvalstatus == 'pending' ? 'text-warning' : '' }}">
                                                {{ $job->approvalstatus }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="{{ route('job_detail', ['id' => $job->id]) }}"
                                                    data-bs-toggle="tooltip" data-bs-title="View">
                                                    <i class="fas fa-external-link"></i>
                                                </a>
                                                @if ($job->approvalstatus == 'pending')
                                                    <button type="button" class="listtdbtn" data-bs-toggle="modal"
                                                        data-bs-target="#jobstatus{{ $job->id }}">
                                                        Update
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal -->
                                    <div class="modal fade" id="jobstatus{{ $job->id }}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
                                        aria-labelledby="updateModal" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="updateModal">Update Status</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body mt-2">
                                                    <div class="col-sm-12 mb-2">
                                                        <label for="status">Status <span>*</span></label>
                                                        <select class="form-select status-select"
                                                            id="status{{ $job->id }}" name="status" required>
                                                            <option value="" selected disabled>Select Status
                                                            </option>
                                                            <option value="approved">Approved</option>
                                                            <option value="rejected">Rejected</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-12 mb-2">
                                                        <label for="remark">Remarks <span>*</span></label>
                                                        <textarea rows="2" class="form-control remark-input" id="remark{{ $job->id }}" name="remark" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-center my-2">
                                                    <button type="button" class="modalbtn job-toggle-btn"
                                                        data-id="{{ $job->id }}">
                                                        Update
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
        $(document).ready(function() {
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
        $(document).ready(function() {
            var table = $('.example').DataTable();
            $('.example thead th').each(function(index) {
                var headerText = $(this).text();
                if (headerText != "" && headerText.toLowerCase() != "action" && headerText.toLowerCase() !=
                    "progress") {
                    $('.headerDropdown').append('<option value="' + index + '">' + headerText +
                        '</option>');
                }
            });
            $('.filterInput').on('keyup', function() {
                var selectedColumn = $('.headerDropdown').val();
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
        $(document).on('click', '.job-toggle-btn', function() {
            let jobId = $(this).data('id');
            let status = $('#status' + jobId).val();
            let remark = $('#remark' + jobId).val();
            let $btn = $(this);
            let originalHtml = $btn.html();

            if (!status) {
                showToast("Select a status");
                return;
            }
            if (!remark) {
                showToast("Remarks cannot be empty");
                return;
            }

            $btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Updating...'
            );

            $.ajax({
                url: '{{ route('job.changeStatus', ['id' => '__jobId__']) }}'.replace('__jobId__', jobId),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status,
                    remark: remark
                },
                success: function(response) {
                    if (response.success) {
                        let statusSpan = $("#statusText" + jobId);
                        statusSpan
                            .text(response.approvalstatus)
                            .removeClass("text-success text-danger text-warning")
                            .addClass(response.approvalstatus === "approved" ? "text-success" :
                                response.approvalstatus === "rejected" ? "text-danger" : "text-warning"
                                );

                        $('#jobstatus' + jobId).modal('hide');
                        $('#jobRow' + jobId + ' .listtdbtn').hide();
                        // location.reload();
                        showToast("Status updated successfully!", "success");
                    } else {
                        showToast("Failed to update status!", "warning");
                    }
                },
                error: function() {
                    showToast("Something went wrong! Please try again.", "danger");
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        });
    </script>

</body>

</html>
