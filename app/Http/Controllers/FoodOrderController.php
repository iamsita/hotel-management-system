<?php

namespace App\Http\Controllers;

use App\Models\FoodOrder;

class FoodOrderController extends Controller
{
    public function index()
    {
        $orders = FoodOrder::with('reservation.user', 'food')->latest()->paginate(15);

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
