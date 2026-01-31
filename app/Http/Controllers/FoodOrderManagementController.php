<?php

namespace App\Http\Controllers;

use App\Models\FoodOrder;
use Illuminate\Http\Request;

class FoodOrderManagementController extends Controller
{
    public function index()
    {
        $orders = FoodOrder::with('food', 'reservation.guest')
            ->latest()
            ->paginate(20);

        return view('staff.food-orders.index', compact('orders'));
    }

    public function show(FoodOrder $order)
    {
        return view('staff.food-orders.show', compact('order'));
    }

    public function updateStatus(Request $request, FoodOrder $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,delivered,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        if ($validated['status'] === 'delivered') {
            $order->update(['delivered_at' => now()]);
        }

        return redirect()->back()->with('success', 'Order status updated!');
    }
}
