<!DOCTYPE html>
<html>

<head>
    <title>Booking Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <h2>Booking Details</h2>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Reservation Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Booking Reference:</strong> #{{ $reservation->id }}</p>
                                <p><strong>Room Number:</strong> {{ $reservation->room->room_number }}</p>
                                <p><strong>Room Type:</strong> {{ ucfirst($reservation->room->room_type) }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Check-in:</strong> {{ $reservation->check_in->format('M d, Y H:i') }}</p>
                                <p><strong>Check-out:</strong> {{ $reservation->check_out->format('M d, Y H:i') }}</p>
                                <p><strong>Nights:</strong>
                                    {{ $reservation->check_out->diffInDays($reservation->check_in) }}</p>
                            </div>
                        </div>
                        <p><strong>Status:</strong> <span
                                class="badge bg-{{ $reservation->status === 'checked_in' ? 'success' : ($reservation->status === 'cancelled' ? 'danger' : 'warning') }}">{{ ucfirst($reservation->status) }}</span>
                        </p>
                        <p><strong>Number of Guests:</strong> {{ $reservation->number_of_guests }}</p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Room Details</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Price per Night:</strong>
                            ${{ number_format($reservation->room->price_per_night, 2) }}</p>
                        <p><strong>Total Stay Cost:</strong>
                            ${{ number_format($reservation->room->price_per_night * $reservation->check_out->diffInDays($reservation->check_in), 2) }}
                        </p>
                        <p><strong>Room Features:</strong></p>
                        <ul>
                            <li>Capacity: {{ $reservation->room->capacity }} guests</li>
                            <li>Floor: {{ $reservation->room->floor_number ?? 'Standard' }}</li>
                        </ul>
                    </div>
                </div>

                @if ($reservation->charges->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Additional Charges</h5>
                        </div>
                        <div class="card-body">
                            <table class="table">
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
                                            <td>{{ $charge->service->name }}</td>
                                            <td>${{ number_format($charge->amount, 2) }}</td>
                                            <td>{{ $charge->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if ($reservation->foodOrders->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Food Orders</h5>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reservation->foodOrders as $order)
                                        <tr>
                                            <td>{{ $order->food->name }}</td>
                                            <td>{{ $order->quantity }}</td>
                                            <td>${{ number_format($order->price * $order->quantity, 2) }}</td>
                                            <td><span
                                                    class="badge bg-{{ $order->status === 'delivered' ? 'success' : 'warning' }}">{{ ucfirst($order->status) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body d-flex flex-column gap-2">
                        @if ($reservation->status !== 'cancelled')
                            <a href="{{ route('guest.food.menu') }}" class="btn btn-primary">Order Food & Drinks</a>
                            <a href="{{ route('guest.cleaning.create') }}" class="btn btn-primary">Request Cleaning</a>
                            <a href="{{ route('guest.payment.create') }}" class="btn btn-primary">Make Payment</a>
                        @endif
                        <a href="{{ route('guest.bookings') }}" class="btn btn-secondary">Back to Bookings</a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Base Room Cost</small>
                            <p>${{ number_format($reservation->room->price_per_night * $reservation->check_out->diffInDays($reservation->check_in), 2) }}
                            </p>
                        </div>
                        @if ($reservation->charges->count() > 0)
                            <div class="mb-3">
                                <small class="text-muted">Additional Charges</small>
                                <p>${{ number_format($reservation->charges->sum('amount'), 2) }}</p>
                            </div>
                        @endif
                        @if ($reservation->foodOrders->count() > 0)
                            <div class="mb-3">
                                <small class="text-muted">Food Orders</small>
                                <p>${{ number_format($reservation->foodOrders->sum(fn($o) => $o->price * $o->quantity), 2) }}
                                </p>
                            </div>
                        @endif
                        <hr>
                        <div>
                            <small class="text-muted">Total Amount Due</small>
                            <h4>${{ number_format($reservation->room->price_per_night * $reservation->check_out->diffInDays($reservation->check_in) + $reservation->charges->sum('amount') + $reservation->foodOrders->sum(fn($o) => $o->price * $o->quantity), 2) }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
