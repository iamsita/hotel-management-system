<!DOCTYPE html>
<html>

<head>
    <title>Food Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Food & Beverage Menu</h2>

        <form action="{{ route('guest.food.order') }}" method="POST" class="card p-4 mb-4">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Active Booking</label>
                    <select name="reservation_id" class="form-control" required>
                        <option value="">Select Your Active Booking</option>
                        @foreach ($reservations as $res)
                            <option value="{{ $res->id }}">
                                Room {{ $res->room->room_number }} ({{ $res->check_in_date->format('M d') }} -
                                {{ $res->check_out_date->format('M d') }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            @foreach ($foods as $category => $items)
                <div class="mb-4">
                    <h4>{{ $category }}</h4>
                    <div class="row">
                        @foreach ($items as $food)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $food->name }}</h5>
                                        <p class="card-text">${{ number_format($food->price, 2) }}</p>
                                        <div class="row">
                                            <div class="col">
                                                <input type="hidden" name="food_id[]" value="{{ $food->id }}">
                                                <input type="number" name="quantity[]" class="form-control"
                                                    min="0" value="0" placeholder="Qty">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <button type="submit" class="btn btn-success">Place Order</button>
        </form>
    </div>
</body>

</html>
