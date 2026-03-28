@extends('layout')
@section('title', 'Reservations')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Reservations</h4>
    <a href="{{ route('admin.reservations.create') }}" class="btn btn-primary" style="background:#1a3263;border:none">New Reservation</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>#</th><th>Guest</th><th>Room</th><th>Check In</th><th>Check Out</th><th>Amount</th><th>Status</th><th>Actions</th></tr>
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
                        <span class="badge bg-{{ match($r->status) { 'checked_in' => 'success', 'confirmed' => 'primary', 'pending' => 'warning', 'cancelled' => 'danger', default => 'secondary' } }}">
                            {{ ucfirst(str_replace('_', ' ', $r->status)) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="{{ route('admin.reservations.show', $r) }}" class="btn btn-sm btn-outline-primary">View</a>
                            <a href="{{ route('admin.reservations.edit', $r) }}" class="btn btn-sm btn-outline-secondary">Edit</a>

                            @if($r->status === 'pending')
                                <form action="{{ route('admin.reservations.update-status', $r) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button class="btn btn-sm btn-primary">Confirm</button>
                                </form>
                            @endif

                            @if($r->status === 'confirmed')
                                <form action="{{ route('admin.reservations.update-status', $r) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="checked_in">
                                    <button class="btn btn-sm btn-success">Check In</button>
                                </form>
                            @endif

                            @if($r->status === 'checked_in')
                                <form action="{{ route('admin.reservations.update-status', $r) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="checked_out">
                                    <button class="btn btn-sm btn-warning">Check Out</button>
                                </form>
                            @endif

                            @if(in_array($r->status, ['pending', 'confirmed']))
                                <form action="{{ route('admin.reservations.update-status', $r) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this reservation?')">Cancel</button>
                                </form>
                            @endif

                            <form action="{{ route('admin.reservations.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this reservation and all its food orders & payments?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-3">No reservations yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $reservations->links() }}</div>
    </div>
</div>
@endsection
