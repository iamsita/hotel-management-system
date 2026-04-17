<?php

namespace App\Http\Controllers;

use App\Models\FoodOrder;
use Illuminate\Http\Request;

class FoodOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = FoodOrder::with('reservation.user', 'food')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('reservation.user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('reservation.room', fn ($r) => $r->where('room_number', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('food_id')) {
            $query->where('food_id', $request->food_id);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.food-orders.index', [
            'orders' => $orders,
        ]);
    }

    public function updateStatus(FoodOrder $order, string $status)
    {
        if (in_array($status, ['preparing', 'delivered', 'cancelled'])) {
            $order->update(['status' => $status]);
        }

        return back()->with('success', 'Order status updated.');
    }
}
