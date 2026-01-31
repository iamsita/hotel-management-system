@extends('layout')

@section('title', 'Create Invoice')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4"><i class="fas fa-file-invoice"></i> Create Invoice</h1>

        <div class="row">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('invoices.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6>Guest Information</h6>
                                    <p><strong>{{ $reservation->guest->full_name }}</strong></p>
                                    <p>{{ $reservation->guest->email }}<br>{{ $reservation->guest->phone ?? '' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Reservation Information</h6>
                                    <p><strong>Room:</strong> {{ $reservation->room->room_number }}</p>
                                    <p><strong>Check-in:</strong> {{ $reservation->check_in_date->format('M d, Y') }}</p>
                                    <p><strong>Check-out:</strong> {{ $reservation->check_out_date->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <hr>

                            <h6 class="mb-3">Charges</h6>
                            <div class="table-responsive mb-3">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($charges as $charge)
                                            <tr>
                                                <td>{{ $charge->description }}</td>
                                                <td class="text-end">${{ number_format($charge->amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr style="background: #f8f9fa;">
                                            <td><strong>Subtotal</strong></td>
                                            <td class="text-end"><strong>${{ number_format($subtotal, 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 offset-md-6">
                                    <div class="mb-3">
                                        <label for="tax" class="form-label">Tax (%) *</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01"
                                                class="form-control @error('tax') is-invalid @enderror" id="tax"
                                                name="tax" value="{{ $tax }}" readonly>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="discount" class="form-label">Discount ($)</label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('discount') is-invalid @enderror" id="discount"
                                            name="discount" value="{{ $discount }}" onchange="calculateTotal()">
                                    </div>

                                    <div class="mb-3" style="background: #f0f0f0; padding: 15px; border-radius: 5px;">
                                        <h5>Total: <span id="totalAmount">${{ number_format($total, 2) }}</span></h5>
                                        <input type="hidden" id="total_amount" name="total_amount"
                                            value="{{ $total }}">
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="">Not Paid Yet</option>
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="check">Check</option>
                                </select>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Generate Invoice
                                </button>
                                <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function calculateTotal() {
                const subtotal = {{ $subtotal }};
                const taxPercent = 10;
                const discount = parseFloat(document.getElementById('discount').value) || 0;
                const tax = (subtotal * taxPercent) / 100;
                const total = subtotal + tax - discount;
                document.getElementById('totalAmount').innerText = '$' + total.toFixed(2);
                document.getElementById('total_amount').value = total.toFixed(2);
            }
        </script>
    @endpush

@endsection
