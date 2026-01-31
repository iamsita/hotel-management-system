<!DOCTYPE html>
<html>

<head>
    <title>Make Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Make Payment</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <form action="{{ route('guest.payment.process') }}" method="POST" class="card p-4">
                    @csrf

                    <div class="mb-3">
                        <label for="reservation_id" class="form-label">Select Booking</label>
                        <select class="form-select @error('reservation_id') is-invalid @enderror" name="reservation_id"
                            id="reservation_id" required>
                            <option value="">-- Select Booking --</option>
                            @foreach ($reservations as $reservation)
                                <option value="{{ $reservation->id }}"
                                    {{ old('reservation_id') == $reservation->id ? 'selected' : '' }}>
                                    Room {{ $reservation->room->room_number }} -
                                    {{ $reservation->check_in->format('M d, Y') }}
                                </option>
                            @endforeach
                        </select>
                        @error('reservation_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                name="amount" id="amount" step="0.01" placeholder="0.00" required
                                value="{{ old('amount') }}">
                        </div>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" name="payment_method"
                            id="payment_method" required>
                            <option value="">-- Select Method --</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Credit/Debit
                                Card</option>
                            <option value="bank_transfer"
                                {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online
                                Payment</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" id="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Process Payment</button>
                        <a href="{{ route('guest.payment.history') }}" class="btn btn-secondary">View Payment
                            History</a>
                    </div>
                </form>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Payment Info</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Payment Methods Accepted:</strong></p>
                        <ul>
                            <li>Cash at front desk</li>
                            <li>Credit/Debit Card</li>
                            <li>Bank Transfer</li>
                            <li>Online Payment</li>
                        </ul>
                        <p class="text-muted small">All payments are securely processed. You will receive a receipt
                            immediately.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
