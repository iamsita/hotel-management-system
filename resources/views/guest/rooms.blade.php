@extends('layout')
@section('title', 'Browse Rooms')
@section('content')
<h4 class="mb-4">Available Rooms</h4>

<div class="row g-3">
    @forelse($rooms as $room)
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">Room {{ $room->room_number }}</h5>
                <p class="mb-1"><strong>Type:</strong> {{ ucfirst($room->type) }}</p>
                <p class="mb-1"><strong>Floor:</strong> {{ $room->floor }}</p>
                <p class="mb-1"><strong>Capacity:</strong> {{ $room->capacity }} guests</p>
                <p class="mb-3"><strong>Price:</strong> <span class="text-success fw-bold">${{ number_format($room->price_per_night, 2) }}/night</span></p>

                <form method="POST" action="{{ route('guest.book') }}">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <div class="mb-2">
                        <input type="date" name="check_in" class="form-control form-control-sm" placeholder="Check In" required>
                    </div>
                    <div class="mb-2">
                        <input type="date" name="check_out" class="form-control form-control-sm" placeholder="Check Out" required>
                    </div>
                    <div class="mb-2">
                        <input type="number" name="guests" class="form-control form-control-sm" value="1" min="1" max="{{ $room->capacity }}" placeholder="Guests" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100" style="background:#1a3263;border:none">Book Now</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-4">
                <p class="text-muted mb-0">No rooms available at the moment.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
