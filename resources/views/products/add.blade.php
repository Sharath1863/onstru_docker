@extends('layouts.app')

@section('title', 'Onstru | Add Products')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.css') }}">

    <style>
        .form-div {
            border: 1px solid var(--border);
            border-radius: 8px;
        }

        h5 {
            font-weight: var(--fw-lg);
            font-size: 16px;
        }

        h6 {
            font-weight: var(--fw-md);
            font-size: 12px;
        }

        .pac-container {
            z-index: 1099 !important;
        }

        @media screen and (max-width: 767px) {

            .col-md-8,
            .col-md-4 {
                padding-inline: 0px !important;
            }
        }
    </style>

    <div class="container main-div">

        <div class="body-head d-block my-3">
            <h5 class="mb-2">Add Product</h5>
            <marquee behavior="" direction="left">
                <h6 id="commission-fee" style="display: none;">
                    <span class="text-danger">*</span>
                    Vendor Commission Fee - <span id="commission-value">{{ $commision ?? '0' }}%</span> will be deducted
                    when
                    you complete the full process of products.
                    <span class="text-danger">*</span>
                </h6>
            </marquee>
        </div>


        <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data" id="form_input">
            <div class="row m-0">
                @csrf
                <div class="col-sm-12 col-md-8 ps-0">
                    <div class="col-12 form-div py-2 row mx-0 mb-2">
                        <div class="col-sm-12 col-md-6 mb-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <label for="location">Hub Location <span>*</span></label>
                                <button type="button" class="listtdbtn mb-1 px-1" data-bs-toggle="modal"
                                    data-bs-target="#addHub">Add Hub</button>
                            </div>
                            <select class="form-select" name="hub" id="location" required autofocus>
                                <option value="" disabled {{ old('location') ? '' : 'selected' }}>Select Hub Location
                                </option>
                                @foreach ($hubs as $hub)
                                    <option value="{{ $hub->id }}" {{ old('hub') == $hub->id ? 'selected' : '' }}>
                                        {{ $hub->hub_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="category">Category <span>*</span></label>
                            <select class="form-select" name="category" id="category" required>
                                <option value="" disabled {{ old('category') ? '' : 'selected' }}>Select Category
                                </option>
                                @foreach ($category as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('category') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="name">Product Name <span>*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name') }}" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="brandname">Brand Name <span>*</span></label>
                            <input type="text" class="form-control" id="brandname" name="brand_name"
                                value="{{ old('brand_name') }}" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="deltimeto">Maximum Days To Delivery <span>*</span></label>
                            <input type="number" class="form-control number" name="d_days" id="deltimeto"
                                value="{{ old('d_to') }}" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="deltimefrom">Maximum Distance You Can Deliver (km) <span>*</span></label>
                            <input type="number" class="form-control number" name="d_km" id="deltimefrom"
                                value="{{ old('d_from') }}" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="dimensize">Dimension / Size <span>*</span></label>
                            <input type="text" class="form-control" name="size" id="dimensize"
                                value="{{ old('size') }}" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="hsn">HSN Code <span>*</span></label>
                            <input type="text" class="form-control" name="hsn" id="hsn"
                                value="{{ old('hsn') }}" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="availability">Availability <span>*</span></label>
                            <select class="form-select" name="availability" id="availability" required>
                                <option value="" disabled {{ old('availability') ? '' : 'selected' }}>Select
                                    Availability</option>
                                <option value="In Stock" {{ old('availability') == 'In Stock' ? 'selected' : '' }}>In Stock
                                </option>
                                <option value="Out Of Stock" {{ old('availability') == 'Out Of Stock' ? 'selected' : '' }}>
                                    Out Of Stock</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="keyfeatures">Key Features <span>*</span></label>
                            <textarea rows="2" class="form-control" name="key_feature" id="keyfeatures">{{ old('key_feature') }}</textarea>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="additional">Additional Details <span>*</span></label>
                            <textarea rows="2" class="form-control" name="description" id="additional">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="col-12 form-div py-2 row mx-0 mb-2">
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="mrp">MRP <span>*</span></label>
                            <input type="number" class="form-control number" name="mrp" id="mrp"
                                value="{{ old('mrp') }}" step="1" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="mdd">Base Price <span>*</span></label>
                            <input type="text" class="form-control number" name="base_price" id="mdd"
                                value="{{ old('base_price') }}" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="tax">Tax % <span>*</span></label>
                            <select name="tax_percentage" id="tax" class="form-select" required>
                                <option value="" disabled {{ old('tax_percentage') ? '' : 'selected' }}>Select Tax
                                    Percentage</option>
                                @foreach ([5, 7, 12, 18, 28] as $tax)
                                    <option value="{{ $tax }}"
                                        {{ old('tax_percentage') == $tax ? 'selected' : '' }}>{{ $tax }}%
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="sellprice">Selling Price (Tax Included) <span>*</span></label>
                            <input type="number" class="form-control number" name="sp" id="sellprice"
                                value="{{ old('sp') }}" required readonly>
                            <small id="sp-error" style="display:none;">Selling Price cannot be greater than MRP.</small>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="prdtunit">Product Unit <span>*</span></label>
                            <select class="form-select" name="product_unit" id="prdtunit" required>
                                <option value="" disabled {{ old('product_unit') ? '' : 'selected' }}>Select Product
                                    Unit</option>
                                <option value="Pair" {{ old('product_unit') == 'Pair' ? 'selected' : '' }}>Pair</option>
                                <option value="Piece" {{ old('product_unit') == 'Piece' ? 'selected' : '' }}>Piece
                                </option>
                                <option value="Bag" {{ old('product_unit') == 'Bag' ? 'selected' : '' }}>Bag</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="moq">Minimum Order Quantity <span>*</span></label>
                            <input type="text" class="form-control number" name="moq" id="moq"
                                value="{{ old('moq') }}" required>
                        </div>
                        {{-- <div class="col-sm-12 col-md-6 mb-2">
                            <label for="cbkprice">Cashback Price <span>*</span></label>
                            <input type="text" class="form-control number" name="cashback_price" id="cbkprice"
                                value="{{ old('cashback_price') }}" required>
                            <small id="cbkprice-error" class="text-danger" style="display:none;">Cashback Price cannot be
                                more than 10% of MRP.</small>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="cbk">Cashback %</label>
                            <input type="number" class="form-control number" name="cashback_percentage" id="cbk"
                                value="{{ old('cashback_percentage') }}">
                            <small id="cbk-error" class="text-danger" style="display:none;">Cashback % cannot be more
                                than 10%.</small>
                        </div> --}}
                        {{-- <div class="col-sm-12 col-md-6 mb-2">
                            <label for="shipmethod" class="mb-0">Shipping Method <span>*</span></label>
                            <select class="form-select" name="ship_method" id="shipmethod">
                                <option value="" disabled {{ old('ship_method') ? '' : 'selected' }}>Select Shipping
                                    Method</option>
                                <option value="Fixed" {{ old('ship_method') == 'Fixed' ? 'selected' : '' }}>Fixed
                                </option>
                                <option value="Per_unit" {{ old('ship_method') == 'kg' ? 'selected' : '' }}>Per Unit
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="price" class="mb-0">Shipping Price (Tax Included) <span>*</span></label>
                            <input type="number" class="form-control number" name="ship_charge" id="price"
                                value="{{ old('ship_charge') }}">
                        </div> --}}
                    </div>
                </div>

                <!-- Right Side -->
                <div class="col-sm-12 col-md-4 ps-0">
                    <div class="col-12 form-div py-2 row mx-0 mb-2">
                        <div class="col-sm-12 col-md-12">
                            <div class="d-flex align-items-center justify-content-between">
                                <label for="prdtspec" class="mb-1">Product Specification <span>*</span></label>
                                <button type="button" class="listtdbtn px-1 mb-1" id="specAppend">+ Add</button>
                            </div>
                            <div id="specificContainer">
                                @php
                                    $oldSpecs = old('specifications', []);
                                @endphp
                                @if (count($oldSpecs) > 0)
                                    @foreach ($oldSpecs as $key => $value)
                                        <div class="row specificContainer">
                                            <div class="col-sm-6 mb-2">
                                                <input type="text" class="form-control key-input"
                                                    value="{{ $key }}" readonly>
                                            </div>
                                            <div class="col-sm-6 mb-2">
                                                <input type="text" class="form-control value-input"
                                                    name="specifications[{{ $key }}]"
                                                    value="{{ $value }}">
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @for ($i = 0; $i < 2; $i++)
                                        <div class="row specificContainer">
                                            <div class="col-sm-6 mb-2">
                                                <input type="text" class="form-control key-input" placeholder="Name">
                                            </div>
                                            <div class="col-sm-6 mb-2">
                                                <input type="text" class="form-control value-input"
                                                    placeholder="Spec">
                                            </div>
                                        </div>
                                    @endfor
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12 form-div py-2 row mx-0 mb-2">
                        <div class="col-sm-12 col-md-12">
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="mb-1">Transportation Charges (Quantity) <span>*</span></label>
                                <button type="button" class="listtdbtn px-1 mb-1" id="transpAppend">+ Add</button>
                            </div>

                            <div id="transpContainer">
                                <div class="row transpContainer">
                                    <div class="col-sm-4 mb-2">
                                        <input type="number" class="form-control from-input" min="0"
                                            name="transport[0][from]" value="{{ old('moq', 1) }}" placeholder="From"
                                            readonly>
                                    </div>
                                    <div class="col-sm-4 mb-2">
                                        <input type="number" class="form-control to-input" min="0"
                                            name="transport[0][to]" placeholder="To" required>

                                    </div>
                                    <div class="col-sm-4 mb-2">
                                        <input type="number" class="form-control price-input" min="0"
                                            name="transport[0][price]" placeholder="Price per km" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 form-div py-2 row mx-0 mb-2">
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="catalogue">Upload Product Catalogue</label>
                            <input type="file" name="catlogue" id="catalogue" class="form-control"
                                accept=".pdf, .doc, .docx" onchange="validateFile(this)">
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="images">Upload Images <span>*</span></label>
                            <div class="col-sm-12 col-md-12 mb-2" id="imgContainer">
                                @for ($i = 1; $i <= 4; $i++)
                                    <div class="mb-2">
                                        <label class="custom-file-upload w-100" for="image{{ $i }}">
                                            <div class="icon mb-2">
                                                <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="25px"
                                                    alt="">
                                            </div>
                                            @if ($i == 1)
                                                <span class="text-muted">Cover</span>
                                            @endif
                                            <input type="file" id="image{{ $i }}"
                                                name="{{ $i == 1 ? 'cover_img' : 'image' . ($i - 1) }}" accept="image/*"
                                                onchange="previewImage(this, 'preview-img-{{ $i }}')">
                                        </label>
                                        <img src="" class="rounded-2" width="100%"
                                            id="preview-img-{{ $i }}" style="display: none;" alt="">
                                    </div>
                                @endfor
                            </div>
                        </div>
                        <small id="image-error" class="text-danger" style="display:none;">Please upload a cover
                            image.</small>
                    </div>

                    <div class="col-12 form-div py-2 row mx-0 mb-2">
                        <div class="col-sm-12 d-flex align-items-start justify-content-between flex-wrap mb-2">
                            <div>
                                <label>Listing (Included Tax)</label>
                                <h5 class="mb-0 text-muted">₹ {{ $list_charge }}</h5>
                            </div>
                            <div>
                                <label>Wallet</label>
                                <h5 class="mb-0 text-muted">₹ {{ auth()->user()->balance }}</h5>
                                @if ($list_charge > auth()->user()->balance)
                                    <small>Insufficient Balance.</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label>Notes <span>*</span></label>
                            <h6 class="text-muted d-block">Amount will be deducted from the wallet</h6>
                        </div>
                        <div class="col-sm-12 d-flex align-items-center column-gap-2 mb-2">
                            <input type="checkbox" name="agree" id="agree" required>
                            <label class="mb-0" for="agree">Agree to Pay</label>
                        </div>

                        <div class="col-sm-12 d-flex gap-2">
                            <button type="submit"
                                class="formbtn w-100 {{ $list_charge > auth()->user()->balance ? 'd-none' : 'd-block' }}">
                                Add Product
                            </button>
                            @if ($list_charge > auth()->user()->balance)
                                <a href="{{ url('wallet') }}" class="w-100">
                                    <button type="button" class="removebtn w-100">Recharge Now</button>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @include('products.hub')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            const $categorySelect = $('#category');
            const $commissionFeeDiv = $('#commission-fee');
            const $commissionValueSpan = $('#commission-value');

            // Initially hide commission fee
            $commissionFeeDiv.hide();

            $categorySelect.on('change', function() {
                const categoryId = $(this).val();

                if (categoryId) {
                    // Optional: Show a loading message/spinner here
                    $commissionFeeDiv.hide();

                    $.ajax({
                        url: "{{ route('get-commission', ['categoryId' => '__categoryId__']) }}/".replace('__categoryId__', categoryId),
                        type: 'GET',
                        success: function(response) {
                            if (response.success && response.commission) {
                                $commissionValueSpan.text(response.commission + '%');
                                $commissionFeeDiv.show();
                            } else {
                                $commissionFeeDiv.hide();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", status, error);
                            $commissionFeeDiv.hide();
                        }
                    });
                } else {
                    $commissionFeeDiv.hide();
                }
            });
        });
    </script>

    <!-- Select 2 -->
    <script>
        $(document).ready(function() {
            // let select2 = ['prdtunit', 'category', 'location']
            // select2.forEach(ele => {
            //     $(`#${ele}`).select2({
            //         width: "100%",
            //         placeholder: "Select Options",
            //         allowClear: true,
            //     });
            // });
            $('#category').select2({
                width: "100%",
                placeholder: "Select Category",
                allowClear: true,
            });

            // $('#category').on('change', function() {
            //     const categoryId = $(this).val();
            //     alert(categoryId);

            // if (categoryId) {
            //     $.ajax({
            //         url: '/get-commission/' + categoryId, // 👈 your route
            //         type: 'GET',
            //         success: function(response) {
            //             if (response.success && response.commission) {
            //                 $('#commission-value').text(response.commission + '%');
            //                 $('#commission-fee').show();
            //             } else {
            //                 $('#commission-fee').hide();
            //             }
            //         },
            //         error: function(xhr, status, error) {
            //             console.error("AJAX Error:", status, error);
            //             $('#commission-fee').hide();
            //         }
            //     });
            // } else {
            //     $('#commission-fee').hide();
            // }
            // });


        });




        // select2.on('change', function() {
        //     $(this).valid();
        // });
    </script>

    <!-- Image Preview -->
    <script>
        function previewImage(input, previewId) {
            const file = input.files[0];
            const preview = document.getElementById(previewId);

            if (file) {
                // 15 MB in bytes
                const maxSize = 15 * 1024 * 1024;

                if (file.size > maxSize) {
                    showToast("Each image must be less than 15 MB.");
                    input.value = "";
                    preview.src = "";
                    preview.style.display = "none";
                    return;
                }

                if (file.type.startsWith("image/")) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = "block";
                        preview.style.height = "75px";
                        preview.style.objectFit = "cover";
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.src = "";
                    preview.style.display = "none";
                }
            }
        }
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

            if (file.size > 15 * 1024 * 1024) {
                showToast("File size must be less than 15 MB.");
                input.value = "";
                return false;
            }

            return true;
        }
    </script>

    <!-- Specification Append -->
    <script>
        // Add new key-value input rows
        document.getElementById('specAppend').addEventListener('click', function() {
            const newContainer = document.createElement('div');
            newContainer.className = 'row specificContainer align-items-center';

            newContainer.innerHTML = `
                    <div class="col-sm-5 pe-0 mb-2">
                        <input type="text" class="form-control key-input" placeholder="Name" required>
                    </div>
                    <div class="col-sm-5 pe-0 mb-2">
                        <input type="text" class="form-control value-input" placeholder="Spec" required>
                    </div>
                    <div class="col-sm-2 ps-0 mb-2 text-end">
                        <button type="button" class="red-label specRemove">X</button>
                    </div>
                `;

            document.getElementById('specificContainer').appendChild(newContainer);

            // Add event listener to the new remove button
            newContainer.querySelector('.specRemove').addEventListener('click', function() {
                newContainer.remove();
            });
        });

        // On form submit: convert key-value to proper associative input names
        document.querySelector('form').addEventListener('submit', function() {
            const containers = document.querySelectorAll('.specificContainer');

            containers.forEach(container => {
                const keyInput = container.querySelector('.key-input');
                const valueInput = container.querySelector('.value-input');

                const key = keyInput.value.trim();

                if (key !== '') {
                    valueInput.setAttribute('name', `specifications[${key}]`);
                    keyInput.removeAttribute('name');
                }
            });
        });
    </script>

    <!-- Transportation Charges Append -->
    <script>
        let transportIndex = 1;
        document.getElementById('transpAppend').addEventListener('click', function() {
            const container = document.getElementById('transpContainer');
            const lastRow = container.querySelector('.row.transpContainer:last-child');
            let newFromValue = 1;

            if (lastRow) {
                const lastToInput = lastRow.querySelector('.to-input');
                if (lastToInput && lastToInput.value !== "") {
                    newFromValue = parseInt(lastToInput.value, 10) + 1;
                }
            }

            const newRow = document.createElement('div');
            newRow.className = 'row transpContainer align-items-center';
            newRow.innerHTML = `
                <div class="col-sm-3 pe-0 mb-2">
                    <input type="number" class="form-control from-input" min="0" 
                        name="transport[${transportIndex}][from]" readonly>
                </div>
                <div class="col-sm-3 pe-0 mb-2">
                    <input type="number" class="form-control to-input" min="0" 
                        name="transport[${transportIndex}][to]" placeholder="To" required>
                    <small class="text-danger to-error" style="display:none;">"To" must be greater than "From".</small>
                </div>
                <div class="col-sm-4 pe-0 mb-2">
                    <input type="number" class="form-control price-input" min="0" 
                        name="transport[${transportIndex}][price]" placeholder="Price per km" required>
                </div>
                <div class="col-sm-2 ps-0 mb-2 text-end">
                    <button type="button" class="red-label transpRemove">X</button>
                </div>
            `;
            container.appendChild(newRow);
            transportIndex++;

            const toInput = newRow.querySelector('.to-input');
            toInput.addEventListener('input', function() {
                refreshFromInputs();
            });
            newRow.querySelector('.transpRemove').addEventListener('click', function() {
                newRow.remove();
                refreshFromInputs();
            });
            refreshFromInputs();
        });

        function refreshFromInputs() {
            const rows = document.querySelectorAll('.row.transpContainer');
            const moqVal = parseInt(document.getElementById('moq').value || 1, 10);
            rows.forEach((row, index) => {
                const currentFromInput = row.querySelector('.from-input');
                if (index === 0) {
                    currentFromInput.value = moqVal;
                } else {
                    const prevToInput = rows[index - 1].querySelector('.to-input');
                    if (prevToInput && currentFromInput) {
                        const val = prevToInput.value ? parseInt(prevToInput.value, 10) + 1 : '';
                        currentFromInput.value = val;
                    }
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            const moqInput = document.getElementById('moq');
            refreshFromInputs();
            moqInput.addEventListener('input', function() {
                refreshFromInputs();
            });
            const firstToInput = document.querySelector('.row.transpContainer .to-input');
            if (firstToInput) {
                firstToInput.addEventListener('input', function() {
                    refreshFromInputs();
                });
            }
        });
    </script>

    <!-- Number Input Restriction -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputs = document.querySelectorAll(".number");

            inputs.forEach(function(input) {
                input.addEventListener("input", function() {
                    let val = input.value;
                    val = val.replace(/\.(\d+)$/, ".");
                    if (val !== "") {
                        val = String(parseInt(val, 10));
                    }
                    input.value = val;
                });
            });
        });
    </script>

    <!-- Cashback Calculation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mrpInput = document.querySelector('input[name="sp"]');
            const cashbackPriceInput = document.querySelector('input[name="cashback_price"]');
            const cashbackPercentageInput = document.querySelector('input[name="cashback_percentage"]');

            function updatePercentage() {
                const mrp = parseFloat(mrpInput.value);
                const cashback = parseFloat(cashbackPriceInput.value);

                if (!isNaN(mrp) && mrp > 0 && !isNaN(cashback)) {
                    const percent = ((cashback / mrp) * 100).toFixed(0);
                    cashbackPercentageInput.value = percent;
                    cashbackPercentageInput.setCustomValidity('');
                } else {
                    cashbackPercentageInput.value = '';
                }
            }

            function updatePrice() {
                const mrp = parseFloat(mrpInput.value);
                const percentage = parseFloat(cashbackPercentageInput.value);

                if (!isNaN(mrp) && mrp > 0 && !isNaN(percentage)) {
                    const price = ((percentage / 100) * mrp).toFixed(0);
                    cashbackPriceInput.value = price;
                    cashbackPriceInput.setCustomValidity('');
                } else {
                    cashbackPriceInput.value = '';
                }
            }
            mrpInput.addEventListener('input', () => {
                updatePercentage();
                updatePrice();
            });
            cashbackPriceInput.addEventListener('input', () => {
                updatePercentage();
            });
            cashbackPercentageInput.addEventListener('input', () => {
                updatePrice();
            });
        });
    </script>

    <!-- Selling Price and Base Price Calculation -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const spInput = document.getElementById("sellprice");
            const baseInput = document.getElementById("mdd");
            const taxSelect = document.getElementById("tax");

            let isCalculating = false;

            function calculateBaseFromSP() {
                const sp = parseFloat(spInput.value);
                const tax = parseFloat(taxSelect.value);

                if (!isNaN(sp) && !isNaN(tax)) {
                    const base = sp / (1 + tax / 100);
                    isCalculating = true;
                    baseInput.value = base.toFixed(0);
                    isCalculating = false;
                } else {
                    baseInput.value = '';
                }
            }

            function calculateSPFromBase() {
                const base = parseFloat(baseInput.value);
                const tax = parseFloat(taxSelect.value);

                if (!isNaN(base) && !isNaN(tax)) {
                    const sp = base * (1 + tax / 100);
                    isCalculating = true;
                    spInput.value = sp.toFixed(0);
                    isCalculating = false;
                } else {
                    spInput.value = '';
                }
            }

            spInput.addEventListener("input", function() {
                if (!isCalculating) calculateBaseFromSP();
            });
            baseInput.addEventListener("input", function() {
                if (!isCalculating) calculateSPFromBase();
            });
            taxSelect.addEventListener("change", function() {
                if (!isCalculating) {
                    if (spInput.value) {
                        calculateBaseFromSP();
                    } else if (baseInput.value) {
                        calculateSPFromBase();
                    }
                }
            });
        });
    </script>

    <!-- Form Validations -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const mrpInput = document.getElementById("mrp");
            const spInput = document.getElementById("sellprice");
            const spError = document.getElementById("sp-error");

            const cashbackPercentageInput = document.getElementById("cbk");
            const cbkError = document.getElementById("cbk-error");

            const cashbackPriceInput = document.getElementById("cbkprice");
            const cbkpriceError = document.getElementById("cbkprice-error");

            // Validate Selling Price <= MRP
            function validateSP() {
                const mrp = parseFloat(mrpInput.value);
                const sp = parseFloat(spInput.value);
                const submitBtn = document.querySelector('.formbtn');

                if (!isNaN(mrp) && !isNaN(sp)) {
                    if (sp > mrp) {
                        spError.style.display = "block";
                        spInput.setCustomValidity("Selling Price cannot be greater than MRP.");
                        if (submitBtn) submitBtn.disabled = true; // disable button
                    } else {
                        spError.style.display = "none";
                        spInput.setCustomValidity("");
                        if (submitBtn) submitBtn.disabled = false; // enable button
                    }
                } else {
                    spError.style.display = "none";
                    spInput.setCustomValidity("");
                    if (submitBtn) submitBtn.disabled = false;
                }
            }

            // Validate Cashback % ≤ 10
            function validateCashbackPercentage() {
                const cashbackPercent = parseFloat(cashbackPercentageInput.value);

                if (!isNaN(cashbackPercent)) {
                    if (cashbackPercent > 10) {
                        cbkError.style.display = "block";
                        cashbackPercentageInput.setCustomValidity("Cashback % cannot be more than 10%.");
                    } else {
                        cbkError.style.display = "none";
                        cashbackPercentageInput.setCustomValidity("");
                    }
                } else {
                    cbkError.style.display = "none";
                    cashbackPercentageInput.setCustomValidity("");
                }
            }

            // Validate Cashback Price ≤ 10% of MRP
            function validateCashbackPrice() {
                const cashbackPrice = parseFloat(cashbackPriceInput.value);
                const mrp = parseFloat(mrpInput.value);

                if (!isNaN(cashbackPrice) && !isNaN(mrp)) {
                    if (cashbackPrice > (0.10 * mrp)) {
                        cbkpriceError.style.display = "block";
                        cashbackPriceInput.setCustomValidity("Cashback Price cannot be more than 10% of MRP.");
                    } else {
                        cbkpriceError.style.display = "none";
                        cashbackPriceInput.setCustomValidity("");
                    }
                } else {
                    cbkpriceError.style.display = "none";
                    cashbackPriceInput.setCustomValidity("");
                }
            }

            // Event listeners
            mrpInput.addEventListener("input", () => {
                validateSP();
                validateCashbackPrice();
            });

            spInput.addEventListener("input", validateSP);
            cashbackPercentageInput.addEventListener("input", validateCashbackPercentage);
            cashbackPriceInput.addEventListener("input", validateCashbackPrice);
        });
    </script>

    <!-- Prevent Multiple Submissions -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('form_input');
            const submitBtn = document.querySelector('.formbtn');
            let isSubmitting = false;

            form.addEventListener('submit', function(e) {
                const requiredImageInput = document.getElementById('image1');
                if (!requiredImageInput.files.length) {
                    e.preventDefault();
                    showToast("Product Cover Image is Mandatory. Please Fill all the fields");
                    requiredImageInput.focus();
                    return;
                }
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }
                isSubmitting = true;
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Submitting...`;
            });
        });
    </script>

@endsection
