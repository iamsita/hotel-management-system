@extends('guest-layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <h6>Total Bookings</h6>
                <div class="value">{{ $totalBookings }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h6>Total Spent</h6>
                <div class="value">${{ number_format($totalSpent, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h6>Active Booking</h6>
                <div class="value">{{ $activeBooking ? 'Yes' : 'No' }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h6>New Booking</h6>
                <a href="{{ route('guest.booking.create') }}" class="btn btn-primary btn-sm">Book Now</a>
            </div>
        </div>
    </div>

    @if ($activeBooking)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Active Booking</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Room:</strong> {{ $activeBooking->room->room_number }}
                    </div>
                    <div class="col-md-3">
                        <strong>Check-in:</strong> {{ $activeBooking->check_in_date->format('M d, Y') }}
                    </div>
                    <div class="col-md-3">
                        <strong>Check-out:</strong> {{ $activeBooking->check_out_date->format('M d, Y') }}
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('guest.booking.show', $activeBooking) }}" class="btn btn-sm btn-primary">View
                            Details</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('guest.food.menu') }}" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-utensils"></i> Order Food/Drink
                        </a>
                        <a href="{{ route('guest.cleaning.create') }}" class="btn btn-warning w-100">
                            <i class="fas fa-broom"></i> Request Cleaning
                        </a>
                    </div>
                </div>
            </div>

            @if ($recentOrders->count() > 0)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Orders</h5>
                        </div>
                        <div class="card-body">
                            @foreach ($recentOrders as $order)
                                <p class="mb-2">
                                    <strong>{{ $order->food->name }}</strong><br>
                                    Qty: {{ $order->quantity }} | Status:
                                    <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : 'warning' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
@endsection
