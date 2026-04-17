@extends('layout')
@section('title', 'Edit Reservation')
@section('content')
<h4 class="mb-4">Edit Reservation #{{ $reservation->id }}</h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.reservations.update', $reservation) }}">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Guest</label>
                    <select name="user_id" class="form-select" required>
                        @foreach($guests as $guest)
                            <option value="{{ $guest->id }}" {{ $reservation->user_id == $guest->id ? 'selected' : '' }}>{{ $guest->name }} ({{ $guest->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Room</label>
                    <select name="room_id" class="form-select" required>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ $reservation->room_id == $room->id ? 'selected' : '' }}>{{ $room->room_number }} - {{ ucfirst($room->type) }} (Rs. {{ $room->price_per_night }}/night)</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Check In</label>
                    <input type="date" name="check_in" class="form-control" value="{{ $reservation->check_in->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Check Out</label>
                    <input type="date" name="check_out" class="form-control" value="{{ $reservation->check_out->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Guests</label>
                    <input type="number" name="guests" class="form-control" value="{{ $reservation->guests }}" min="1" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        @foreach(['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'] as $status)
                            <option value="{{ $status }}" {{ $reservation->status === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="background:#1a3263;border:none">Update Reservation</button>
            <a href="{{ route('admin.reservations.show', $reservation) }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
