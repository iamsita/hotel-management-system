@extends('layout')
@section('title', 'Food Orders')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Food Orders</h4>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.food-orders.index') }}" class="row g-2 align-items-end">
                <div class="col-sm-3">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Guest name or room"
                        value="{{ request('search') }}">
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        @foreach (['pending', 'preparing', 'delivered', 'cancelled'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>
                                {{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Food</label>
                    <select name="food_id" class="form-select form-select-sm">
                        <option value="">All Items</option>
                        @foreach (\App\Models\Food::all() as $food)
                            <option value="{{ $food->id }}" @selected((int) request('food_id') === $food->id)>
                                {{ $food->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100"
                        style="background:#1a3263;border:none">Filter</button>
                    @if (request()->hasAny(['search', 'status', 'food_id']))
                        <a href="{{ route('admin.food-orders.index') }}"
                            class="btn btn-sm btn-outline-secondary w-100">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->reservation->user->name }}</td>
                            <td>{{ $order->reservation->room->room_number ?? '-' }}</td>
                            <td>{{ $order->food->name }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>Rs. {{ number_format($order->total_price, 2) }}</td>
                            <td><span
                                    class="badge bg-{{ match ($order->status) {'delivered' => 'success','preparing' => 'info','pending' => 'warning',default => 'secondary'} }}">{{ $order->status }}</span>
                            </td>
                            <td>
                                @if ($order->status === 'pending')
                                    <form action="{{ route('admin.food-orders.status', [$order, 'preparing']) }}"
                                        method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-info text-white">Prepare</button>
                                    </form>
                                @endif
                                @if ($order->status === 'preparing')
                                    <form action="{{ route('admin.food-orders.status', [$order, 'delivered']) }}"
                                        method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-success">Deliver</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No food orders found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($orders->hasPages())
                <div class="p-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
            @endif
        </div>
    </div>
@endsection
