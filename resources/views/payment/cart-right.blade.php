<div class="body-head mb-3">
    <h5>Price Details</h5>
</div>

@php
    $grandTotal = 0;
    $totalShipping = 0;
    $totalCashback = 0;
@endphp

@foreach ($cartItems as $vendorId => $items)
    @php
        $vendor = optional($items->first()->vendor);
        $vendorId = $vendor->id ?? $vendorId;
        $vendorName = $vendor->name ?? 'Vendor';
        $itemCount = $items->count();

        $vendorSubtotal = 0;
        $vendorShipping = 0;

        foreach ($items as $item) {
            $product = $item->product;
            $qty = $item->quantity ?? 1;

            $basePrice = $product->base_price + $product->cashback_price + $product->margin;
            $priceWithTax = $basePrice * (1 + $product->tax_percentage / 100);
            $vendorSubtotal += round($priceWithTax * $qty, 2);

            $shippingRules = json_decode($product->ship_charge, true) ?? [];
            $shippingRate = 0;
            if (!empty($shippingRules)) {
                $lastRule = end($shippingRules);
                $shippingRate = (float) $lastRule['price'];
                foreach ($shippingRules as $rule) {
                    if ($qty >= (int) $rule['from'] && $qty <= (int) $rule['to']) {
                        $shippingRate = (float) $rule['price'];
                        break;
                    }
                }
            }

            $vendorShipping += $shippingRate;
        }

        // ✅ Always take applied cashback from session, default 0 if empty
        $vendorCashback = session('appliedCashbacks.' . $vendorId, 0);
        $maxCashback = round(0.25 * ($vendorSubtotal + $vendorShipping), 2);

        $totalShipping += $vendorShipping;
        $totalCashback += $vendorCashback;
        $grandTotal += $vendorSubtotal + $vendorShipping; // DO NOT subtract cashback here
        $cb = $vendorId ? collect($available_cashback)->firstWhere('vendor_id', $vendorId) : null;
    @endphp

    <div class="side-cards shadow-none mb-2" id="vendor-summary-{{ $vendorId }}">
        <div class="cards-content">
            <div class="d-flex align-items-start justify-content-between">
                <h6 class="text-muted mb-2">{{ $vendorName }} ({{ $itemCount }} items)</h6>
                <h6 class="vendor-subtotal mb-2" data-value="{{ $vendorSubtotal }}">
                    ₹ {{ number_format($vendorSubtotal, 2) }}
                </h6>
            </div>
            <div class="d-none">
                <h6 class="text-muted">CB Applied</h6>
                <h6 class="vendor-cashback" data-value="{{ $vendorCashback }}">
                    ₹ {{ number_format($vendorCashback, 2) }}
                </h6>
            </div>
            <div class="d-flex align-items-start justify-content-between">
                <h6 class="text-muted mb-2">CB Applied</h6>
                <h6 class="applied-cb mb-2" data-vendor-id="{{ $vendorId }}">
                    @if ($vendorCashback > 0)
                        ₹ {{ number_format($vendorCashback, 2) }}
                    @endif
                </h6>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="text-muted mb-2">Shipping Charge</h6>
                <h6 class="vendor-shipping mb-2">₹ {{ number_format($vendorShipping, 2) }}</h6>
            </div>
            <div class="cashback-section">
                <h6 class="max-cb-text mb-2">
                    <i class="fas fa-coins text-warning pe-1"></i> Max CB : <span data-vendor-id="{{ $vendorId }}"
                        data-max-cb="{{ $maxCashback }}">₹ {{ number_format($maxCashback, 0) }}</span>
                </h6>
            </div>
        </div>

        <div class="cashback-section mt-2">
            <div class="input-group form-div">
                <input type="number" class="form-control cb-input" style="font-size: 12px;" placeholder="Enter Cashback Amount"
                    data-vendor-id="{{ $vendorId }}" data-available="{{ $cb->avail_cb ?? 0 }}">
                <button class="formbtn apply-cb" data-vendor-id="{{ $vendorId }}">Apply
                    cashback</button>
            </div>
        </div>
    </div>
@endforeach

<div class="side-cards shadow-none mb-2">
    <div class="cards-content d-flex align-items-center justify-content-between">
        <h6 class="mb-2">Total Shipping</h6>
        <h6 class="mb-2" id="total-shipping">₹ {{ number_format($totalShipping, 2) }}</h6>
    </div>
    <div class="cards-content d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Grand Total</h6>
        <h6 class="mb-0" id="grand-total">₹ {{ number_format($grandTotal - $totalCashback, 2) }}</h6>
    </div>
</div>

<div class="form-div">
    <div class="mb-2 notes">
        <label>Notes <span>*</span></label>
        <label class="text-muted">Enter the delivery pincode (Top Right Corner) to calculate the distance and wait for few seconds to calculate distance for all products</label>
    </div>
    @if($cartItems->isNotEmpty())
        <a href="{{ url('checkout') }}" id="checkoutLink">
            <button id="placeOrderBtn" class="formbtn w-100">Place Order</button>
        </a>
    @else
        <button class="formbtn w-100" disabled>Your cart is empty</button>
    @endif
</div>
