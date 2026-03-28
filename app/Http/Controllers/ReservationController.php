<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with('user', 'room')->latest()->paginate(15);

        return view('admin.reservations.index', compact('reservations'));
    }

    public function create()
    {
        $guests = User::where('role', 'guest')->get();
        $rooms = Room::where('status', 'available')->get();

        return view('admin.reservations.create', compact('guests', 'rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        $nights = Carbon::parse($validated['check_in'])->diffInDays(Carbon::parse($validated['check_out']));
        $validated['total_amount'] = $nights * $room->price_per_night;
        $validated['status'] = 'confirmed';

        Reservation::create($validated);
        $room->update(['status' => 'occupied']);

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation created.');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load('user', 'room', 'foodOrders.food', 'payments');

        return view('admin.reservations.show', compact('reservation'));
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
        ]);

        $oldStatus = $reservation->status;
        $newStatus = $validated['status'];

        $reservation->update(['status' => $newStatus]);

        // Update room status based on reservation status
        if ($newStatus === 'checked_in') {
            $reservation->room->update(['status' => 'occupied']);
        } elseif (in_array($newStatus, ['checked_out', 'cancelled'])) {
            $reservation->room->update(['status' => 'available']);
        } elseif ($newStatus === 'confirmed') {
            $reservation->room->update(['status' => 'occupied']);
        }

        return back()->with('success', "Status changed from {$oldStatus} to {$newStatus}.");
    }

    public function checkIn(Reservation $reservation)
    {
        $reservation->update(['status' => 'checked_in']);
        $reservation->room->update(['status' => 'occupied']);

        return back()->with('success', 'Guest checked in.');
    }

    public function checkOut(Reservation $reservation)
    {
        $reservation->update(['status' => 'checked_out']);
        $reservation->room->update(['status' => 'available']);

        return back()->with('success', 'Guest checked out.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->room->update(['status' => 'available']);
        $reservation->update(['status' => 'cancelled']);

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation cancelled.');
    }
}
