@extends('layout')
@section('title', 'Reservation Details')
@section('content')
<h4 class="mb-4">Reservation #{{ $reservation->id }}</h4>

<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white"><h6 class="mb-0">Booking Details</h6></div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><th>Guest:</th><td>{{ $reservation->user->name }}</td></tr>
                    <tr><th>Room:</th><td>{{ $reservation->room->room_number }} ({{ ucfirst($reservation->room->type) }})</td></tr>
                    <tr><th>Check In:</th><td>{{ $reservation->check_in->format('M d, Y') }}</td></tr>
                    <tr><th>Check Out:</th><td>{{ $reservation->check_out->format('M d, Y') }}</td></tr>
                    <tr><th>Guests:</th><td>{{ $reservation->guests }}</td></tr>
                    <tr><th>Total:</th><td class="fw-bold">${{ number_format($reservation->total_amount, 2) }}</td></tr>
                    <tr><th>Status:</th><td><span class="badge bg-{{ match($reservation->status) { 'checked_in' => 'success', 'confirmed' => 'primary', 'pending' => 'warning', 'cancelled' => 'danger', default => 'secondary' } }}">{{ $reservation->status }}</span></td></tr>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white"><h6 class="mb-0">Change Status</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.reservations.update-status', $reservation) }}">
                    @csrf @method('PATCH')
                    <div class="input-group">
                        <select name="status" class="form-select">
                            @foreach(['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'] as $status)
                                <option value="{{ $status }}" {{ $reservation->status === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary" style="background:#1a3263;border:none">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white"><h6 class="mb-0">Food Orders</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Item</th><th>Qty</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($reservation->foodOrders as $order)
                        <tr>
                            <td>{{ $order->food->name }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>${{ number_format($order->total_price, 2) }}</td>
                            <td><span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'secondary') }}">{{ $order->status }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-2">No food orders.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0">Payments</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        @forelse($reservation->payments as $payment)
                        <tr>
                            <td>${{ number_format($payment->amount, 2) }}</td>
                            <td>{{ strtoupper($payment->method) }}</td>
                            <td><span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">{{ $payment->status }}</span></td>
                            <td>{{ $payment->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-2">No payments yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('admin.reservations.index') }}" class="btn btn-secondary mt-3">Back</a>
@endsection
