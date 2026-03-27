@extends('layout')

@section('title', 'Revenue Report')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4"><i class="fas fa-money-bill-wave"></i> Revenue Report</h1>

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
                <div class="stat-card" style="border-top-color: #27ae60;">
                    <h6>Room Revenue</h6>
                    <div class="value">${{ number_format($roomRevenue, 0) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-top-color: #f39c12;">
                    <h6>Service Revenue</h6>
                    <div class="value">${{ number_format($serviceRevenue, 0) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-top-color: #9b59b6;">
                    <h6>Total Revenue</h6>
                    <div class="value">${{ number_format($totalRevenue, 0) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-top-color: #3498db;">
                    <h6>Invoices Count</h6>
                    <div class="value">{{ $invoices->count() }}</div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Revenue by Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Payment Method</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($byPaymentMethod as $method)
                                        <tr>
                                            <td>{{ ucfirst(str_replace('_', ' ', $method->payment_method)) }}</td>
                                            <td class="text-end">${{ number_format($method->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Revenue Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Room Charges:</span>
                                <strong>${{ number_format($roomRevenue, 2) }}</strong>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Service Charges:</span>
                                <strong>${{ number_format($serviceRevenue, 2) }}</strong>
                            </div>
                        </div>
                        <hr>
                        <div>
                            <div class="d-flex justify-content-between">
                                <span><strong>Total Revenue:</strong></span>
                                <h5 class="text-success">${{ number_format($totalRevenue, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Invoice Details</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Guest</th>
                                <th>Issue Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Payment Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td>{{ $invoice->reservation->user->name }}</td>
                                    <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                                    <td>${{ number_format($invoice->total_amount, 2) }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $invoice->status }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $invoice->payment_method ?? '-')) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
