@extends('layout')

@section('title', 'User Details')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-user"></i> User Details</h1>
            <div class="btn-group" role="group">
                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;"
                    onsubmit="return confirm('Are you sure you want to delete this user?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Account Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p><strong>Name:</strong></p>
                                <p class="text-primary">{{ $user->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Email:</strong></p>
                                <p>{{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p><strong>Phone:</strong></p>
                                <p>{{ $user->phone ?? 'Not provided' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Role:</strong></p>
                                <p>
                                    <span
                                        class="badge bg-{{ $user->type === 'admin' ? 'danger' : ($user->type === 'manager' ? 'warning' : 'info') }}">
                                        {{ ucfirst($user->type) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Status:</strong></p>
                                <p>
                                    <span class="status-badge status-{{ $user->status }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Created:</strong></p>
                                <p>{{ $user->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Last Updated:</strong></p>
                                <p>{{ $user->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body d-flex flex-column gap-2">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit User
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">User Info</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>User ID:</strong><br>
                            <code>{{ $user->id }}</code>
                        </p>
                        <p>
                            <strong>Type:</strong><br>
                            <span class="badge bg-secondary">{{ $user->type }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
