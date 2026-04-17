@extends('layout')
@section('title', 'Invoice #' . $reservation->id)
@section('content')
<div class="container" style="max-width: 800px; margin: 0 auto; font-size: 14px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 style="font-size: 20px;">Invoice</h4>
        <div>
            <button onclick="window.print()" class="btn btn-sm btn-primary">Print / Save as PDF</button>
            <a href="{{ route('admin.reservations.show', $reservation) }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>

    @php
        $nights = $reservation->check_in->diffInDays($reservation->check_out);
        $roomTotal = $reservation->total_amount;
        $foodTotal = $reservation->foodOrders->where('status', 'delivered')->sum('total_price');
        $grandTotal = $roomTotal + $foodTotal;
        $paidTotal = $reservation->payments->where('status', 'completed')->sum('amount');
        $balanceDue = $grandTotal - $paidTotal;
        $invoiceNumber = 'INV-' . $reservation->id . '-' . now()->year;
    @endphp

    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            {{-- Header --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <h3 style="font-weight: 700; color: #1a3263; font-size: 22px; margin-bottom: 2px;">HMS</h3>
                    <p class="text-muted" style="font-size: 12px; margin: 0;">Hotel Management System<br>Kathmandu, Nepal</p>
                </div>
                <div class="col-md-6 text-end">
                    <h5 style="color: #1a3263; font-weight: 700; font-size: 16px; margin-bottom: 2px;">INVOICE</h5>
                    <table class="table table-sm table-borderless float-end" style="width: auto; font-size: 13px;">
                        <tr><th class="text-end" style="font-size: 12px;">Invoice #:</th><td style="font-size: 12px;">{{ $invoiceNumber }}</td></tr>
                        <tr><th class="text-end" style="font-size: 12px;">Date:</th><td style="font-size: 12px;">{{ now()->format('M d, Y') }}</td></tr>
                        <tr><th class="text-end" style="font-size: 12px;">Reservation #:</th><td style="font-size: 12px;">{{ $reservation->id }}</td></tr>
                    </table>
                </div>
            </div>

            <hr>

            {{-- Guest Info --}}
            <div class="row mb-2">
                <div class="col-md-6">
                    <h6 style="color: #1a3263; font-weight: 700; font-size: 12px; margin-bottom: 4px;">BILL TO:</h6>
                    <p class="mb-0" style="font-size: 13px;"><strong>{{ $reservation->user->name }}</strong></p>
                    <p class="mb-0" style="font-size: 12px;">{{ $reservation->user->email }}</p>
                    <p class="mb-0" style="font-size: 12px;">{{ $reservation->user->phone }}</p>
                </div>
                <div class="col-md-6">
                    <h6 style="color: #1a3263; font-weight: 700; font-size: 12px; margin-bottom: 4px;">BOOKING DETAILS:</h6>
                    <p class="mb-0" style="font-size: 12px;"><strong>Room:</strong> {{ $reservation->room->room_number }} ({{ ucfirst($reservation->room->type) }})</p>
                    <p class="mb-0" style="font-size: 12px;"><strong>Check-in:</strong> {{ $reservation->check_in->format('M d, Y') }}</p>
                    <p class="mb-0" style="font-size: 12px;"><strong>Check-out:</strong> {{ $reservation->check_out->format('M d, Y') }}</p>
                    <p class="mb-0" style="font-size: 12px;"><strong>Guests:</strong> {{ $reservation->guests }}</p>
                </div>
            </div>

            <hr>

            {{-- Items Table --}}
            <table class="table table-sm" style="font-size: 13px; margin-bottom: 0;">
                <thead style="background: #f8f9fa;">
                    <tr>
                        <th style="font-size: 12px; padding: 4px 6px;">Description</th>
                        <th class="text-end" style="font-size: 12px; padding: 4px 6px;">Qty</th>
                        <th class="text-end" style="font-size: 12px; padding: 4px 6px;">Rate</th>
                        <th class="text-end" style="font-size: 12px; padding: 4px 6px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Room Charge --}}
                    <tr style="padding: 2px;">
                        <td style="padding: 4px 6px; font-size: 13px;">Room Charge - {{ ucfirst($reservation->room->type) }} Room ({{ $nights }} night{{ $nights > 1 ? 's' : '' }})</td>
                        <td class="text-end" style="padding: 4px 6px; font-size: 13px;">{{ $nights }}</td>
                        <td class="text-end" style="padding: 4px 6px; font-size: 13px;">Rs. {{ number_format($reservation->room->price_per_night, 2) }}</td>
                        <td class="text-end" style="padding: 4px 6px; font-size: 13px;"><strong>Rs. {{ number_format($roomTotal, 2) }}</strong></td>
                    </tr>

                    {{-- Food Orders --}}
                    @forelse($reservation->foodOrders->where('status', 'delivered') as $order)
                    <tr style="padding: 2px;">
                        <td style="padding: 4px 6px; font-size: 13px;">{{ $order->food->name }}</td>
                        <td class="text-end" style="padding: 4px 6px; font-size: 13px;">{{ $order->quantity }}</td>
                        <td class="text-end" style="padding: 4px 6px; font-size: 13px;">Rs. {{ number_format($order->food->price, 2) }}</td>
                        <td class="text-end" style="padding: 4px 6px; font-size: 13px;"><strong>Rs. {{ number_format($order->total_price, 2) }}</strong></td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>

            <hr>

            {{-- Totals --}}
            <div class="row justify-content-end mb-2">
                <div class="col-md-4">
                    <table class="table table-sm table-borderless" style="font-size: 13px;">
                        <tr style="padding: 2px;">
                            <th style="padding: 4px 6px;">Subtotal:</th>
                            <td class="text-end" style="padding: 4px 6px;">Rs. {{ number_format($grandTotal, 2) }}</td>
                        </tr>
                        <tr style="padding: 2px;">
                            <th style="padding: 4px 6px;">Tax (0%):</th>
                            <td class="text-end" style="padding: 4px 6px;">Rs. 0.00</td>
                        </tr>
                        <tr style="background: #f8f9fa; padding: 2px;">
                            <th style="padding: 4px 6px;">Total:</th>
                            <td class="text-end" style="padding: 4px 6px;"><strong>Rs. {{ number_format($grandTotal, 2) }}</strong></td>
                        </tr>
                        <tr class="text-success" style="padding: 2px;">
                            <th style="padding: 4px 6px;">Paid:</th>
                            <td class="text-end" style="padding: 4px 6px;">Rs. {{ number_format($paidTotal, 2) }}</td>
                        </tr>
                        <tr class="fw-bold {{ $balanceDue > 0 ? 'text-danger' : 'text-success' }}" style="background: #f8f9fa; padding: 2px;">
                            <th style="padding: 4px 6px;">Balance Due:</th>
                            <td class="text-end" style="padding: 4px 6px;">Rs. {{ number_format($balanceDue, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <hr>

            {{-- Payment Terms --}}
            <div class="row">
                <div class="col-md-12">
                    <h6 style="color: #1a3263; font-weight: 700; font-size: 12px; margin-bottom: 6px;">PAYMENT DETAILS:</h6>
                    <table class="table table-sm" style="font-size: 13px; margin-bottom: 0;">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th style="font-size: 12px; padding: 4px 6px;">Payment Date</th>
                                <th style="font-size: 12px; padding: 4px 6px;">Method</th>
                                <th class="text-end" style="font-size: 12px; padding: 4px 6px;">Amount</th>
                                <th style="font-size: 12px; padding: 4px 6px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservation->payments->where('status', 'completed') as $payment)
                            <tr style="padding: 2px;">
                                <td style="padding: 4px 6px; font-size: 13px;">{{ $payment->created_at->format('M d, Y') }}</td>
                                <td style="padding: 4px 6px; font-size: 13px;">{{ strtoupper($payment->method) }}</td>
                                <td class="text-end" style="padding: 4px 6px; font-size: 13px;">Rs. {{ number_format($payment->amount, 2) }}</td>
                                <td style="padding: 4px 6px;"><span class="badge bg-success" style="font-size: 11px;">Completed</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted" style="padding: 6px; font-size: 12px;">No payments recorded</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <hr>

            {{-- Footer --}}
            <div class="text-center mt-3" style="color: #999; font-size: 11px;">
                <p style="margin: 2px 0;">Thank you for your business!</p>
                <p style="margin: 2px 0;">This is a computer-generated invoice. No signature is required.</p>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn-group, .btn { display: none !important; }
        body { background: white; }
        .card { box-shadow: none !important; border: none !important; }
    }
</style>
@endsection
