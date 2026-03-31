@extends('layout')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="mb-4">
        <a href="{{ route('segmentation.dashboard') }}" class="text-decoration-none small mb-2 d-block">← Back to Dashboard</a>
        <h1 class="fs-2 fw-bold">{{ $segment }} Guests</h1>
        <p class="text-muted">{{ $guests->count() }} guest{{ $guests->count() != 1 ? 's' : '' }}</p>
    </div>

    <!-- Segment Info -->
    @php
        $segmentInfo = [
            'VIP' => ['description' => 'Very Important Person', 'criteria' => 'Lifetime value ≥ $50,000 and ≥ 5 bookings'],
            'LOYAL' => ['description' => 'Repeat Customers', 'criteria' => '≥ 10 bookings and average stay ≥ 3 days'],
            'BUSINESS' => ['description' => 'Business Travelers', 'criteria' => 'Average stay ≤ 2 days and ≥ 4 bookings'],
            'LEISURE' => ['description' => 'Vacation Planners', 'criteria' => 'Average stay ≥ 4 days'],
            'BUDGET' => ['description' => 'Price-Conscious', 'criteria' => 'Below median spending'],
            'RISK' => ['description' => 'Potential Issues', 'criteria' => 'Cancellation > 30% or payment reliability < 70%'],
            'REGULAR' => ['description' => 'Occasional Visitors', 'criteria' => 'Average metrics'],
        ];
        $info = $segmentInfo[$segment] ?? [];
    @endphp

    <div class="bg-light border p-4 mb-4">
        <p class="fw-semibold mb-1">{{ $info['description'] ?? 'Unknown' }}</p>
        <p class="text-muted small mb-0">{{ $info['criteria'] ?? 'N/A' }}</p>
    </div>

    <!-- Guests Table -->
    @if($guests->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th class="fw-semibold">Name</th>
                    <th class="fw-semibold">Email</th>
                    <th class="fw-semibold">Lifetime Value</th>
                    <th class="fw-semibold">Bookings</th>
                    <th class="fw-semibold">Avg Stay</th>
                    <th class="fw-semibold">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($guests as $guest)
                <tr>
                    <td class="fw-semibold">{{ $guest->name }}</td>
                    <td>{{ $guest->email }}</td>
                    <td class="fw-semibold">${{ number_format($guest->segment_metrics['lifetime_value'] ?? 0, 0) }}</td>
                    <td>{{ $guest->segment_metrics['total_bookings'] ?? 0 }}</td>
                    <td>{{ number_format($guest->segment_metrics['avg_stay_duration'] ?? 0, 1) }} days</td>
                    <td>
                        <a href="{{ route('segmentation.guest', $guest->id) }}" class="text-decoration-none fw-semibold">View →</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="p-4 text-center text-muted">
        <p>No guests found in this segment</p>
    </div>
    @endif

    <!-- Quick Links -->
    <div class="border p-4 mb-4">
        <h3 class="fw-semibold mb-2">Quick Links</h3>
        <ul class="list-unstyled small">
            <li><a href="{{ route('segmentation.segment', 'vip') }}" class="text-decoration-none">View VIP Guests →</a></li>
            <li><a href="{{ route('segmentation.segment', 'risk') }}" class="text-decoration-none">View Risk Guests →</a></li>
            <li><a href="{{ route('segmentation.segment', 'loyal') }}" class="text-decoration-none">View Loyal Guests →</a></li>
        </ul>
    </div>

    <!-- Back Button -->
    <div class="mt-4">
        <a href="{{ route('segmentation.dashboard') }}" class="text-decoration-none fw-semibold small">
            ← Back to Dashboard
        </a>
    </div>
</div>
@endsection
