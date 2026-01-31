@extends('guest-layout')

@section('title', 'Food Menu')
@section('page-title', 'Food & Beverage Menu')

@section('content')
    <form action="{{ route('guest.food.order') }}" method="POST">
        @csrf

        <div class="card mb-4">
            <div class="card-body">
                <label class="form-label">Active Booking</label>
                <select name="reservation_id" class="form-control @error('reservation_id') is-invalid @enderror" required>
                    <option value="">Select Your Active Booking</option>
                    @foreach ($reservations as $res)
                        <option value="{{ $res->id }}">
                            Room {{ $res->room->room_number }} ({{ $res->check_in_date->format('M d') }} -
                            {{ $res->check_out_date->format('M d') }})
                        </option>
                    @endforeach
                </select>
                @error('reservation_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        @foreach ($foods as $category => $items)
            <div class="mb-4">
                <h5 class="mb-3"><i class="fas fa-utensils"></i> {{ $category }}</h5>
                <div class="row">
                    @foreach ($items as $food)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $food->name }}</h6>
                                    <p class="card-text text-muted small">{{ $food->description ?? 'No description' }}</p>
                                    <h5 class="text-primary mb-3">${{ number_format($food->price, 2) }}</h5>
                                    <div class="input-group">
                                        <input type="hidden" name="food_id[]" value="{{ $food->id }}">
                                        <input type="number" name="quantity[]" class="form-control" min="0"
                                            value="0" placeholder="Quantity">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-shopping-cart"></i> Place Order
            </button>
            <a href="{{ route('guest.dashboard') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
@endsection
