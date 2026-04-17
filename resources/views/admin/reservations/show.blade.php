@extends('layout')
@section('title', 'Reservation Details')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Reservation #{{ $reservation->id }}</h4>
    <div>
        <a href="{{ route('admin.reservations.generate-invoice', $reservation) }}" class="btn btn-sm btn-success">Generate Invoice</a>
        <a href="{{ route('admin.reservations.edit', $reservation) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
        <a href="{{ route('admin.reservations.index') }}" class="btn btn-sm btn-secondary">Back</a>
    </div>
</div>

@php
    $nights = $reservation->check_in->diffInDays($reservation->check_out);
    $roomTotal = $reservation->total_amount;
    $foodTotal = $reservation->foodOrders->where('status', 'delivered')->sum('total_price');
    $grandTotal = $roomTotal + $foodTotal;
    $paidTotal = $reservation->payments->where('status', 'completed')->sum('amount');
    $balanceDue = $grandTotal - $paidTotal;
@endphp

<div class="row">
    <div class="col-md-6">
        {{-- Booking Details --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white"><h6 class="mb-0">Booking Details</h6></div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><th>Guest:</th><td>{{ $reservation->user->name }} ({{ $reservation->user->email }})</td></tr>
                    <tr><th>Room:</th><td>{{ $reservation->room->room_number }} ({{ ucfirst($reservation->room->type) }})</td></tr>
                    <tr><th>Check In:</th><td>{{ $reservation->check_in->format('M d, Y') }}</td></tr>
                    <tr><th>Check Out:</th><td>{{ $reservation->check_out->format('M d, Y') }}</td></tr>
                    <tr><th>Nights:</th><td>{{ $nights }}</td></tr>
                    <tr><th>Guests:</th><td>{{ $reservation->guests }}</td></tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <span class="badge bg-{{ match($reservation->status) { 'checked_in' => 'success', 'confirmed' => 'primary', 'pending' => 'warning', 'cancelled' => 'danger', default => 'secondary' } }}">
                                {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Billing Summary --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white"><h6 class="mb-0">Billing Summary</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr>
                            <td>Room Charge ({{ $nights }} night{{ $nights > 1 ? 's' : '' }} x Rs. {{ number_format($reservation->room->price_per_night, 2) }})</td>
                            <td class="text-end">Rs. {{ number_format($roomTotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Food Orders ({{ $reservation->foodOrders->count() }} item{{ $reservation->foodOrders->count() != 1 ? 's' : '' }})</td>
                            <td class="text-end">Rs. {{ number_format($foodTotal, 2) }}</td>
                        </tr>
                        <tr class="fw-bold" style="background:#f0f0f0">
                            <td>Grand Total</td>
                            <td class="text-end">Rs. {{ number_format($grandTotal, 2) }}</td>
                        </tr>
                        <tr class="text-success">
                            <td>Paid</td>
                            <td class="text-end">- Rs. {{ number_format($paidTotal, 2) }}</td>
                        </tr>
                        <tr class="fw-bold {{ $balanceDue > 0 ? 'text-danger' : 'text-success' }}" style="background:#f0f0f0">
                            <td>Balance Due</td>
                            <td class="text-end">Rs. {{ number_format($balanceDue, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Record Payment --}}
        @if($balanceDue > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white"><h6 class="mb-0">Record Payment</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.reservations.pay', $reservation) }}">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label form-label-sm">Amount (Rs.)</label>
                            <input type="number" name="amount" class="form-control form-control-sm" value="{{ number_format($balanceDue, 2, '.', '') }}" step="0.01" min="1" max="{{ number_format($balanceDue, 2, '.', '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label form-label-sm">Method</label>
                            <select name="method" class="form-select form-select-sm">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="upi">UPI</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success btn-sm w-100">Record Payment</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center">
                <span class="text-success fw-bold">Fully Paid</span>
            </div>
        </div>
        @endif

        {{-- Change Status --}}
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
        {{-- Food Orders --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white"><h6 class="mb-0">Food Orders</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Item</th><th>Qty</th><th>Rate</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($reservation->foodOrders as $order)
                        <tr>
                            <td>{{ $order->food->name }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>Rs. {{ number_format($order->food->price, 2) }}</td>
                            <td>Rs. {{ number_format($order->total_price, 2) }}</td>
                            <td><span class="badge bg-{{ match($order->status) { 'delivered' => 'success', 'preparing' => 'info', 'pending' => 'warning', 'cancelled' => 'danger', default => 'secondary' } }}">{{ ucfirst($order->status) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-2">No food orders.</td></tr>
                        @endforelse
                    </tbody>
                    @if($reservation->foodOrders->count())
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="3">Total Food</td>
                            <td>Rs. {{ number_format($foodTotal, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Payment History --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white"><h6 class="mb-0">Payment History</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        @forelse($reservation->payments as $payment)
                        <tr>
                            <td>Rs. {{ number_format($payment->amount, 2) }}</td>
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

        {{-- Delete --}}
        <div class="card border-0 shadow-sm border-danger">
            <div class="card-body">
                <h6 class="text-danger">Danger Zone</h6>
                <p class="text-muted small mb-2">Permanently delete this reservation along with all food orders and payments.</p>
                <form action="{{ route('admin.reservations.destroy', $reservation) }}" method="POST" onsubmit="return confirm('Are you sure? This will permanently delete this reservation and all related data.')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm">Delete Reservation</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
