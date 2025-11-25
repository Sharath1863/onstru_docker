<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onstru | Order Settlement</title>

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
                    <h4 class="m-0">Order Settlement List</h4>
                    <div class="d-flex align-items-center column-gap-2 form-div p-0 bg-none">
                        <input type="date" class="form-control" id="fromdate">
                        <input type="date" class="form-control" id="todate">
                        <select class="form-select" id="vendor">
                            <option value="" selected disabled>Select Vendor</option>
                        </select>
                    </div>
                </div>

                <div class="container-fluid listtable mt-3">
                    <div class="filter-container">
                        <div class="filter-container-start">
                            <select class="headerDropdown form-select filter-option">
                                <option value="All" selected>All</option>
                            </select>
                            <input type="text" class="form-control filterInput" placeholder=" Search">
                        </div>
                        <div class="filter-container-end ms-auto">
                            <button class="listbtn" onclick="settle()">Settle Payment</button>
                        </div>
                    </div>
                    <div class="table-wrapper">
                        <form action="{{ route('orders_settlement_action') }}" method="POST" id="settle_form">
                            @csrf
                            <table class="example table">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="d-flex align-items-center column-gap-1">
                                                <input type="checkbox" class="selectAll">
                                                <span>All</span>
                                            </div>
                                        </th>
                                        <th>Vendor Name</th>
                                        <th>Order ID</th>
                                        <th>Order Date</th>
                                        <th>Amount</th>
                                        <th>Settlement</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($list as $orderId => $orderProducts)
                                        @foreach ($orderProducts as $product)
                                            <tr>
                                                <td>
                                                    @if ($product->settlement_status == 'pending')
                                                        <input type="checkbox" name="product_ids[]"
                                                            value="{{ $product->id }}" class="checkbox">
                                                    @endif
                                                </td>
                                                <td>{{ $product->product->vendor->name }}</td>
                                                <td>{{ $product->order->order_id }}</td>
                                                <td>{{ \Carbon\Carbon::parse($product->order->created_at)->format('d-m-Y') }}
                                                </td>
                                                <td>₹ {{ number_format($product->base_price, 2) }}</td>
                                                <td>
                                                    <span id="statusText"
                                                        class="text-capitalize">{{ $product->settlement_status == 'completed' ? 'Settled' : 'Pending' }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a
                                                            href="{{ route('orders_detail', ['id' => $product->order->id]) }}">
                                                            <i class="fas fa-external-link"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.toaster')

    <!-- script -->
    @include('admin.cdn_script')

    <!-- DataTables List -->
    <!-- DataTables + Filters + Checkbox Logic -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ✅ Initialize DataTable once
            const table = $('.example').DataTable({
                paging: true,
                searching: true,
                ordering: false,
                bDestroy: true,
                info: false,
                responsive: true,
                pageLength: 15,
                dom: '<"top"f>rt<"bottom"lp><"clear">'
            });

            // ===============================
            // 🔹 Column Search Filter
            // ===============================
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

            // ===============================
            // 🔹 Checkbox Selection
            // ===============================
            function toggleMainPayNowButton() {
                if ($('.checkbox:checked').length >= 1) {
                    $('.listbtn').show();
                } else {
                    $('.listbtn').hide();
                }
            }

            $('.selectAll').on('change', function() {
                $('.checkbox').prop('checked', this.checked).trigger('change');
                toggleMainPayNowButton();
            });

            $('.checkbox').on('change', function() {
                $('.selectAll').prop('checked', $('.checkbox:checked').length === $('.checkbox').length);
                toggleMainPayNowButton();
            });

            $('.listbtn').hide();

            // ===============================
            // 🔹 Date + Vendor Filters
            // ===============================
            const fromDateInput = document.getElementById("fromdate");
            const toDateInput = document.getElementById("todate");
            const vendorSelect = document.getElementById("vendor");

            // Custom filter that applies globally (all pages)
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const fromDateVal = fromDateInput.value ? new Date(fromDateInput.value) : null;
                const toDateVal = toDateInput.value ? new Date(toDateInput.value) : null;
                const selectedVendor = vendorSelect.value ? vendorSelect.value.trim().toLowerCase() : '';

                const vendorName = data[1].trim().toLowerCase();
                const dateText = data[3].trim();

                const [day, month, year] = dateText.split("-");
                const orderDate = new Date(`${year}-${month}-${day}`);

                let show = true;
                if (fromDateVal && orderDate < fromDateVal) show = false;
                if (toDateVal && orderDate > toDateVal) show = false;
                if (selectedVendor && vendorName !== selectedVendor) show = false;

                return show;
            });

            // Build Vendor dropdown from all rows
            function updateVendorOptions() {
                const allData = table.data().toArray();
                const vendors = new Set();
                allData.forEach(row => vendors.add(row[1].trim().toLowerCase()));

                vendorSelect.innerHTML = '<option value="" selected disabled>Select Vendor</option>';
                vendors.forEach(vendor => {
                    const option = document.createElement("option");
                    option.value = vendor;
                    option.textContent = vendor.charAt(0).toUpperCase() + vendor.slice(1);
                    vendorSelect.appendChild(option);
                });
            }

            // Run once after table loads
            updateVendorOptions();

            // Trigger filter on change
            [fromDateInput, toDateInput, vendorSelect].forEach(el => {
                el.addEventListener("change", function() {
                    table.draw();
                });
            });
        });
    </script>

    <script>
        function settle() {
            document.getElementById('settle_form').submit();
        }
    </script>

</body>

</html>
