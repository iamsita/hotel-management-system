<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodOrder;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GuestBookingController extends Controller
{
    public function rooms()
    {
        $rooms = Room::where('status', 'available')->get();

        return view('guest.rooms', compact('rooms'));
    }

    public function book(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        $nights = Carbon::parse($validated['check_in'])->diffInDays(Carbon::parse($validated['check_out']));

        $validated['user_id'] = auth()->id();
        $validated['total_amount'] = $nights * $room->price_per_night;
        $validated['status'] = 'pending';

        Reservation::create($validated);

        return redirect()->route('guest.dashboard')->with('success', 'Booking request submitted!');
    }

    public function menu()
    {
        $foods = Food::where('available', true)->get();
        $activeReservation = auth()->user()->reservations()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->first();

        return view('guest.menu', compact('foods', 'activeReservation'));
    }

    public function orderFood(Request $request)
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'food_id' => 'required|exists:foods,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $food = Food::findOrFail($validated['food_id']);
        $validated['total_price'] = $food->price * $validated['quantity'];
        $validated['status'] = 'pending';

        FoodOrder::create($validated);

        return back()->with('success', 'Food order placed!');
    }

    public function pay(Request $request)
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:cash,card,upi',
        ]);

        $validated['status'] = 'completed';

        Payment::create($validated);

        return redirect()->route('guest.dashboard')->with('success', 'Payment recorded!');
    }
}
