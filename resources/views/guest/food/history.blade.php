@extends('guest-layout')

@section('title', 'Order History')
@section('page-title', 'Order History')

@section('content')
    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Food Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ $order->food->name }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>${{ number_format($order->price * $order->quantity, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ $order->status }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                @if ($order->status === 'pending')
                                    <form action="{{ route('guest.orders.cancel', $order) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <p class="text-muted">No orders found</p>
                                <a href="{{ route('guest.food.menu') }}" class="btn btn-primary btn-sm">Order Now</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($orders->hasPages())
        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    @endif
@endsection
