@extends('layout')
@section('title', 'Guests')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Guests</h4>
    <a href="{{ route('admin.guests.create') }}" class="btn btn-primary" style="background:#1a3263;border:none">Add Guest</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Phone</th><th>Reservations</th><th>Joined</th></tr>
            </thead>
            <tbody>
                @foreach($guests as $guest)
                <tr>
                    <td>{{ $guest->name }}</td>
                    <td>{{ $guest->email }}</td>
                    <td>{{ $guest->phone ?? '-' }}</td>
                    <td>{{ $guest->reservations_count }}</td>
                    <td>{{ $guest->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-3">{{ $guests->links() }}</div>
    </div>
</div>
@endsection
