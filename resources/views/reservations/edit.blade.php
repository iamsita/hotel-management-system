@extends('layout')

@section('title', 'Edit Reservation')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4"><i class="fas fa-calendar-alt"></i> Edit Reservation</h1>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('reservations.update', $reservation) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="user_id" class="form-label">Guest *</label>
                                <select class="form-select @error('user_id') is-invalid @enderror" id="user_id"
                                    name="user_id" required>
                                    <option value="">Select Guest</option>
                                    @foreach ($guests as $user)
                                        <option value="{{ $user->id }}"
                                            {{ $reservation->user_id === $user->id ? 'selected' : '' }}>
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
                                            value="{{ $reservation->check_in_date->format('Y-m-d\TH:i') }}">
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
                                            value="{{ $reservation->check_out_date->format('Y-m-d\TH:i') }}">
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
                                            @foreach ($rooms as $room)
                                                <option value="{{ $room->id }}"
                                                    {{ $reservation->room_id === $room->id ? 'selected' : '' }}>
                                                    {{ $room->room_number }} ({{ ucfirst($room->room_type) }})
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
                                            value="{{ $reservation->number_of_guests }}">
                                        @error('number_of_guests')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="special_requests" class="form-label">Special Requests</label>
                                <textarea class="form-control @error('special_requests') is-invalid @enderror" id="special_requests"
                                    name="special_requests" rows="3">{{ $reservation->special_requests }}</textarea>
                                @error('special_requests')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Reservation
                                </button>
                                <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-secondary">
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
