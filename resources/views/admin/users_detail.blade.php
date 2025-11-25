<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Onstru | User Details</title>

    <!-- Stylesheets CDN -->
    @include('admin.cdn_style')

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
                <div class="body-head">
                    <h4 class="m-0">User Details</h4>
                </div>

                <div class="mt-3 profile-card">
                    <div class="cards mb-2">
                        <h6 class="mb-1">Name</h6>
                        <div class="d-flex align-items-center column-gap-2">
                            <img src="{{ asset($users->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $users->profile_img : 'assets/images/Avatar.png') }}"
                                height="25px" width="25px" class="avatar" alt="">
                            <h5 class="mb-0">{{ $users->name ?? '-' }}</h5>
                        </div>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Username</h6>
                        <h5 class="mb-0">{{ $users->user_name ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">You Are</h6>
                        <h5 class="mb-0">{{ $users->you_are ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">As A</h6>
                        <h5 class="mb-0">{{ $users->as_a ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Type Of</h6>
                        <h5 class="mb-0">{{ $users->typeOf->value ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Bio</h6>
                        <h5 class="mb-0">{{ $users->bio ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Gender</h6>
                        <h5 class="mb-0">{{ $users->gender ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Email ID</h6>
                        <h5 class="mb-0">{{ $users->email ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Contact Number</h6>
                        <h5 class="mb-0">+91 {{ $users->number ?? '-' }}</h5>
                    </div>
                </div>

                @if ($users->you_are === 'Business')
                    <div class="body-head mt-3">
                        <h4 class="m-0">GST Details</h4>
                    </div>

                    <div class="mt-3 profile-card">
                        <div class="cards mb-2">
                            <h6 class="mb-1">GST Number</h6>
                            <h5 class="mb-0">{{ $gst->gst_number ?? '-' }}</h5>
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">Business Legal Name</h6>
                            <h5 class="mb-0">{{ $gst->business_legal ?? '-' }}</h5>
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">GST Contact Number</h6>
                            <h5 class="mb-0">{{ $gst->contact_no ?? '-' }}</h5>
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">GST Email ID</h6>
                            <h5 class="mb-0">{{ $gst->email_id ?? '-' }}</h5>
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">PAN Number</h6>
                            <h5 class="mb-0">{{ $gst->pan_no ?? '-' }}</h5>
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">Registrastion Date</h6>
                            <h5 class="mb-0">{{ $gst->register_date ?? '-' }}</h5>
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">Nature Business</h6>
                            <h5 class="mb-0">{{ $gst->nature_business ?? '-' }}</h5>
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">GST Address</h6>
                            <h5 class="mb-0">{{ $gst->gst_address ?? '-' }}</h5>
                        </div>
                        <div class="cards mb-2">
                            <h6 class="mb-1">Annual Turnover</h6>
                            <h5 class="mb-0">{{ $gst->annual_turnover ?? '-' }}</h5>
                        </div>
                    </div>
                @endif

                @if ($users->as_a == 'Vendor' || count($badges) > 0)
                    <div class="body-head my-3">
                        <h4 class="m-0">Badges</h4>
                    </div>

                    <div class="container-fluid listtable">
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
                                        <th>Badge</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($badges as $badge)
                                        <tr id="{{ $badge->id }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if ($badge->badge == '5')
                                                    Titan Seller
                                                @elseif ($badge->badge == '10')
                                                    Crown Seller
                                                @elseif ($badge->badge == '15')
                                                    Empire Seller
                                                @endif
                                            </td>
                                            <td>₹ {{ $badge->amount * 1.18 ?? '-' }}</td>
                                            <td>{{ $badge->created_at->format('d-m-Y') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center column-gap-2">
                                                    <a href="{{ url('badges-bill', ['id' => $badge->id]) }}" target="_blank" data-bs-toggle="tooltip" data-bs-title="Print Invoice">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
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

</body>

</html>