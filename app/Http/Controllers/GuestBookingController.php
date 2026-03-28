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
    public function rooms(Request $request)
    {
        // ALGORITHM: Search, Filter & Sort
        $rooms = Room::searchAndFilter($request->all());

        $hasActive = auth()->user()->reservations()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->exists();

        return view('guest.rooms', compact('rooms', 'hasActive'));
    }

    public function book(Request $request)
    {
        $hasActive = auth()->user()->reservations()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->exists();

        if ($hasActive) {
            return back()->withErrors(['booking' => 'You already have an active reservation. Complete or cancel it before booking again.']);
        }

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
        ]);

        $room = Room::findOrFail($validated['room_id']);

        // ALGORITHM: Interval Overlap Detection - check if room is available for these dates
        if (!$room->isAvailable($validated['check_in'], $validated['check_out'])) {
            return back()->withErrors(['booking' => 'This room is already booked for the selected dates. Please choose different dates.']);
        }

        $nights = Carbon::parse($validated['check_in'])->diffInDays(Carbon::parse($validated['check_out']));

        $validated['user_id'] = auth()->id();
        $validated['total_amount'] = $nights * $room->price_per_night;
        $validated['status'] = 'pending';

        Reservation::create($validated);

        return redirect()->route('guest.dashboard')->with('success', 'Booking request submitted!');
    }

    public function show(Reservation $reservation)
    {
        if ($reservation->user_id !== auth()->id()) {
            abort(403);
        }

        $reservation->load('room', 'foodOrders.food', 'payments');

        return view('guest.reservation', compact('reservation'));
    }

    public function menu()
    {
        $foods = Food::where('available', true)->get();
        $activeReservation = auth()->user()->reservations()
            ->where('status', 'checked_in')
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

        $reservation = Reservation::where('id', $validated['reservation_id'])
            ->where('user_id', auth()->id())
            ->where('status', 'checked_in')
            ->firstOrFail();

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

        $reservation = Reservation::where('id', $validated['reservation_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $grandTotal = $reservation->total_amount + $reservation->foodOrders()->where('status', 'delivered')->sum('total_price');
        $paidTotal = $reservation->payments()->where('status', 'completed')->sum('amount');
        $balanceDue = $grandTotal - $paidTotal;

        if ($balanceDue <= 0) {
            return back()->withErrors(['amount' => 'This reservation is already fully paid.']);
        }

        if ($validated['amount'] > $balanceDue) {
            return back()->withErrors(['amount' => 'Payment amount cannot exceed the balance due of Rs. '.number_format($balanceDue, 2)]);
        }

        $validated['status'] = 'completed';

        Payment::create($validated);

        return redirect()->route('guest.reservation.show', $reservation)->with('success', 'Payment of Rs. '.number_format($validated['amount'], 2).' recorded!');
    }
}
