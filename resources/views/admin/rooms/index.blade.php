@extends('layout')
@section('title', 'Rooms')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Rooms</h4>
    <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary" style="background:#1a3263;border:none">Add Room</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Room #</th><th>Type</th><th>Floor</th><th>Capacity</th><th>Price/Night</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($rooms as $room)
                <tr>
                    <td class="fw-bold">{{ $room->room_number }}</td>
                    <td>{{ ucfirst($room->type) }}</td>
                    <td>{{ $room->floor }}</td>
                    <td>{{ $room->capacity }}</td>
                    <td>Rs. {{ number_format($room->price_per_night, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $room->status === 'available' ? 'success' : ($room->status === 'occupied' ? 'warning' : 'danger') }}">
                            {{ $room->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this room?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
