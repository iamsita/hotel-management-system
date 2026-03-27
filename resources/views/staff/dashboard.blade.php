@extends('layout')
@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="fas fa-calendar-alt"></i> Reservations</h1>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('reservations.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Reservation
                </a>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Guests</h5>
                        <h2>{{ $totalGuests }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Checked In</h5>
                        <h2>{{ $checkedIn }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Pending Orders</h5>
                        <h2>{{ $pendingOrders }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <h2>${{ number_format($totalRevenue, 0) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Bookings</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Guest</th>
                                        <th>Room</th>
                                        <th>Check-in</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentBookings as $booking)
                                        <tr>
                                            <td>{{ $booking->user->name }}</td>
                                            <td>{{ $booking->room->room_number }}</td>
                                            <td>{{ $booking->check_in_date->format('M d') }}</td>
                                            <td><span
                                                    class="badge bg-{{ $booking->status === 'checked_in' ? 'success' : 'warning' }}">{{ ucfirst($booking->status) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No bookings</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Pending Cleaning Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Room</th>
                                        <th>Type</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingCleaning as $cleaning)
                                        <tr>
                                            <td>{{ $cleaning->room->room_number }}</td>
                                            <td>{{ ucfirst($cleaning->request_type) }}</td>
                                            <td><span
                                                    class="badge bg-{{ $cleaning->priority === 'urgent' ? 'danger' : 'warning' }}">{{ ucfirst($cleaning->priority) }}</span>
                                            </td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $cleaning->status)) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No pending requests</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
