<!DOCTYPE html>
<html>

<head>
    <title>Make a Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Make a Booking</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form action="{{ route('guest.booking.store') }}" method="POST" class="card p-4">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Check-in Date</label>
                    <input type="date" name="check_in_date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Check-out Date</label>
                    <input type="date" name="check_out_date" class="form-control" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Number of Guests</label>
                    <input type="number" name="number_of_guests" class="form-control" min="1" value="1"
                        required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Room</label>
                    <select name="room_id" class="form-control" required>
                        <option value="">Select Room</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}">
                                Room {{ $room->room_number }} - {{ ucfirst($room->room_type) }}
                                (${{ $room->price_per_night }}/night)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Special Requests</label>
                <textarea name="special_requests" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Complete Booking</button>
            <a href="{{ route('guest.dashboard') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>

</html>
