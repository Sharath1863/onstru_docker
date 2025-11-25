<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Onstru | Leads</title>

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
                    <h4 class="m-0">Lead List</h4>
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
                                    <th>Lead Title</th>
                                    <th>Service Type</th>
                                    <th>Buildup Area</th>
                                    <th>Budget</th>
                                    <th>Start Date</th>
                                    <th>Location</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Repost</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($leads as $lead)
                                    <tr id="leadRow{{ $lead->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $lead->title ?? '-' }}</td>
                                        <td>{{ $lead->serviceRelation->value }}</td>
                                        <td>{{ $lead->buildup_area ?? '-' }} .sqft</td>
                                        <td>{{ $lead->budget ?? '-' }}</td>
                                        <td>{{ $lead->start_date ?? '-' }}</td>
                                        <td>{{ $lead->locationRelation->value }}</td>
                                        <td>{{ $lead->user->name ?? '-' }}</td>
                                        <td>
                                            <span id="statusText{{ $lead->id }}"
                                                class="text-capitalize {{ $lead->approval_status == 'approved' ? 'text-success' : '' }} {{ $lead->approval_status == 'rejected' ? 'text-danger' : '' }} {{ $lead->approval_status == 'pending' ? 'text-warning' : '' }}">
                                                {{ $lead->approval_status }}
                                            </span>
                                        </td>
                                        <td>{{ $lead->repost ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="{{ route('lead_detail', ['id' => $lead->id]) }}"
                                                    data-bs-toggle="tooltip" data-bs-title="View">
                                                    <i class="fas fa-external-link"></i>
                                                </a>
                                                @if ($lead->approval_status == 'pending')
                                                    <button type="button" class="listtdbtn" data-bs-toggle="modal"
                                                        data-bs-target="#leadstatus{{ $lead->id }}">
                                                        Update
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal -->
                                    <div class="modal fade" id="leadstatus{{ $lead->id }}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
                                        aria-labelledby="updateModal" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="updateModal">Update Status</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body my-2 pb-0">
                                                    <div class="col-sm-12 mb-2">
                                                        <label for="status">Status <span>*</span></label>
                                                        <select class="form-select status-select"
                                                            id="status{{ $lead->id }}" name="status" required>
                                                            <option value="" selected disabled>Select Status
                                                            </option>
                                                            <option value="approved">Approved</option>
                                                            <option value="rejected">Rejected</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-12 mb-2">
                                                        <label for="admin_charge">Admin Charge</label>
                                                        <input type="number" class="form-control"
                                                            id="admin_charge{{ $lead->id }}" name="admin_charge"
                                                            value="{{ $lead->admin_charge }}"
                                                            {{ $lead->admin_charge !== 0 ? 'readonly' : '' }}
                                                            required>
                                                    </div>
                                                    <div class="col-sm-12 mb-2">
                                                        <label for="remark">Remarks <span>*</span></label>
                                                        <textarea rows="2" class="form-control remark-input" id="remark{{ $lead->id }}" name="remark" required>{{ $lead->remark }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer d-flex justify-content-center my-2">
                                                    <button type="button" class="modalbtn lead-toggle-btn"
                                                        data-id="{{ $lead->id }}">
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
        $(document).on('click', '.lead-toggle-btn', function() {
            let leadId = $(this).data('id');
            let status = $('#status' + leadId).val();
            //alert(status);
            let admin_charge = $('#admin_charge' + leadId).val().trim();
            let remark = $('#remark' + leadId).val().trim();
            let $btn = $(this);
            let originalHtml = $btn.html();

            // Simple validation
            if (!status) {
                showToast("Select a status");
                return;
            }

            if (admin_charge === "") {
                showToast("Admin charge is mandatory");
                return;
            }

            if (remark === "") {
                showToast("Remarks cannot be empty");
                return;
            }

            $btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Updating...'
            );
            var url = "{{ route('leads.changeStatus', ['id' => '__leadId__']) }}";
            url = url.replace('__leadId__', leadId);
            // AJAX request
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status,
                    admin_charge: admin_charge,
                    remark: remark
                },
                success: function(response) {
                    if (response.success) {
                        // Update status text
                        let statusSpan = $("#statusText" + leadId);
                        statusSpan
                            .text(response.status)
                            .removeClass("text-success text-danger text-warning")
                            .addClass(
                                response.status === "approved" ? "text-success" :
                                response.status === "rejected" ? "text-danger" :
                                "text-warning"
                            );

                        // Hide update button
                        $('#leadRow' + leadId + ' .listtdbtn').hide();

                        // Close modal
                        $('#leadstatus' + leadId).modal('hide');

                        // Show success toast
                        showToast("Status updated successfully!", "success");
                    } else {
                        showToast("Failed to update status!", "warning");
                    }
                },
                error: function(xhr) {
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
