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
                <div class="col-md-3">
                    <small class="text-muted">Check In</small>
                    <br>{{ $r->check_in->format('M d, Y') }}
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Check Out</small>
                    <br>{{ $r->check_out->format('M d, Y') }}
                </div>
                <div class="col-md-2">
                    <small class="text-muted">Total</small>
                    <br><strong>${{ number_format($r->total_amount, 2) }}</strong>
                </div>
                <div class="col-md-2 text-end">
                    <span class="badge bg-{{ match($r->status) { 'checked_in' => 'success', 'confirmed' => 'primary', 'pending' => 'warning', 'cancelled' => 'danger', default => 'secondary' } }} mb-2">
                        {{ $r->status }}
                    </span>
                    @if(in_array($r->status, ['confirmed', 'checked_in']))
                    <br>
                    <form action="{{ route('guest.pay') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="reservation_id" value="{{ $r->id }}">
                        <input type="hidden" name="amount" value="{{ $r->total_amount }}">
                        <input type="hidden" name="method" value="card">
                        <button class="btn btn-sm btn-success">Pay Now</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endif
@endsection
