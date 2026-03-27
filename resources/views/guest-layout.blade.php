<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Guest Portal</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-color: #ecf0f1;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
            min-height: 100vh;
            padding: 20px 0;
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            z-index: 1000;
            color: white;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar-brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .sidebar-brand h4 {
            margin: 0;
            color: white;
        }

        .sidebar-brand small {
            color: rgba(255, 255, 255, 0.7);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: var(--secondary-color);
        }

        .sidebar-menu .submenu {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .sidebar-menu li.active .submenu {
            max-height: 500px;
        }

        .sidebar-menu .submenu a {
            padding-left: 40px;
            font-size: 0.9rem;
        }

        main {
            margin-left: 250px;
            padding: 20px;
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
            color: white;
            border: none;
            border-radius: 8px 8px 0 0;
        }

        .btn-primary {
            background: var(--secondary-color);
            border: none;
        }

        .btn-primary:hover {
            background: #2980b9;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: center;
            border-top: 4px solid var(--secondary-color);
        }

        .stat-card h6 {
            color: #666;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .status-available {
            background: #d4edda;
            color: #155724;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-checked-in {
            background: #cce5ff;
            color: #004085;
        }

        .status-checked-out {
            background: #e2e3e5;
            color: #383d41;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .table thead th {
            background: var(--primary-color);
            color: white;
            border: none;
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-color);
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .user-info {
            padding: 15px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: auto;
            font-size: 0.9rem;
        }

        .user-info small {
            color: rgba(255, 255, 255, 0.7);
            display: block;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            main {
                margin-left: 200px;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
                margin-bottom: 20px;
            }

            main {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="sidebar">
        <div class="sidebar-brand">
            <h4><i class="fas fa-bed"></i> Guest Portal</h4>
            <small>Hotel Management</small>
        </div>
        <ul class="sidebar-menu">
            <li @if (Route::currentRouteName() === 'guest.dashboard') class="active" @endif>
                <a href="{{ route('guest.dashboard') }}" @if (Route::currentRouteName() === 'guest.dashboard') class="active" @endif>
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
            </li>

            <li @if (str_starts_with(Route::currentRouteName(), 'guest.booking')) class="active" @endif>
                <a href="#bookingMenu" data-toggle="collapse">
                    <i class="fas fa-calendar-check"></i> Bookings
                </a>
                <ul class="submenu" id="bookingMenu">
                    <li><a href="{{ route('guest.bookings') }}">My Bookings</a></li>
                    <li><a href="{{ route('guest.booking.create') }}">New Booking</a></li>
                </ul>
            </li>

            <li @if (str_starts_with(Route::currentRouteName(), 'guest.food') ||
                    str_starts_with(Route::currentRouteName(), 'guest.orders')) class="active" @endif>
                <a href="#foodMenu" data-toggle="collapse">
                    <i class="fas fa-utensils"></i> Food & Orders
                </a>
                <ul class="submenu" id="foodMenu">
                    <li><a href="{{ route('guest.food.menu') }}">Menu</a></li>
                    <li><a href="{{ route('guest.orders') }}">Order History</a></li>
                </ul>
            </li>

            <li @if (str_starts_with(Route::currentRouteName(), 'guest.cleaning')) class="active" @endif>
                <a href="#cleaningMenu" data-toggle="collapse">
                    <i class="fas fa-broom"></i> Cleaning Requests
                </a>
                <ul class="submenu" id="cleaningMenu">
                    <li><a href="{{ route('guest.cleaning.requests') }}">My Requests</a></li>
                    <li><a href="{{ route('guest.cleaning.create') }}">Request Cleaning</a></li>
                </ul>
            </li>

            <li @if (str_starts_with(Route::currentRouteName(), 'guest.payment')) class="active" @endif>
                <a href="#paymentMenu" data-toggle="collapse">
                    <i class="fas fa-credit-card"></i> Payments
                </a>
                <ul class="submenu" id="paymentMenu">
                    <li><a href="{{ route('guest.payments') }}">Payment History</a></li>
                    <li><a href="{{ route('guest.payment.create') }}">Make Payment</a></li>
                </ul>
            </li>

            <li @if (Route::currentRouteName() === 'guest.profile') class="active" @endif>
                <a href="{{ route('guest.profile') }}" @if (Route::currentRouteName() === 'guest.profile') class="active" @endif>
                    <i class="fas fa-user"></i> Profile
                </a>
            </li>
        </ul>

        <div class="user-info">
            <div><strong>{{ Auth::user()->name }}</strong></div>
            <small>{{ Auth::user()->email }}</small>
        </div>
    </div>

    <main>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>@yield('page-title')</h2>
            <div>
                <form action="{{ route('guest.logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        // Sidebar menu toggle
        document.querySelectorAll('.sidebar-menu a[href^="#"]').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const submenu = document.querySelector(this.getAttribute('href'));
                if (submenu) {
                    submenu.parentElement.classList.toggle('active');
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
