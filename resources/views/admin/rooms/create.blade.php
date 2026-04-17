@extends('layout')
@section('title', 'Add Room')
@section('content')
<h4 class="mb-4">Add Room</h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.rooms.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Room Number</label>
                    <input type="text" name="room_number" class="form-control" value="{{ old('room_number') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select" required>
                        <option value="single">Single</option>
                        <option value="double">Double</option>
                        <option value="suite">Suite</option>
                        <option value="deluxe">Deluxe</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Capacity</label>
                    <input type="number" name="capacity" class="form-control" value="{{ old('capacity', 1) }}" min="1" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Price Per Night (Rs.)</label>
                    <input type="number" name="price_per_night" class="form-control" value="{{ old('price_per_night') }}" step="0.01" min="0" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Floor</label>
                    <input type="number" name="floor" class="form-control" value="{{ old('floor', 1) }}" min="1" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="background:#1a3263;border:none">Add Room</button>
            <a href="{{ route('admin.rooms.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
