    @extends('layouts.app')

    @section('title', 'Onstru | Wallet')

    @section('content')

        <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/list.css') }}">

        <div class="container-xl main-div">
            <div class="body-head d-block mb-3">
                <h5 class="mb-2">My Wallet</h5>
                <h6>Recharge your wallet to boost products and gain visibility</h6>
            </div>

            <div class="side-cards mb-4">
                <div class="cards-head">
                    <h5 class="mb-3">
                        <i class="fas fa-wallet pe-1"></i> Current Balance
                    </h5>
                    <h4 class="mb-3">₹ {{ $balance }}</h4>
                    <form action="" class="form-div" id="payForm">
                        <div class="col-sm-12 col-md-3 mb-3">
                            <label for="amount">Amount</label>
                            <input type="number" class="form-control" name="amount" id="amount" min="0"
                                required>
                        </div>
                        <button type="submit" class="formbtn" id="payBtn">Recharge Now</button>
                    </form>
                </div>
            </div>

            <div class="body-head mb-3">
                <h5>Transaction History</h5>
            </div>

            <div class="listtable">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Transaction ID</th>
                                <th>Payment Type</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($list as $lt)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($lt->created_at)->format('d-m-Y') }}</td>
                                    <td>TNX123456</td>
                                    <td>Card</td>
                                    <td>₹ {{ $lt->amount }}</td>
                                    <td>
                                        <div class="d-flex align-items-center column-gap-2">
                                            <a href="" target="_blank" data-bs-toggle="tooltip" data-bs-title="Print Invoice">
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
        </div>
        <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

        <script>
            document.getElementById("payForm").addEventListener("submit", function(e) {
                e.preventDefault(); // stop form reload

                // Ask user for confirmation before proceeding
                if (!confirm("Are you sure you want to proceed with the payment?")) {
                    return; // stop if user clicks cancel
                }

                let payBtn = document.getElementById("payBtn");
                payBtn.disabled = true; // disable button to prevent multiple clicks
                payBtn.innerText = "Processing...";

                let amount = document.getElementById("amount").value;

                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                var mode = "{{ env('CASHFREE_ENV') === 'sandbox' ? 'sandbox' : 'production' }}";

                fetch("{{ route('pay') }}", {
                        method: "POST", // usually POST
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": token
                        },
                        body: JSON.stringify({
                            amount: amount
                        })
                    })
                    .then(
                        response => {
                            let payBtn = document.getElementById("payBtn");
                            payBtn.disabled = false;
                            payBtn.innerText = "Pay Now";
                            return response.json();
                        }
                    )
                    .then(data => {
                        if (data.payment_session_id) {
                            // console.log("Payment Session ID:", data.payment_session_id);


                            const cashfree = Cashfree({
                                mode: mode
                            }); // or "sandbox"

                            cashfree.checkout({
                                paymentSessionId: data.payment_session_id,
                                redirectTarget: "_blank"
                            });
                        } else {
                            alert("Payment session ID not found");
                        }
                    });
            });
        </script>

        <!-- <script>
        document.getElementById("payBtn").addEventListener("click", function() {
            fetch("/pay", {
                    method: "GET",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.payment_session_id) {

                        console.log("Payment Session ID:", data.payment_session_id);
                        const cashfree = Cashfree({
                            mode: "sandbox", // Change to "production" later
                        });
                        cashfree.checkout({
                            paymentSessionId: data.payment_session_id,
                            redirectTarget: "_blank" // opens full page
                        });
                    } else {
                        alert("Payment session ID not found");
                    }
                });
        });
    </script> -->

    @endsection
