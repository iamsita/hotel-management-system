@extends('guest-layout')

@section('title', 'Request Cleaning')
@section('page-title', 'Request Cleaning Service')

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">New Cleaning Request</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('guest.cleaning.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="reservation_id" class="form-label">Select Booking</label>
                            <select class="form-select @error('reservation_id') is-invalid @enderror" name="reservation_id"
                                id="reservation_id" required>
                                <option value="">-- Select Booking --</option>
                                @foreach ($reservations as $reservation)
                                    <option value="{{ $reservation->id }}"
                                        {{ old('reservation_id') == $reservation->id ? 'selected' : '' }}>
                                        Room {{ $reservation->room->room_number }} -
                                        {{ $reservation->check_in_date->format('M d, Y') }}
                                        to {{ $reservation->check_out_date->format('M d, Y') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reservation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="request_type" class="form-label">Service Type</label>
                            <select class="form-select @error('request_type') is-invalid @enderror" name="request_type"
                                id="request_type" required>
                                <option value="">-- Select Type --</option>
                                <option value="towels" {{ old('request_type') == 'towels' ? 'selected' : '' }}>Towel
                                    Replacement</option>
                                <option value="cleaning" {{ old('request_type') == 'cleaning' ? 'selected' : '' }}>Room
                                    Cleaning</option>
                                <option value="linens" {{ old('request_type') == 'linens' ? 'selected' : '' }}>Linen Change
                                </option>
                                <option value="maintenance" {{ old('request_type') == 'maintenance' ? 'selected' : '' }}>
                                    Maintenance</option>
                            </select>
                            @error('request_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select @error('priority') is-invalid @enderror" name="priority"
                                id="priority" required>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Additional Details</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description"
                                rows="4" placeholder="Describe your request...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Request
                            </button>
                            <a href="{{ route('guest.cleaning.requests') }}" class="btn btn-secondary">View My Requests</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
