@extends('layout')
@section('title', 'Guests')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Guests</h4>
        <a href="{{ route('admin.guests.create') }}" class="btn btn-primary" style="background:#1a3263;border:none">Add
            Guest</a>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.guests.index') }}" class="row g-2 align-items-end">
                <div class="col-sm-4">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Name or email"
                        value="{{ request('search') }}">
                </div>
                <div class="col-sm-3">
                    <label class="form-label small mb-1">Segment</label>
                    <select name="segment" class="form-select form-select-sm">
                        <option value="">All Segments</option>
                        @foreach (['vip', 'loyal', 'at_risk', 'high_value_new', 'unreliable', 'regular'] as $seg)
                            <option value="{{ $seg }}" @selected(request('segment') === $seg)>
                                {{ str_replace('_', ' ', ucfirst($seg)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100"
                        style="background:#1a3263;border:none">Filter</button>
                    @if (request()->hasAny(['search', 'segment']))
                        <a href="{{ route('admin.guests.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
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
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Reservations</th>
                        <th>Segment</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($guests as $guest)
                        <tr>
                            <td>{{ $guest->name }}</td>
                            <td>{{ $guest->email }}</td>
                            <td>{{ $guest->phone ?? '-' }}</td>
                            <td>{{ $guest->reservations_count }}</td>
                            <td>
                                @php
                                    $colors = [
                                        'vip' => 'warning',
                                        'loyal' => 'success',
                                        'at_risk' => 'danger',
                                        'high_value_new' => 'info',
                                        'unreliable' => 'secondary',
                                        'regular' => 'primary',
                                    ];
                                    $color = $colors[$guest->segment] ?? 'primary';
                                @endphp
                                <span
                                    class="badge bg-{{ $color }}">{{ str_replace('_', ' ', ucfirst($guest->segment)) }}</span>
                            </td>
                            <td>{{ $guest->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-3">{{ $guests->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
@endsection
