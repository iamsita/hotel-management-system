@extends('layout')

@section('title', 'Guest Report')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4"><i class="fas fa-users"></i> Guest Report</h1>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filter by Date Range</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4 align-self-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Apply Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card" style="border-top-color: #3498db;">
                    <h6>Total Guests</h6>
                    <div class="value">{{ $totalGuests }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-top-color: #27ae60;">
                    <h6>New Guests</h6>
                    <div class="value">{{ $newGuests }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-top-color: #f39c12;">
                    <h6>Repeat Guests</h6>
                    <div class="value">{{ $repeatGuests->count() }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-top-color: #9b59b6;">
                    <h6>Reservations</h6>
                    <div class="value">{{ $guests->sum(fn($g) => $g->reservations->count()) }}</div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Repeat Guests ({{ $repeatGuests->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if ($repeatGuests->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Guest Name</th>
                                            <th>Reservations</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($repeatGuests as $user)
                                            <tr>
                                                <td>{{ $user->full_name }}</td>
                                                <td><span class="badge bg-success">{{ $user->reservation_count }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No repeat guests in this period</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Guest Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Total Guests:</strong>
                            <p class="text-muted">{{ $totalGuests }} guests</p>
                        </div>
                        <div class="mb-3">
                            <strong>New Guests (This Period):</strong>
                            <p class="text-muted">{{ $newGuests }} guests</p>
                        </div>
                        <div class="mb-3">
                            <strong>Repeat Rate:</strong>
                            <p class="text-muted">
                                {{ $totalGuests > 0 ? number_format(($repeatGuests->count() / $totalGuests) * 100, 1) : 0 }}%
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Guests in Period</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Type</th>
                                <th>Reservations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($guests as $user)
                                <tr>
                                    <td><strong>{{ $user->full_name }}</strong></td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?? '-' }}</td>
                                    <td>
                                        <span class="badge"
                                            style="background: {{ $user->guest_type === 'individual' ? '#3498db' : '#e74c3c' }}">
                                            {{ ucfirst($user->guest_type) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->reservations->count() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
