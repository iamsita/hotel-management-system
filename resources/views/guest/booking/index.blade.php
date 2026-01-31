<!DOCTYPE html>
<html>

<head>
    <title>My Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>My Bookings</h2>
        <a href="{{ route('guest.booking.create') }}" class="btn btn-primary mb-3">New Booking</a>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Guests</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $res)
                        <tr>
                            <td>{{ $res->room->room_number }}</td>
                            <td>{{ $res->check_in_date->format('M d, Y') }}</td>
                            <td>{{ $res->check_out_date->format('M d, Y') }}</td>
                            <td>{{ $res->number_of_guests }}</td>
                            <td>${{ number_format($res->total_amount, 2) }}</td>
                            <td><span
                                    class="badge bg-{{ $res->status === 'checked_in' ? 'success' : 'info' }}">{{ ucfirst($res->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('guest.booking.show', $res) }}" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No bookings found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $reservations->links() }}
    </div>
</body>

</html>
