<!DOCTYPE html>
<html>

<head>
    <title>Payment Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Payment Report</h2>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Payments</h5>
                        <h2>{{ $totalPayments }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Amount</h5>
                        <h2>${{ number_format($totalAmount, 0) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Completed</h5>
                        <h2>{{ $completedPayments }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Pending</h5>
                        <h2>{{ $pendingPayments }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5>Payment Methods Breakdown</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>Count</th>
                            <th>Amount</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paymentsByMethod as $method => $data)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $method)) }}</td>
                                <td>{{ $data['count'] }}</td>
                                <td>${{ number_format($data['amount'], 2) }}</td>
                                <td>{{ round(($data['amount'] / $totalAmount) * 100, 1) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5>Payment Status Breakdown</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Count</th>
                            <th>Amount</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paymentsByStatus as $status => $data)
                            <tr>
                                <td>
                                    <span
                                        class="badge bg-{{ $status === 'completed' ? 'success' : ($status === 'failed' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td>{{ $data['count'] }}</td>
                                <td>${{ number_format($data['amount'], 2) }}</td>
                                <td>{{ round(($data['amount'] / $totalAmount) * 100, 1) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-4">
            <a href="{{ route('staff.payments.index') }}" class="btn btn-secondary">Back to Payments</a>
            <button class="btn btn-outline-secondary" onclick="window.print()">Print Report</button>
        </div>
    </div>
</body>

</html>
