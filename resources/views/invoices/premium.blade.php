<div class="body-head mb-3">
    <h5>Premium Invoice</h5>
</div>

<div class="listtable">
    <div class="filter-container">
        <div class="filter-container-start">
            <select class="form-select filter-option" id="premiumDropdown">
                <option value="All" selected>All</option>
            </select>
            <input type="text" class="form-control" id="premiumInput" placeholder=" Search">
        </div>
    </div>
    <div class="table-wrapper mt-2">
        <table class="table" id="premiumTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Month</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($premium as $prm)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $prm->created_at->format('F') ?? '' }}</td>
                        <td>₹ {{ $prm->price ?? '' }}</td>
                        <td>
                            <div class="d-flex align-items-center column-gap-2">
                                <a href="{{ url('premium-bill/' . $prm->id) }}" target="_blank" data-bs-toggle="tooltip"
                                    data-bs-title="Print Invoice">
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