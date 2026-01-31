<!DOCTYPE html>
<html>

<head>
    <title>Guest Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Hotel Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('guest.dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('guest.bookings') }}">My Bookings</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('guest.food.menu') }}">Order Food</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('guest.orders') }}">Order History</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('guest.payments') }}">Payments</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('guest.profile') }}">Profile</a></li>
                    <li class="nav-item">
                        <form action="{{ route('guest.logout') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Bookings</h5>
                        <h2>{{ $totalBookings }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Spent</h5>
                        <h2>${{ number_format($totalSpent, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Active Booking</h5>
                        <h2>{{ $activeBooking ? 'Yes' : 'No' }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">New Booking</h5>
                        <a href="{{ route('guest.booking.create') }}" class="btn btn-primary btn-sm">Book Now</a>
                    </div>
                </div>
            </div>
        </div>

        @if ($activeBooking)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5>Active Booking</h5>
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
                            <a href="{{ route('guest.booking.show', $activeBooking) }}"
                                class="btn btn-sm btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Quick Actions</div>
                        <div class="card-body">
                            <a href="{{ route('guest.food.menu') }}" class="btn btn-success btn-block w-100 mb-2">
                                <i class="fas fa-utensils"></i> Order Food/Drink
                            </a>
                            <a href="{{ route('guest.cleaning.create') }}" class="btn btn-warning btn-block w-100">
                                <i class="fas fa-broom"></i> Request Cleaning
                            </a>
                        </div>
                    </div>
                </div>

                @if ($recentOrders->count() > 0)
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">Recent Orders</div>
                            <div class="card-body">
                                @foreach ($recentOrders as $order)
                                    <p class="mb-2">
                                        <strong>{{ $order->food->name }}</strong><br>
                                        Qty: {{ $order->quantity }} | Status:
                                        <span
                                            class="badge bg-{{ $order->status === 'delivered' ? 'success' : 'warning' }}">
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
