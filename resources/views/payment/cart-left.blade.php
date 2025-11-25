<!-- Cart -->
@foreach ($cartItems as $vendorId => $items)
    @php
        $vendor = $items->first()->vendor;
        $vendorTotal = $items->sum(function ($item) {
            $product = $item->product;
            $price = $product->base_price + $product->cashback_price + $product->margin;
            $priceWithTax = $price * (1 + $product->tax_percentage / 100);
            return round($priceWithTax * $item->quantity, 2);
        });
        $vendorId = optional($vendor)->id;
        $cb = $vendorId ? collect($available_cashback)->firstWhere('vendor_id', $vendorId) : null;
    @endphp
    <div class="vendor-cart mb-2" data-vendor-id="{{ $vendorId }}">
        <div class="listtable">
            <div class="body-head mb-2">
                <h5 class="mb-2 text-capitalize">{{ $vendor->name ?? 'Vendor' }}</h5>
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-coins text-warning pe-1"></i>
                    <h6 class="mb-0"><span class="text-dark pe-1">CB</span> {{ $cb->avail_cb ?? 0 }}</h6>
                </div>
            </div>
            <div class="listtable border-0 border-top p-0">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Transport</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                @php
                                    $product = $item->product;
                                    $qty = $item->quantity ?? 1;
                                    $price = $product->base_price + $product->cashback_price + $product->margin;
                                    $priceWithTax = $price * (1 + $product->tax_percentage / 100);
                                    $subTotal = $priceWithTax * $qty;

                                    $shippingRules = json_decode($product->ship_charge, true) ?? [];
                                    $shippingCost = 0;
                                    if (!empty($shippingRules)) {
                                        $lastRule = end($shippingRules);
                                        $shippingCost = (float) $lastRule['price'];
                                        foreach ($shippingRules as $rule) {
                                            if ($qty >= (int) $rule['from'] && $qty <= (int) $rule['to']) {
                                                $shippingCost = (float) $rule['price'];
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                <tr class="product-row" data-item-id="{{ $item->id }}" data-hub-lat="{{ $product->hub->latitude }}" data-product-id="{{ $product->id }}"
                                    data-hub-lng="{{ $product->hub->longitude }}" data-charge="{{ $shippingCost }}"
                                    data-price="{{ $priceWithTax }}" data-qty="{{ $item->quantity }}"
                                    data-cashback="{{ $item->cashback ?? 0 }}" data-max-distance="{{ $product->d_km }}"
                                    data-tax="{{ $product->tax_percentage }}" data-deliverable="{{ $item->deliverable ? 1 : 0 }}">
                                    <td>
                                        <div class="d-flex align-items-start gap-2">
                                            <img src="{{ asset($product->cover_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $product->cover_img : 'assets/Images/NoImages.png') }}"
                                                width="90px" class="rounded-2">
                                            <div>
                                                <h5 class="text-capitalize mb-1">{{ $product->name }}</h5>
                                                <h6 class="mb-1">Delivered on {{$product->d_days ?? 0 }} days</h6>
                                            </div>
                                        </div>
                                    <td>
                                        <span class="product-price">₹ {{ $priceWithTax }}</span>
                                    </td>
                                    <td class="shipping-cell">
                                        <span>₹ {{ $shippingCost }}</span><br>
                                        <span>(Per Km)</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-start gap-1">
                                            <button class="btn btn-sm btn-light border fw-bold change-qty" data-id="{{ $item->id }}"
                                                data-type="minus">−</button>
                                            <span class="qty-text" id="qty-{{ $item->id }}">{{ $qty }}</span>
                                            <button class="btn btn-sm btn-light border fw-bold change-qty" data-id="{{ $item->id }}"
                                                data-type="plus">+</button>
                                        </div>
                                    </td>
                                    <td>
                                        ₹ {{ $subTotal }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span data-id="{{ $item->id }}" class="remove-from-cart">
                                                <a data-bs-toggle="tooltip" data-bs-title="Remove">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </a>
                                            </span>
                                            <span data-id="{{ $item->id }}" class="save-for-later">
                                                <a data-bs-toggle="tooltip" data-bs-title="Save for Later">
                                                    <i class="far fa-bookmark"></i>
                                                    </button>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="deliverable-cell d-none">Checking...</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Save For Later -->
@foreach ($savedItems as $vendorId => $items)
    @php
        $vendor = $items->first()->vendor;
        $vendorTotal = $items->sum(function ($item) {
            $product = $item->product;
            $price = $product->base_price + $product->cashback_price + $product->margin;
            $priceWithTax = $price * (1 + $product->tax_percentage / 100);
            return round($priceWithTax * $item->quantity, 2);
        });
        // $vendorTotal = $items->sum(function ($item) {
        //     $product = $item->product;
        //     $cashbackWithTax = $product->cashback_price * (1 + $product->tax_percentage / 100);
        //     $total = ($product->sp + $cashbackWithTax) * $item->quantity;
        //     return round($total, 2);
        // });
        $vendorId = optional($vendor)->id;
        $cb = $vendorId ? collect($available_cashback)->firstWhere('vendor_id', $vendorId) : null;
    @endphp
    <div class="vendor-cart1 mt-3" data-vendor-id="{{ $vendorId }}">
        <div class="body-head mb-2">
            <h4>Saved for later</h4>
        </div>
        <div class="listtable">
            <div class="body-head mb-2">
                <h5 class="mb-0 text-capitalize">{{ $vendor->name ?? 'Vendor' }}</h5>
                <h6 class="mb-0">Total : ₹ {{ $vendorTotal ?? '-'}}</h6>
            </div>

            <div class="listtable border-0 border-top p-0">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Transport</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                @php
                                    $product = $item->product;
                                    $qty = $item->quantity ?? 1;
                                    $price = $product->base_price + $product->cashback_price + $product->margin;
                                    $priceWithTax = $price * (1 + $product->tax_percentage / 100);
                                    $subTotal = $priceWithTax * $qty;
                                    $shippingRules = json_decode($product->ship_charge, true) ?? [];
                                    $shippingCost = 0;
                                    if (!empty($shippingRules)) {
                                        $lastRule = end($shippingRules);
                                        $shippingCost = (float) $lastRule['price'];
                                        foreach ($shippingRules as $rule) {
                                            if ($qty >= (int) $rule['from'] && $qty <= (int) $rule['to']) {
                                                $shippingCost = (float) $rule['price'];
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                <tr class="product-row1" data-item-id="{{ $item->id }}">
                                    <td>
                                        <div class="d-flex align-items-start gap-2">
                                            <img src="{{ asset($product->cover_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $product->cover_img : 'assets/Images/NoImages.png') }}"
                                                width="90px" class="rounded-2">
                                            <div>
                                                <h5 class="text-capitalize mb-1">{{ $product->name }}</h5>
                                                <h6 class="mb-1">Delivered on {{$product->d_days ?? 0 }} days</h6>
                                            </div>
                                        </div>
                                    <td>
                                        ₹ {{ $priceWithTax }}
                                    </td>
                                    <td class="shipping-cell">
                                        <span>₹ {{ $shippingCost }}</span><br>
                                        <span>(Per Km)</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-start gap-1">
                                            <span>{{ $qty }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        ₹ {{ $subTotal }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span data-id="{{ $item->id }}" class="remove-from-cart">
                                                <a data-bs-toggle="tooltip" data-bs-title="Remove">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </a>
                                            </span>
                                            <span data-id="{{ $item->id }}" class="move-to-cart">
                                                <a data-bs-toggle="tooltip" data-bs-title="Move to Cart">
                                                    <i class="fas fa-bookmark"></i>
                                                </a>
                                            </span>
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
@endforeach