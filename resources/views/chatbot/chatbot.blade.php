<div class="body-head mb-3">
    <h5>Chatbot</h5>
</div>

<div class="listtable">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($botlist as $list)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $list->created_at->format('d-m-Y') }}</td>
                        <td>₹ {{ $list->amount }}</td>
                        <td>
                            <div class="d-flex align-items-center column-gap-2">
                                <a href="{{ url('chatbot-bill', ['id' => $list->id]) }}" target="_blank"
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
