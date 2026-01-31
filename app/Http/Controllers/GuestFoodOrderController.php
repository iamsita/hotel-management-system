<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodOrder;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestFoodOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:guest');
    }

    public function showMenu()
    {
        $foods = Food::where('available', true)->get()->groupBy('category');
        $reservations = Auth::guard('guest')->user()->reservations()
            ->where('status', 'checked_in')
            ->get();

        return view('guest.food.menu', compact('foods', 'reservations'));
    }

    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'food_id' => 'required|exists:foods,id',
            'quantity' => 'required|integer|min:1',
            'special_notes' => 'nullable|string',
        ]);

        $reservation = Reservation::findOrFail($validated['reservation_id']);
        if ($reservation->guest_id !== Auth::guard('guest')->id()) {
            abort(403);
        }

        $food = Food::findOrFail($validated['food_id']);

        FoodOrder::create([
            'reservation_id' => $validated['reservation_id'],
            'food_id' => $validated['food_id'],
            'quantity' => $validated['quantity'],
            'price' => $food->price,
            'special_notes' => $validated['special_notes'],
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Order placed successfully!');
    }

    public function orderHistory()
    {
        $guest = Auth::guard('guest')->user();
        $orders = FoodOrder::whereIn('reservation_id', $guest->reservations->pluck('id'))
            ->with('food', 'reservation')
            ->latest()
            ->paginate(15);

        return view('guest.food.history', compact('orders'));
    }

    public function cancelOrder(FoodOrder $order)
    {
        if ($order->reservation->guest_id !== Auth::guard('guest')->id()) {
            abort(403);
        }

        if ($order->status === 'pending') {
            $order->update(['status' => 'cancelled']);

            return redirect()->back()->with('success', 'Order cancelled!');
        }

        return redirect()->back()->withErrors(['error' => 'Cannot cancel this order']);
    }
}
