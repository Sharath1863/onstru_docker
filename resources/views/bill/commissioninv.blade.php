@include('bill.billheader')

<div class="pdf-main" id="invoice">

    <div class="pdf-logo">
        <img src="{{ asset('assets/images/Favicon.png') }}" height="200px" alt="logo">
    </div>

    <!-- Header -->
    <div class="pdf-header mb-3">
        <div class="pdf-head-left">
            <h4>Commission Invoice</h4>
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
    <div class="pdf-mid midtwo mb-3">
        <div class="pdf-billing me-auto">
            <h5 class="text-uppercase">Billing Information</h5>
            <h6>{{ $invoiceData['customer']['name'] }}</h6>
            <h6>{{ $invoiceData['customer']['role'] }}</h6>
            <h6>{{ $invoiceData['address']['billing_address'] }}</h6>
            <h6>Contact: {{ $invoiceData['customer']['contact'] }}</h6>
        </div>
        <div class="pdf-billing ms-auto">
            <h5 class="text-uppercase">Company Information</h5>
            <h6>{{ $invoiceData['company']['name'] }}</h6>
            <h6>Admin - {{ $invoiceData['company']['admin'] }}</h6>
            <h6>{{ $invoiceData['company']['address'] }}</h6>
            <h6>Contact: +91 {{ $invoiceData['company']['contact'] }}</h6>
        </div>
    </div>

    <!-- Table -->
    <div class="pdf-table">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    @if ($invoiceData['type'] == 'order')
                        <th>Category</th>
                    @endif
                    <th>Quantity</th>
                    <th>Commission (%)</th>
                    <th>Commission (₹)</th>
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
                        // Base Price
                        $pricePerUnit = $item['price'];
                        $commissionPercent = $item['commission'];
                        $commissionAmt = ($pricePerUnit * $commissionPercent) / 100;
                        $commissionTotal = $commissionAmt * $qty;
                        $vendorTotal += $commissionTotal;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item['title'] }} (₹ {{ $item['price'] }})</td>
                        @if ($invoiceData['type'] == 'order')
                            <td>{{ $item['category'] }}</td>
                        @endif
                        <td>{{ $item['quantity'] }}</td>
                        <td>{{ $item['commission'] }}</td>
                        <td>₹ {{ $commissionAmt }}</td>
                        <td>₹ {{ number_format($commissionTotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5"></td>
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