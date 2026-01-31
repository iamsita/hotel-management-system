<!DOCTYPE html>
<html>

<head>
    <title>Food Orders Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Food Orders Management</h2>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Food Item</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Ordered</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->reservation->guest->full_name }}</td>
                            <td>{{ $order->reservation->room->room_number }}</td>
                            <td>{{ $order->food->name }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>
                                <span
                                    class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>{{ $order->ordered_at->format('M d H:i') }}</td>
                            <td>
                                <a href="{{ route('staff.food-orders.show', $order) }}"
                                    class="btn btn-sm btn-info">Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No orders found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $orders->links() }}
    </div>
</body>

</html>
