@extends('layout')
@section('title', 'Guest Segmentation')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Guest Segmentation</h4>
        <small class="text-muted">Rule-Based Decision Tree Algorithm — guests classified by behavior</small>
    </div>
    <form action="{{ route('admin.algorithms.segment') }}" method="POST">
        @csrf
        <button class="btn btn-primary" style="background:#1a3263;border:none">Re-run Segmentation</button>
    </form>
</div>

{{-- Decision Tree Rules --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h6 class="mb-0">Decision Tree Rules</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @php
                $rules = [
                    ['segment'=>'VIP',            'color'=>'warning',   'text'=>'dark',  'condition'=>'Total spend ≥ Rs.10,000  AND  visits ≥ 5'],
                    ['segment'=>'Loyal',          'color'=>'success',   'text'=>'white', 'condition'=>'Visits ≥ 3  AND  cancellation rate < 20%'],
                    ['segment'=>'At Risk',        'color'=>'danger',    'text'=>'white', 'condition'=>'Days since last visit > 90  AND  visits ≥ 2'],
                    ['segment'=>'High Value New', 'color'=>'info',      'text'=>'dark',  'condition'=>'Visits = 1  AND  total spend ≥ Rs.5,000'],
                    ['segment'=>'Unreliable',     'color'=>'secondary', 'text'=>'white', 'condition'=>'Cancellation rate ≥ 50%'],
                    ['segment'=>'Regular',        'color'=>'primary',   'text'=>'white', 'condition'=>'None of the above (default)'],
                ];
            @endphp
            @foreach($rules as $i => $rule)
            <div class="col-md-4">
                <div class="d-flex align-items-start gap-2">
                    <span class="badge bg-{{ $rule['color'] }} text-{{ $rule['text'] }} mt-1" style="min-width:90px">{{ $rule['segment'] }}</span>
                    <small class="text-muted">{{ $rule['condition'] }}</small>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Segment Summary Cards --}}
@php
    $segColors = [
        'vip'            => 'warning',
        'loyal'          => 'success',
        'at_risk'        => 'danger',
        'high_value_new' => 'info',
        'unreliable'     => 'secondary',
        'regular'        => 'primary',
    ];
    $counts = collect($segmentedGuests)->groupBy('segment')->map->count();
@endphp

<div class="row g-3 mb-4">
    @foreach($segColors as $seg => $color)
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <span class="badge bg-{{ $color }} text-{{ in_array($seg, ['vip','high_value_new']) ? 'dark' : 'white' }} mb-1">
                    {{ str_replace('_', ' ', ucfirst($seg)) }}
                </span>
                <h3 class="fw-bold mb-0">{{ $counts[$seg] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Guest Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0">All Guests</h6>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Guest</th>
                    <th>Email</th>
                    <th>Visits</th>
                    <th>Total Spend</th>
                    <th>Days Since Visit</th>
                    <th>Cancel Rate</th>
                    <th>Segment</th>
                </tr>
            </thead>
            <tbody>
                @forelse($segmentedGuests as $g)
                <tr>
                    <td>{{ $g->name }}</td>
                    <td>{{ $g->email }}</td>
                    <td>{{ $g->visit_count }}</td>
                    <td>Rs. {{ number_format($g->total_spend, 2) }}</td>
                    <td>{{ $g->days_since_last_visit == 9999 ? '—' : $g->days_since_last_visit . ' days' }}</td>
                    <td>{{ number_format($g->cancellation_rate * 100, 0) }}%</td>
                    <td>
                        <span class="badge bg-{{ $segColors[$g->segment] ?? 'primary' }} text-{{ in_array($g->segment, ['vip','high_value_new']) ? 'dark' : 'white' }}">
                            {{ str_replace('_', ' ', ucfirst($g->segment)) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">No guests found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
