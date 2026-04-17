@extends('layout')
@section('title', 'Payments')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Payments</h4>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-2 align-items-end">
                <div class="col-sm-3">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Guest name or ID"
                        value="{{ request('search') }}">
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        @foreach (['completed', 'pending'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>
                                {{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Method</label>
                    <select name="method" class="form-select form-select-sm">
                        <option value="">All Methods</option>
                        @foreach (['credit_card', 'debit_card', 'cash', 'bank_transfer'] as $m)
                            <option value="{{ $m }}" @selected(request('method') === $m)>
                                {{ str_replace('_', ' ', ucfirst($m)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100"
                        style="background:#1a3263;border:none">Filter</button>
                    @if (request()->hasAny(['search', 'status', 'method']))
                        <a href="{{ route('admin.payments.index') }}"
                            class="btn btn-sm btn-outline-secondary w-100">Clear</a>
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
                        <th>#</th>
                        <th>Guest</th>
                        <th>Reservation</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>{{ $payment->reservation->user->name }}</td>
                            <td>#{{ $payment->reservation_id }}</td>
                            <td>Rs. {{ number_format($payment->amount, 2) }}</td>
                            <td>{{ strtoupper($payment->method) }}</td>
                            <td><span
                                    class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">{{ $payment->status }}</span>
                            </td>
                            <td>{{ $payment->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No payments found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($payments->hasPages())
                <div class="p-3">{{ $payments->links('pagination::bootstrap-5') }}</div>
            @endif
        </div>
    </div>
@endsection
