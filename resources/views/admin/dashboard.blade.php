@extends('layout')
@section('title', 'Admin Dashboard')
@section('content')
    <h4 class="mb-4">Dashboard</h4>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Rooms</h6>
                    <h2 class="fw-bold" style="color:#1a3263">{{ $totalRooms }}</h2>
                    <small class="text-success">{{ $availableRooms }} available</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Guests</h6>
                    <h2 class="fw-bold" style="color:#1a3263">{{ $totalGuests }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Checked In</h6>
                    <h2 class="fw-bold" style="color:#1a3263">{{ $checkedIn }}</h2>
                    <small class="text-muted">{{ $pendingOrders }} pending orders</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Revenue</h6>
                    <h2 class="fw-bold text-success">Rs. {{ number_format($totalRevenue, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h6 class="mb-0">Recent Reservations</h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentReservations as $r)
                        <tr>
                            <td>{{ $r->user->name }}</td>
                            <td>{{ $r->room->room_number }}</td>
                            <td>{{ $r->check_in->format('M d, Y') }}</td>
                            <td>{{ $r->check_out->format('M d, Y') }}</td>
                            <td><span
                                    class="badge bg-{{ $r->status === 'checked_in' ? 'success' : ($r->status === 'pending' ? 'warning' : 'secondary') }}">{{ $r->status }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.reservations.show', $r->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">No reservations yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
