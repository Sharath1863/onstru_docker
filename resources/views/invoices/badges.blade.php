<div class="body-head mb-3">
    <h5>Badges Invoice</h5>
</div>

<div class="listtable">
    <div class="filter-container">
        <div class="filter-container-start">
            <select class="form-select filter-option" id="badgeDropdown">
                <option value="All" selected>All</option>
            </select>
            <input type="text" class="form-control" id="badgeInput" placeholder=" Search">
        </div>
    </div>
    <div class="table-wrapper mt-2">
        <table class="table" id="badgeTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($badges as $badge)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if ($badge->badge == 5)
                                <span>Titan Seller</span>
                            @elseif ($badge->badge == 10)
                                <span>Crown Seller</span>
                            @elseif ($badge->badge == 15)
                                <span>Empire Seller</span>
                            @endif
                        </td>
                        <td>₹ {{ $badge->amount * 1.18 ?? '' }}</td>
                        <td>
                            <div class="d-flex align-items-center column-gap-2">
                                <a href="{{ url('badges-bill', ['id' => $badge->id]) }}" target="_blank"
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