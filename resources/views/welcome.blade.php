<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark" style="background:#1a3263">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">Hotel Management System</a>
            <div>
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('guest.dashboard') }}" class="btn btn-outline-light btn-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-light btn-sm">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold" style="color:#1a3263">Hotel Management System</h1>
            <p class="text-muted">A simple web application to manage hotel rooms, reservations, food orders, and payments.</p>
            @guest
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg mt-3" style="background:#1a3263;border:none">Get Started</a>
            @endguest
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title" style="color:#1a3263">Room Management</h5>
                        <p class="card-text text-muted">Add, edit, and track room availability and status.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title" style="color:#1a3263">Reservations</h5>
                        <p class="card-text text-muted">Book rooms, check-in/out guests, manage bookings.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title" style="color:#1a3263">Food & Payments</h5>
                        <p class="card-text text-muted">Order food, track orders, and process payments.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <h5 class="text-muted">Demo Credentials</h5>
            <p><strong>Admin:</strong> admin@gmail.com / password</p>
            <p><strong>Guest:</strong> guest@gmail.com / password</p>
        </div>
    </div>
</body>
</html>
