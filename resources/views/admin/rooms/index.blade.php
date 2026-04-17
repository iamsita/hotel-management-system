@extends('layout')
@section('title', 'Rooms')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Rooms</h4>
        <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary" style="background:#1a3263;border:none">Add Room</a>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.rooms.index') }}" class="row g-2 align-items-end">
                <div class="col-sm-3">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Room number"
                        value="{{ request('search') }}">
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        @foreach (['single', 'double', 'suite', 'deluxe'] as $t)
                            <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        @foreach (['available', 'occupied', 'maintenance'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>
                                {{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Floor</label>
                    <input type="number" name="floor" class="form-control form-control-sm" placeholder="Floor"
                        min="1" value="{{ request('floor') }}">
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Price From</label>
                    <input type="number" name="price_from" class="form-control form-control-sm" placeholder="Min price"
                        min="0" value="{{ request('price_from') }}">
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Price To</label>
                    <input type="number" name="price_to" class="form-control form-control-sm" placeholder="Max price"
                        min="0" value="{{ request('price_to') }}">
                </div>
                <div class="col-sm-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100"
                        style="background:#1a3263;border:none">Filter</button>
                    @if (request()->hasAny(['search', 'type', 'status', 'floor', 'price_from', 'price_to']))
                        <a href="{{ route('admin.rooms.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Room #</th>
                        <th>Type</th>
                        <th>Floor</th>
                        <th>Capacity</th>
                        <th>Price/Night</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                        <tr>
                            <td class="fw-bold">{{ $room->room_number }}</td>
                            <td>{{ ucfirst($room->type) }}</td>
                            <td>{{ $room->floor }}</td>
                            <td>{{ $room->capacity }}</td>
                            <td>Rs. {{ number_format($room->price_per_night, 2) }}</td>
                            <td>
                                <span
                                    class="badge bg-{{ $room->status === 'available' ? 'success' : ($room->status === 'occupied' ? 'warning' : 'danger') }}">
                                    {{ $room->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.rooms.edit', $room) }}"
                                    class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this room?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No rooms found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($rooms->hasPages())
            <div class="card-footer border-top">
                {{ $rooms->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
