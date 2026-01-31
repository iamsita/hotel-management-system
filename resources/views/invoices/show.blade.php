@extends('layout')

@section('title', 'Invoice Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="fas fa-receipt"></i> Invoice {{ $invoice->invoice_number }}</h1>
            </div>
            <div class="col-md-4 text-end">
                @if ($invoice->status === 'draft')
                    <form action="{{ route('invoices.mark-sent', $invoice) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-paper-plane"></i> Mark as Sent
                        </button>
                    </form>
                @elseif($invoice->status === 'sent')
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#markPaidModal">
                        <i class="fas fa-check"></i> Mark as Paid
                    </button>
                @endif
                <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-secondary" download>
                    <i class="fas fa-download"></i> Download PDF
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>INVOICE TO</h6>
                                <p>
                                    <strong>{{ $invoice->reservation->guest->full_name }}</strong><br>
                                    {{ $invoice->reservation->guest->email }}<br>
                                    {{ $invoice->reservation->guest->phone ?? '' }}<br>
                                    {{ $invoice->reservation->guest->address ?? '' }}<br>
                                    {{ $invoice->reservation->guest->city ?? '' }},
                                    {{ $invoice->reservation->guest->country ?? '' }}
                                </p>
                            </div>
                            <div class="col-md-6 text-end">
                                <h6>INVOICE DETAILS</h6>
                                <p>
                                    <strong>Invoice Number:</strong> {{ $invoice->invoice_number }}<br>
                                    <strong>Issue Date:</strong> {{ $invoice->issue_date->format('M d, Y') }}<br>
                                    <strong>Status:</strong> <span
                                        class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span><br>
                                    @if ($invoice->payment_method)
                                        <strong>Payment Method:</strong>
                                        {{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <h6>RESERVATION DETAILS</h6>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <p><strong>Room:</strong> {{ $invoice->reservation->room->room_number }}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Check-in:</strong> {{ $invoice->reservation->check_in_date->format('M d, Y') }}
                                </p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Check-out:</strong>
                                    {{ $invoice->reservation->check_out_date->format('M d, Y') }}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Nights:</strong> {{ $invoice->reservation->number_of_nights }}</p>
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">CHARGES</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-sm">
                                <thead>
                                    <tr style="background: #f8f9fa;">
                                        <th>Description</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->reservation->charges as $charge)
                                        <tr>
                                            <td>{{ $charge->description }}
                                                ({{ ucfirst(str_replace('_', ' ', $charge->charge_type)) }})</td>
                                            <td class="text-end">${{ number_format($charge->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Invoice Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>${{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        @if ($invoice->tax > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <span>${{ number_format($invoice->tax, 2) }}</span>
                            </div>
                        @endif
                        @if ($invoice->discount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Discount:</span>
                                <span>-${{ number_format($invoice->discount, 2) }}</span>
                            </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total Amount:</strong>
                            <h5 class="text-success">${{ number_format($invoice->total_amount, 2) }}</h5>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Invoice Status</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Current Status:</strong></p>
                        <span class="status-badge status-{{ $invoice->status }}" style="font-size: 1.1rem;">
                            {{ ucfirst($invoice->status) }}
                        </span>
                        @if ($invoice->paid_date)
                            <p class="mt-3"><strong>Paid Date:</strong> {{ $invoice->paid_date->format('M d, Y') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark as Paid Modal -->
    <div class="modal fade" id="markPaidModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mark Invoice as Paid</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method *</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="">Select Method</option>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="check">Check</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="paid_date" class="form-label">Payment Date *</label>
                            <input type="date" class="form-control" id="paid_date" name="paid_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Mark as Paid</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
