<div class="body-head mb-3">
    <h5>Projects Invoice</h5>
</div>

<div class="listtable">
    <div class="filter-container">
        <div class="filter-container-start">
            <select class="form-select filter-option" id="projectDropdown">
                <option value="All" selected>All</option>
            </select>
            <input type="text" class="form-control" id="projectInput" placeholder=" Search">
        </div>
    </div>
    <div class="table-wrapper mt-2">
        <table class="table" id="projectTable">
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
                @foreach ($projects as $project)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $project->title ?? '' }}</td>
                        <td>Listing</td>
                        <td>₹ {{ $project->amount ?? '' }}</td>
                        <td>
                            <div class="d-flex align-items-center column-gap-2">
                                <a href="{{ url('project-list-bill', ['id' => $project->id]) }}" target="_blank"
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