<div class="body-head mb-3">
    <h5>Owned Leads Invoice</h5>
</div>

<div class="listtable">
    <div class="filter-container">
        <div class="filter-container-start">
            <select class="form-select filter-option" id="leadDropdown">
                <option value="All" selected>All</option>
            </select>
            <input type="text" class="form-control" id="leadInput" placeholder=" Search">
        </div>
    </div>
    <div class="table-wrapper mt-2">
        <table class="table" id="leadTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Service Type</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($leads as $lead)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $lead->lead->title ?? '' }}</td>
                        <td>{{ $lead->lead->serviceRelation->value ?? '' }}</td>
                        <td>₹ {{ $lead->lead->admin_charge * 1.18 ?? '' }}</td>
                        <td>
                            <div class="d-flex align-items-center column-gap-2">
                                <a href="{{ url('lead-owned-bill', ['id' => $lead->id]) }}" target="_blank"
                                    data-bs-toggle="tooltip" data-bs-title="Print Invoice">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>