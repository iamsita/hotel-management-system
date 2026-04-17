@extends('layout')
@section('title', 'New Reservation')
@section('content')
    <h4 class="mb-4">New Reservation</h4>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.reservations.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guest</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Select Guest</option>
                            @foreach ($guests as $guest)
                                <option value="{{ $guest->id }}">{{ $guest->name }} ({{ $guest->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Room</label>
                        <select name="room_id" class="form-select" required>
                            <option value="">Select Room</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->room_number }} - {{ ucfirst($room->type) }}
                                    (Rs. {{ $room->price_per_night }}/night)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Check In</label>
                        <input type="date" name="check_in" class="form-control" value="{{ old('check_in') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Check Out</label>
                        <input type="date" name="check_out" class="form-control" value="{{ old('check_out') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Number of Guests</label>
                        <input type="number" name="guests" class="form-control" value="{{ old('guests', 1) }}"
                            min="1" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="background:#1a3263;border:none">Create
                    Reservation</button>
                <a href="{{ route('admin.reservations.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
