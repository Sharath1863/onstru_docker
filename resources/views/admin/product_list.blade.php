<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Onstru | Products</title>

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
                    <h4 class="m-0">Product List</h4>
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
                                    <th>Product</th>
                                    <th>Brand Name</th>
                                    <th>Category</th>
                                    <th>SP</th>
                                    <th>Availability</th>
                                    <th>Location</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr id="productRow{{ $product->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $product->name ?? '-' }}</td>
                                        <td>{{ $product->brand_name ?? '-' }}</td>
                                        <td>{{ $product->categoryRelation->value }}</td>
                                        <td>{{ $product->sp ?? '0' }}</td>
                                        <td>{{ $product->availability ?? '-' }}</td>
                                        <td>{{ $product->locationRelation->value }}</td>
                                        <td>{{ $product->vendor->name ?? '-' }}</td>
                                        <td>
                                            <span id="statusText{{ $product->id }}"
                                                class="text-capitalize {{ $product->approvalstatus == 'approved' ? 'text-success' : '' }} {{ $product->approvalstatus == 'rejected' ? 'text-danger' : '' }} {{ $product->approvalstatus == 'pending' ? 'text-warning' : '' }}">
                                                {{ $product->approvalstatus }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="{{ route('product_detail', ['id' => $product->id]) }}"
                                                    data-bs-toggle="tooltip" data-bs-title="View">
                                                    <i class="fas fa-external-link"></i>
                                                </a>
                                                @if ($product->approvalstatus == 'pending')
                                                    <button type="button" class="listtdbtn" data-bs-toggle="modal"
                                                        data-bs-target="#productstatus{{ $product->id }}">
                                                        Update
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal -->
                                    <div class="modal fade" id="productstatus{{ $product->id }}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
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
                                                        <label for="cashback">Cashback Price <span>*</span></label>
                                                        <input type="number" class="form-control"
                                                            id="cashback{{ $product->id }}" name="cashback" required>
                                                    </div>
                                                    <div class="col-sm-12 mb-2">
                                                        <label for="margin">Margin Price <span>*</span></label>
                                                        <input type="number" class="form-control"
                                                            id="margin{{ $product->id }}" name="margin" required>
                                                    </div>
                                                    <div class="col-sm-12 mb-2">
                                                        <label for="status">Status <span>*</span></label>
                                                        <select class="form-select status-select"
                                                            id="status{{ $product->id }}" name="status" required>
                                                            <option value="" selected disabled>Select Status
                                                            </option>
                                                            <option value="approved">Approved</option>
                                                            <option value="rejected">Rejected</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-12 mb-2">
                                                        <label for="remark">Remarks <span>*</span></label>
                                                        <textarea rows="2" class="form-control remark-input"
                                                            id="remark{{ $product->id }}" name="remark" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer d-flex justify-content-center my-2">
                                                    <button type="submit" class="modalbtn product-toggle-btn"
                                                        data-id="{{ $product->id }}">
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

    <script>
        $(document).on('click', '.product-toggle-btn', function () {
            let productId = $(this).data('id');
            let status = $('#status' + productId).val();
            let remark = $('#remark' + productId).val();
            let cashback = $('#cashback' + productId).val();
            let margin = $('#margin' + productId).val();
            let $btn = $(this);
            let originalHtml = $btn.html();

            
            if (!status) {
                showToast("Select a Status");
                return;
            }
            if(status === 'approved') {
                if (!cashback) {
                    showToast("Cashback Price cannot be empty");
                    return;
                }
                if (!margin) {
                    showToast("Margin Price cannot be empty");
                    return;
                }
            }
            if (!remark) {
                showToast("Remarks cannot be empty");
                return;
            }

            $btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Updating...'
            );

            $.ajax({
                // url: '/product/status/' + productId,
                url: "{{ route('product.changeStatus', ['id' => '__productId__']) }}".replace('__productId__', productId),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status,
                    remark: remark,
                    cashback: cashback,
                    margin: margin
                },
                success: function (response) {
                    if (response.success) {
                        let statusSpan = $("#statusText" + productId);

                        statusSpan
                            .text(response.status)
                            .removeClass("text-success text-danger text-warning")
                            .addClass(response.status === "approved" ? "text-success" :
                                response.status === "rejected" ? "text-danger" : "text-warning");

                        $('#productstatus' + productId).modal('hide');
                        $('#productRow' + productId + ' .listtdbtn').hide();
                        // location.reload();
                        showToast("Status updated successfully!", "success");
                    } else {
                        showToast("Failed to update status!", "warning");
                    }
                },
                error: function () {
                    showToast("Something went wrong! Please try again.", "danger");
                },
                complete: function () {
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        });
    </script>

</body>

</html>