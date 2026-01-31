@extends('guest-layout')

@section('title', 'Payment History')
@section('page-title', 'Payment History')

@section('content')
    <div class="mb-3">
        <a href="{{ route('guest.payment.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Make Payment
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>Booking</th>
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
                            <td>Room {{ $payment->reservation->room->room_number }}</td>
                            <td>${{ number_format($payment->amount, 2) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                            <td>
                                <span class="status-badge status-{{ $payment->status }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td>{{ $payment->paid_at?->format('M d, Y H:i') ?? 'N/A' }}</td>
                            <td>
                                @if ($payment->status === 'completed')
                                    <a href="{{ route('guest.payment.receipt', $payment) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-receipt"></i> Receipt
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <p class="text-muted">No payments found</p>
                                <a href="{{ route('guest.payment.create') }}" class="btn btn-primary btn-sm">Make Your First
                                    Payment</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($payments->hasPages())
        <div class="mt-3">
            {{ $payments->links() }}
        </div>
    @endif
@endsection
