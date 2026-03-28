@extends('layout')
@section('title', 'Food Orders')
@section('content')
<h4 class="mb-4">Food Orders</h4>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>#</th><th>Guest</th><th>Room</th><th>Item</th><th>Qty</th><th>Total</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->reservation->user->name }}</td>
                    <td>{{ $order->reservation->room->room_number ?? '-' }}</td>
                    <td>{{ $order->food->name }}</td>
                    <td>{{ $order->quantity }}</td>
                    <td>Rs. {{ number_format($order->total_price, 2) }}</td>
                    <td><span class="badge bg-{{ match($order->status) { 'delivered' => 'success', 'preparing' => 'info', 'pending' => 'warning', default => 'secondary' } }}">{{ $order->status }}</span></td>
                    <td>
                        @if($order->status === 'pending')
                            <form action="{{ route('admin.food-orders.status', [$order, 'preparing']) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-info text-white">Prepare</button>
                            </form>
                        @endif
                        @if($order->status === 'preparing')
                            <form action="{{ route('admin.food-orders.status', [$order, 'delivered']) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-success">Deliver</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-3">{{ $orders->links() }}</div>
    </div>
</div>
@endsection
