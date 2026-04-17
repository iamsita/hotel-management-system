@extends('layout')
@section('title', 'Reservations')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Reservations</h4>
        <a href="{{ route('admin.reservations.create') }}" class="btn btn-primary" style="background:#1a3263;border:none">New
            Reservation</a>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reservations.index') }}" class="row g-2 align-items-end">
                <div class="col-sm-3">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Guest name or room no." value="{{ request('search') }}">
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        @foreach (['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>
                                {{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Room Type</label>
                    <select name="room_type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        @foreach (['single', 'double', 'suite', 'deluxe'] as $t)
                            <option value="{{ $t }}" @selected(request('room_type') === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Check-in From</label>
                    <input type="date" name="check_in_from" class="form-control form-control-sm"
                        value="{{ request('check_in_from') }}">
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Check-in To</label>
                    <input type="date" name="check_in_to" class="form-control form-control-sm"
                        value="{{ request('check_in_to') }}">
                </div>
                <div class="col-sm-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100"
                        style="background:#1a3263;border:none">Filter</button>
                    @if (request()->hasAny(['search', 'status', 'room_type', 'check_in_from', 'check_in_to']))
                        <a href="{{ route('admin.reservations.index') }}"
                            class="btn btn-sm btn-outline-secondary w-100">Clear</a>
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
                        <th>Id</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ $r->user->name }}</td>
                            <td>{{ $r->room->room_number }}</td>
                            <td>{{ $r->check_in->format('M d, Y') }}</td>
                            <td>{{ $r->check_out->format('M d, Y') }}</td>
                            <td>Rs. {{ number_format($r->total_amount, 2) }}</td>
                            <td>
                                <span
                                    class="badge bg-{{ match ($r->status) {'checked_in' => 'success','confirmed' => 'primary','pending' => 'warning','cancelled' => 'danger',default => 'secondary'} }}">
                                    {{ ucfirst(str_replace('_', ' ', $r->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('admin.reservations.show', $r) }}"
                                        class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="{{ route('admin.reservations.edit', $r) }}"
                                        class="btn btn-sm btn-outline-secondary">Edit</a>

                                    @if ($r->status === 'pending')
                                        <form action="{{ route('admin.reservations.update-status', $r) }}" method="POST"
                                            class="d-inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="confirmed">
                                            <button class="btn btn-sm btn-primary">Confirm</button>
                                        </form>
                                    @endif

                                    @if ($r->status === 'confirmed')
                                        <form action="{{ route('admin.reservations.update-status', $r) }}" method="POST"
                                            class="d-inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="checked_in">
                                            <button class="btn btn-sm btn-success">Check In</button>
                                        </form>
                                    @endif

                                    @if ($r->status === 'checked_in')
                                        <form action="{{ route('admin.reservations.update-status', $r) }}" method="POST"
                                            class="d-inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="checked_out">
                                            <button class="btn btn-sm btn-warning">Check Out</button>
                                        </form>
                                    @endif

                                    @if (in_array($r->status, ['pending', 'confirmed']))
                                        <form action="{{ route('admin.reservations.update-status', $r) }}" method="POST"
                                            class="d-inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Cancel this reservation?')">Cancel</button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.reservations.destroy', $r) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Permanently delete this reservation and all its food orders & payments?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">No reservations yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-3">{{ $reservations->links() }}</div>
        </div>
    </div>
@endsection
