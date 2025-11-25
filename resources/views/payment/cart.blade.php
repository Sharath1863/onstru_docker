@extends('layouts.app')

@section('title', 'Onstru | Cart')

@section('content')
    {{-- @dd(session()->all()); --}}
    {{-- @dd(session('appliedCashbacks')) --}}
    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list.css') }}">
    <style>
        .flex-side {
            grid-template-columns: 70% 28%;
        }

        .flex-sidebar {
            display: block !important;
        }

        @media screen and (min-width: 767px) {
            .distance-group {
                width: 28%;
            }
        }
    </style>

    <div class="container-xl main-div">
        <div class="body-head mb-3">
            <a href="javascript:history.back()">
                <h5><i class="fas fa-angle-left pe-1"></i> Cart</h5>
            </a>
            <div class="input-group form-div distance-group">
                <input type="text" class="form-control" id="userPincode" pattern="^[1-9][0-9]{5}$"
                    oninput="validate_pincode(this)" maxlength="6" style="font-size: 12px;"
                    title="Pincode must be exactly 6 digits" placeholder="Enter your Delivery Pincode">
                <button id="getDistance" class="formbtn">Get Distance</button>
            </div>
        </div>

        @if ($cartItems->count() == 0 && $savedItems->count() == 0)
            <div class="side-cards shadow-none border-0">
                <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                    <img src="{{ asset('assets/images/Empty/NoCart.png') }}" height="200px" class="d-flex mx-auto mb-2"
                        alt="">
                    <h5 class="text-center mb-0">Your Cart Is Empty</h5>
                    <h6 class="text-center bio">Looks like you haven't added anything yet - start exploring and fill your cart
                        with products you'll love.</h6>
                    <a href="{{ url('products') }}">
                        <button class="removebtn">Go to Products</button>
                    </a>
                </div>
            </div>
        @else
            <div class="flex-side">
                <div class="flex-sidebar border-0">
                    <div id="cart-left-container" class="side-cards border-0 p-0">
                        @include('payment.cart-left')
                    </div>
                </div>
                <div id="cart-right-container" class="side-cards second">
                    @include('payment.cart-right')
                </div>
            </div>
        @endif
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // ---------- PINCODE STORAGE ----------
        function savePincode(pincode) {
            sessionStorage.setItem('userPincode', pincode);
        }

        function getSavedPincode() {
            return sessionStorage.getItem('userPincode');
        }

        async function fetchDrivingDistance(hubLat, hubLng, buyerLat, buyerLng) {
            try {
                const response = await fetch('{{ route("distance.calculate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        hubLat,
                        hubLng,
                        buyerLat,
                        buyerLng
                    })
                });

                const data = await response.json();

                if (data.success) {
                    return Math.round(data.distance_km);
                } else {
                    console.error('Driving distance error:', data.message);
                    return Infinity;
                }
            } catch (err) {
                console.error('Fetch error:', err);
                return Infinity;
            }
        }

        async function calculateDistances(pincode) {
            const placeOrderBtn = document.getElementById("placeOrderBtn");
            const notes = document.querySelector(".notes");
            placeOrderBtn.disabled = true;
            notes.style.display = 'block';

            try {
                const apiKey = "AIzaSyAmDotOMSahWwA7WAjmtremPvNNRN1Nye0";
                const url = `https://maps.googleapis.com/maps/api/geocode/json?address=${pincode},IN&key=${apiKey}`;

                const res = await fetch(url);
                const data = await res.json();

                if (data.status !== "OK") {
                    showToast("Unable to find location for this pincode");
                    return;
                }

                const buyerLoc = data.results[0].geometry.location;
                const buyerLat = buyerLoc.lat;
                const buyerLng = buyerLoc.lng;

                let deliverableProducts = {};
                let totalShipping = 0;
                let totalSubtotal = 0;
                let totalCashback = 0;

                const vendorCarts = document.querySelectorAll('.vendor-cart');

                for (const vendorCart of vendorCarts) {
                    const vendorId = vendorCart.dataset.vendorId;
                    let vendorShipping = 0;
                    let vendorSubtotal = 0;

                    const productRows = vendorCart.querySelectorAll('.product-row');

                    for (const row of productRows) {
                        const productId = row.dataset.productId;
                        const hubLat = parseFloat(row.dataset.hubLat) || 0;
                        const hubLng = parseFloat(row.dataset.hubLng) || 0;
                        const perKmCharge = parseFloat(row.dataset.charge) || 0;
                        const maxDistance = parseFloat(row.dataset.maxDistance) || Infinity;
                        const price = parseFloat(row.dataset.price) || 0;
                        const qtyElem = row.querySelector('.qty-text');
                        const qty = qtyElem ? parseInt(qtyElem.textContent) : parseInt(row.dataset.qty || 1);
                        const taxPercent = parseFloat(row.dataset.tax) || 0;

                        const distance = await fetchDrivingDistance(hubLat, hubLng, buyerLat, buyerLng);

                        const deliverable = distance <= maxDistance ? 1 : 0;
                        row.dataset.deliverable = deliverable;

                        let shipping = distance * perKmCharge;

                        if (deliverable) shipping *= (1 + taxPercent / 100);

                        // row.dataset.shipping = shipping.toFixed(2);
                        shipping = Math.round(shipping);
                        row.dataset.shipping = shipping;

                        if (deliverable) {
                            deliverableProducts[productId] = parseFloat(row.dataset.shipping);
                        }

                        // Save deliverable status to backend
                        await fetch("{{ route('cart.store.deliverable') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                deliverables: deliverableProducts,
                                pincode: pincode
                            })
                        });

                        // Update shipping display
                        const shippingCell = row.querySelector('.shipping-cell');
                        if (shippingCell) {
                            shippingCell.innerHTML = deliverable ?
                                `₹ ${shipping.toFixed(0)} <br><span>(${distance.toFixed(1)} km)</span>` :
                                `<span class="text-muted">Not Deliverable</span>`;
                        }

                        // Update deliverable status
                        const deliverableCell = row.querySelector('.deliverable-cell');
                        if (deliverableCell) {
                            deliverableCell.innerHTML = deliverable ?
                                `<span class="text-success">Deliverable</span>` :
                                `<span class="text-danger">Not Deliverable</span>`;
                        }

                        // Disable quantity buttons if not deliverable
                        row.querySelectorAll('.change-qty').forEach(btn => btn.disabled = !deliverable);

                        if (deliverable) {
                            vendorShipping += shipping;
                            vendorSubtotal += price * qty;
                        }
                    }

                    // Update vendor summary
                    const vendorSummary = document.querySelector(`#vendor-summary-${vendorId}`);
                    if (vendorSummary) {
                        const shippingCell = vendorSummary.querySelector('.vendor-shipping');
                        if (shippingCell) shippingCell.textContent = `₹ ${vendorShipping.toFixed(2)}`;

                        const subtotalCell = vendorSummary.querySelector('.vendor-subtotal');
                        if (subtotalCell) {
                            subtotalCell.textContent = `₹ ${vendorSubtotal.toFixed(2)}`;
                            subtotalCell.dataset.value = vendorSubtotal;
                        }

                        const cbElem = vendorSummary.querySelector('.vendor-cashback');
                        let appliedCb = 0;
                        if (cbElem) {
                            appliedCb = parseFloat(cbElem.dataset.value) || 0;
                        }

                        const maxCashback = 0.25 * (vendorSubtotal + vendorShipping);
                        const maxLabel = vendorSummary.querySelector('.cashback-section h6');
                        if (maxLabel) {
                            maxLabel.textContent = `Max CB : ₹ ${maxCashback.toFixed(0)}`;
                        }

                        const appliedSpan = vendorSummary.querySelector(`.applied-cb[data-vendor-id="${vendorId}"]`);
                        if (appliedSpan) {
                            appliedSpan.textContent = appliedCb > 0 ? `₹ ${appliedCb.toFixed(2)}` : '';
                        }

                        totalShipping += vendorShipping;
                        totalSubtotal += vendorSubtotal;
                        totalCashback += appliedCb;
                    }
                }

                // Update totals
                const totalShipCell = document.querySelector('#total-shipping');
                if (totalShipCell) totalShipCell.textContent = `₹ ${totalShipping.toFixed(2)}`;

                const grandTotalCell = document.querySelector('#grand-total');
                if (grandTotalCell) {
                    grandTotalCell.textContent = `₹ ${(totalSubtotal + totalShipping - totalCashback).toFixed(0)}`;
                }

            } catch (err) {
                console.error("Error in calculateDistances:", err);
                showToast("Something went wrong while calculating distances.");
            } finally {
                placeOrderBtn.disabled = false;
                notes.style.display = 'none';
            }
        }

        // ---------- PINCODE BUTTON ----------
        $('#getDistance').on('click', function() {
            const pincode = $('#userPincode').val();
            if (!pincode) return showToast("Enter a delivery pincode");
            savePincode(pincode);
            calculateDistances(pincode);
        });

        // ---------- AUTO RUN PINCODE ----------
        $(document).ready(function() {
            const savedPincode = getSavedPincode();
            if (savedPincode) {
                $('#userPincode').val(savedPincode);
                calculateDistances(savedPincode);
            }

        });

        function parseCurrency(str) {
            if (!str && str !== 0) return 0;
            str = String(str);
            // find first numeric-ish token (digits, commas, dot)
            const m = str.match(/[\d.,]+/);
            if (!m) return 0;
            // remove commas and parse
            console.log(str);

            return parseFloat(m[0].replace(/,/g, '')) || 0;
        }

        $(document).on('click', '.apply-cb', function() {
            const vendorId = $(this).data('vendor-id');
            const input = $(`.cb-input[data-vendor-id="${vendorId}"]`);
            let cbAmount = parseFloat(input.val()) || 0;
            const availableCb = parseFloat(input.data('available')) || 0;
            if (availableCb == 0) {
                showToast('You Dont have enough Cashback Amount!');
            }else if(cbAmount == 0){
                showToast("Enter valid Cashback Amount!");
            }else{
                applyCashback(vendorId, cbAmount, availableCb);
            }
            input.val('');
        });

        function applyCashback(vendorId, cbAmount, availableCb) {
            // Read subtotal from dataset (set by Blade / updated by calculateDistances)
            const subtotal = parseFloat($(`#vendor-summary-${vendorId} .vendor-subtotal`).data('value')) || 0;

            // Read shipping text and parse robustly
            const shippingText = $(`#vendor-summary-${vendorId} .vendor-shipping`).text() || '';
            const shipping = parseCurrency(shippingText);

            // Compute max cashback reliably (25% of subtotal + shipping)
            let maxCb = 0.25 * (subtotal + shipping);
            // console.log(maxCb);

            // Fallback: if subtotal+shipping are both zero or NaN, try parsing the displayed max text
            if (!isFinite(maxCb) || (subtotal === 0 && shipping === 0)) {
                const maxText = $(`#vendor-summary-${vendorId} .max-cb-text`).text() || '';
                // console.log('max-text', maxText);

                maxCb = parseCurrency(maxText);
            }
            console.log('max-cb', maxCb);

            // Clamp cbAmount to allowed limits
            cbAmount1 = Math.min(cbAmount || 0, maxCb || 0, availableCb || 0);
            let cbmsg = '';
            
            if (cbAmount != cbAmount1) {
                console.log(cbAmount, cbAmount1, maxCb, availableCb);
                if (cbAmount1 == maxCb) {
                    cbAmount = Math.round(cbAmount1);
                    cbmsg = 'Max CB ' + cbAmount + ' Applied';
                    console.log(cbmsg);
                }
                else if(cbAmount1 == availableCb) {
                    cbAmount = Math.round(cbAmount1);
                    cbmsg = 'Available CB ' + cbAmount + ' Applied';
                    console.log(cbmsg);
                }
            }
            
            // Round to 2 decimals
            // cbAmount = Math.round(cbAmount * 100) / 100;
            cbAmount = Math.round(cbAmount1);


            // post to server
            $.post("{{ route('cart.apply.cashback') }}", {
                _token: '{{ csrf_token() }}',
                vendor_id: vendorId,
                cb_amount: cbAmount,
                available_cb: availableCb,
                subtotal: subtotal,
                shipping: shipping
            }, function(response) {
                if (response.success) {
                    // Update client-side appliedCashbacks object (if you keep it)
                    appliedCashbacks = appliedCashbacks || {};
                    appliedCashbacks[vendorId] = response.appliedCb;
                    // sessionStorage.setItem('appliedCashbacks', JSON.stringify(appliedCashbacks));

                    // Update the displayed "Applied: ₹ ..." span
                    const appliedSpan = document.querySelector(`.applied-cb[data-vendor-id="${vendorId}"]`);
                    if (appliedSpan) {
                        appliedSpan.textContent = response.appliedCb > 0 ?
                            `₹${response.appliedCb.toFixed(0)}` :
                            "";
                    }
                    
                    // Also update the main CB Applied h6 (session-based display)
                    const cbH6 = document.querySelector(`#vendor-summary-${vendorId} .vendor-cashback`);
                    if (cbH6) {
                        cbH6.dataset.value = response.appliedCb; // keep dataset consistent
                        cbH6.textContent = `CB Applied: ₹ ${response.appliedCb.toFixed(0)}`;
                    }
                    
                    // Recalculate totals (your existing function)
                    if (typeof recalcTotals === 'function') {
                        recalcTotals();
                    } else if (typeof calculateDistances === 'function') {
                        // optional fallback if you want to re-run distance-based calc (pass current pincode)
                        const pincode = $('#userPincode').val() || '';
                        if (pincode) calculateDistances(pincode);
                    }
                    console.log(cbmsg);
                    showToast(cbmsg || "Cashback Applied!");
                } else {
                    // handle failure (server returned success: false)
                    showToast("Cashback not Applicable!")
                }
            }).fail(function(jqxhr, status, err) {
                console.error('Apply cashback POST error:', status, err);
                showToast('Unable to apply cashback at the moment. Please try again.');
            });
        }


        // ---------- CART OPERATIONS ----------
        // Update Quantity
        $(document).on('click', '.change-qty', function() {
            let itemId = $(this).data('id');
            let actionType = $(this).data('type');
            sessionStorage.removeItem('appliedCashbacks');
            $.post("{{ route('cart.update.quantity') }}", {
                _token: '{{ csrf_token() }}',
                item_id: itemId,
                action: actionType
            }, function(response) {
                $('#cart-left-container').html(response.cart_left);
                $('#cart-right-container').html(response.cart_right);
                const savedPincode = getSavedPincode();
                if (savedPincode) {
                    $('#userPincode').val(savedPincode);
                    calculateDistances(savedPincode);
                }
            });
        });

        // Remove from Cart
        $(document).on('click', '.remove-from-cart', function() {
            let itemId = $(this).data('id');
            sessionStorage.removeItem('appliedCashbacks');
            $.post("{{ route('cart.remove') }}", {
                _token: '{{ csrf_token() }}',
                item_id: itemId
            }, function(response) {
                if (response.success) {
                    $('#cart-left-container').html(response.cart_left);
                    $('#cart-right-container').html(response.cart_right);
                    const savedPincode = getSavedPincode();
                    if (savedPincode) {
                        $('#userPincode').val(savedPincode);
                        calculateDistances(savedPincode);
                    }
                }
            });
        });

        // Save for Later
        $(document).on('click', '.save-for-later', function() {
            let itemId = $(this).data('id');
            sessionStorage.removeItem('appliedCashbacks');
            $.post("{{ route('cart.save_for_later') }}", {
                _token: '{{ csrf_token() }}',
                item_id: itemId
            }, function(response) {
                if (response.success) {
                    $('#cart-left-container').html(response.cart_left);
                    $('#cart-right-container').html(response.cart_right);
                    const savedPincode = getSavedPincode();
                    if (savedPincode) {
                        $('#userPincode').val(savedPincode);
                        calculateDistances(savedPincode);
                    }
                }
            });
        });

        // Move to Cart
        $(document).on('click', '.move-to-cart', function() {
            let itemId = $(this).data('id');

            $.post("{{ route('cart.move') }}", {
                _token: '{{ csrf_token() }}',
                item_id: itemId
            }, function(response) {
                $('#cart-left-container').html(response.cart_left);
                $('#cart-right-container').html(response.cart_right);
                const savedPincode = getSavedPincode();
                if (savedPincode) {
                    $('#userPincode').val(savedPincode);
                    calculateDistances(savedPincode);
                }
            });
        });

        // ---------- CASHBACK STORAGE ----------
        let appliedCashbacks = JSON.parse(sessionStorage.getItem("appliedCashbacks") || "{}");
        // Recalculate totals with cashback
        function recalcTotals() {
            let totalSubtotal = 0;
            let totalShipping = 0;
            let totalCashback = 0;

            document.querySelectorAll('.vendor-cart').forEach(vendorCart => {
                const vendorId = vendorCart.dataset.vendorId;
                let vendorSubtotal = 0;
                let vendorShipping = 0;

                vendorCart.querySelectorAll('.product-row').forEach(row => {
                    if (row.dataset.deliverable == "1") {
                        vendorSubtotal += parseFloat(row.dataset.price) * parseInt(row.dataset.qty);
                        vendorShipping += parseFloat(row.dataset.shipping || 0);
                    }
                });

                // ✅ Get cashback from Laravel session
                const vendorCb = appliedCashbacks[vendorId] || 0;
                console.log(vendorCb);

                totalCashback += vendorCb;

                totalSubtotal += vendorSubtotal;
                totalShipping += vendorShipping;
            });

            // Update UI
            const totalShipCell = document.querySelector('#total-shipping');
            if (totalShipCell) totalShipCell.textContent = `₹ ${totalShipping.toFixed(2)}`;

            const grandTotalCell = document.querySelector('#grand-total');
            if (grandTotalCell)
                grandTotalCell.textContent = `₹ ${(totalSubtotal + totalShipping - totalCashback).toFixed(0)}`;
        }

        // ---------- BIND APPLY BUTTON ----------
        function bindCashbackButtons() {
            document.querySelectorAll('.apply-cb').forEach(btn => {
                btn.onclick = function() {
                    const vendorId = this.dataset.vendorId;
                    const input = document.querySelector(`.cb-input[data-vendor-id="${vendorId}"]`);
                    const cbAmount = parseFloat(input.value) || 0;
                    const availableCb = parseFloat(input.dataset.available) || 0;

                    applyCashback(vendorId, cbAmount, availableCb);
                }
            });
        }

        // ---------- AFTER AJAX REFRESH ----------
        function afterAjaxRefresh() {
            document.querySelectorAll('.vendor-cart').forEach(vendorCart => {
                const vendorId = vendorCart.dataset.vendorId;

                // Calculate vendor subtotal + shipping dynamically
                let vendorSubtotal = 0;
                let vendorShipping = 0;

                vendorCart.querySelectorAll('.product-row').forEach(row => {
                    if (row.dataset.deliverable == "1") {
                        vendorSubtotal += parseFloat(row.dataset.price) * parseInt(row.dataset.qty);
                        vendorShipping += parseFloat(row.dataset.shipping || 0);
                    }
                });

                // Max cashback = 25% of (subtotal + shipping)
                const maxCashback = 0.25 * (vendorSubtotal + vendorShipping);

                // Update input max-cb attribute & label
                const input = vendorCart.querySelector(`.cb-input[data-vendor-id="${vendorId}"]`);
                const maxLabel = vendorCart.querySelector('.cashback-section h6');
                if (input) input.dataset.maxCb = maxCashback.toFixed(2);
                if (maxLabel) maxLabel.textContent = `Max CB : ₹ ${maxCashback.toFixed(0)}`;

                // Restore applied cashback from sessionStorage
                let cbAmount = appliedCashbacks[vendorId] || 0;
                const availableCb = parseFloat(input.dataset.available || 0);

                if (cbAmount > maxCashback) cbAmount = maxCashback;
                if (cbAmount > availableCb) cbAmount = availableCb;

                if (input) input.value = cbAmount;
                appliedCashbacks[vendorId] = cbAmount;

                // Update applied-cb span
                const span = vendorCart.querySelector(`.applied-cb[data-vendor-id="${vendorId}"]`);
                if (span) span.textContent = cbAmount > 0 ? `₹${cbAmount.toFixed(2)}` : "";
            });

            // Rebind Apply cashback buttons
            bindCashbackButtons();

            // Recalculate totals
            recalcTotals();

            // Recalculate distances if pincode exists
            const savedPincode = getSavedPincode();
            if (savedPincode) {
                $('#userPincode').val(savedPincode);
                calculateDistances(savedPincode);
            }
        }

        // Call after page load
        $(document).ready(function() {
            afterAjaxRefresh();
        });

        // Example: after cart AJAX update
        function refreshCart(response) {
            $('#cart-left-container').html(response.cart_left);
            $('#cart-right-container').html(response.cart_right);
            afterAjaxRefresh();
        }
    </script>

@endsection
