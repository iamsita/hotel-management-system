@extends('layout')
@section('title', 'Browse Rooms')
@section('content')
<h4 class="mb-4">Available Rooms</h4>

@if($hasActive)
    <div class="alert alert-info">You already have an active reservation. Complete or cancel it before booking a new room.</div>
@endif

{{-- ALGORITHM: Search, Filter & Sort --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white"><h6 class="mb-0">Search & Filter</h6></div>
    <div class="card-body">
        <form method="GET" action="{{ route('guest.rooms') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label form-label-sm">Room Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        @foreach(['single','double','suite','deluxe'] as $type)
                            <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label form-label-sm">Min Price (Rs.)</label>
                    <input type="number" name="min_price" class="form-control form-control-sm" value="{{ request('min_price') }}" min="0" step="1">
                </div>
                <div class="col-md-2">
                    <label class="form-label form-label-sm">Max Price (Rs.)</label>
                    <input type="number" name="max_price" class="form-control form-control-sm" value="{{ request('max_price') }}" min="0" step="1">
                </div>
                <div class="col-md-2">
                    <label class="form-label form-label-sm">Min Guests</label>
                    <input type="number" name="capacity" class="form-control form-control-sm" value="{{ request('capacity') }}" min="1">
                </div>
                <div class="col-md-2">
                    <label class="form-label form-label-sm">Sort By</label>
                    <select name="sort_by" class="form-select form-select-sm">
                        <option value="price_per_night" {{ request('sort_by') === 'price_per_night' ? 'selected' : '' }}>Price</option>
                        <option value="capacity" {{ request('sort_by') === 'capacity' ? 'selected' : '' }}>Capacity</option>
                        <option value="room_number" {{ request('sort_by') === 'room_number' ? 'selected' : '' }}>Room No.</option>
                        <option value="floor" {{ request('sort_by') === 'floor' ? 'selected' : '' }}>Floor</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label form-label-sm">Order</label>
                    <select name="sort_order" class="form-select form-select-sm">
                        <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Asc</option>
                        <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Desc</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100" style="background:#1a3263;border:none">Search</button>
                </div>
            </div>
        </form>
    </div>
</div>

<p class="text-muted mb-3">{{ $rooms->count() }} room{{ $rooms->count() !== 1 ? 's' : '' }} found</p>

<div class="row g-3">
    @forelse($rooms as $room)
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">Room {{ $room->room_number }}</h5>
                <p class="mb-1"><strong>Type:</strong> {{ ucfirst($room->type) }}</p>
                <p class="mb-1"><strong>Floor:</strong> {{ $room->floor }}</p>
                <p class="mb-1"><strong>Capacity:</strong> {{ $room->capacity }} guests</p>
                <p class="mb-3"><strong>Price:</strong> <span class="text-success fw-bold">Rs. {{ number_format($room->price_per_night, 2) }}/night</span></p>

                @if(!$hasActive)
                <form method="POST" action="{{ route('guest.book') }}">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <div class="mb-2">
                        <label class="form-label form-label-sm">Check In</label>
                        <input type="date" name="check_in" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label form-label-sm">Check Out</label>
                        <input type="date" name="check_out" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label form-label-sm">Guests</label>
                        <input type="number" name="guests" class="form-control form-control-sm" value="1" min="1" max="{{ $room->capacity }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100" style="background:#1a3263;border:none">Book Now</button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-4">
                <p class="text-muted mb-0">No rooms match your criteria.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
