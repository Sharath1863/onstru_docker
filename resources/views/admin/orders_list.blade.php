<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onstru | Orders</title>

    <!-- Stylesheets CDN -->
    @include('admin.cdn_style')

    <!-- Stylesheet -->
    <link rel="stylesheet" href="assets/css/admin/form.css">

</head>

<body>
    <div class="main">

        <!-- aside -->
        @include('admin.aside')

        <div class="body-main">

            <!-- Navbar -->
            @include('admin.navbar')

            <div class="main-div px-4 mb-3">
                <div class="body-head mt-3">
                    <h4>Orders List</h4>
                </div>
                <div class="container-fluid mt-3 listtable">
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
                                    <th>Order ID</th>
                                    <th>Total Amount</th>
                                    <th>Transaction Status</th>
                                    <th>Order Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $order->order_id ?? '-' }}</td>
                                        <td>₹ {{ $order->total ?? '-' }}</td>
                                        <td><span class="text-capitalize">{{ $order->transaction_status ?? '-' }}</span></td>
                                        <td><span class="text-capitalize">{{ $order->status ?? '-' }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="{{ route('orders_detail', ['id' => $order->id]) }}"
                                                    data-bs-toggle="tooltip" data-bs-title="View">
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