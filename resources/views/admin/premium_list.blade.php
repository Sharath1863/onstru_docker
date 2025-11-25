<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onstru | Premium Content</title>

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
                    <h4>Add Premium</h4>
                </div>

                <form action="{{ route('premium.store') }}" method="POST" enctype="multipart/form-data" id="addForm">
                    @csrf
                    <div class="container-fluid form-div">
                        <div class="row">
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <label for="type">Premium Type <span>*</span></label>
                                <select class="form-select" name="premium_type" id="type" required>
                                    <option value="" disabled selected>Select Premium Type</option>
                                    <option value="post">Post</option>
                                    <option value="reel">Reel</option>
                                    <option value="blog">Blog</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3" id="post" style="display: none;">
                                <label for="image">Upload Image <span>*</span></label>
                                <input type="file" class="form-control" name="image" id="image" accept="image/*">
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3" id="reel" style="display: none;">
                                <label for="video">Upload Video <span>*</span></label>
                                <input type="file" class="form-control" name="video" id="video" accept="video/*">
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <label for="caption">Caption <span>*</span></label>
                                <textarea rows="1" class="form-control" name="caption" id="caption" required></textarea>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mt-auto mb-3">
                                <button type="submit" class="formbtn addbtn">Add</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div>
                    <div class="body-head mt-3">
                        <h4>Premium List</h4>
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
                                        <th style="width: 50px;">#</th>
                                        <th style="width: 150px;">Premium Type</th>
                                        <th style="width: 75px;">Media</th>
                                        <th style="width: 300px;">Caption</th>
                                        <th style="width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($premiums as $premium)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ ucfirst($premium->premium_type) ?? '-' }}</td>
                                            <td>
                                                @if (strtolower($premium->premium_type) == 'post' && $premium->image)
                                                    <a href="{{ asset($premium->image ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $premium->image : 'assets/images/NoImage.png') }}"
                                                        data-fancybox="post">
                                                        <i class="fas fa-image" data-bs-toggle="tooltip"
                                                            data-bs-title="View Post"></i>
                                                    </a>
                                                @elseif (strtolower($premium->premium_type) == 'reel' && $premium->video)
                                                    <a href="{{ asset($premium->video ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $premium->video : 'assets/images/NoImage.png') }}"
                                                        data-fancybox="reel">
                                                        <i class="fas fa-video" data-bs-toggle="tooltip"
                                                            data-bs-title="View Reel"></i>
                                                    </a>
                                                @else
                                                    <span>-</span>
                                                @endif
                                            </td>
                                            <td>{{ $premium->caption ?? '-' }}</td>
                                            <td>
                                                <button type="button" class="listtdbtn" data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $premium->id }}">Edit</button>
                                            </td>
                                        </tr>

                                        <!-- Edit Modal (inside loop) -->
                                        <div class="modal fade" id="editModal{{ $premium->id }}" tabindex="-1"
                                            data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <form action="{{ route('premium.update', $premium->id) }}" method="POST"
                                                    enctype="multipart/form-data" class="editForm">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4>Edit Premium</h4>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>

                                                        <div class="modal-body mt-2">
                                                            <div class="col-sm-12 mb-2">
                                                                <label>Premium Type <span>*</span></label>
                                                                <select class="form-select edit-type-select"
                                                                    name="premium_type" required>
                                                                    <option value="" disabled>Select Premium Type</option>
                                                                    <option value="post" {{ strtolower($premium->premium_type) == 'post' ? 'selected' : '' }}>Post</option>
                                                                    <option value="reel" {{ strtolower($premium->premium_type) == 'reel' ? 'selected' : '' }}>Reel</option>
                                                                    <option value="blog" {{ strtolower($premium->premium_type) == 'blog' ? 'selected' : '' }}>Blog</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-sm-12 mb-2 edit-post"
                                                                style="{{ strtolower($premium->premium_type) == 'post' ? '' : 'display:none;' }}">
                                                                <label>Upload Image</label>
                                                                @if ($premium->image)
                                                                    <div class="mb-2">
                                                                        <a href="{{ asset($premium->image ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $premium->image : 'assets/images/NoImage.png') }}"
                                                                            data-fancybox="post">
                                                                            <i class="fas fa-image" data-bs-toggle="tooltip"
                                                                                data-bs-title="View Reel"></i>
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                <input type="file" class="form-control" name="image"
                                                                    accept="image/*">
                                                            </div>

                                                            <div class="col-sm-12 mb-2 edit-reel"
                                                                style="{{ strtolower($premium->premium_type) == 'reel' ? '' : 'display:none;' }}">
                                                                <label>Upload Video</label>
                                                                @if ($premium->video)
                                                                    <div class="mb-2">
                                                                        <a href="{{ asset($premium->video ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $premium->video : 'assets/images/NoImage.png') }}"
                                                                            data-fancybox="reel">
                                                                            <i class="fas fa-video" data-bs-toggle="tooltip"
                                                                                data-bs-title="View Reel"></i>
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                <input type="file" class="form-control" name="video"
                                                                    accept="video/*">
                                                            </div>

                                                            <div class="col-sm-12">
                                                                <label>Caption <span>*</span></label>
                                                                <textarea rows="2" class="form-control" name="caption"
                                                                    required>{{ $premium->caption }}</textarea>
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
    $(document).ready(function () {
        $("#type").change(function () {
            var type = $("#type").val();
            var post = $("#post").find("input, select");
            var reel = $("#reel").find("input, select");
            $("#post, #reel").hide();
            post.prop('required', false);
            reel.prop('required', false);
            if (type === "reel") {
                $("#reel").show();
                reel.prop('required', true);
            } else if (type === "post") {
                $("#post").show();
                post.prop('required', true);
            }
        })

        $("#edit_type").change(function () {
            var type = $("#edit_type").val();
            var post = $("#edit_post").find("input, select");
            var reel = $("#edit_reel").find("input, select");
            $("#edit_post, #edit_reel").hide();
            post.prop('required', false);
            reel.prop('required', false);
            if (type === "reel") {
                $("#edit_reel").show();
                reel.prop('required', true);
            } else if (type === "post") {
                $("#edit_post").show();
                post.prop('required', true);
            }
        })
    })
</script>

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
    $(document).on('change', '.edit-type-select', function () {
        var $modal = $(this).closest('.modal');
        var type = $(this).val();
        var $post = $modal.find('.edit-post');
        var $reel = $modal.find('.edit-reel');

        $post.hide().find('input').prop('required', false);
        $reel.hide().find('input').prop('required', false);

        if (type === "reel") {
            $reel.show().find('input').prop('required', true);
        } else if (type === "post") {
            $post.show().find('input').prop('required', true);
        }
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
        const form = document.querySelector('.editForm');
        const submitBtn = document.querySelector('.editbtn');
        let isSubmitting = false;

        form.addEventListener('submit', function(e) {
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

</html>