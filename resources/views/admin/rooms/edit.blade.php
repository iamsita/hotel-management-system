@extends('layout')
@section('title', 'Edit Room')
@section('content')
<h4 class="mb-4">Edit Room {{ $room->room_number }}</h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.rooms.update', $room) }}">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Room Number</label>
                    <input type="text" name="room_number" class="form-control" value="{{ $room->room_number }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select" required>
                        @foreach(['single','double','suite','deluxe'] as $type)
                            <option value="{{ $type }}" {{ $room->type === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Capacity</label>
                    <input type="number" name="capacity" class="form-control" value="{{ $room->capacity }}" min="1" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Price Per Night (Rs.)</label>
                    <input type="number" name="price_per_night" class="form-control" value="{{ $room->price_per_night }}" step="0.01" min="0" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Floor</label>
                    <input type="number" name="floor" class="form-control" value="{{ $room->floor }}" min="1" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        @foreach(['available','occupied','maintenance'] as $status)
                            <option value="{{ $status }}" {{ $room->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="background:#1a3263;border:none">Update Room</button>
            <a href="{{ route('admin.rooms.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
