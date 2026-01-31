@extends('layout')

@section('title', 'Invoices')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="fas fa-receipt"></i> Invoices</h1>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Invoices</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Guest</th>
                                <th>Issue Date</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Payment Method</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                    <td>{{ $invoice->reservation->user->name }}</td>
                                    <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                                    <td>${{ number_format($invoice->total_amount, 2) }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $invoice->status }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $invoice->payment_method ?? '-')) }}</td>
                                    <td>
                                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <nav>{{ $invoices->links() }}</nav>
            </div>
        </div>
    </div>
@endsection
