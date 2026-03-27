@extends('guest-layout')

@section('title', 'My Bookings')
@section('page-title', 'My Bookings')

@section('content')
    <div class="mb-3">
        <a href="{{ route('guest.booking.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Booking
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Guests</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $res)
                        <tr>
                            <td>{{ $res->room->room_number }}</td>
                            <td>{{ $res->check_in_date->format('M d, Y') }}</td>
                            <td>{{ $res->check_out_date->format('M d, Y') }}</td>
                            <td>{{ $res->number_of_guests }}</td>
                            <td>${{ number_format($res->total_amount, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ str_replace('_', '-', $res->status) }}">
                                    {{ ucfirst(str_replace('_', ' ', $res->status)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('guest.booking.show', $res) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <p class="text-muted">No bookings found</p>
                                <a href="{{ route('guest.booking.create') }}" class="btn btn-primary btn-sm">Create Your
                                    First Booking</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($reservations->hasPages())
        <div class="mt-3">
            {{ $reservations->links() }}
        </div>
    @endif
@endsection
