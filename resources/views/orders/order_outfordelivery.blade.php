@extends('layouts.app')

@section('title', 'Onstru | Order Status Update')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/timeline.css') }}">

    <div class="container-xl main-div">
        <div class="row">
            <div class="side-cards shadow-none border-0 col-sm-12 col-md-8 mb-2">
                <div class="body-head mb-3">
                    <h5>{{ $orderId }}</h5>
                </div>

                <div class="listtable">
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Selected Quantity</th>
                                    <th>Pending Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $op)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ asset($op->product->cover_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $op->product->cover_img : 'assets/Images/NoImages.png') }}"
                                                    height="50px" width="50px" class="object-fit-cover rounded">
                                                <div>
                                                    <span class="text-capitalize">{{ $op->product->name }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $op->selected_quantity }}</td>
                                        <td>{{ $op->bal_qty - $op->selected_quantity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="side-cards shadow-none border-0 col-sm-12 col-md-4 mb-2">
                <div class="body-head mb-3">
                    <h5>Tracking Details</h5>
                </div>
                <div class="py-2 px-3 border rounded">
                    <div class="form-div">
                        <form action="{{ route('order-tracking-update') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $orderId ?? '' }}">
                            <input type="hidden" name="products" value='@json($products->map(fn($op) => ['product_id' => $op->product->id, 'qty' => $op->selected_quantity]))'>
                            <input type="hidden" name="status" value="shipped">

                            <div class="col-sm-12 mb-2">
                                <label for="">Upload Invoice <span>*</span></label>
                                <input type="file" class="form-control" name="vendor_invoice" id=""
                                    onchange="validateFile(this)" required>
                            </div>

                            <div class="col-sm-12 mb-2">
                                <label for="type">Type <span>*</span></label>
                                <select name="type" id="type" class="form-select" onchange="toggleFields()"
                                    required>
                                    <option value="own_vehicle">By Own Vehicle</option>
                                    <option value="courier">By Courier Service</option>
                                </select>
                            </div>

                            <!-- Own Vehicle Fields -->
                            <div id="ownVehicleFields">
                                <div class="col-sm-12 mb-2">
                                    <label for="vehicle_number">Vehicle Number <span>*</span></label>
                                    <input type="text" name="vehicle_number" id="vehicle_number" class="form-control"
                                        required>
                                </div>

                                <div class="col-sm-12 mb-2">
                                    <label for="driver_name">Driver Name <span>*</span></label>
                                    <input type="text" name="driver_name" id="driver_name" class="form-control" required>
                                </div>

                                <div class="col-sm-12 mb-2">
                                    <label for="driver_number">Driver Mobile No <span>*</span></label>
                                    <input type="number" name="driver_number" id="driver_number" class="form-control"
                                        oninput="validate_contact(this)" min="6000000000" max="9999999999" required>
                                </div>
                            </div>

                            <!-- Courier Fields -->
                            <div id="courierFields" style="display: none;">
                                <div class="col-sm-12 mb-2">
                                    <label for="courier_name">Courier Name <span>*</span></label>
                                    <input type="text" name="courier_name" id="courier_name" class="form-control"
                                        required>
                                </div>

                                <div class="col-sm-12 mb-2">
                                    <label for="tracking_id">Tracking Id <span>*</span></label>
                                    <input type="text" name="tracking_id" id="tracking_id" class="form-control" required>
                                </div>
                            </div>

                            <!-- Estimated Delivery -->
                            <div class="col-sm-12 mb-2">
                                <label for="estdate">Estimated Delivery Date <span>*</span></label>
                                <input type="date" class="form-control" name="estimated_delivery_date" id="estdate"
                                    min="{{ \Carbon\Carbon::now()->addDay()->toDateString() }}"
                                    max="{{ \Carbon\Carbon::now()->addDays(30)->toDateString() }}" required>
                            </div>

                            <div class="d-flex align-items-center justify-content-end">
                                <button class="formbtn">Out for Delivery</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleFields() {
            const type = document.getElementById("type").value;
            const ownVehicleFields = document.getElementById("ownVehicleFields");
            const courierFields = document.getElementById("courierFields");

            // Get all inputs in each section
            const ownInputs = ownVehicleFields.querySelectorAll("input");
            const courierInputs = courierFields.querySelectorAll("input");

            if (type === "own_vehicle") {
                ownVehicleFields.style.display = "block";
                courierFields.style.display = "none";

                ownInputs.forEach(input => input.required = true);
                courierInputs.forEach(input => input.required = false);
            } else {
                ownVehicleFields.style.display = "none";
                courierFields.style.display = "block";

                ownInputs.forEach(input => input.required = false);
                courierInputs.forEach(input => input.required = true);
            }
        }

        // Run once on page load (in case a value is preselected)
        document.addEventListener("DOMContentLoaded", toggleFields);    
    </script>

    <script>
        function validateFile(input) {
            if (input.files.length === 0) return true;
            const file = input.files[0];
            const allowedExtensions = ['pdf', 'doc', 'docx'];
            const ext = file.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(ext)) {
                showToast("Only PDF, DOC, or DOCX files are allowed.");
                input.value = "";
                return false;
            }
            if (file.size > 15 * 1024 * 1024) { // 5MB
                showToast("File size must be less than 15 MB.");
                input.value = "";
                return false;
            }
            return true;
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="{{ route('order-tracking-update') }}"]');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                const submitButton = form.querySelector('button[type="submit"], .formbtn');
                if (submitButton.disabled) {
                    e.preventDefault();
                    return false;
                }

                submitButton.disabled = true;
                submitButton.textContent = 'Processing...';
            });
        });
    </script>


@endsection
