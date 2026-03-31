@extends('layout')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="mb-5">
        <h1 class="display-4 fw-bold">Guest Segmentation</h1>
        <p class="text-muted fs-5 mt-2">Analyze and manage guest segments</p>
    </div>

    <!-- Main Table -->
    <div class="table-responsive mb-12">
        <table class="table table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th class="fw-semibold">Segment</th>
                    <th class="text-center fw-semibold">Count</th>
                    <th class="text-center fw-semibold">Percentage</th>
                    <th class="fw-semibold">Distribution</th>
                    <th class="text-center fw-semibold">Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $segments = [
                        ['name' => 'VIP', 'count' => $summary['by_segment']['vip'] ?? 0],
                        ['name' => 'LOYAL', 'count' => $summary['by_segment']['loyal'] ?? 0],
                        ['name' => 'BUSINESS', 'count' => $summary['by_segment']['business'] ?? 0],
                        ['name' => 'LEISURE', 'count' => $summary['by_segment']['leisure'] ?? 0],
                        ['name' => 'BUDGET', 'count' => $summary['by_segment']['budget'] ?? 0],
                        ['name' => 'RISK', 'count' => $summary['by_segment']['risk'] ?? 0],
                        ['name' => 'REGULAR', 'count' => $summary['by_segment']['regular'] ?? 0],
                    ];
                    $total = $summary['total_guests'] ?? 1;
                @endphp
                @foreach($segments as $segment)
                @php
                    $percentage = $total > 0 ? round(($segment['count'] / $total) * 100) : 0;
                @endphp
                <tr>
                    <td class="fw-semibold">{{ $segment['name'] }}</td>
                    <td class="text-center fw-bold">{{ $segment['count'] }}</td>
                    <td class="text-center fw-semibold">{{ $percentage }}%</td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="progress flex-grow-1" style="height: 20px; width: 150px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%;"></div>
                            </div>
                            <span class="small text-muted fw-medium">{{ $percentage }}%</span>
                        </div>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('segmentation.segment', strtolower($segment['name'])) }}" class="text-decoration-none fw-semibold">
                            View →
                        </a>
                    </td>
                </tr>
                @endforeach
                <tr class="table-light fw-semibold">
                    <td>TOTAL</td>
                    <td class="text-center">{{ $summary['total_guests'] ?? 0 }}</td>
                    <td class="text-center">100%</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Summary Stats -->
    <div class="row mb-5 g-4">
        <div class="col-md-4">
            <div class="border p-4 text-center">
                <p class="text-muted text-uppercase small fw-semibold">Total Guests</p>
                <p class="display-5 fw-bold">{{ $summary['total_guests'] ?? 0 }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border p-4 text-center">
                <p class="text-muted text-uppercase small fw-semibold">Avg Lifetime Value</p>
                <p class="display-5 fw-bold">${{ number_format($summary['average_lifetime_value'] ?? 0, 0) }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border p-4 text-center">
                <p class="text-muted text-uppercase small fw-semibold">Segments</p>
                <p class="display-5 fw-bold">7</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="row g-4">
        <div class="col-md-6">
            <a href="{{ route('segmentation.segment-form') }}" class="border p-4 d-block text-center text-decoration-none text-dark">
                <p class="fw-bold mb-2 fs-5">Re-segment All Guests</p>
                <p class="text-muted small">Recalculate segmentation based on latest data</p>
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('guests.index') }}" class="border p-4 d-block text-center text-decoration-none text-dark">
                <p class="fw-bold mb-2 fs-5">Manage Guests</p>
                <p class="text-muted small">View and manage guest profiles</p>
            </a>
        </div>
    </div>
</div>
@endsection
