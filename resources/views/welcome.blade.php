<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Management System | Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-indigo:#1A3263;
            --dark-slate: #1e293b;
            --soft-slate: #475569;
            --bg-neutral: #f8fafc;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: var(--dark-slate);
            margin: 0;
        }

        /* Navigation */
        .navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-indigo) !important;
            letter-spacing: -0.02em;
        }

        .nav-link {
            font-weight: 500;
            color: var(--soft-slate) !important;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--primary-indigo) !important;
        }

        /* Hero Section */
        .hero {
            padding: 100px 0;
            background: linear-gradient(to bottom, #f8fafc, #ffffff);
            text-align: center;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--dark-slate);
            letter-spacing: -0.04em;
            margin-bottom: 1.5rem;
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--soft-slate);
            max-width: 700px;
            margin: 0 auto 2.5rem;
            line-height: 1.6;
        }

        /* Features Grid */
        .feature-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 2.5rem;
            height: 100%;
            transition: all 0.3s ease;
            background: #ffffff;
        }

        .feature-card:hover {
            border-color: var(--primary-indigo);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
            transform: translateY(-5px);
        }

        .icon-box {
            width: 56px;
            height: 56px;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-indigo);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .feature-card h4 {
            font-weight: 700;
            font-size: 1.15rem;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: var(--soft-slate);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 0;
        }

        /* Custom Buttons */
        .btn-indigo {
            background-color: var(--primary-indigo);
            color: white;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            transition: background 0.2s;
        }

        .btn-indigo:hover {
            background-color:rgb(91, 79, 230);
            color: white;
        }

        .btn-outline-indigo {
            border: 2px solid var(--primary-indigo);
            color: var(--primary-indigo);
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 8px;
            background: transparent;
            transition: all 0.2s;
        }

        .btn-outline-indigo:hover {
            background: var(--primary-indigo);
            color: white;
        }

        /* Footer */
        footer {
            border-top: 1px solid #e2e8f0;
            padding: 4rem 0 2rem;
            background: #f8fafc;
            margin-top: 6rem;
        }

        .footer-text {
            color: #94a3b8;
            font-size: 0.875rem;
        }

        .footer-link {
            color: var(--soft-slate);
            text-decoration: none;
            margin: 0 10px;
        }

        .footer-link:hover {
            color: var(--primary-indigo);
        }

        @media (max-width: 768px) {
            .hero h1 { font-size: 2.5rem; }
            .hero { padding: 60px 0; }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-hotel me-2"></i>HMS Core
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    @auth
                        <li class="nav-item">
                            <a class="btn btn-indigo btn-sm px-4" href="{{ Auth::user()->type === 'guest' ? route('guest.dashboard') : route('dashboard') }}">
                                <i class="fas fa-columns me-1"></i> Dashboard
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link px-3" href="{{ route('login') }}">Sign In</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-indigo btn-sm px-4 ms-lg-3" href="{{ route('register') }}">Get Started</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container">
            <span class="badge rounded-pill px-3 py-2 mb-3" style="background: rgba(79,70,229,0.1); color: var(--primary-indigo);">v2.0 Now Live</span>
            <h1>Modern Hospitality <br><span style="color: var(--primary-indigo)">Management</span></h1>
            <p>A unified, web-based platform designed to streamline reservations, room service, and billing for the modern hotel industry in Nepal.</p>
            
            <div class="d-flex justify-content-center gap-3">
                @auth
                    <a href="{{ Auth::user()->type === 'guest' ? route('guest.dashboard') : route('dashboard') }}" class="btn btn-indigo shadow-sm">Enter Dashboard</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-indigo shadow-sm">Create Account</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-indigo">Staff Login</a>
                @endauth
            </div>
        </div>
    </section>

    <section class="container mb-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Powerful Features</h2>
            <p class="text-muted">Everything you need to manage your hotel operations efficiently.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="icon-box"><i class="fas fa-door-open"></i></div>
                    <h4>Room Management</h4>
                    <p>Track real-time room availability, maintenance schedules, and cleaning status in one view.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="icon-box"><i class="fas fa-calendar-check"></i></div>
                    <h4>Smart Reservations</h4>
                    <p>Prevent double bookings with our intelligent date-validation system and guest database.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="icon-box"><i class="fas fa-receipt"></i></div>
                    <h4>Auto Billing</h4>
                    <p>Instantly consolidate room rent, food orders, and service charges into one printable invoice.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="icon-box"><i class="fas fa-utensils"></i></div>
                    <h4>Service Orders</h4>
                    <p>Allow guests to order food or laundry services directly linked to their room reservation.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="icon-box"><i class="fas fa-chart-pie"></i></div>
                    <h4>Staff Insights</h4>
                    <p>Access detailed reports on daily occupancy and revenue performance from your dashboard.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="icon-box"><i class="fas fa-shield-halved"></i></div>
                    <h4>Secure RBAC</h4>
                    <p>Role-based security ensuring staff only see what they need—keeping financial data safe.</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container text-center">
            <p class="fw-bold mb-2">HMS Core</p>
            <div class="mb-3">
                <a href="#" class="footer-link">Privacy</a>
                <a href="#" class="footer-link">Terms</a>
                <a href="mailto:support@hms.test" class="footer-link">Support</a>
            </div>
            <p class="footer-text">&copy; 2026 Hotel Management System. Developed for BCA Project.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>