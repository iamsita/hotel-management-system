<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login | HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; /* Very light neutral gray */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .card-title {
            color: #1e293b; /* Dark slate */
            font-weight: 600;
            letter-spacing: -0.025em;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #475569;
        }

        .form-control {
            padding: 0.625rem 0.875rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .btn-primary {
            background-color: #4f46e5; /* Modern Indigo */
            border: none;
            padding: 0.625rem;
            font-weight: 500;
            border-radius: 8px;
            transition: background-color 0.2s;
        }

        .btn-primary:hover {
            background-color: #4338ca;
        }

        .btn-link {
            color: #64748b;
            text-decoration: none;
            font-size: 0.875rem;
        }

        .btn-link:hover {
            color: #4f46e5;
            text-decoration: underline;
        }

        .alert {
            font-size: 0.875rem;
            border-radius: 8px;
        }
    </style>

@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="login-card card">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <h2 class="card-title h4">Welcome Back</h2>
                <p class="text-muted small">Please enter your details to sign in.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger border-0">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           name="email" id="email" placeholder="name@company.com" 
                           value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label">Password</label>
                    </div>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           name="password" id="password" placeholder="••••••••" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 shadow-sm">Sign In</button>
                
                <div class="text-center mt-4">
                    <p class="mb-0 small text-muted">Don't have an account? 
                        <a href="{{ route('register') }}" class="btn-link fw-medium">Register here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>