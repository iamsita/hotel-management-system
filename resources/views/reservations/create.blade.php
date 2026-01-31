@extends('layout')

@section('title', 'New Reservation')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4"><i class="fas fa-calendar-plus"></i> New Reservation</h1>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('reservations.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="user_id" class="form-label">Guest *</label>
                                <select class="form-select @error('user_id') is-invalid @enderror" id="user_id"
                                    name="user_id" required>
                                    <option value="">Select Guest</option>
                                    @foreach ($guests as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('user_id') === (string) $user->id ? 'selected' : '' }}>
                                            {{ $user->full_name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="check_in_date" class="form-label">Check-in Date *</label>
                                        <input type="datetime-local"
                                            class="form-control @error('check_in_date') is-invalid @enderror"
                                            id="check_in_date" name="check_in_date" required
                                            value="{{ old('check_in_date') }}">
                                        @error('check_in_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="check_out_date" class="form-label">Check-out Date *</label>
                                        <input type="datetime-local"
                                            class="form-control @error('check_out_date') is-invalid @enderror"
                                            id="check_out_date" name="check_out_date" required
                                            value="{{ old('check_out_date') }}">
                                        @error('check_out_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="room_id" class="form-label">Room *</label>
                                        <select class="form-select @error('room_id') is-invalid @enderror" id="room_id"
                                            name="room_id" required>
                                            <option value="">Select Room</option>
                                            @foreach ($availableRooms as $room)
                                                <option value="{{ $room->id }}"
                                                    {{ old('room_id') === (string) $room->id ? 'selected' : '' }}>
                                                    {{ $room->room_number }} ({{ ucfirst($room->room_type) }} -
                                                    ${{ number_format($room->price_per_night, 2) }}/night)
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('room_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="number_of_guests" class="form-label">Number of Guests *</label>
                                        <input type="number"
                                            class="form-control @error('number_of_guests') is-invalid @enderror"
                                            id="number_of_guests" name="number_of_guests" min="1" required
                                            value="{{ old('number_of_guests', 1) }}">
                                        @error('number_of_guests')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="special_requests" class="form-label">Special Requests</label>
                                <textarea class="form-control @error('special_requests') is-invalid @enderror" id="special_requests"
                                    name="special_requests" rows="3">{{ old('special_requests') }}</textarea>
                                @error('special_requests')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Reservation
                                </button>
                                <a href="{{ route('reservations.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
