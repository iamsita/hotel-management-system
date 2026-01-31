<!DOCTYPE html>
<html>

<head>
    <title>Payment Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Payment Details</h2>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Payment Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Receipt ID:</strong> {{ $payment->transaction_id }}</p>
                                <p><strong>Amount:</strong> ${{ number_format($payment->amount, 2) }}</p>
                                <p><strong>Payment Method:</strong>
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong> <span
                                        class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning') }}">{{ ucfirst($payment->status) }}</span>
                                </p>
                                <p><strong>Date:</strong> {{ $payment->paid_at?->format('M d, Y H:i A') ?? 'N/A' }}</p>
                                <p><strong>Processed By:</strong> {{ $payment->processedBy?->name ?? 'System' }}</p>
                            </div>
                        </div>
                        @if ($payment->notes)
                            <p><strong>Notes:</strong></p>
                            <p>{{ $payment->notes }}</p>
                        @endif
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Booking Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Booking Reference:</strong> #{{ $payment->reservation->id }}</p>
                        <p><strong>Guest:</strong> {{ $payment->reservation->guest->full_name }}</p>
                        <p><strong>Room:</strong> {{ $payment->reservation->room->room_number }}</p>
                        <p><strong>Check-in:</strong> {{ $payment->reservation->check_in->format('M d, Y') }}</p>
                        <p><strong>Check-out:</strong> {{ $payment->reservation->check_out->format('M d, Y') }}</p>
                    </div>
                </div>

                @if ($payment->status !== 'completed' && $payment->status !== 'failed')
                    <div class="card">
                        <div class="card-header">
                            <h5>Update Status</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('payments.update-status', $payment) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="status" class="form-label">New Status</label>
                                    <select class="form-select" name="status" id="status" required>
                                        <option value="">-- Select Status --</option>
                                        <option value="pending" {{ $payment->status === 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="completed"
                                            {{ $payment->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="failed">Failed</option>
                                        <option value="refunded">Refunded</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Status</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Actions</h5>
                    </div>
                    <div class="card-body d-flex flex-column gap-2">
                        <a href="{{ route('payments.index') }}" class="btn btn-secondary">Back to Payments</a>
                        <a href="{{ route('reservations.show', $payment->reservation) }}" class="btn btn-info">View
                            Booking</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
