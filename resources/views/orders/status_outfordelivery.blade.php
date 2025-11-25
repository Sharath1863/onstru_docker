<div class="body-head mb-3">
    <h5>Out For Delivery Orders</h5>
</div>

@if ($orders->filter(fn($order) => $order->products->first()?->status === 'shipped')->count() === 0)
    <div class="side-cards shadow-none border-0">
        <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
            <img src="{{ asset('assets/images/Empty/NoOrderStatus.png') }}" height="200px" class="d-flex mx-auto mb-2"
                alt="">
            <h5 class="text-center mb-0">No Orders Found</h5>
            <h6 class="text-center bio">No orders found - once you make a purchase, your orders will appear here.</h6>
        </div>
    </div>
@else
    <div class="listtable">
        <div class="table-wrapper">
            <table class="table" id="table4">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order ID</th>
                        <th>Ordered By</th>
                        <th>Ordered On</th>
                        <th>Location</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $i = 1; 
                    @endphp
                    @foreach ($orders as $order)
                        @if ($order->products->contains('status', 'shipped'))
                            <tr id="{{ $order->order_id }}">
                                <td>{{ $i }}</td>
                                <td>{{ $order->order_id }}</td>
                                <td>{{ $order->user->name ?? 'N/A' }}</td>
                                <td>{{ $order->created_at->format('d-m-Y') }}</td>
                                <td>{{ $order->address->shipping_city ?? 'N/A' }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ url('order-accept', $order->order_id) }}" data-bs-toggle="tooltip"
                                            data-bs-title="View Tracking">
                                            <i class="fas fa-external-link"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @php $i++; @endphp
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif