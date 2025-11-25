<div class="body-head mb-3">
    <h5>Services Invoice</h5>
</div>

<div class="listtable">
    <div class="filter-container">
        <div class="filter-container-start">
            <select class="form-select filter-option" id="serviceDropdown">
                <option value="All" selected>All</option>
            </select>
            <input type="text" class="form-control" id="serviceInput" placeholder=" Search">
        </div>
    </div>
    <div class="table-wrapper mt-2">
        <table class="table" id="serviceTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Service Type</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($services as $service)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $service->service->title ?? '' }}</td>
                        <td>{{ $service->service->serviceType->value ?? '' }}</td>
                        @if ($service->type == 'list')
                            <td>Listing</td>
                        @elseif ($service->type == 'click')
                            <td>Highlighting</td>
                        @endif
                        <td>₹ {{ $service->amount ?? '' }}</td>
                        <td>
                            <div class="d-flex align-items-center column-gap-2">
                                @if ($service->type == 'list')
                                    <a href="{{ url('service-list-bill', ['id' => $service->service_id]) }}" target="_blank" data-bs-toggle="tooltip" data-bs-title="Print Invoice">
                                        <i class="fas fa-print"></i>
                                    </a>
                                @elseif ($service->type == 'click')
                                    <a href="{{ url('service-click-bill', ['id' => $service->id]) }}" target="_blank" data-bs-toggle="tooltip" data-bs-title="Print Invoice">
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