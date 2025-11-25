<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Onstru | Product Details</title>

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
                    <h4 class="m-0">Product Details</h4>
                </div>

                <div class="mt-3 profile-card">
                    <div class="cards mb-2">
                        <h6 class="mb-1">Product Name</h6>
                        <h5 class="mb-0">{{ $product->name ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Brand Name</h6>
                        <h5 class="mb-0">{{ $product->brand_name ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Category</h6>
                        <h5 class="mb-0">{{ $product->categoryRelation->value ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Maximum Delivery Days</h6>
                        <h5 class="mb-0">{{ $product->d_days ?? '-' }} days</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Maximum Distance (km)</h6>
                        <h5 class="mb-0">{{ $product->d_km ?? '-' }} days</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Availability</h6>
                        <h5 class="mb-0">{{ $product->availability ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">MRP / SP</h6>
                        <h5 class="mb-0">{{ $product->mrp ?? '-' }} / {{ $product->sp ?? '-' }}</h5>
                    </div>
                    <!-- <div class="cards mb-2">
                        <h6 class="mb-1">Selling Price (SP)</h6>
                        <h5 class="mb-0"></h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Tax Percentage</h6>
                        <h5 class="mb-0"></h5>
                    </div> -->
                    <div class="cards mb-2">
                        <h6 class="mb-1">Base Price / Tax %</h6>
                        <h5 class="mb-0">{{ $product->base_price ?? '-' }} / {{ $product->tax_percentage ?? '-' }}
                        </h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Product Unit</h6>
                        <h5 class="mb-0">{{ $product->product_unit ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Cashback Price</h6>
                        <h5 class="mb-0">{{ $product->cashback_price ?? '-' }}</h5>
                    </div>
                    {{-- <div class="cards mb-2">
                        <h6 class="mb-1">Cashback Percentage</h6>
                        <h5 class="mb-0">{{ $product->cashback_percentage ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Shipping Method</h6>
                        <h5 class="mb-0">{{ $product->ship_method ?? '-' }}</h5>
                    </div> --}}
                    <div class="cards mb-2">
                        <h6 class="mb-1">MOQ</h6>
                        <h5 class="mb-0">{{ $product->moq ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Key Features</h6>
                        <h5 class="mb-0">{{ $product->key_feature ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Description</h6>
                        <h5 class="mb-0">{{ $product->description ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Created By</h6>
                        <h5 class="mb-0">{{ $product->vendor->name ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Listing Charge</h6>
                        <h5 class="mb-0">₹ {{ $productListing->amount ?? '0' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Highlighted</h6>
                        @if ($product->highlighted == 0)
                            <h5 class="mb-0">No</h5>
                        @elseif ($product->highlighted == 1)
                            <h5 class="mb-0">Yes</h5>
                        @endif
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Admin Approval</h6>
                        @if ($product->approvalstatus == 'pending')
                            <h5 class="mb-0 text-warning text-capitalize">{{ $product->approvalstatus }}</h5>
                        @elseif ($product->approvalstatus == 'approved')
                            <h5 class="mb-0 text-success text-capitalize">{{ $product->approvalstatus }}</h5>
                        @elseif ($product->approvalstatus == 'rejected')
                            <h5 class="mb-0 text-danger text-capitalize">{{ $product->approvalstatus }}</h5>
                        @endif
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Admin Remarks</h6>
                        <h5 class="mb-0">{{ $product->remark ?? '' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Cover Image</h6>
                        <h5 class="mb-0" data-bs-toggle="tooltip" data-bs-title="View Image">
                            <a href="{{ asset($product->cover_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $product->cover_img : 'assets/images/NoImage.png') }}"
                                data-fancybox="product">
                                <i class="fas fa-image"></i>
                            </a>
                        </h5>
                    </div>
                    @if (!empty($product->decoded_images))
                        <div class="cards mb-2">
                            <h6 class="mb-1">Gallery Images</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach ((array) $product->decoded_images as $imagePath)
                                    <h5 class="mb-0 {{ $loop->iteration == 1 ? '' : 'd-none' }}" data-bs-toggle="tooltip"
                                        data-bs-title="View Image">
                                        <a href="{{ $imagePath ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $imagePath : 'assets/images/NoImage.png' }}"
                                            data-fancybox="product">
                                            <i class="fas fa-image"></i>
                                        </a>
                                    </h5>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="cards mb-2">
                        <h6 class="mb-1">Shipping Charge</h6>
                        @if ($product->ship_charge && is_array(json_decode($product->ship_charge)))
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="py-1">Quantity</th>
                                        <th class="py-1">Price (per km)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (json_decode($product->ship_charge) as $charge)
                                        <tr>
                                            <td class="py-1">{{ $charge->from }} - {{ $charge->to }}</td>
                                            <td class="py-1">{{ $charge->price }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <h5 class="mb-0">No Shipping Charge Available</h5>
                        @endif
                    </div>
                </div>

                <div class="body-head mt-3">
                    <h4 class="m-0">Vendor Details</h4>
                </div>

                <div class="mt-3 profile-card">
                    <div class="cards mb-2">
                        <h6 class="mb-1">Name</h6>
                        <h5 class="mb-0">{{ $product->vendor->name ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Username</h6>
                        <h5 class="mb-0 text-lowercase">{{ $product->vendor->user_name ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Gender</h6>
                        <h5 class="mb-0">{{ $product->vendor->gender ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Contact</h6>
                        <h5 class="mb-0">{{ $product->vendor->number ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Email</h6>
                        <h5 class="mb-0">{{ $product->vendor->email ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">GST Number</h6>
                        <h5 class="mb-0">{{ $product->vendor->gst->gst_number ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Address</h6>
                        <h5 class="mb-0">{{ $product->vendor->address ?? '-' }}</h5>
                    </div>
                </div>

                <div class="body-head mt-3">
                    <h4 class="m-0">Hub Details</h4>
                </div>

                <div class="mt-3 profile-card">
                    <div class="cards mb-2">
                        <h6 class="mb-1">Hub Name</h6>
                        <h5 class="mb-0">{{ $product->hub->hub_name ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Address</h6>
                        <h5 class="mb-0">{{ $product->hub->address ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Town/City</h6>
                        <h5 class="mb-0">{{ $product->hub->city ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">State</h6>
                        <h5 class="mb-0">{{ $product->hub->state ?? '-' }}</h5>
                    </div>
                    <div class="cards mb-2">
                        <h6 class="mb-1">Pincode</h6>
                        <h5 class="mb-0">{{ $product->hub->pincode ?? '-' }}</h5>
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
                                @foreach ($productHighlighting as $products)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $products->click ?? '0' }}</td>
                                        <td>₹ {{ $products->amount ?? '0' }}</td>
                                        <td>{{ $products->created_at->format('d-m-Y') ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center column-gap-2">
                                                <a href="{{ url('product-click-bill', ['id' => $products->id]) }}"
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