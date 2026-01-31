@extends('layout')

@section('title', 'Guest Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="fas fa-user"></i> {{ $user->full_name }}</h1>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('guests.edit', $user) }}" class="btn btn-info">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Guest Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Full Name:</strong> {{ $user->full_name }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Phone:</strong> {{ $user->phone ?? '-' }}</p>
                        <p><strong>Type:</strong> <span class="badge"
                                style="background: {{ $user->guest_type === 'individual' ? '#3498db' : '#e74c3c' }}">{{ ucfirst($user->guest_type) }}</span>
                        </p>
                        <p><strong>ID Type:</strong> {{ ucfirst(str_replace('_', ' ', $user->id_type ?? '-')) }}</p>
                        <p><strong>ID Number:</strong> {{ $user->id_number ?? '-' }}</p>
                        <p><strong>Address:</strong> {{ $user->address ?? '-' }}</p>
                        <p><strong>City:</strong> {{ $user->city ?? '-' }}</p>
                        <p><strong>Country:</strong> {{ $user->country ?? '-' }}</p>
                        <p><strong>Member Since:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Reservations ({{ $user->reservations->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if ($user->reservations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Room</th>
                                            <th>Check-in</th>
                                            <th>Check-out</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user->reservations as $reservation)
                                            <tr>
                                                <td>{{ $reservation->room->room_number }}</td>
                                                <td>{{ $reservation->check_in_date->format('M d, Y') }}</td>
                                                <td>{{ $reservation->check_out_date->format('M d, Y') }}</td>
                                                <td>
                                                    <span class="status-badge status-{{ $reservation->status }}">
                                                        {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                                                    </span>
                                                </td>
                                                <td>${{ number_format($reservation->total_amount, 2) }}</td>
                                                <td>
                                                    <a href="{{ route('reservations.show', $reservation) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No reservations yet</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
