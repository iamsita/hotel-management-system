@extends('layout')

@section('title', 'Guests Management')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="fas fa-users"></i> Guests Management</h1>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('guests.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Guest
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Guests</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Type</th>
                                <th>Reservations</th>
                                <th>Member Since</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($guests as $guest)
                                <tr>
                                    <td><strong>{{ $guest->full_name }}</strong></td>
                                    <td>{{ $guest->email }}</td>
                                    <td>{{ $guest->phone ?? '-' }}</td>
                                    <td>
                                        <span class="badge"
                                            style="background: {{ $guest->guest_type === 'individual' ? '#3498db' : '#e74c3c' }}">
                                            {{ ucfirst($guest->guest_type) }}
                                        </span>
                                    </td>
                                    <td>{{ $guest->reservations->count() }}</td>
                                    <td>{{ $guest->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('guests.show', $guest) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('guests.edit', $guest) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('guests.destroy', $guest) }}"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <nav>{{ $guests->links() }}</nav>
            </div>
        </div>
    </div>
@endsection
