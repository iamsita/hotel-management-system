<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: #1a3263;
            min-height: 100vh;
            width: 220px;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
        }

        .sidebar .brand {
            color: #fff;
            text-align: center;
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 10px;
        }

        .sidebar a {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        main {
            margin-left: 220px;
            padding: 20px;
            background: #f8f9fa;
            min-height: 100vh;
        }

        .table thead th {
            background: #1a3263;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="brand">
            <h5 class="mb-0">HMS</h5>
            <small class="text-white-50">Hotel Management</small>
            <small class="text-white-50">{{ auth()->user()?->email }}</small>

        </div>

        @if (auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}"
                class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('admin.rooms.index') }}"
                class="{{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">Rooms</a>
            <a href="{{ route('admin.reservations.index') }}"
                class="{{ request()->routeIs('admin.reservations.*') ? 'active' : '' }}">Reservations</a>
            <a href="{{ route('admin.guests.index') }}"
                class="{{ request()->routeIs('admin.guests.*') ? 'active' : '' }}">Guests</a>
            <a href="{{ route('admin.foods.index') }}"
                class="{{ request()->routeIs('admin.foods.*') ? 'active' : '' }}">Food Menu</a>
            <a href="{{ route('admin.food-orders.index') }}"
                class="{{ request()->routeIs('admin.food-orders.*') ? 'active' : '' }}">Food Orders</a>
            <a href="{{ route('admin.payments.index') }}"
                class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">Payments</a>
        @else
            <a href="{{ route('guest.dashboard') }}"
                class="{{ request()->routeIs('guest.dashboard') ? 'active' : '' }}">My Bookings</a>
            <a href="{{ route('guest.rooms') }}"
                class="{{ request()->routeIs('guest.rooms') ? 'active' : '' }}">Browse Rooms</a>
            <a href="{{ route('guest.menu') }}" class="{{ request()->routeIs('guest.menu') ? 'active' : '' }}">Food
                Menu</a>
        @endif

        <a href="{{ route('profile.show') }}"
            class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">Profile</a>

        <hr class="border-secondary mx-3">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                style="display:block;width:100%;background:none;border:none;color:rgba(255,255,255,0.8);padding:10px 20px;text-align:left;font-size:0.9rem;cursor:pointer;">Logout</button>
        </form>
    </div>

    <main>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
