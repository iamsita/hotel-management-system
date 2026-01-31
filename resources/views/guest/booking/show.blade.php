@extends('guest-layout')

@section('title', 'Booking Details')
@section('page-title', 'Booking Details - #' . $reservation->id)

@php
    $nights = $reservation->check_out_date->diffInDays($reservation->check_in_date);
    $baseCost = $reservation->room->price_per_night * $nights;
    $additionalCharges = $reservation->charges->sum('amount');
    $foodCost = $reservation->foodOrders->sum(fn($o) => $o->price * $o->quantity);
    $totalCost = $baseCost + $additionalCharges + $foodCost;
@endphp

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Reservation Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Booking Reference:</strong> <span
                                    class="badge bg-primary">#{{ $reservation->id }}</span></p>
                            <p><strong>Room Number:</strong> <span
                                    class="text-primary">{{ $reservation->room->room_number }}</span></p>
                            <p><strong>Room Type:</strong> {{ ucfirst($reservation->room->room_type) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Check-in:</strong> {{ $reservation->check_in_date->format('M d, Y H:i') }}</p>
                            <p><strong>Check-out:</strong> {{ $reservation->check_out_date->format('M d, Y H:i') }}</p>
                            <p><strong>Number of Nights:</strong> <span class="badge bg-info">{{ $nights }}</span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Status:</strong></p>
                            <span class="status-badge status-{{ str_replace('_', '-', $reservation->status) }}">
                                {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Number of Guests:</strong> {{ $reservation->number_of_guests }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-door-open"></i> Room Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Price per Night:</strong> <span
                                    class="text-success">${{ number_format($reservation->room->price_per_night, 2) }}</span>
                            </p>
                            <p><strong>Number of Nights:</strong> {{ $nights }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Room Cost:</strong> <span
                                    class="text-primary">${{ number_format($baseCost, 2) }}</span></p>
                        </div>
                    </div>
                    <p><strong>Room Features:</strong></p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-users"></i> Capacity: {{ $reservation->room->capacity }} guests</li>
                        <li><i class="fas fa-layer-group"></i> Floor: {{ $reservation->room->floor_number ?? 'Standard' }}
                        </li>
                    </ul>
                    @if ($reservation->special_requests)
                        <hr>
                        <p><strong>Special Requests:</strong></p>
                        <p class="text-muted">{{ $reservation->special_requests }}</p>
                    @endif
                </div>
            </div>

            @if ($reservation->charges->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Additional Charges</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reservation->charges as $charge)
                                        <tr>
                                            <td>{{ $charge->service?->name ?? 'Extra Charge' }}</td>
                                            <td>${{ number_format($charge->amount, 2) }}</td>
                                            <td>{{ $charge->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if ($reservation->foodOrders->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-utensils"></i> Food Orders</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reservation->foodOrders as $order)
                                        <tr>
                                            <td>{{ $order->food->name }}</td>
                                            <td>{{ $order->quantity }}</td>
                                            <td>${{ number_format($order->price, 2) }}</td>
                                            <td>${{ number_format($order->price * $order->quantity, 2) }}</td>
                                            <td>
                                                <span class="status-badge status-{{ $order->status }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    @if ($reservation->status !== 'cancelled')
                        <a href="{{ route('guest.food.menu') }}" class="btn btn-outline-primary">
                            <i class="fas fa-utensils"></i> Order Food & Drinks
                        </a>
                        <a href="{{ route('guest.cleaning.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-broom"></i> Request Cleaning
                        </a>
                        <a href="{{ route('guest.payment.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-credit-card"></i> Make Payment
                        </a>
                    @else
                        <p class="text-muted text-center mb-0">Booking is cancelled</p>
                    @endif
                    <a href="{{ route('guest.bookings') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Bookings
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-receipt"></i> Cost Summary</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted">Base Room Cost</small>
                        <p class="mb-0 h6">${{ number_format($baseCost, 2) }}</p>
                        <small class="text-muted">{{ $nights }} nights ×
                            ${{ number_format($reservation->room->price_per_night, 2) }}</small>
                    </div>

                    @if ($additionalCharges > 0)
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted">Additional Charges</small>
                            <p class="mb-0 h6">${{ number_format($additionalCharges, 2) }}</p>
                        </div>
                    @endif

                    @if ($foodCost > 0)
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted">Food Orders</small>
                            <p class="mb-0 h6">${{ number_format($foodCost, 2) }}</p>
                        </div>
                    @endif

                    <div class="pt-3">
                        <small class="text-muted">Total Amount</small>
                        <p class="mb-0 h5 text-success"><strong>${{ number_format($totalCost, 2) }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
</div>
</div>
</div>
</body>

</html>
