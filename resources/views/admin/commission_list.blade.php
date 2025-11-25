<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onstru | Commissions</title>

    <!-- Stylesheets CDN -->
    @include('admin.cdn_style')

    <!-- Stylesheet -->
    <link rel="stylesheet" href="assets/css/admin/form.css">

</head>
{{-- @dd($items) --}}

<body>
    <div class="main">

        <!-- aside -->
        @include('admin.aside')

        <div class="body-main">

            <!-- Navbar -->
            @include('admin.navbar')

            <div class="main-div px-4 mb-3">
                <div class="body-head mb-3">
                    <h4>Add Commissions</h4>
                </div>

                <form action="{{ route('add-commission') }}" method="POST" class="form">
                    @csrf
                    <div class="container-fluid form-div">
                        <div class="row">
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <label for="category">Product Category <span>*</span></label>
                                <select name="category_id" id="category" class="form-select" required>
                                    <option value="" disabled selected>Select Product Category</option>
                                    @foreach ($category as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <label for="percentage">Percentage <span>*</span></label>
                                <input type="number" name="commission" id="percentage" class="form-control" min="0" step="0.01"
                                    required>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mt-auto mb-3">
                                <button type="submit" class="formbtn">Add</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div>
                    <div class="body-head mt-3">
                        <h4>Commissions List</h4>
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
                                        <th>Product Category</th>
                                        <th>Percentage</th>
                                        <th>Status</th>
                                        <th>Created On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($commissions as $commission)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $commission->categoryRelation->value ?? '-' }}</td>
                                            <td>{{ $commission->commission ?? '-' }}</td>
                                            <td>
                                                @if ($commission->status == 'active')
                                                    <span class="text-capitalize text-success">{{ $commission->status ?? '-' }}</span>
                                                @elseif($commission->status == 'inactive')
                                                    <span class="text-capitalize text-danger">{{ $commission->status ?? '-' }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $commission->created_at->format('d M Y') ?? '-' }}</td>
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

    @include('admin.toaster')

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
            "pageLength": 20,
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