@extends('layout')
@section('title', 'My Bookings')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>My Bookings</h4>
        <a href="{{ route('guest.rooms') }}" class="btn btn-primary" style="background:#1a3263;border:none">Book a Room</a>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('guest.dashboard') }}" class="row g-2 align-items-end">
                <div class="col-sm-3">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Room number"
                        value="{{ request('search') }}">
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        @foreach (['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>
                                {{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Check In From</label>
                    <input type="date" name="check_in_from" class="form-control form-control-sm"
                        value="{{ request('check_in_from') }}">
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Check In To</label>
                    <input type="date" name="check_in_to" class="form-control form-control-sm"
                        value="{{ request('check_in_to') }}">
                </div>
                <div class="col-sm-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100"
                        style="background:#1a3263;border:none">Filter</button>
                    @if (request()->hasAny(['search', 'status', 'check_in_from', 'check_in_to']))
                        <a href="{{ route('guest.dashboard') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @forelse($reservations as $r)
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
                        <span
                            class="badge bg-{{ match ($r->status) {'checked_in' => 'success','confirmed' => 'primary','pending' => 'warning','cancelled' => 'danger',default => 'secondary'} }}">
                            {{ ucfirst(str_replace('_', ' ', $r->status)) }}
                        </span>
                    </div>
                    <div class="col-md-2 text-end">
                        <a href="{{ route('guest.reservation.show', $r) }}" class="btn btn-sm btn-outline-primary">View
                            Details</a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <h5 class="text-muted">No bookings found</h5>
                <p class="text-muted">Browse available rooms and make your first booking!</p>
                <a href="{{ route('guest.rooms') }}" class="btn btn-primary" style="background:#1a3263;border:none">Browse
                    Rooms</a>
            </div>
        </div>
    @endforelse

    @if ($reservations->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $reservations->links('pagination::bootstrap-5') }}
        </div>
    @endif
@endsection
