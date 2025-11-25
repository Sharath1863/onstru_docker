<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Onstru | Service Details</title>

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
                    <h4 class="m-0">Service Details</h4>
                </div>

                <div class="mt-3 profile-card">
                    <div class="cards mb-2">
                        <h6 class="mb-1">Title</h6>
                        <h5 class="mb-0">{{ $service->title ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Service Type</h6>
                        <h5 class="mb-0">{{ $service->serviceType->value ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Price per sqft</h6>
                        <h5 class="mb-0">{{ $service->price_per_sq_ft ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Location</h6>
                        <h5 class="mb-0">{{ $service->locationRelation->value ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Description</h6>
                        <h5 class="mb-0">{{ $service->description ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Created By</h6>
                        <h5 class="mb-0">{{ $service->creator->name ?? '-' }}</h5>
                    </div>
                    @if (!empty($service->decoded_images))
                        <div class="cards mb-2">
                            <h6 class="mb-1">Images</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach ((array) $service->decoded_images as $imagePath)
                                    <h5 class="mb-0 {{ $loop->iteration == 1 ? '' : 'd-none' }}" data-bs-toggle="tooltip"
                                        data-bs-title="View Image">
                                        <a href="{{ $imagePath ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $imagePath : 'assets/images/NoImage.png' }}"
                                            data-fancybox="service">
                                            <i class="fas fa-image"></i>
                                        </a>
                                    </h5>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if ($service->video)
                        <div class="cards mb-2">
                            <h6 class="mb-1">Service Video</h6>
                            <h5 class="mb-0" data-bs-toggle="tooltip" data-bs-title="View Video">
                                <a href="{{ asset($service->video ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $service->video : 'assets/images/NoImage.png') }}"
                                    data-fancybox="service">
                                    <i class="fas fa-video"></i>
                                </a>
                            </h5>
                        </div>
                    @endif
                    <div class="cards mb-2">
                        <h6 class="mb-1">Listing Charge</h6>
                        <h5 class="mb-0">₹ {{ $serviceListing->amount ?? '0' }}</h5>
                    </div>

                    <div class="cards mb-2">
                        <h6 class="mb-1">Highlighted</h6>
                        @if ($service->highlighted == 0)
                            <h5 class="mb-0">No</h5>
                        @elseif ($service->highlighted == 1)
                            <h5 class="mb-0">Yes</h5>
                        @endif
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Admin Approval</h6>
                        @if ($service->approvalstatus == 'pending')
                            <h5 class="mb-0 text-warning text-capitalize">{{ $service->approvalstatus }}</h5>
                        @elseif ($service->approvalstatus == 'approved')
                            <h5 class="mb-0 text-success text-capitalize">{{ $service->approvalstatus }}</h5>
                        @elseif ($service->approvalstatus == 'rejected')
                            <h5 class="mb-0 text-danger text-capitalize">{{ $service->approvalstatus }}</h5>
                        @endif
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Admin Remarks</h6>
                        <h5 class="mb-0">{{ $service->remark ?? '' }}</h5>
                    </div>
                </div>

                <div class="body-head my-3">
                    <h4 class="m-0">Highlight Details</h4>
                </div>

                <!-- Highlighting -->
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
                                    <th>Clicks</th>
                                    <th>Amount</th>
                                    <th>Highlighted On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($serviceHighlighting as $services)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $services->click ?? '0' }}</td>
                                        <td>₹ {{ $services->amount ?? '0' }}</td>
                                        <td>{{ $services->created_at->format('d-m-Y') ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center column-gap-2">
                                                <a href="{{ url('service-click-bill', ['id' => $services->id]) }}"
                                                    target="_blank" data-bs-toggle="tooltip" data-bs-title="Print Invoice">
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