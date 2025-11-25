<div class="body-head mb-3">
    <h5>Chatbot Invoice</h5>
</div>

<div class="listtable">
    <div class="filter-container">
        <div class="filter-container-start">
            <select class="form-select filter-option" id="chatbotDropdown">
                <option value="All" selected>All</option>
            </select>
            <input type="text" class="form-control" id="chatbotInput" placeholder=" Search">
        </div>
    </div>
    <div class="table-wrapper mt-2">
        <table class="table" id="chatbotTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chatbots as $chatbot)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $chatbot->created_at->format('d-m-Y') }}</td>
                        <td>₹ {{ $chatbot->amount * 1.18 ?? '' }}</td>
                        <td>
                            <div class="d-flex align-items-center column-gap-2">
                                <a href="{{ url('chatbot-bill', ['id' => $chatbot->id]) }}" target="_blank"
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