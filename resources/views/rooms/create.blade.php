@extends('layout')

@section('title', 'Add Room')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4"><i class="fas fa-door-open"></i> Add New Room</h1>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('rooms.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="room_number" class="form-label">Room Number *</label>
                                <input type="text" class="form-control @error('room_number') is-invalid @enderror"
                                    id="room_number" name="room_number" required value="{{ old('room_number') }}">
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
                                            <option value="">Select Type</option>
                                            <option value="single" {{ old('room_type') === 'single' ? 'selected' : '' }}>
                                                Single</option>
                                            <option value="double" {{ old('room_type') === 'double' ? 'selected' : '' }}>
                                                Double</option>
                                            <option value="suite" {{ old('room_type') === 'suite' ? 'selected' : '' }}>
                                                Suite</option>
                                            <option value="deluxe" {{ old('room_type') === 'deluxe' ? 'selected' : '' }}>
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
                                            id="floor" name="floor" value="{{ old('floor') }}">
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
                                            value="{{ old('capacity', 1) }}">
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
                                            value="{{ old('price_per_night') }}">
                                        @error('price_per_night')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Room
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
