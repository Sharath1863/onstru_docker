@include('bill.billheader')

<div class="pdf-main" id="invoice">

    <div class="pdf-logo">
        <img src="{{ asset('assets/images/Favicon.png') }}" height="200px" alt="">
    </div>

    <!-- Header -->
    <div class="pdf-header mb-3">
        <div class="pdf-head-left">
            @if ($invoiceData['type'] == 'list')
                <h4>Listing Invoice</h4>
            @elseif ($invoiceData['type'] == 'click')
                <h4>Highlights Invoice</h4>
            @elseif ($invoiceData['type'] == 'badge')
                <h4>Badge Invoice</h4>
            @elseif ($invoiceData['type'] == 'bot')
                <h4>Chatbot Invoice</h4>
            @elseif ($invoiceData['type'] == 'own')
                <h4>Owned Invoice</h4>
            @else
                <h4>Invoice</h4>
            @endif
        </div>
        <div class="pdf-head-center">
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
            <h6>{{ $invoiceData['customer']['address'] }}</h6>
            <h6>Contact: +91 {{ $invoiceData['customer']['contact'] }}</h6>
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
                    <th>
                        @if ($invoiceData['type'] == 'click' || $invoiceData['type'] == 'boost' || $invoiceData['type'] == 'list' || $invoiceData['type'] == 'own')
                            <span>Type</span>
                        @elseif ($invoiceData['type'] == 'premium')
                            <span>Month</span>
                        @elseif($invoiceData['type'] == 'order')
                            <span>Products</span>
                        @elseif ($invoiceData['type'] == 'badge' || $invoiceData['type'] == 'bot')
                            <span>Date</span>
                        @endif
                    </th>
                    <th>
                        @if ($invoiceData['type'] == 'click')
                            <span>Clicks</span>
                        @elseif ($invoiceData['type'] == 'premium')
                            <span>Date</span>
                        @elseif ($invoiceData['type'] == 'boost')
                            <span>Days</span>
                        @else
                            <span>Quantity</span>
                        @endif
                    </th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal = 0; @endphp
                @foreach ($invoiceData['items'] as $item)
                    @php
                        $lineTotal = $item['price'] * $item['quantity'];
                        $subtotal += $lineTotal;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item['title'] }}</td>
                        <td>
                            @if ($invoiceData['type'] == 'click')
                                <span>Highlighting</span>
                            @elseif($invoiceData['type'] == 'list')
                                <span>Listing</span>
                            @elseif($invoiceData['type'] == 'boost')
                                <span>Boosting</span>
                            @elseif ($invoiceData['type'] == 'premium')
                                <span>{{ $item['month'] }}</span>
                            @elseif($invoiceData['type'] == 'own')
                                <span>Owned</span>
                            @elseif($invoiceData['type'] == 'order')
                                <span>1</span>
                            @elseif ($invoiceData['type'] == 'badge' || $invoiceData['type'] == 'bot')
                                <span>{{ $item['date'] }}</span>
                            @endif
                        </td>
                        <td>
                            @if ($invoiceData['type'] == 'click')
                                <span>{{ $item['clicks'] }}</span>
                            @elseif ($invoiceData['type'] == 'premium')
                                <span>{{ $item['date'] }}</span>
                            @elseif ($invoiceData['type'] == 'boost')
                                <span>{{ $item['days'] }} Days</span>
                            @else
                                <span>{{ $item['quantity'] }}</span>
                            @endif
                        </td>
                        <td>₹ {{ number_format($lineTotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                @php
                    $tax = ($subtotal * $invoiceData['tax_percent']) / 100;
                    $grandTotal = $subtotal + $tax;
                @endphp
                <!-- <tr>
                    <td colspan="3"></td>
                    <td>Subtotal</td>
                    <td>₹ {{ number_format($subtotal, 2) }}</td>
                </tr> -->
                <tr>
                    <td colspan="3"></td>
                    <td>Tax ({{ $invoiceData['tax_percent'] }}%)</td>
                    <td>₹ {{ number_format($tax, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td><strong>Grand Total (Incl. Tax)</strong></td>
                    <td><strong>₹ {{ number_format($grandTotal, 2) }}</strong></td>
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