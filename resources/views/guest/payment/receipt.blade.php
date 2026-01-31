<!DOCTYPE html>
<html>

<head>
    <title>Payment Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .receipt {
            max-width: 600px;
            margin: 50px auto;
            border: 1px solid #ddd;
            padding: 20px;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #333;
            margin-bottom: 20px;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }

        .receipt-total {
            border-top: 2px solid #333;
            margin-top: 20px;
            padding-top: 10px;
            font-weight: bold;
            font-size: 1.2em;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="receipt-header">
            <h2>Payment Receipt</h2>
            <p class="text-muted">Receipt #{{ $payment->transaction_id }}</p>
        </div>

        <div class="alert alert-success">
            <strong>Payment Successful!</strong> Your payment has been processed successfully.
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="receipt-row">
                    <span><strong>Booking Reference:</strong></span>
                    <span>{{ $payment->reservation->id }}</span>
                </div>
                <div class="receipt-row">
                    <span><strong>Room Number:</strong></span>
                    <span>{{ $payment->reservation->room->room_number }}</span>
                </div>
                <div class="receipt-row">
                    <span><strong>Check-in:</strong></span>
                    <span>{{ $payment->reservation->check_in->format('M d, Y') }}</span>
                </div>
                <div class="receipt-row">
                    <span><strong>Check-out:</strong></span>
                    <span>{{ $payment->reservation->check_out->format('M d, Y') }}</span>
                </div>
                <hr>
                <div class="receipt-row">
                    <span><strong>Payment Method:</strong></span>
                    <span>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                </div>
                <div class="receipt-row">
                    <span><strong>Payment Status:</strong></span>
                    <span>
                        <span class="badge bg-success">{{ ucfirst($payment->status) }}</span>
                    </span>
                </div>
                <div class="receipt-row">
                    <span><strong>Date & Time:</strong></span>
                    <span>{{ $payment->paid_at->format('M d, Y H:i A') }}</span>
                </div>
                <hr>
                <div class="receipt-row receipt-total">
                    <span>Amount Paid:</span>
                    <span>${{ number_format($payment->amount, 2) }}</span>
                </div>
            </div>
        </div>

        @if ($payment->notes)
            <div class="card mb-4">
                <div class="card-header">Notes</div>
                <div class="card-body">{{ $payment->notes }}</div>
            </div>
        @endif

        <div class="d-flex justify-content-between gap-2">
            <a href="{{ route('guest.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
            <a href="{{ route('guest.payment.history') }}" class="btn btn-secondary">View All Payments</a>
            <button class="btn btn-outline-secondary" onclick="window.print()">Print Receipt</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
