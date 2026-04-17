@extends('layout')
@section('title', 'My Bookings')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>My Bookings</h4>
    <a href="{{ route('guest.rooms') }}" class="btn btn-primary" style="background:#1a3263;border:none">Book a Room</a>
</div>

@if($reservations->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <h5 class="text-muted">No bookings yet</h5>
            <p class="text-muted">Browse available rooms and make your first booking!</p>
            <a href="{{ route('guest.rooms') }}" class="btn btn-primary" style="background:#1a3263;border:none">Browse Rooms</a>
        </div>
    </div>
@else
    @foreach($reservations as $r)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <strong>Room {{ $r->room->room_number }}</strong>
                    <br><small class="text-muted">{{ ucfirst($r->room->type) }}</small>
                </div>
                <div class="col-md-2">
                    <small class="text-muted">Check In</small>
                    <br>{{ $r->check_in->format('M d, Y') }}
                </div>
                <div class="col-md-2">
                    <small class="text-muted">Check Out</small>
                    <br>{{ $r->check_out->format('M d, Y') }}
                </div>
                <div class="col-md-2">
                    <small class="text-muted">Total</small>
                    <br><strong>Rs. {{ number_format($r->total_amount, 2) }}</strong>
                </div>
                <div class="col-md-2">
                    <span class="badge bg-{{ match($r->status) { 'checked_in' => 'success', 'confirmed' => 'primary', 'pending' => 'warning', 'cancelled' => 'danger', default => 'secondary' } }}">
                        {{ ucfirst(str_replace('_', ' ', $r->status)) }}
                    </span>
                </div>
                <div class="col-md-2 text-end">
                    <a href="{{ route('guest.reservation.show', $r) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endif
@endsection
