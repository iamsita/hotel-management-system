@extends('layout')
@section('title', 'Food Menu')
@section('content')
<h4 class="mb-4">Food Menu</h4>

@if(!$activeReservation)
    <div class="alert alert-warning">You can only order food during check-in. <a href="{{ route('guest.dashboard') }}">View your bookings.</a></div>
@endif

@php $categories = $foods->groupBy('category'); @endphp

@foreach($categories as $category => $items)
<h5 class="mt-4 mb-3">{{ ucfirst($category) }}</h5>
<div class="row g-3">
    @foreach($items as $food)
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6>{{ $food->name }}</h6>
                <p class="text-success fw-bold mb-2">Rs. {{ number_format($food->price, 2) }}</p>

                @if($activeReservation)
                <form method="POST" action="{{ route('guest.order-food') }}">
                    @csrf
                    <input type="hidden" name="reservation_id" value="{{ $activeReservation->id }}">
                    <input type="hidden" name="food_id" value="{{ $food->id }}">
                    <div class="input-group input-group-sm">
                        <input type="number" name="quantity" class="form-control" value="1" min="1" max="10">
                        <button type="submit" class="btn btn-primary" style="background:#1a3263;border:none">Order</button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endforeach
@endsection
