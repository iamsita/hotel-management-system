@extends('layout')

@section('title', 'My Profile')

@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2>My Profile</h2>
                    </div>
                    <div class="card-body">
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

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Name:</strong></p>
                                <p class="text-muted">{{ $user->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Email:</strong></p>
                                <p class="text-muted">{{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Phone:</strong></p>
                                <p class="text-muted">{{ $user->phone ?? 'Not provided' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Role:</strong></p>
                                <p class="text-muted">
                                    <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'info' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Member Since:</strong></p>
                                <p class="text-muted">{{ $user->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Last Updated:</strong></p>
                                <p class="text-muted">{{ $user->updated_at->format('M d, Y H:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back to Admin</a>
                    @else
                        <a href="{{ route('guest.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                    @endif
                </div>
            </div>


        </div>
    </div>
@endsection
