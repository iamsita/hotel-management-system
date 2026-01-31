<!DOCTYPE html>
<html>

<head>
    <title>Payment Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Payment Management</h2>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Receipt ID</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->transaction_id }}</td>
                            <td>{{ $payment->reservation->guest->full_name }}</td>
                            <td>{{ $payment->reservation->room->room_number }}</td>
                            <td>${{ number_format($payment->amount, 2) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                            <td>
                                <span
                                    class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td>{{ $payment->paid_at?->format('M d, Y H:i') ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('staff.payments.show', $payment) }}"
                                    class="btn btn-sm btn-info">Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No payments found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $payments->links() }}

        <div class="mt-4">
            <a href="{{ route('staff.payments.report') }}" class="btn btn-primary">View Payment Report</a>
        </div>
    </div>
</body>

</html>
