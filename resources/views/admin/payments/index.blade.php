@extends('layout')
@section('title', 'Payments')
@section('content')
<h4 class="mb-4">Payments</h4>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>#</th><th>Guest</th><th>Reservation</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->reservation->user->name }}</td>
                    <td>#{{ $payment->reservation_id }}</td>
                    <td>${{ number_format($payment->amount, 2) }}</td>
                    <td>{{ strtoupper($payment->method) }}</td>
                    <td><span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">{{ $payment->status }}</span></td>
                    <td>{{ $payment->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-3">{{ $payments->links() }}</div>
    </div>
</div>
@endsection
