@extends('layout')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="mb-4">
        <a href="{{ route('segmentation.segment', strtolower($user->segment)) }}" class="text-decoration-none small mb-2 d-block">← Back to {{ $user->segment }} Segment</a>
        <h1 class="fs-3 fw-bold">{{ $user->name }}</h1>
        <p class="text-muted small">{{ $user->email }}</p>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="border p-3">
                <p class="text-muted small fw-semibold">SEGMENT</p>
                <p class="fs-5 fw-bold">{{ $user->segment }}</p>
                @if($user->last_segmented_at)
                    <p class="text-muted small mt-2">{{ $user->last_segmented_at->diffForHumans() }}</p>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="border p-3">
                <p class="text-muted small fw-semibold">LIFETIME VALUE</p>
                <p class="fs-5 fw-bold">${{ number_format($user->segment_metrics['lifetime_value'] ?? 0, 0) }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border p-3">
                <p class="text-muted small fw-semibold">PAYMENT RELIABILITY</p>
                @php
                    $reliability = $user->segment_metrics['payment_reliability'] ?? 0;
                    $reliabilityColor = $reliability >= 80 ? 'text-success' : ($reliability >= 60 ? 'text-warning' : 'text-danger');
                @endphp
                <p class="fs-5 fw-bold {{ $reliabilityColor }}">{{ number_format($reliability, 0) }}%</p>
            </div>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="border p-4 mb-4">
        <h2 class="fw-semibold mb-3">Metrics</h2>
        <div class="row g-3">
            <div class="col-6 col-md-2-4">
                <p class="text-muted small fw-semibold">Total Bookings</p>
                <p class="fs-5 fw-bold">{{ $user->segment_metrics['total_bookings'] ?? 0 }}</p>
            </div>
            <div class="col-6 col-md-2-4">
                <p class="text-muted small fw-semibold">Avg Stay</p>
                <p class="fs-5 fw-bold">{{ number_format($user->segment_metrics['avg_stay_duration'] ?? 0, 1) }} days</p>
            </div>
            <div class="col-6 col-md-2-4">
                <p class="text-muted small fw-semibold">Cancellations</p>
                <p class="fs-5 fw-bold">{{ number_format($user->segment_metrics['cancellation_rate'] ?? 0, 0) }}%</p>
            </div>
            <div class="col-6 col-md-2-4">
                <p class="text-muted small fw-semibold">Avg Booking</p>
                <p class="fs-5 fw-bold">${{ number_format($user->segment_metrics['avg_booking_value'] ?? 0, 0) }}</p>
            </div>
            <div class="col-6 col-md-2-4">
                <p class="text-muted small fw-semibold">Member Since</p>
                <p class="fs-5 fw-bold">{{ $user->segment_metrics['membership_years'] ?? 0 }} yrs</p>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-4">
        <!-- Recent Reservations -->
        <div class="col-lg-6">
            <div class="border p-4">
                <h2 class="fw-semibold mb-3">Recent Reservations</h2>
                @if($reservations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <tbody>
                            @foreach($reservations->take(5) as $reservation)
                            <tr>
                                <td>
                                    <p class="fw-semibold mb-1">Room {{ $reservation->room->room_number ?? 'N/A' }}</p>
                                    <p class="text-muted small">{{ $reservation->check_in_date?->format('M d') }} - {{ $reservation->check_out_date?->format('M d') }}</p>
                                </td>
                                <td class="text-end">
                                    <p class="fw-semibold mb-1">${{ number_format($reservation->total_price ?? 0, 0) }}</p>
                                    <small>
                                        @switch($reservation->status)
                                            @case('completed')<span class="badge bg-success">Complete</span>@break
                                            @case('cancelled')<span class="badge bg-danger">Cancelled</span>@break
                                            @case('pending')<span class="badge bg-warning">Pending</span>@break
                                            @default<span class="badge bg-secondary">{{ ucfirst($reservation->status) }}</span>@endswitch
                                        </small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted small">No reservations found</p>
                @endif
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="col-lg-6">
            <div class="border p-4">
                <h2 class="fw-semibold mb-3">Recent Payments</h2>
                @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <tbody>
                            @foreach($payments->take(5) as $payment)
                            <tr>
                                <td>
                                    <p class="fw-semibold mb-1">{{ ucfirst($payment->payment_method ?? 'Unknown') }}</p>
                                    <p class="text-muted small">{{ $payment->created_at?->format('M d, Y') }}</p>
                                </td>
                                <td class="text-end">
                                    <p class="fw-semibold mb-1">${{ number_format($payment->amount ?? 0, 0) }}</p>
                                    <small>
                                        @switch($payment->status)
                                            @case('completed')<span class="badge bg-success">Complete</span>@break
                                            @case('failed')<span class="badge bg-danger">Failed</span>@break
                                            @case('pending')<span class="badge bg-warning">Pending</span>@break
                                            @default<span class="badge bg-secondary">{{ ucfirst($payment->status) }}</span>@endswitch
                                        </small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted small">No payments found</p>
                @endif
            </div>
        </div>
    </div>

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
        <a href="{{ route('segmentation.segment', strtolower($user->segment)) }}" class="text-decoration-none fw-semibold small">
            ← Back to {{ $user->segment }} Segment
        </a>
    </div>
</div>
@endsection
