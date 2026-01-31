@extends('guest-layout')

@section('title', 'New Booking')
@section('page-title', 'Make a Booking')

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Create New Booking</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('guest.booking.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Check-in Date</label>
                                <input type="date" name="check_in_date"
                                    class="form-control @error('check_in_date') is-invalid @enderror" required>
                                @error('check_in_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Check-out Date</label>
                                <input type="date" name="check_out_date"
                                    class="form-control @error('check_out_date') is-invalid @enderror" required>
                                @error('check_out_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Number of Guests</label>
                                <input type="number" name="number_of_guests"
                                    class="form-control @error('number_of_guests') is-invalid @enderror" min="1"
                                    value="1" required>
                                @error('number_of_guests')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Room</label>
                                <select name="room_id" class="form-control @error('room_id') is-invalid @enderror" required>
                                    <option value="">Select Room</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}">
                                            Room {{ $room->room_number }} - {{ ucfirst($room->room_type) }}
                                            (${{ $room->price_per_night }}/night)
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Special Requests</label>
                            <textarea name="special_requests" class="form-control @error('special_requests') is-invalid @enderror" rows="3"></textarea>
                            @error('special_requests')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Complete Booking</button>
                            <a href="{{ route('guest.dashboard') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
