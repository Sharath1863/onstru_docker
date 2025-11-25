@extends('layouts.app')

@section('title', 'Onstru | Edit Products')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.css') }}">

    <style>
        .form-div {
            border: 1px solid var(--border);
            border-radius: 8px;
        }

        @media screen and (max-width: 767px) {

            .col-md-8,
            .col-md-4 {
                padding-inline: 0px !important;
            }
        }
    </style>

    @php
        $images = json_decode($products->image, true);
        $specs = json_decode($products->specifications, true);
        $trans = json_decode($products->ship_charge, true);
    @endphp

    <div class="container main-div">

        <div class="body-head my-3">
            <h5>Edit Product</h5>
        </div>

        <form action="{{ url('update-product/' . $products->id) }}" method="POST" enctype="multipart/form-data"
            id="form_input">
            <div class="row m-0">
                @csrf
                @method('PUT')

                <div class="col-sm-12 col-md-8 ps-0">
                    <div class="col-12 form-div py-2 row mx-0 mb-2">
                        <div class="col-sm-12 col-md-6 mb-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <label for="hub_id">Hub Location <span>*</span></label>
                                <button type="button" class="listtdbtn mb-1 px-1" data-bs-toggle="modal"
                                    data-bs-target="#addHub">Add Hub</button>
                            </div>
                            <select class="form-select" name="hub_id" id="hub_id" required>
                                <option value="" disabled>Select Hub Location</option>
                                @foreach ($hubs as $loc)
                                    <option value="{{ $loc->id }}"
                                        {{ $products->hub_id == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->hub_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="category">Category <span>*</span></label>
                            {{-- <select class="form-select" name="category" id="category" required>
                                <option value="" selected disabled>Select Category</option>
                                @foreach ($category as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ $products->category == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->value }}
                                    </option>
                                @endforeach
                            </select> --}}
                            <input type="text" class="form-control" name="category" id="category"
                                value="{{ $products->categoryRelation->value }}" readonly>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="name">Product Name <span>*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ $products->name }}" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="brandname">Brand Name <span>*</span></label>
                            <input type="text" class="form-control" id="brandname" name="brand_name"
                                value="{{ $products->brand_name }}" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="deltimefrom">Maximum Days To Delivery <span>*</span></label>
                            <input type="number" class="form-control number" name="d_days" id="deltimefrom"
                                value="{{ $products->d_days }}" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="deltimeto">Maximum Distance You Can Deliver (km) <span>*</span></label>
                            <input type="number" class="form-control number" name="d_km" id="deltimeto"
                                value="{{ $products->d_km }}" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="dimensize">Dimension / Size <span>*</span></label>
                            <input type="text" class="form-control" name="size" id="dimensize"
                                value="{{ $products->size }}" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="hsn">HSN Code <span>*</span></label>
                            <input type="text" class="form-control" name="hsn" id="hsn"
                                value="{{ $products->hsn }}" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="availability">Availability <span>*</span></label>
                            <select class="form-select" name="availability" id="availability" required>
                                <option value="" selected disabled>Select Availability</option>
                                <option value="In Stock" {{ $products->availability == 'In Stock' ? 'selected' : '' }}>
                                    In Stock
                                </option>
                                <option value="Out Of Stock"
                                    {{ $products->availability == 'Out Of Stock' ? 'selected' : '' }}>
                                    Out Of Stock
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="keyfeatures">Key Features <span>*</span></label>
                            <textarea rows="2" class="form-control" name="key_feature" id="keyfeatures">{{ $products->key_feature }}</textarea>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="additional">Additional Details <span>*</span></label>
                            <textarea rows="2" class="form-control" name="description" id="additional">{{ $products->description }}</textarea>
                        </div>
                    </div>

                    <div class="col-12 form-div py-2 row mx-0 mb-2">
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="mrp">MRP <span>*</span></label>
                            <input type="number" class="form-control number" name="mrp" id="mrp"
                                value="{{ $products->mrp }}" required readonly>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="mdd">Base Price <span>*</span></label>
                            <input type="number" class="form-control number" name="base_price" id="mdd"
                                value="{{ $products->base_price }}" required readonly>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="tax">Tax % <span>*</span></label>
                            <select name="tax_percentage" id="tax" class="form-select" required>
                                <option value="" selected disabled>Select Tax Percenntage</option>
                                <option value="5" {{ $products->tax_percentage == 5 ? 'selected' : '' }}>
                                    5%
                                </option>
                                <option value="7" {{ $products->tax_percentage == 7 ? 'selected' : '' }}>
                                    7%
                                </option>
                                <option value="12" {{ $products->tax_percentage == 12 ? 'selected' : '' }}>
                                    12%
                                </option>
                                <option value="18" {{ $products->tax_percentage == 18 ? 'selected' : '' }}>
                                    18%
                                </option>
                                <option value="28" {{ $products->tax_percentage == 28 ? 'selected' : '' }}>
                                    28%
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="sellprice">Selling Price (Tax Included) <span>*</span></label>
                            <input type="number" class="form-control number" name="sp" id="sellprice"
                                value="{{ $products->sp }}" required readonly>
                            <small id="sp-error" style="display:none;">Selling Price cannot be greater than MRP.</small>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="prdtunit">Product Unit <span>*</span></label>
                            <select class="form-select" name="product_unit" id="prdtunit" required>
                                <option value="" selected disabled>Select Product Unit</option>
                                <option value="Pair" {{ $products->product_unit == 'Pair' ? 'selected' : '' }}>
                                    Pair
                                </option>
                                <option value="Piece" {{ $products->product_unit == 'Piece' ? 'selected' : '' }}>
                                    Piece
                                </option>
                                <option value="Bag" {{ $products->product_unit == 'Bag' ? 'selected' : '' }}>
                                    Bag
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="moq">MOQ <span>*</span></label>
                            <input type="number" class="form-control number" name="moq" id="moq"
                                value="{{ $products->moq }}" required>
                        </div>
                        {{-- <div class="col-sm-12 col-md-6 mb-2">
                            <label for="cbkprice">Cashback Price <span>*</span></label>
                            <input type="number" class="form-control number" name="cashback_price" id="cbkprice"
                                value="{{ $products->cashback_price }}" required>
                            <small id="cbkprice-error" class="text-danger" style="display:none;">Cashback Price cannot be
                                more than 10% of MRP.</small>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="cbk">Cashback %</label>
                            <input type="number" class="form-control number" name="cashback_percentage"
                                value="{{ $products->cashback_percentage }}" id="cbk">
                            <small id="cbk-error" class="text-danger" style="display:none;">Cashback % cannot be more
                                than 10%.</small>
                        </div> --}}
                        {{-- <div class="col-sm-12 col-md-6 mb-2">
                            <label for="shipmethod" class="mb-0">Shipping Method <span>*</span></label>
                            <select class="form-select" name="ship_method" id="shipmethod">
                                <option value="" selected disabled>Select Shipping Method</option>
                                <option value="Fixed" {{ $products->ship_method == 'Fixed' ? 'selected' : '' }}>
                                    Fixed
                                </option>
                                <option value="Kg" {{ $products->ship_method == 'Kg' ? 'selected' : '' }}>
                                    Kg / km
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="price" class="mb-0">Shipping Price (Tax Included) <span>*</span></label>
                            <input type="number" class="form-control number" name="ship_charge" id="price"
                                value="{{ $products->ship_charge }}">

                        </div> --}}
                    </div>
                </div>

                <div class="col-sm-12 col-md-4 ps-0">
                    <div class="col-12 form-div py-2 row mx-0 mb-2">
                        <div class="col-sm-12 col-md-12">
                            <div class="d-flex align-items-center justify-content-between">
                                <label for="prdtspec" class="mb-1">Product Specification <span>*</span></label>
                                <button type="button" class="listtdbtn mb-1 px-1" id="specAppend">+ Add</button>
                            </div>

                            <div id="specificContainer">
                                @if (is_array($specs))
                                    @foreach ($specs as $key => $value)
                                        <div class="row specificContainer">
                                            <div class="col-sm-6 col-md-6 mb-2">
                                                <input type="text" class="form-control key-input"
                                                    value="{{ $key }}" placeholder="Name">
                                            </div>
                                            <div class="col-sm-6 col-md-6 mb-2">
                                                <input type="text" class="form-control value-input"
                                                    value="{{ $value }}" placeholder="Spec">
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12 form-div py-2 row mx-0 mb-2">
                        <div class="col-sm-12 col-md-12">
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="mb-1">Transportation Charges (Quantity) <span>*</span></label>
                                <button type="button" class="listtdbtn mb-1 px-1" id="transpAppend">+ Add</button>
                            </div>
                            <div id="transpContainer">
                                {{-- @php $trans = old('transport', $trans ?? []); @endphp --}}
                                @if (!empty($trans) && is_array($trans))
                                    @foreach ($trans as $transp)
                                        <div class="row transpContainer">
                                            <div class="col-sm-4 mb-2">
                                                <input type="number" class="form-control from-input"
                                                    name="transport[0][from]" value="{{ $transp['from'] ?? '' }}"
                                                    placeholder="From" readonly>
                                            </div>
                                            <div class="col-sm-4 mb-2">
                                                <input type="number" class="form-control to-input"
                                                    name="transport[0][to]" value="{{ $transp['to'] ?? '' }}"
                                                    placeholder="To" required>
                                            </div>
                                            <div class="col-sm-4 mb-2">
                                                <input type="number" class="form-control price-input"
                                                    name="transport[0][price]" value="{{ $transp['price'] ?? '' }}"
                                                    placeholder="Price per km" required>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12 form-div py-2 row mx-0 mb-2">
                        <div class="col-sm-12 col-md-12 mb-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <label for="catalogue">Upload Product catlogue</label>
                                @if($products->catlogue)
                                    <button type="button" class="listtdbtn mb-1 px-1"
                                        onclick="openCatalogue('{{ $products->catlogue }}')">
                                        View PDF
                                    </button>
                                @endif
                            </div>
                            <input type="file" name="catlogue" id="catalogue"
                                value="{{ $products->catlogue ?? '' }}" class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="images">Upload Images <span>*</span></label>
                            <div class="col-sm-12 col-md-12 mb-2" id="imgContainer">
                                <div class="mb-2">
                                    <label class="custom-file-upload w-100" for="image1">
                                        <div class="icon mb-1">
                                            <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="25px"
                                                alt="">
                                        </div>
                                        <span class="text-muted">Cover</span>
                                        <input type="file" id="image1" name="cover_img" accept="image/*"
                                            onchange="previewImage(this, 'preview-img-1')">
                                    </label>

                                    {{-- Preview Selected or Existing Image --}}
                                    <img src="{{ 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $products->cover_img }}"
                                        class="rounded-2" width="100%" id="preview-img-1"
                                        style="{{ $products->cover_img ? '' : 'display: none;' }}" alt="">
                                </div>
                                @php
                                    $images = $products->image ? json_decode($products->image, true) : [];
                                @endphp

                                @php
                                    $s3BaseUrl = 'https://onstru-social.s3.ap-south-1.amazonaws.com/';
                                @endphp

                                <div class="mb-2">
                                    <label class="custom-file-upload w-100" for="image2">
                                        <div class="icon mb-2">
                                            <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="25px"
                                                alt="">
                                        </div>
                                        <input type="file" id="image2" name="image1" accept="image/*"
                                            onchange="previewImage(this, 'preview-img-2')">
                                    </label>
                                    <img src="{{ isset($images['image1']) ? $s3BaseUrl . $images['image1'] : '' }}"
                                        class="rounded-2" width="100%" id="preview-img-2"
                                        style="{{ isset($images['image1']) ? '' : 'display: none;' }}" alt="">
                                </div>

                                {{-- Image 2 --}}
                                <div class="mb-2">
                                    <label class="custom-file-upload w-100" for="image3">
                                        <div class="icon mb-2">
                                            <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="25px"
                                                alt="">
                                        </div>
                                        <input type="file" id="image3" name="image2" accept="image/*"
                                            onchange="previewImage(this, 'preview-img-3')">
                                    </label>
                                    <img src="{{ isset($images['image2']) ? $s3BaseUrl . $images['image2'] : '' }}"
                                        class="rounded-2" width="100%" id="preview-img-3"
                                        style="{{ isset($images['image2']) ? '' : 'display: none;' }}" alt="">
                                </div>

                                {{-- Image 3 --}}
                                <div class="mb-2">
                                    <label class="custom-file-upload w-100" for="image4">
                                        <div class="icon mb-2">
                                            <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="25px"
                                                alt="">
                                        </div>
                                        <input type="file" id="image4" name="image3" accept="image/*"
                                            onchange="previewImage(this, 'preview-img-4')">
                                    </label>
                                    <img src="{{ isset($images['image3']) ? $s3BaseUrl . $images['image3'] : '' }}"
                                        class="rounded-2" width="100%" id="preview-img-4"
                                        style="{{ isset($images['image3']) ? '' : 'display: none;' }}" alt="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 d-flex gap-2">
                            <button type="submit" class="formbtn w-100">Update Product</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @include('products.hub')

    <!-- Modal for Product Added Successfully -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="staticBackdropLabel">Product Posted Successfully</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-center">
                        <img src="assets/images/job_applied.png" width="120px" alt="">
                    </div>
                    <p class="text-secondary text-center mt-3">Date : 16-07-2025 at 15:11:02</p>
                    <p class="text-center"><a href="products" class="text-secondary f11 ">View Details</a></p>
                    <div class="container d-flex justify-content-around mx-auto">
                        <div class="px-2">
                            <img src="assets/images/hugeicons_copy-link.png" width="35px"
                                class="p-2 rounded-circle bg-light" alt="">
                            <p class="mb-0" style="font-size: 8px;">Copy Link</p>
                        </div>
                        <div class="px-2">
                            <img src="assets/images/facebook_link.png" width="35px" class="p-2 rounded-circle bg-light"
                                alt="">
                            <p class="mb-0" style="font-size: 8px;">Copy Link</p>
                        </div>
                        <div class="px-2">
                            <img src="assets/images/messanger.png" width="35px" class="p-2 rounded-circle "
                                alt="">
                            <p class="mb-0" style="font-size: 8px;">Copy Link</p>
                        </div>
                        <div class="px-2">
                            <img src="assets/images/whatsapp.png" width="35px" class="p-2 rounded-circle bg-light"
                                alt="">
                            <p class="mb-0" style="font-size: 8px;">Copy Link</p>
                        </div>
                        <div class="px-2">
                            <img src="assets/images/email.png" width="35px" class="p-1 rounded-circle bg-light"
                                alt="">
                            <p class="mb-0" style="font-size: 8px;">Copy Link</p>
                        </div>
                        <div class="px-2">
                            <img src="assets/images/threads.png" width="35px" class="p-2 rounded-circle bg-light"
                                alt="">
                            <p class="mb-0" style="font-size: 8px;">Copy Link</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function openCatalogue(filename) {
            if (!filename) {
                alert("Catalogue not available");
                return;
            }
            const url = 'https://onstru-social.s3.ap-south-1.amazonaws.com/' + filename;
            window.open(url, '_blank');
        }
    </script>

    <!-- Select 2 -->
    <script>
        $(document).ready(function() {
            let select2 = ['prdtunit', 'hub_id']
            select2.forEach(ele => {
                $(`#${ele}`).select2({
                    width: "100%",
                    placeholder: "Select Options",
                    allowClear: true,
                });
            });
        });
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

    <!-- Number Input Validation -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputs = document.querySelectorAll(".number");

            inputs.forEach(function(input) {
                input.addEventListener("input", function() {
                    let val = input.value;

                    // Remove anything that's not a digit
                    val = val.replace(/\.(\d+)$/, ".");

                    // Prevent leading zeros like 0005
                    if (val !== "") {
                        val = String(parseInt(val, 10));
                    }

                    input.value = val;
                });
            });
        });
    </script>

    <!-- Cashback Calculation and Validation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mrpInput = document.querySelector('input[name="sp"]');
            const cashbackPriceInput = document.querySelector('input[name="cashback_price"]');
            const cashbackPercentageInput = document.querySelector('input[name="cashback_percentage"]');

            // Calculate cashback % when MRP or cashback price is changed
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

            // Calculate cashback price when MRP or cashback % is changed
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

            // Bind input events
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

            let isCalculating = false; // prevent infinite loops

            // Calculate base from selling price and tax
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

            // Calculate selling price from base price and tax
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
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }
                isSubmitting = true;
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Updating...`;
            });
        });
    </script>

@endsection
