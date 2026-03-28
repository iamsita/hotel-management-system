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
                @foreach($reservations as $r)
                <tr>
                    <td>{{ $r->id }}</td>
                    <td>{{ $r->user->name }}</td>
                    <td>{{ $r->room->room_number }}</td>
                    <td>{{ $r->check_in->format('M d, Y') }}</td>
                    <td>{{ $r->check_out->format('M d, Y') }}</td>
                    <td>${{ number_format($r->total_amount, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ match($r->status) { 'checked_in' => 'success', 'confirmed' => 'primary', 'pending' => 'warning', 'cancelled' => 'danger', default => 'secondary' } }}">
                            {{ $r->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.reservations.show', $r) }}" class="btn btn-sm btn-outline-primary">View</a>

                        @if($r->status === 'confirmed')
                            <form action="{{ route('admin.reservations.checkin', $r) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success">Check In</button>
                            </form>
                        @endif

                        @if($r->status === 'checked_in')
                            <form action="{{ route('admin.reservations.checkout', $r) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-warning">Check Out</button>
                            </form>
                        @endif

                        @if(in_array($r->status, ['pending', 'confirmed']))
                            <form action="{{ route('admin.reservations.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this reservation?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Cancel</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-3">{{ $reservations->links() }}</div>
    </div>
</div>
@endsection
