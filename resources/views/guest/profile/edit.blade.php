@extends('guest-layout')

@section('title', 'Profile')
@section('page-title', 'My Profile')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('guest.profile.update') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                id="name" value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="{{ route('guest.dashboard') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Account Status</h5>
                </div>
                <div class="card-body">
                    <p><strong>Member Since:</strong></p>
                    <p>{{ auth()->user()->created_at->format('M d, Y') }}</p>
                    <hr>
                    <p><strong>Account Status:</strong></p>
                    <span class="badge bg-{{ auth()->user()->status === 'active' ? 'success' : 'danger' }}">
                        {{ ucfirst(auth()->user()->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection
