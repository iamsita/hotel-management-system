<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | HMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; /* Clean, soft neutral */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .register-card {
            width: 100%;
            max-width: 500px;
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .card-title {
            color: #1e293b;
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
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .btn-primary {
            background-color: #4f46e5;
            border: none;
            padding: 0.75rem;
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
            border: none;
        }

        .hr-text {
            display: flex;
            align-items: center;
            text-align: center;
            color: #94a3b8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 1.5rem 0;
        }

        .hr-text::before, .hr-text::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .hr-text:not(:empty)::before { margin-right: .75em; }
        .hr-text:not(:empty)::after { margin-left: .75em; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="register-card card">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <h2 class="card-title h4">Create Account</h2>
                <p class="text-muted small">Join our system to manage your stay seamlessly.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger shadow-sm">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           name="name" id="name" placeholder="John Doe"
                           value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" id="email" placeholder="name@company.com"
                               value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                               name="phone" id="phone" placeholder="98XXXXXXXX"
                               value="{{ old('phone') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               name="password" id="password" placeholder="••••••••" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control"
                               name="password_confirmation" id="password_confirmation"
                               placeholder="••••••••" required>
                    </div>
                </div>

                <input type="hidden" name="type" value="guest">

                <button type="submit" class="btn btn-primary w-100 shadow-sm">
                    Register Account
                </button>

                <div class="hr-text">or</div>

                <div class="text-center">
                    <p class="mb-0 small text-muted">Already have an account?
                        <a href="{{ route('login') }}" class="btn-link fw-medium">Login here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
