@extends('layout')

@section('title', 'Occupancy Report')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4"><i class="fas fa-chart-bar"></i> Occupancy Report</h1>

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
            <div class="col-md-4">
                <div class="stat-card" style="border-top-color: #3498db;">
                    <h6>Occupancy Rate</h6>
                    <div class="value">{{ number_format($occupancyRate, 1) }}%</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="border-top-color: #27ae60;">
                    <h6>Total Reservations</h6>
                    <div class="value">{{ $reservations->count() }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="border-top-color: #f39c12;">
                    <h6>Date Range</h6>
                    <div style="font-size: 0.9rem;">{{ $startDate }} to {{ $endDate }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Reservations Details</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Guest</th>
                                <th>Room</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Nights</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reservations as $reservation)
                                <tr>
                                    <td>{{ $reservation->user->name }}</td>
                                    <td>{{ $reservation->room->room_number }}</td>
                                    <td>{{ $reservation->check_in_date->format('M d, Y') }}</td>
                                    <td>{{ $reservation->check_out_date->format('M d, Y') }}</td>
                                    <td>{{ $reservation->number_of_nights }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $reservation->status }}">
                                            {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                                        </span>
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
