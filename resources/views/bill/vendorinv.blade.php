@include('bill.billheader')

<div class="pdf-main" id="invoice">

    <div class="pdf-logo">
        <img src="{{ asset('assets/images/Favicon.png') }}" height="200px" alt="logo">
    </div>

    <!-- Header -->
    <div class="pdf-header mb-3">
        <div class="pdf-head-left">
            <h4>Invoice</h4>
        </div>
        <div class="pdf-head-left">
            <img src="{{ asset('assets/images/Logo_Admin.png') }}" height="35px" class="d-flex mx-auto" alt="logo">
        </div>
        <div class="pdf-head-right ms-auto">
            <h5 class="mb-2">Order ID : #{{ $invoiceData['invoice_no'] }}</h5>
            <h5 class="mb-0">Order Date : {{ $invoiceData['date'] }}</h5>
        </div>
    </div>

    <!-- Customer & Invoice Info -->
    <div class="pdf-mid midthree mb-3">
        <div class="pdf-billing me-auto">
            <h5 class="text-uppercase">Billing Information</h5>
            <h6>{{ $invoiceData['customer']['name'] }}</h6>
            <h6>{{ $invoiceData['customer']['role'] }}</h6>
            <h6>{{ $invoiceData['address']['billing_address'] }}</h6>
            <h6>Contact: {{ $invoiceData['customer']['contact'] }}</h6>
            @if ($invoiceData['gst_billing'] == 'yes')
                <h6>GST Number: {{ $invoiceData['billing_gst'] }}</h6>
            @endif
        </div>
        <div class="pdf-billing mx-auto">
            <h5 class="text-uppercase">Shipping Information</h5>
            <h6>{{ $invoiceData['customer']['name'] }}</h6>
            <h6>{{ $invoiceData['customer']['role'] }}</h6>
            <h6>{{ $invoiceData['address']->shipping_address }}</h6>
            <h6>Contact: +91 {{ $invoiceData['customer']['contact'] }}</h6>
            @if ($invoiceData['shipping_gst'])
                <h6>GST Number: {{ $invoiceData['shipping_gst'] }}</h6>
            @endif
        </div>
        <div class="pdf-billing ms-auto">
            <h5 class="text-uppercase">Company Information</h5>
            <h6>{{ $invoiceData['company']['name'] }}</h6>
            <h6>Admin - {{ $invoiceData['company']['admin'] }}</h6>
            <h6>{{ $invoiceData['company']['address'] }}</h6>
            <h6>Contact: +91 {{ $invoiceData['company']['contact'] }}</h6>
            <h6>GST Number: {{ $invoiceData['company']['gst'] }}</h6>
        </div>
    </div>

    <!-- Table -->
    <div class="pdf-table">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Items</th>
                    @if ($invoiceData['type'] == 'order')
                        <th>Category</th>
                    @endif
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Shipping</th>
                    <th>Tax (%)</th>
                    <th>Tax (₹)</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subtotal = 0;
                    $vendorTotal = 0;
                @endphp
                @foreach ($invoiceData['items'] as $item)
                    @php
                        $qty = $item['quantity'];
                        // Base Price + Cashback
                        $pricePerUnit = $item['price'] + $item['cashback'] + $item['margin'];
                        $shipping = $item['shipping'] ?? 0;
                        $subtotal = $pricePerUnit * $qty + $shipping;
                        $tax = ($subtotal * $item['tax']) / 100;
                        $itemTotal = $subtotal + $tax;
                        $vendorTotal += $itemTotal;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item['title'] }}</td>
                        @if ($invoiceData['type'] == 'order')
                            <td>{{ $item['category'] }}</td>
                        @endif
                        <td>{{ $item['quantity'] }}</td>
                        <td>₹ {{ $pricePerUnit }}</td>
                        <td>₹ {{ $item['shipping'] }}</td>
                        <td>{{ $item['tax'] }}</td>
                        <td>₹ {{ $tax }}</td>
                        <td>₹ {{ number_format($itemTotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7"></td>
                    <td><strong>Grand Total (Incl. Tax)</strong></td>
                    <td><strong>₹ {{ number_format($vendorTotal, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Company Info -->
    <div class="pdf-end d-flex align-items-end justify-content-end">
        <div class="pdf-serial text-end">
            <h5 class="text-uppercase">Computer Generated Signature</h5>
        </div>
    </div>

</div>

@include('bill.billfooter')