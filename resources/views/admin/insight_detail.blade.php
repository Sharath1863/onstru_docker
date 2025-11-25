<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Onstru | Insight Details</title>

    <!-- Stylesheets CDN -->
    @include('admin.cdn_style')

    <link rel="stylesheet" href="{{ asset('assets/css/admin/list.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/profile.css') }}">

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
                    <h4 class="m-0">Insight Details</h4>
                    @if ($insights->first()->type == 'post')
                        @if ($post->status == 'active')
                        <form action="{{ route('deactivatePost') }}" method="POST">
                            @csrf
                            <input type="hidden" name="post_id" value="{{ $insights->first()->f_id }}">
                            <button class="redbtn">Deactivate</button>
                        </form>
                        @else
                            <form action="{{ route('activatePost') }}" method="POST">
                                @csrf
                                <input type="hidden" name="post_id" value="{{ $insights->first()->f_id }}">
                                <button class="greenbtn">Activate</button>
                            </form>
                        @endif
                    @endif
                    @if ($insights->first()->type == 'user')
                        @if ($user->status == 'active')
                            <form action="{{ route('deactivateUser') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $insights->first()->f_id }}">
                                <button class="redbtn">Deactivate</button>
                            </form>
                        @else
                            <form action="{{ route('activateUser') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $insights->first()->f_id }}">
                                <button class="greenbtn">Activate</button>
                            </form>
                        @endif
                    @endif
                </div>

                @if ($insights->first()->type == 'post')
                    <!-- Post Details -->
                    <div class="mt-3 profile-card">
                        <div class="cards mb-2">
                            <h6 class="mb-1">Created User</h6>
                            <div class="d-flex align-items-center column-gap-2">
                                <img src="{{ asset($post->user->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $post->user->profile_img : 'assets/images/Avatar.png') }}"
                                    height="25px" width="25px" class="avatar" alt="">
                                <h5 class="mb-0">{{ $post->user->name ?? '-' }}</h5>
                            </div>
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">Type</h6>
                            @if ($post->file_type == 'image')
                                <h5 class="mb-0">Post</h5>
                            @elseif ($post->file_type == 'video')
                                <h5 class="mb-0">Reel</h5>
                            @endif
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">Caption</h6>
                            <h5 class="mb-0">{{ $post->caption ?? '-' }}</h5>
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">Location</h6>
                            <h5 class="mb-0">{{ $post->location ?? '-' }}</h5>
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">Created On</h6>
                            <h5 class="mb-0">{{ $post->created_at->format('d M Y') ?? '-' }}</h5>
                        </div>
                        @if ($post->file_type == 'image')
                            <div class="cards mb-2">
                                <h6 class="mb-1">Post</h6>
                                @foreach ((array) $post->file as $imagePath)
                                    <h5 class="mb-0 {{ $loop->iteration === 1 ? '' : 'd-none' }}" data-bs-toggle="tooltip"
                                        data-bs-title="View Post">
                                        <a href="{{ asset($imagePath ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $imagePath : 'assets/images/NoImage.png') }}"
                                            data-fancybox="post">
                                            <i class="fas fa-image"></i>
                                        </a>
                                    </h5>
                                @endforeach
                            </div>
                        @elseif ($post->file_type == 'video')
                            <div class="cards mb-2">
                                <h6 class="mb-1">Reel</h6>
                                @foreach ((array) $post->file as $imagePath)
                                    <h5 class="mb-0" data-bs-toggle="tooltip" data-bs-title="View Reel">
                                        <a href="{{ asset($imagePath ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $imagePath : 'assets/images/NoImage.png') }}"
                                            data-fancybox="reel">
                                            <i class="fas fa-video"></i>
                                        </a>
                                    </h5>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @elseif ($insights->first()->type == 'user')
                    <!-- User Details -->
                    <div class="mt-3 profile-card">
                        <div class="cards mb-2">
                            <h6 class="mb-1">Profile User</h6>
                            <div class="d-flex align-items-center column-gap-2">
                                <img src="{{ asset($user->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $user->profile_img : 'assets/images/Avatar.png') }}"
                                    height="25px" width="25px" class="avatar" alt="">
                                <h5 class="mb-0">{{ $user->name ?? '-' }}</h5>
                            </div>
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">Username</h6>
                            <h5 class="mb-0">{{ $user->user_name ?? '-' }}</h5>
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">You Are</h6>
                            <h5 class="mb-0">{{ $user->you_are ?? '-' }}</h5>
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">As A</h6>
                            <h5 class="mb-0">{{ $user->as_a ?? '-' }}</h5>
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">Contact Number</h6>
                            <h5 class="mb-0">+91 {{ $user->number ?? '-' }}</h5>
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">Location</h6>
                            <h5 class="mb-0">{{ $user->user_location->value ?? '-' }}</h5>
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">Status</h6>
                            <h5 class="mb-0">
                                @if ($user->status == 'active')
                                    <span class="text-success text-capitalize">{{ $user->status ?? '-' }}</span>
                                @else
                                    <span class="text-danger text-capitalize">{{ $user->status ?? '-' }}</span>
                                @endif
                            </h5>
                        </div>
                    </div>
                @endif

                <div class="body-head my-3">
                    <h4 class="m-0">Reported Users</h4>
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
                                    <th>User</th>
                                    <th>Reason</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($insights as $report)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center column-gap-2">
                                                <img src="{{ asset($report->reporter->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $report->reporter->profile_img : 'assets/images/Avatar.png') }}"
                                                    height="25px" width="25px" class="avatar" alt="">
                                                <span>{{ $report->reporter->name ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $report->message ?? '-' }}</td>
                                        <td>{{ $report->created_at->format('d M Y') ?? '-' }}</td>
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