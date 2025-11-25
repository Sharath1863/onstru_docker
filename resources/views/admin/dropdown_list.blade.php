<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onstru | Dropdowns</title>

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
                <div class="body-head mb-3">
                    <h4>Add Dropdowns</h4>
                </div>

                <form action="{{ route('dropdownlist.store') }}" method="POST" id="addForm">
                    @csrf
                    <div class="container-fluid form-div">
                        <div class="row">
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <label for="dropdown_id">Dropdown Category <span>*</span></label>
                                <select name="dropdown_id" id="dropdown_id" class="form-select" required>
                                    <option value="" disabled selected>Select Category</option>
                                    @foreach ($items as $dropdown)
                                        @if ($dropdown->id != 0)
                                            <option value="{{ $dropdown->id }}">{{ $dropdown->dropdowns }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <label for="value">Category Value <span>*</span></label>
                                <input type="text" name="value" id="value" class="form-control" required>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mt-auto mb-3">
                                <button type="submit" class="formbtn addbtn">Add</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div>
                    <div class="body-head mt-3">
                        <h4>Dropdown Values List</h4>
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
                                        <th>Category</th>
                                        <th>List Values</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($values as $index => $val)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $val->category_name }}</td>
                                            <td>{{ $val->value }}</td>
                                            <td>
                                                <button type="button" class="listtdbtn" data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $val->id }}">
                                                    Edit
                                                </button>
                                            </td>
                                        </tr>
                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editModal{{ $val->id }}" tabindex="-1"
                                            data-bs-backdrop="static" data-bs-keyboard="false"
                                            aria-labelledby="modalLabel{{ $val->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <form action="{{ route('dropdown.update', $val->id) }}" method="POST"
                                                    class="editForm{{ $val->id }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="modalLabel{{ $val->id }}">
                                                                Edit Value
                                                            </h4>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body mt-2">
                                                            <div class="col-sm-12">
                                                                <label for="value">Category Value <span>*</span></label>
                                                                <input type="text" name="value" id="value"
                                                                    class="form-control" value="{{ $val->value }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-center my-2">
                                                            <button type="submit" class="modalbtn editbtn">Update</button>
                                                        </div>
                                                    </div>
                                                </form>
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

<!-- Prevent Multiple Submissions -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('addForm');
        const submitBtn = document.querySelector('.addbtn');
        let isSubmitting = false;

        form.addEventListener('submit', function (e) {
            if (isSubmitting) {
                e.preventDefault();
                return;
            }
            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>`;
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('submit', function(e) {
            if (e.target.matches('[class^="editForm"]')) {
                const form = e.target;
                const submitBtn = form.querySelector('.editbtn');

                if (form.dataset.submitting === 'true') {
                    e.preventDefault();
                    return;
                }

                form.dataset.submitting = 'true';
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>`;
            }
        });
    });
</script>

</html>