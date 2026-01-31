<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Management System - Welcome</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #667eea !important;
        }

        .navbar-brand i {
            margin-right: 8px;
        }

        /* Hero Section */
        .hero {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 40px 20px;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            animation: slideDown 0.8s ease-out;
        }

        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 40px;
            opacity: 0.95;
            animation: slideUp 0.8s ease-out 0.2s backwards;
        }

        .feature-list {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
            margin: 40px 0;
            animation: fadeIn 0.8s ease-out 0.4s backwards;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .feature-item i {
            font-size: 1.5rem;
            color: #ffc107;
        }

        /* Buttons */
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            animation: slideUp 0.8s ease-out 0.6s backwards;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            color: white;
        }

        .btn-secondary-custom {
            background: white;
            color: #667eea;
            border: 2px solid white;
            padding: 12px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            animation: slideUp 0.8s ease-out 0.8s backwards;
        }

        .btn-secondary-custom:hover {
            background: transparent;
            color: white;
            transform: translateY(-2px);
        }

        /* Cards Section */
        .features-section {
            background: white;
            padding: 60px 20px;
            margin-top: auto;
        }

        .features-section h2 {
            text-align: center;
            margin-bottom: 50px;
            font-size: 2.5rem;
            color: #333;
            font-weight: 700;
        }

        .feature-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 30px;
            border-top: 3px solid #667eea;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .feature-card i {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 20px;
        }

        .feature-card h4 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: #333;
            font-weight: 600;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* Animations */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Footer */
        .footer {
            background: rgba(0, 0, 0, 0.3);
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }

        .footer p {
            margin: 0;
            opacity: 0.9;
        }

        .footer a {
            color: #ffc107;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer a:hover {
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-content p {
                font-size: 1.1rem;
            }

            .feature-list {
                flex-direction: column;
                gap: 15px;
            }

            .features-section h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-hotel"></i> Hotel Management System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        @if (Auth::user()->type === 'guest')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('guest.dashboard') }}">
                                    <i class="fas fa-home"></i> Guest Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <form action="{{ route('guest.logout') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="nav-link btn btn-link">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}">
                                    <i class="fas fa-chart-line"></i> Staff Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="nav-link btn btn-link">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </li>
                        @endif
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus"></i> Register
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1><i class="fas fa-building"></i> Hotel Management System</h1>
            <p>Complete solution for managing your hotel operations efficiently</p>

            <div class="feature-list">
                <div class="feature-item">
                    <i class="fas fa-door-open"></i>
                    <span>Room Management</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-calendar-check"></i>
                    <span>Reservations</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-users"></i>
                    <span>Guest Management</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-receipt"></i>
                    <span>Billing</span>
                </div>
            </div>

            <div>
                @auth
                    @if (Auth::user()->type === 'guest')
                        <a href="{{ route('guest.dashboard') }}" class="btn btn-primary-custom me-3">
                            <i class="fas fa-arrow-right"></i> Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-primary-custom me-3">
                            <i class="fas fa-arrow-right"></i> Go to Dashboard
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary-custom me-3">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-secondary-custom">
                        <i class="fas fa-user-plus"></i> Create Account
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2><i class="fas fa-star"></i> Key Features</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-door-open"></i>
                        <h4>Room Management</h4>
                        <p>Easily manage room inventory, availability, pricing, and maintenance schedules with our
                            intuitive interface.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-calendar-alt"></i>
                        <h4>Reservation System</h4>
                        <p>Handle reservations efficiently with real-time availability checking, automated
                            confirmations, and guest communication.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-users"></i>
                        <h4>Guest Management</h4>
                        <p>Maintain comprehensive guest profiles, track preferences, manage check-ins/check-outs, and
                            enhance customer experience.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-receipt"></i>
                        <h4>Billing & Invoicing</h4>
                        <p>Generate accurate invoices, track payments, manage charges, and maintain financial records
                            seamlessly.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-chart-bar"></i>
                        <h4>Reports & Analytics</h4>
                        <p>Get detailed insights with occupancy reports, revenue analysis, guest statistics, and
                            performance metrics.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-shield-alt"></i>
                        <h4>Security & Access</h4>
                        <p>Role-based access control with different user types: Admin, Manager, Staff, and Guest with
                            appropriate permissions.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Hotel Management System. All rights reserved. |
                <a href="{{ route('home') }}">Home</a> |
                <a href="mailto:support@hms.test">Support</a>
            </p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
