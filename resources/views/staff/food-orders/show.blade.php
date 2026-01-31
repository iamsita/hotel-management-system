<!DOCTYPE html>
<html>

<head>
    <title>Food Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Food Order Details</h2>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Order Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                                <p><strong>Guest:</strong> {{ $order->reservation->guest->full_name }}</p>
                                <p><strong>Room:</strong> {{ $order->reservation->room->room_number }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Ordered:</strong> {{ $order->ordered_at->format('M d, Y H:i A') }}</p>
                                @if ($order->delivered_at)
                                    <p><strong>Delivered:</strong> {{ $order->delivered_at->format('M d, Y H:i A') }}
                                    </p>
                                @endif
                                <p><strong>Status:</strong> <span
                                        class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">{{ ucfirst($order->status) }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Order Details</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Food Item:</strong> {{ $order->food->name }}</p>
                        <p><strong>Category:</strong> {{ ucfirst($order->food->category) }}</p>
                        <p><strong>Quantity:</strong> {{ $order->quantity }}</p>
                        <p><strong>Unit Price:</strong> ${{ number_format($order->price, 2) }}</p>
                        <p><strong>Total Price:</strong> ${{ number_format($order->price * $order->quantity, 2) }}</p>
                        @if ($order->special_notes)
                            <p><strong>Special Notes:</strong></p>
                            <p>{{ $order->special_notes }}</p>
                        @endif
                    </div>
                </div>

                @if ($order->status !== 'delivered' && $order->status !== 'cancelled')
                    <div class="card">
                        <div class="card-header">
                            <h5>Update Status</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('staff.food-orders.update-status', $order) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="status" class="form-label">New Status</label>
                                    <select class="form-select" name="status" id="status" required>
                                        <option value="">-- Select Status --</option>
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="preparing"
                                            {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                                        <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready
                                        </option>
                                        <option value="delivered"
                                            {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="cancelled">Cancelled</option>
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
                        <h5>Guest Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> {{ $order->reservation->guest->full_name }}</p>
                        <p><strong>Email:</strong> {{ $order->reservation->guest->email }}</p>
                        <p><strong>Phone:</strong> {{ $order->reservation->guest->phone }}</p>
                        <p><strong>Room:</strong> {{ $order->reservation->room->room_number }}</p>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Actions</h5>
                    </div>
                    <div class="card-body d-flex flex-column gap-2">
                        <a href="{{ route('staff.food-orders.index') }}" class="btn btn-secondary">Back to Orders</a>
                        <a href="{{ route('staff.reservations.show', $order->reservation) }}" class="btn btn-info">View
                            Booking</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
