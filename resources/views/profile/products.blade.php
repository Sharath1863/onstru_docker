<div class="body-head mb-3">
    <h5>Products</h5>
    <div class="d-flex align-items-center column-gap-2">
        @if ($gstverified == 'yes')
            <a>
                <button class="removebtn" id="highlightProducts">Highlighted Products</button>
            </a>
            @if (count($products) > 0 || auth()->user()->as_a != 'Vendor')
                <a href="{{ route('add_product') }}">
                    <button class="listbtn">+ Add Product</button>
                </a>
            @elseif (count($products) == 0 || auth()->user()->as_a == 'Vendor')
                <a data-bs-toggle="modal" data-bs-target="#productBadges">
                    <button class="listbtn">+ Add Product</button>
                </a>
            @endif
        @elseif ($gstverified == 'no')
            <a href="{{ url('my-profile') }}">
                <button class="removebtn">Verify GST</button>
            </a>
        @endif
        @if (auth()->user()->as_a == 'Vendor')
            <a data-bs-toggle="modal" data-bs-target="#productBadges">
                <button class="iconbtn"><i class="fas fa-info-circle" data-bs-toggle="tooltip"
                        data-bs-title="About"></i></button>
            </a>
        @endif
    </div>
</div>

<!-- Search -->
<div class="form-div">
    <div class="inpleftflex mb-3">
        <i class="fas fa-search"></i>
        <input type="text" name="keyword" id="productSearch" class="form-control border-0" placeholder="Search"
            value="{{ request('productsKeyword') }}">
    </div>
</div>

<!-- Empty State -->
<div class="side-cards shadow-none border-0" id="noProducts"
    style="{{ count($products) > 0 ? 'display: none;' : 'display: block;' }}">
    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
        <img src="{{ asset('assets/images/Empty/NoProducts.png') }}" height="200px" class="d-flex mx-auto mb-2"
            alt="">
        <h5 class="text-center mb-0">No Products Found</h5>
        <h6 class="text-center bio">No products are available at the moment - try a different search or explore other
            categories.</h6>
    </div>
</div>

<!-- Product Cards -->
<div class="product-cards">
    @foreach ($products as $product)
        <div class="side-cards product-card filter-products position-relative" data-category="{{ $product->category }}"
            data-location="{{ $product->location }}" data-stock="{{ $product->availability }}"
            data-price="{{ $product->price }}" data-size="{{ $product->size }}"
            data-highlight="{{ $product->highlighted }}">
            <img src="{{ asset($product->cover_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $product->cover_img : 'assets/images/NoImage.png') }}"
                class="mb-3 w-100 rounded-3 position-relative object-fit-cover object-center" height="175px"
                alt="">
            @if ($product->highlighted == '1')
                <a href="{{ url('view-product-highlight', $product->id) }}" class="badge d-flex align-items-center">
                    <img src="{{ asset('assets/images/icon_highlights.png') }}" height="15px" class="pe-1"
                        alt="">
                    <span>Highlighted</span>
                </a>
            @endif
            <div class="cards-head">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-2 long-text w-50">{{ $product->name }}</h5>
                    <h6 class="{{ $product->approvalstatus == 'approved' ? 'green-label' : 'yellow-label' }} mb-2">
                        {{ $product->approvalstatus }}
                    </h6>
                </div>
                <h6 class="mb-2 long-text">{{ $product->brand_name ?? '' }} |
                    {{ $product->categoryRelation->value ?? '' }}
                </h6>
            </div>
            <div class="cards-content">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <h6 class="mb-2">
                        <i class="fas fa-star text-warning"></i>
                        {{ number_format($product->reviews_avg_stars, 1) }}
                        ({{ $product->reviews_count }})
                    </h6>
                    {{-- @if ($product->availability === 'In Stock')
                    <h6 class="mb-2 text-success">{{ $product->availability }}</h6>
                    @else
                    <h6 class="mb-2 text-danger">{{ $product->availability }}</h6>
                    @endif --}}
                </div>
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <h6 class="mb-2">₹ {{ $product->sp }} <span class="dashed-line ps-1">₹
                            {{ $product->mrp }}</span>
                    </h6>
                    <h6 class="mb-2"><i class="fas fa-location-dot pe-1"></i>
                        {{ $product->locationRelation->value ?? '-' }}
                    </h6>
                </div>
            </div>
            <div class="row align-items-center justify-content-between">
                <div class="col-3">
                    <a href="{{ url('edit-product/' . $product->id) }}">
                        <button class="iconbtn w-100" data-bs-toggle="tooltip" data-bs-title="Edit Product">
                            <i class="fas fa-pen-to-square"></i>
                        </button>
                    </a>
                </div>
                <div class="col-3 ps-0">
                    <a href="{{ url('individual-product/' . $product->id) }}">
                        <button class="iconbtn w-100" data-bs-toggle="tooltip" data-bs-title="View Product">
                            <i class="fas fa-external-link"></i>
                        </button>
                    </a>
                </div>
                <div class="col-3 ps-0">
                    @if ($product->approvalstatus == 'approved')
                        @if ($product->highlighted == 0)
                            <a data-bs-toggle="modal" data-bs-target="#boostProduct{{ $product->id }}">
                                <button type="button" class="iconbtn w-100" data-bs-toggle="tooltip"
                                    data-bs-title="Highlight">
                                    <i class="far fa-star"></i>
                                </button>
                            </a>
                        @elseif ($product->highlighted == 1)
                            <a href="{{ url('view-product-highlight', $product->id) }}">
                                <button type="button" class="iconbtn w-100 text-warning" data-bs-toggle="tooltip"
                                    data-bs-title="Highlighted">
                                    <i class="fas fa-star"></i>
                                </button>
                            </a>
                        @endif
                    @else
                        <button type="button" class="iconbtn w-100" data-bs-toggle="tooltip"
                            data-bs-title="Wait for Approval">
                            <i class="far fa-star"></i>
                        </button>
                    @endif

                </div>
                <div class="col-3 ps-0">
                    <a>
                        <button class="removebtn priceUpdate w-100" data-product-id="{{ $product->id }}"
                            data-sp="{{ $product->sp }}" data-mrp="{{ $product->mrp }}"
                            data-tax="{{ $product->tax_percentage }}" data-baseprice="{{ $product->base_price }}"
                            data-bs-toggle="tooltip" data-bs-title="Update Price">
                            Price
                        </button>
                    </a>
                </div>
            </div>
        </div>

        <!-- Highlight Modal -->
        <div class="modal fade" id="boostProduct{{ $product->id }}" data-bs-backdrop="static"
            data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('product.highlight') }}" method="POST"
                        class="highlightProductForm{{ $product->id }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title">Highlight Product</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body row mt-2">
                            <input type="hidden" name="product_id" id="boost_product_id"
                                value="{{ $product->id }}">
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="my-2">Highlight / Click : <span class="text-muted">₹
                                        {{ $product_click_charge ?? '0' }} (Included Tax)</span></label>
                                <label class="my-2">Wallet : <span class="text-muted">₹
                                        {{ auth()->user()->balance ?? '0' }}</span></label>
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label>Product Title</label>
                                <h6>{{ $product->name }}</h6>
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label for="product_click">Highlight / Clicks <span>*</span></label>
                                <input type="number" min="5" class="form-control product-click-input""
                                    name=" click" data-id="{{ $product->id }}" id="product_click" required>
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label>Total</label>
                                <h4 class="total-amount" data-product="{{ $product->id }}">₹ 0.00</h4>
                                <small class="balance-message" data-product="{{ $product->id }}"
                                    style="display:none;">Insufficient Balance</small>
                            </div>
                            <div>
                                <label for="video">Video for Reels (Optional)</label>
                                <input type="file" name="video" class="form-control" accept="video/*"
                                    onchange="validateVideo(this)" />
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label>Notes <span>*</span></label>
                                <h6>Amount will be deducted from the wallet</h6>
                            </div>
                            <div class="col-sm-12 mb-2">
                                <div class="d-flex align-items-center column-gap-2">
                                    <input type="checkbox" id="productHighlightCheck" required>
                                    <label for="productHighlightCheck" class="mb-0">Agree To Pay</label>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center column-gap-2 mb-2">
                            <button type="submit" class="higlightProductbtn formbtn">Highlight
                                Product</button>
                            <a href="{{ url('wallet') }}" target="_blank">
                                <button type="button" class="removebtn recharge-button"
                                    data-product="{{ $product->id }}" style="display: none;">
                                    Recharge
                                </button>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Price update Modal -->
<div class="modal fade" id="priceUpdate" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Price</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('update.product.price') }}" method="POST" id="priceUpdateForm">
                @csrf
                <input type="number" name="productId" id="updateProductId" hidden>
                <div class="modal-body row mt-2">
                    <div class="col-sm-12 col-md-6 mb-2">
                        <label for="updateMrp">MRP <span>*</span></label>
                        <input type="number" class="form-control" name="mrp" id="updateMrp" required>
                    </div>
                    <div class="col-sm-12 col-md-6 mb-2">
                        <label for="updateSp">Selling Price <span>*</span></label>
                        <input type="number" class="form-control" name="sp" id="updateSp" required>
                    </div>
                    <div class="col-sm-12 col-md-12 mb-2">
                        <label for="updateBaseprice">Base Price <span>*</span></label>
                        <input type="number" class="form-control" name="baseprice" id="updateBaseprice" required>
                    </div>
                    <label id="priceApprovalNote" style="color: red;">
                        ⚠️ Note: If you update MRP or Base Price more than or less than 20%, this product will need
                        approval from Onstru Admin.
                    </label>

                </div>
                <div class="d-flex align-items-center justify-content-center column-gap-2 mb-2">
                    <button type="submit" class="formbtn updatePricebtn" id="savePriceChanges">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.priceUpdate').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                let sp = parseFloat(this.getAttribute(
                'data-sp')); // use let, since we’ll update it
                let mrp = parseFloat(this.getAttribute(
                'data-mrp')); // use let for dynamic update
                const taxPercentage = parseFloat(this.getAttribute('data-tax'));
                let baseprice = parseFloat(this.getAttribute('data-baseprice'));

                // Set modal values
                const mrpInput = document.querySelector('#updateMrp');
                const baseInput = document.querySelector('#updateBaseprice');
                const spInput = document.querySelector('#updateSp');

                document.querySelector('#updateProductId').value = productId;
                mrpInput.value = mrp;
                spInput.value = sp;
                baseInput.value = baseprice;

                // Calculate allowed ±20% ranges
                const mrpMin = (mrp * 0.8).toFixed(2);
                const mrpMax = (mrp * 1.2).toFixed(2);
                const baseMin = (baseprice * 0.8).toFixed(2);
                const baseMax = (baseprice * 1.2).toFixed(2);

                // Show range info under fields
                let mrpInfo = mrpInput.parentElement.querySelector('.price-range-info');
                let baseInfo = baseInput.parentElement.querySelector('.price-range-info');

                if (!mrpInfo) {
                    mrpInfo = document.createElement('small');
                    mrpInfo.classList.add('price-range-info');
                    mrpInfo.style.display = 'block';
                    mrpInfo.style.color = '#777';
                    mrpInput.insertAdjacentElement('afterend', mrpInfo);
                }

                if (!baseInfo) {
                    baseInfo = document.createElement('small');
                    baseInfo.classList.add('price-range-info');
                    baseInfo.style.display = 'block';
                    baseInfo.style.color = '#777';
                    baseInput.insertAdjacentElement('afterend', baseInfo);
                }

                mrpInfo.textContent = `Range: ${mrpMin} – ${mrpMax}`;
                baseInfo.textContent = `Range: ${baseMin} – ${baseMax}`;

                // Set validation range
                mrpInput.setAttribute('min', mrpMin);
                mrpInput.setAttribute('max', mrpMax);
                baseInput.setAttribute('min', baseMin);
                baseInput.setAttribute('max', baseMax);

                // Highlight red border if price outside ±20%
                function checkApprovalNeed() {
                    const currentMrp = parseFloat(mrpInput.value);
                    const currentBase = parseFloat(baseInput.value);

                    mrpInput.style.borderColor = '';
                    baseInput.style.borderColor = '';

                    if (currentMrp < mrp * 0.8 || currentMrp > mrp * 1.2) {
                        mrpInput.style.borderColor = 'red';
                    }
                    if (currentBase < baseprice * 0.8 || currentBase > baseprice * 1.2) {
                        baseInput.style.borderColor = 'red';
                    }
                }

                // Event listeners for checking
                mrpInput.addEventListener('input', checkApprovalNeed);
                baseInput.addEventListener('input', checkApprovalNeed);

                // 🔁 When SP changes manually
                spInput.addEventListener('input', function() {
                    let updatedSp = parseFloat(this.value);
                    if (!isNaN(updatedSp)) {
                        // If SP exceeds current MRP, auto-update MRP
                        if (updatedSp > mrp) {
                            mrp = updatedSp; // update MRP variable
                            mrpInput.value = updatedSp.toFixed(0);
                        }
                        // Update base price based on new SP
                        let updatedBase = updatedSp / (1 + (taxPercentage / 100));
                        baseInput.value = updatedBase.toFixed(0);
                    }
                });

                // 🔁 When Base Price changes
                baseInput.addEventListener('input', function() {
                    let updatedBase = parseFloat(this.value);
                    if (!isNaN(updatedBase)) {
                        // Calculate new SP
                        let updatedSp = updatedBase * (1 + (taxPercentage / 100));

                        // If SP exceeds MRP → update MRP automatically
                        if (updatedSp > mrp) {
                            mrp = updatedSp;
                            mrpInput.value = updatedSp.toFixed(0);
                        }

                        // Update SP field
                        spInput.value = updatedSp.toFixed(0);
                    }
                });

                // Show modal
                const priceUpdateModal = new bootstrap.Modal(document.getElementById(
                    'priceUpdate'));
                priceUpdateModal.show();
            });
        });
    });
</script>

<!-- Search Filter -->
<script>
    // document.addEventListener('DOMContentLoaded', function() {
    //     document.querySelectorAll('.priceUpdate').forEach(button => {
    //         button.addEventListener('click', function() {
    //             const productId = this.getAttribute('data-product-id');
    //             const sp = parseFloat(this.getAttribute('data-sp')); // Selling Price
    //             const mrp = parseFloat(this.getAttribute('data-mrp')); // MRP
    //             const taxPercentage = parseFloat(this.getAttribute('data-tax')); // Tax Percentage
    //             const baseprice = parseFloat(this.getAttribute('data-baseprice')); // Base Price

    //             // Set the initial values in the modal inputs
    //             document.querySelector('#updateProductId').value = productId;
    //             document.querySelector('#updateMrp').value = mrp;
    //             document.querySelector('#updateSp').value = sp;
    //             document.querySelector('#updateBaseprice').value = baseprice;

    //             // Function to set the min and max values for an input based on its value + 20% (max) and - 20% (min)
    //             function setMinMaxValues(inputId, value) {
    //                 const inputElement = document.querySelector(inputId);
    //                 const parsedValue = parseFloat(value);
    //                 if (!isNaN(parsedValue)) {
    //                     const maxValue = parsedValue * 1.2; // Add 20% to the value for max
    //                     const minValue = parsedValue *
    //                     0.8; // Subtract 20% from the value for min
    //                     inputElement.setAttribute('max', maxValue);
    //                     inputElement.setAttribute('min', minValue);
    //                 }
    //             }

    //             // Set min and max values for MRP, SP, and BasePrice
    //             setMinMaxValues('#updateMrp', mrp);
    //             setMinMaxValues('#updateSp', sp);
    //             setMinMaxValues('#updateBaseprice', baseprice);

    //             // Update the SP or BasePrice when one of them changes
    //             document.querySelector('#updateSp').addEventListener('input', function() {
    //                 let updatedSp = parseFloat(this.value);
    //                 if (!isNaN(updatedSp)) {
    //                     // Ensure SP does not exceed MRP
    //                     if (updatedSp > mrp) {
    //                         updatedSp = mrp; // Reset SP to MRP if it exceeds MRP
    //                         document.querySelector('#updateSp').value = updatedSp
    //                             .toFixed(0);
    //                         showToast('Selling Price cannot exceed MRP');
    //                     }
    //                     // Calculate base price from SP (remove tax)
    //                     let updatedBasePrice = updatedSp / (1 + (taxPercentage / 100));
    //                     document.querySelector('#updateBaseprice').value =
    //                         updatedBasePrice.toFixed(0);
    //                 }
    //             });

    //             document.querySelector('#updateBaseprice').addEventListener('input',
    //         function() {
    //                 let updatedBasePrice = parseFloat(this.value);
    //                 if (!isNaN(updatedBasePrice)) {
    //                     // Calculate SP from Base Price (apply tax)
    //                     let updatedSp = updatedBasePrice * (1 + (taxPercentage / 100));
    //                     // Ensure SP does not exceed MRP
    //                     if (updatedSp > mrp) {
    //                         updatedSp = mrp; // Reset SP to MRP if it exceeds MRP
    //                     }
    //                     document.querySelector('#updateSp').value = updatedSp.toFixed(
    //                     0);

    //                     // Ensure base price is always less than or equal to selling price excluding tax
    //                     let maxBasePrice = updatedSp / (1 + (taxPercentage / 100));
    //                     if (updatedBasePrice > maxBasePrice) {
    //                         updatedBasePrice =
    //                         maxBasePrice; // Reset base price if it exceeds the limit
    //                         document.querySelector('#updateBaseprice').value =
    //                             updatedBasePrice.toFixed(0);
    //                     }
    //                 }
    //             });

    //             // Show the modal
    //             const priceUpdateModal = new bootstrap.Modal(document.getElementById(
    //                 'priceUpdate'));
    //             priceUpdateModal.show();
    //         });
    //     });
    // });

    // Search Filter
    document.addEventListener('DOMContentLoaded', function() {
        const highlightProducts = document.getElementById('highlightProducts');
        const productSearch = document.getElementById('productSearch');
        const noProducts = document.getElementById('noProducts');
        const productCards = document.querySelectorAll('.filter-products');
        let showHighlighted = false;

        productSearch.addEventListener('input', function() {
            let productMatch = false;
            const productsKeyword = this.value.toLowerCase();
            productCards.forEach(card => {
                const cardText = card.textContent.toLowerCase();
                if (cardText.includes(productsKeyword)) {
                    card.style.display = 'block';
                    productMatch = true;
                } else {
                    card.style.display = 'none';
                }
            });
            noProducts.style.display = productMatch ? 'none' : 'block';
        });

        highlightProducts.addEventListener('click', function() {
            showHighlighted = !showHighlighted;
            let productMatch = false;
            productCards.forEach(card => {
                if (!showHighlighted || card.dataset.highlight === "1") {
                    card.style.display = 'block';
                    productMatch = true;
                } else {
                    card.style.display = 'none';
                }
            });
            noProducts.style.display = productMatch ? 'none' : 'block';
            highlightProducts.textContent = showHighlighted ? "All Products" : "Highlighted Products";
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const perClickCharge = parseFloat("{{ $product_click_charge ?? 0 }}") || 0;
        const walletBalance = parseFloat("{{ auth()->user()->balance ?? 0 }}") || 0;

        document.querySelectorAll('.modal').forEach(modal => {
            const clicksInput = modal.querySelector('.product-click-input');
            if (!clicksInput) return;

            const totalEl = modal.querySelector('.total-amount');
            const balanceMsg = modal.querySelector('.balance-message');
            const rechargeBtn = modal.querySelector('.recharge-button');
            const highlightBtn = modal.querySelector('.higlightProductbtn');

            function updateTotal() {
                const clicks = parseInt(clicksInput.value, 10) || 0;
                const total = clicks * perClickCharge;

                if (totalEl) totalEl.textContent = '₹ ' + total.toFixed(2);

                balanceMsg.style.display = "none";
                rechargeBtn.style.display = "none";
                highlightBtn.disabled = false;

                // Min 5 clicks required
                if (clicks > 0 && clicks < 5) {
                    balanceMsg.textContent = "Minimum 5 clicks required";
                    balanceMsg.style.display = "block";
                    highlightBtn.disabled = true;
                    return;
                }

                // Insufficient balance
                if (total > walletBalance) {
                    balanceMsg.textContent = "Insufficient Balance";
                    balanceMsg.style.display = "block";
                    rechargeBtn.style.display = "inline-block";
                    highlightBtn.disabled = true;
                    return;
                }
            }

            clicksInput.addEventListener("input", updateTotal);
            modal.addEventListener("shown.bs.modal", updateTotal);
        });
    });
</script>

<!-- Prevent Multiple Submissions -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('submit', function(e) {
            if (e.target.matches('[class^="highlightProductForm"]')) {
                const form = e.target;
                const submitBtn = form.querySelector('.higlightProductbtn');

                if (form.dataset.submitting === 'true') {
                    e.preventDefault();
                    return;
                }

                form.dataset.submitting = 'true';
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Highlighting...
            `;
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('submit', function(e) {
            if (e.target.matches('[id^="priceUpdateForm"]')) {
                const form = e.target;
                const submitBtn = form.querySelector('.updatePricebtn');

                if (form.dataset.submitting === 'true') {
                    e.preventDefault();
                    return;
                }

                form.dataset.submitting = 'true';
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Updating Price...
            `;
            }
        });
    });
</script>
