<div class="body-head mb-3">
    <h5>Jobs Invoice</h5>
</div>

<div class="listtable">
    <div class="filter-container">
        <div class="filter-container-start">
            <select class="form-select filter-option" id="jobDropdown">
                <option value="All" selected>All</option>
            </select>
            <input type="text" class="form-control" id="jobInput" placeholder=" Search">
        </div>
    </div>
    <div class="table-wrapper mt-2">
        <table class="table" id="jobTable">
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
                @foreach ($jobs as $job)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $job->job->title ?? '' }}</td>
                        <td>Boosting</td>
                        <td>₹ {{ $job->amount ?? '' }}</td>
                        <td>
                            <div class="d-flex align-items-center column-gap-2">
                                <a href="{{ url('job-boost-bill', ['id' => $job->id]) }}" target="_blank"
                                    data-bs-toggle="tooltip" data-bs-title="Print Invoice">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
        </table>
    </div>
</div>