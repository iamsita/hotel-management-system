@extends('layout')

@section('title', 'Service Report')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4"><i class="fas fa-concierge-bell"></i> Service Report</h1>

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

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Service Breakdown</h5>
            </div>
            <div class="card-body">
                @if ($serviceBreakdown && count($serviceBreakdown) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th class="text-center">Count</th>
                                    <th class="text-end">Total Revenue</th>
                                    <th class="text-end">Average per Service</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($serviceBreakdown as $service)
                                    <tr>
                                        <td><strong>{{ $service['name'] }}</strong></td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $service['count'] }}</span>
                                        </td>
                                        <td class="text-end">${{ number_format($service['total'], 2) }}</td>
                                        <td class="text-end">${{ number_format($service['average'], 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr style="background: #f8f9fa;">
                                    <td><strong>TOTAL</strong></td>
                                    <td class="text-center">
                                        <strong>{{ array_sum(array_column($serviceBreakdown, 'count')) }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <strong>${{ number_format(array_sum(array_column($serviceBreakdown, 'total')), 2) }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <strong>${{ number_format(
                                            array_sum(array_column($serviceBreakdown, 'total')) / max(1, array_sum(array_column($serviceBreakdown, 'count'))),
                                            2,
                                        ) }}</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No service charges found for the selected period.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
