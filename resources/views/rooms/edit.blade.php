@extends('layout')

@section('title', 'Edit Room')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4"><i class="fas fa-door-open"></i> Edit Room {{ $room->room_number }}</h1>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('rooms.update', $room) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="room_number" class="form-label">Room Number *</label>
                                <input type="text" class="form-control @error('room_number') is-invalid @enderror"
                                    id="room_number" name="room_number" required value="{{ $room->room_number }}">
                                @error('room_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="room_type" class="form-label">Room Type *</label>
                                        <select class="form-select @error('room_type') is-invalid @enderror" id="room_type"
                                            name="room_type" required>
                                            <option value="single" {{ $room->room_type === 'single' ? 'selected' : '' }}>
                                                Single</option>
                                            <option value="double" {{ $room->room_type === 'double' ? 'selected' : '' }}>
                                                Double</option>
                                            <option value="suite" {{ $room->room_type === 'suite' ? 'selected' : '' }}>
                                                Suite</option>
                                            <option value="deluxe" {{ $room->room_type === 'deluxe' ? 'selected' : '' }}>
                                                Deluxe</option>
                                        </select>
                                        @error('room_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="floor" class="form-label">Floor</label>
                                        <input type="number" class="form-control @error('floor') is-invalid @enderror"
                                            id="floor" name="floor" value="{{ $room->floor }}">
                                        @error('floor')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="capacity" class="form-label">Capacity (Persons) *</label>
                                        <input type="number" class="form-control @error('capacity') is-invalid @enderror"
                                            id="capacity" name="capacity" min="1" required
                                            value="{{ $room->capacity }}">
                                        @error('capacity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price_per_night" class="form-label">Price Per Night ($) *</label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('price_per_night') is-invalid @enderror"
                                            id="price_per_night" name="price_per_night" required
                                            value="{{ $room->price_per_night }}">
                                        @error('price_per_night')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status *</label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status"
                                            name="status" required>
                                            <option value="available"
                                                {{ $room->status === 'available' ? 'selected' : '' }}>Available</option>
                                            <option value="occupied" {{ $room->status === 'occupied' ? 'selected' : '' }}>
                                                Occupied</option>
                                            <option value="maintenance"
                                                {{ $room->status === 'maintenance' ? 'selected' : '' }}>Maintenance
                                            </option>
                                            <option value="reserved" {{ $room->status === 'reserved' ? 'selected' : '' }}>
                                                Reserved</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="housekeeping_status" class="form-label">Housekeeping Status *</label>
                                        <select class="form-select @error('housekeeping_status') is-invalid @enderror"
                                            id="housekeeping_status" name="housekeeping_status" required>
                                            <option value="clean"
                                                {{ $room->housekeeping_status === 'clean' ? 'selected' : '' }}>Clean
                                            </option>
                                            <option value="dirty"
                                                {{ $room->housekeeping_status === 'dirty' ? 'selected' : '' }}>Dirty
                                            </option>
                                            <option value="in_progress"
                                                {{ $room->housekeeping_status === 'in_progress' ? 'selected' : '' }}>In
                                                Progress</option>
                                            <option value="inspected"
                                                {{ $room->housekeeping_status === 'inspected' ? 'selected' : '' }}>
                                                Inspected</option>
                                        </select>
                                        @error('housekeeping_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3">{{ $room->description }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Room
                                </button>
                                <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
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
