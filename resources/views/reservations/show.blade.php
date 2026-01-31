@extends('layout')

@section('title', 'Reservation Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="fas fa-calendar-alt"></i> Reservation #{{ $reservation->id }}</h1>
            </div>
            <div class="col-md-4 text-end">
                @if ($reservation->status === 'confirmed')
                    <form action="{{ route('reservations.check-in', $reservation) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-sign-in-alt"></i> Check-in
                        </button>
                    </form>
                @elseif($reservation->status === 'checked_in')
                    <form action="{{ route('reservations.check-out', $reservation) }}" method="POST"
                        style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-sign-out-alt"></i> Check-out
                        </button>
                    </form>
                @endif
                <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-info">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Guest Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> {{ $reservation->guest->full_name }}</p>
                        <p><strong>Email:</strong> {{ $reservation->guest->email }}</p>
                        <p><strong>Phone:</strong> {{ $reservation->guest->phone ?? '-' }}</p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Room Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Room:</strong> {{ $reservation->room->room_number }}</p>
                        <p><strong>Type:</strong> {{ ucfirst($reservation->room->room_type) }}</p>
                        <p><strong>Capacity:</strong> {{ $reservation->room->capacity }} persons</p>
                        <p><strong>Price/Night:</strong> ${{ number_format($reservation->room->price_per_night, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Reservation Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Check-in:</strong> {{ $reservation->check_in_date->format('M d, Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Check-out:</strong> {{ $reservation->check_out_date->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Number of Nights:</strong> {{ $reservation->number_of_nights }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Number of Guests:</strong> {{ $reservation->number_of_guests }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Status:</strong>
                                    <span class="status-badge status-{{ $reservation->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Total Amount:</strong>
                                <h4 class="text-success">${{ number_format($reservation->total_amount, 2) }}</h4>
                                </p>
                            </div>
                        </div>

                        @if ($reservation->special_requests)
                            <div class="alert alert-info">
                                <strong>Special Requests:</strong> {{ $reservation->special_requests }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">Charges & Services</h5>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addChargeModal">
                                    <i class="fas fa-plus"></i> Add Charge
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($reservation->charges->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($reservation->charges as $charge)
                                            <tr>
                                                <td>{{ $charge->description }}</td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $charge->charge_type)) }}</td>
                                                <td>${{ number_format($charge->amount, 2) }}</td>
                                                <td>
                                                    <span class="status-badge status-{{ $charge->status }}">
                                                        {{ ucfirst($charge->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr style="background: #f8f9fa;">
                                            <td colspan="2"><strong>Total Charges:</strong></td>
                                            <td><strong>${{ number_format($reservation->total_charges, 2) }}</strong></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No charges added yet</p>
                        @endif
                    </div>
                </div>

                @if ($reservation->invoice)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Invoice</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Invoice Number:</strong> <a
                                    href="{{ route('invoices.show', $reservation->invoice) }}">{{ $reservation->invoice->invoice_number }}</a>
                            </p>
                            <p><strong>Total:</strong> ${{ number_format($reservation->invoice->total_amount, 2) }}</p>
                            <p><strong>Status:</strong> <span
                                    class="status-badge status-{{ $reservation->invoice->status }}">{{ ucfirst($reservation->invoice->status) }}</span>
                            </p>
                        </div>
                    </div>
                @else
                    <div class="card mt-4">
                        <div class="card-body">
                            <a href="{{ route('invoices.create', $reservation) }}" class="btn btn-success">
                                <i class="fas fa-file-invoice"></i> Generate Invoice
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Charge Modal -->
    <div class="modal fade" id="addChargeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Charge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addChargeForm">
                    <div class="modal-body">
                        <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">

                        <div class="mb-3">
                            <label for="charge_type" class="form-label">Charge Type *</label>
                            <select class="form-select" id="charge_type" name="charge_type" required>
                                <option value="">Select Type</option>
                                <option value="room">Room</option>
                                <option value="service">Service</option>
                                <option value="extra">Extra</option>
                                <option value="deposit">Deposit</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <input type="text" class="form-control" id="description" name="description" required>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount ($) *</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Charge</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('addChargeForm').addEventListener('submit', function(e) {
                e.preventDefault();
                fetch('{{ route('charges.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        reservation_id: this.querySelector('[name="reservation_id"]').value,
                        description: this.querySelector('[name="description"]').value,
                        amount: this.querySelector('[name="amount"]').value,
                        charge_type: this.querySelector('[name="charge_type"]').value
                    })
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            });
        </script>
    @endpush

@endsection
