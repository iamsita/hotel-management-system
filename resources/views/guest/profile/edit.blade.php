<!DOCTYPE html>
<html>

<head>
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>My Profile</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <form action="{{ route('guest.profile.update') }}" method="POST" class="card p-4">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                name="first_name" id="first_name"
                                value="{{ old('first_name', auth()->user()->first_name) }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                name="last_name" id="last_name"
                                value="{{ old('last_name', auth()->user()->last_name) }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
                        <small class="text-muted">Email cannot be changed</small>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone"
                            id="phone" value="{{ old('phone', auth()->user()->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="id_type" class="form-label">ID Type</label>
                            <input type="text" class="form-control @error('id_type') is-invalid @enderror"
                                name="id_type" id="id_type" value="{{ old('id_type', auth()->user()->id_type) }}"
                                placeholder="e.g., Passport, License">
                            @error('id_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="id_number" class="form-label">ID Number</label>
                            <input type="text" class="form-control @error('id_number') is-invalid @enderror"
                                name="id_number" id="id_number"
                                value="{{ old('id_number', auth()->user()->id_number) }}">
                            @error('id_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" name="address"
                            id="address" value="{{ old('address', auth()->user()->address) }}" required>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror"
                                name="city" id="city" value="{{ old('city', auth()->user()->city) }}" required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror"
                                name="country" id="country" value="{{ old('country', auth()->user()->country) }}"
                                required>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="guest_type" class="form-label">Guest Type</label>
                        <select class="form-select @error('guest_type') is-invalid @enderror" name="guest_type"
                            id="guest_type">
                            <option value="individual"
                                {{ auth()->user()->guest_type === 'individual' ? 'selected' : '' }}>Individual
                            </option>
                            <option value="vip" {{ auth()->user()->guest_type === 'vip' ? 'selected' : '' }}>VIP
                            </option>
                        </select>
                        @error('guest_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="{{ route('guest.dashboard') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Account Status</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Member Since:</strong></p>
                        <p>{{ auth()->user()->created_at->format('M d, Y') }}</p>
                        <hr>
                        <p><strong>Account Status:</strong></p>
                        <span class="badge bg-success">Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
