<div class="body-head mb-3">
    <h5>Products Invoice</h5>
</div>

<div class="listtable">
    <div class="filter-container">
        <div class="filter-container-start">
            <select class="form-select filter-option" id="productDropdown">
                <option value="All" selected>All</option>
            </select>
            <input type="text" class="form-control" id="productInput" placeholder=" Search">
        </div>
    </div>
    <div class="table-wrapper mt-2">
        <table class="table" id="productTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $product->product->name ?? '' }}</td>
                        @if ($product->type == 'list')
                            <td>Listing</td>
                        @elseif ($product->type == 'click')
                            <td>Highlighting</td>
                        @endif
                        <td>₹ {{ $product->amount ?? '' }}</td>
                        <td>
                            <div class="d-flex align-items-center column-gap-2">
                                @if ($product->type == 'list')
                                    <a href="{{ url('product-list-bill', ['id' => $product->product_id]) }}" target="_blank"
                                        data-bs-toggle="tooltip" data-bs-title="Print Invoice">
                                        <i class="fas fa-print"></i>
                                    </a>
                                @elseif ($product->type == 'click')
                                    <a href="{{ url('product-click-bill', ['id' => $product->id]) }}" target="_blank"
                                        data-bs-toggle="tooltip" data-bs-title="Print Invoice">
                                        <i class="fas fa-print"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>