<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestBookingController extends Controller
{
    public static function middleware(): array
    {
        return [
            'auth:guest',
        ];
    }

    public function showBooking()
    {
        $rooms = Room::where('status', 'available')->get();

        return view('guest.booking.create', compact('rooms'));
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'check_in_date' => 'required|date|after:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'room_type' => 'required|string',
        ]);

        $rooms = Room::where('room_type', $request->room_type)
            ->whereDoesntHave('reservations', function ($query) use ($request) {
                $query->whereBetween('check_in_date', [$request->check_in_date, $request->check_out_date])
                    ->orWhereBetween('check_out_date', [$request->check_in_date, $request->check_out_date]);
            })
            ->get();

        return response()->json(['rooms' => $rooms]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:today',
            'number_of_guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ]);

        $room = Room::findOrFail($validated['room_id']);

        $nights = (strtotime($validated['check_out_date']) - strtotime($validated['check_in_date'])) / 86400;
        $total_amount = $nights * $room->price_per_night;

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';
        $validated['total_amount'] = $total_amount;

        $reservation = Reservation::create($validated);

        // Update room status
        $room->update(['status' => 'reserved']);

        return redirect()->route('guest.booking.show', $reservation)->with('success', 'Booking created! Proceed to payment.');
    }

    public function show(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        return view('guest.booking.show', compact('reservation'));
    }

    public function myBookings()
    {
        $reservations = Auth::user()->reservations()->latest()->paginate(10);

        return view('guest.booking.index', compact('reservations'));
    }
}
