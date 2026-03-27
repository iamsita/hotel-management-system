@extends('layout')

@section('title', 'Rooms Management')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="fas fa-door-open"></i> Rooms Management</h1>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('rooms.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Room
                </a>
            </div>
        </div>

        <div class="card">

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Room #</th>
                                <th>Type</th>
                                <th>Floor</th>
                                <th>Price/Night</th>
                                <th>Status</th>
                                <th>Housekeeping</th>
                                <th>Current Guest</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rooms as $room)
                                <tr>
                                    <td><strong>{{ $room->room_number }}</strong></td>
                                    <td>{{ ucfirst($room->room_type) }}</td>
                                    <td>{{ $room->floor ?? '-' }}</td>
                                    <td>${{ number_format($room->price_per_night, 2) }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $room->status }}">
                                            {{ ucfirst(str_replace('_', ' ', $room->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge" style="background: #d1ecf1; color: #0c5460;">
                                            {{ ucfirst(str_replace('_', ' ', $room->housekeeping_status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($room->current_reservation)
                                            {{ $room->current_reservation->user->name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('rooms.edit', $room) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('rooms.destroy', $room) }}"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
