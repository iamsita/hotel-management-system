@extends('layout')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4"><i class="fas fa-chart-line"></i> Dashboard</h1>

        <div class="row">
            <div class="col-md-3">
                <div class="stat-card" style="border-top-color: #3498db;">
                    <h6>Total Rooms</h6>
                    <div class="value">{{ $totalRooms }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-top-color: #27ae60;">
                    <h6>Available Rooms</h6>
                    <div class="value" id="available-rooms">{{ $availableRooms }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-top-color: #f39c12;">
                    <h6>Occupied Rooms</h6>
                    <div class="value" id="occupied-rooms">{{ $occupiedRooms }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-top-color: #9b59b6;">
                    <h6>Occupancy Rate</h6>
                    <div class="value">{{ number_format($occupancyRate, 1) }}%</div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-3">
                <div class="stat-card" style="border-top-color: #e67e22;">
                    <h6>Total Guests</h6>
                    <div class="value">{{ $totalGuests }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-top-color: #1abc9c;">
                    <h6>Active Reservations</h6>
                    <div class="value">{{ $activeReservations }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-top-color: #34495e;">
                    <h6>Total Reservations</h6>
                    <div class="value">{{ $totalReservations }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-top-color: #16a085;">
                    <h6>Total Revenue</h6>
                    <div class="value">${{ number_format($totalRevenue, 0) }}</div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-coins"></i> Financial Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6>Total Revenue (Paid)</h6>
                            <h4 class="text-success">${{ number_format($totalRevenue, 2) }}</h4>
                        </div>
                        <div>
                            <h6>Pending Invoices</h6>
                            <h4 class="text-warning">${{ number_format($pendingInvoices, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-link"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('reservations.create') }}" class="btn btn-primary btn-sm mb-2 w-100">
                            <i class="fas fa-plus"></i> New Reservation
                        </a>
                        <a href="{{ route('guests.create') }}" class="btn btn-info btn-sm mb-2 w-100">
                            <i class="fas fa-user-plus"></i> Add Guest
                        </a>

                        <a href="{{ route('invoices.index') }}" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-receipt"></i> View Invoices
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
