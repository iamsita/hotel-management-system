@extends('guest-layout')

@section('title', 'Guest Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold mb-0" style="color: #1A3263;">Welcome back, {{ Auth::user()->name }}!</h3>
            <p class="text-muted">Manage your stay and hotel services from here.</p>
        </div>
        <a href="{{ route('guest.booking.create') }}" class="btn btn-indigo shadow-sm">
            <i class="fas fa-plus me-2"></i>New Reservation
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3" style="background: rgba(26,50,99,0.1); color: #1A3263; padding: 10px; border-radius: 10px;">
                        <i class="fas fa-history fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Total Stays</small>
                        <span class="h5 fw-bold mb-0">{{ $totalBookings }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3" style="background: rgba(40,167,69,0.1); color: #28a745; padding: 10px; border-radius: 10px;">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Lifetime Spend</small>
                        <span class="h5 fw-bold mb-0">Rs. {{ number_format($totalSpent, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3" style="background: rgba(255,193,7,0.1); color: #ffc107; padding: 10px; border-radius: 10px;">
                        <i class="fas fa-file-invoice-dollar fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Running Balance</small>
                        <span class="h5 fw-bold mb-0">Rs. {{ number_format($runningBalance ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3" style="background: rgba(79,70,229,0.1); color: #4f46e5; padding: 10px; border-radius: 10px;">
                        <i class="fas fa-concierge-bell fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Loyalty Tier</small>
                        <span class="h5 fw-bold mb-0">Silver Member</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($activeBooking)
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-key me-2 text-primary"></i>Your Current Stay</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center text-md-start">
                            <div class="col-md-3 mb-3 mb-md-0">
                                <span class="text-muted small">Room Number</span>
                                <h4 class="fw-bold mb-0">{{ $activeBooking->room->room_number }}</h4>
                                <span class="badge bg-light text-dark">{{ ucfirst($activeBooking->room->room_type) }}</span>
                            </div>
                            <div class="col-md-3 mb-3 mb-md-0">
                                <span class="text-muted small">Check-In</span>
                                <p class="fw-bold mb-0">{{ $activeBooking->check_in_date->format('D, M d') }}</p>
                            </div>
                            <div class="col-md-3 mb-3 mb-md-0">
                                <span class="text-muted small">Check-Out</span>
                                <p class="fw-bold mb-0">{{ $activeBooking->check_out_date->format('D, M d') }}</p>
                            </div>
                            <div class="col-md-3 d-flex align-items-center justify-content-center">
                                <a href="{{ route('guest.booking.show', $activeBooking) }}" class="btn btn-outline-primary btn-sm w-100">View Folio</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Recent Service Orders</h6>
                        <a href="{{ route('guest.food.menu') }}" class="btn btn-link btn-sm text-decoration-none p-0">New Order</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 small text-muted">Item</th>
                                        <th class="border-0 small text-muted text-center">Qty</th>
                                        <th class="border-0 small text-muted">Price</th>
                                        <th class="border-0 small text-muted">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentOrders as $order)
                                        <tr>
                                            <td>{{ $order->food->name }}</td>
                                            <td class="text-center">{{ $order->quantity }}</td>
                                            <td>Rs. {{ number_format($order->price, 2) }}</td>
                                            <td>
                                                @php
                                                    $badgeClass = match($order->status) {
                                                        'pending' => 'warning',
                                                        'delivered' => 'success',
                                                        'cancelled' => 'danger',
                                                        default => 'info'
                                                    };
                                                @endphp
                                                <span class="badge rounded-pill bg-{{ $badgeClass }}">{{ ucfirst($order->status) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No orders placed during this stay.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4" style="background: #1A3263; color: white;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">Room Services</h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('guest.food.menu') }}" class="btn btn-light text-start py-2">
                                <i class="fas fa-utensils me-2 text-success"></i> Order Food & Drinks
                            </a>
                            <a href="{{ route('guest.cleaning.create') }}" class="btn btn-light text-start py-2">
                                <i class="fas fa-broom me-2 text-warning"></i> Request Cleaning
                            </a>
                            <a href="#" class="btn btn-light text-start py-2">
                                <i class="fas fa-tshirt me-2 text-primary"></i> Laundry Service
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-wifi fa-2x text-primary"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Room Wi-Fi</h6>
                        <p class="text-muted small">SSID: HMS_Guest_WiFi</p>
                        <div class="bg-light p-2 rounded border border-dashed">
                            <code class="fw-bold" style="color: #1A3263;">HMS-{{ date('Y') }}-{{ $activeBooking->room->room_number }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm py-5 text-center">
            <div class="card-body">
                <i class="fas fa-bed fa-4x text-light mb-3"></i>
                <h5>No Active Reservation</h5>
                <p class="text-muted">You are not currently checked into any room.</p>
                <a href="{{ route('guest.booking.create') }}" class="btn btn-indigo px-4">Book Your Next Stay</a>
            </div>
        </div>
    @endif
</div>
@endsection