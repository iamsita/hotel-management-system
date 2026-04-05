<?php

namespace App\Http\Controllers;

use App\Models\Payment;
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

        // ALGORITHM: Interval Overlap Detection - prevent double booking
        if (! $room->isAvailable($validated['check_in'], $validated['check_out'])) {
            return back()->withErrors(['room_id' => 'This room is already booked for the selected dates.'])->withInput();
        }

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

    public function edit(Reservation $reservation)
    {
        $guests = User::where('role', 'guest')->get();
        $rooms = Room::whereIn('status', ['available', 'occupied'])->get();

        return view('admin.reservations.edit', compact('reservation', 'guests', 'rooms'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
        ]);

        $room = Room::findOrFail($validated['room_id']);

        // ALGORITHM: Interval Overlap Detection - prevent double booking (exclude current reservation)
        if (! $room->isAvailable($validated['check_in'], $validated['check_out'], $reservation->id)) {
            return back()->withErrors(['room_id' => 'This room is already booked for the selected dates.'])->withInput();
        }

        $nights = Carbon::parse($validated['check_in'])->diffInDays(Carbon::parse($validated['check_out']));
        $validated['total_amount'] = $nights * $room->price_per_night;

        $oldRoomId = $reservation->room_id;
        $reservation->update($validated);

        // Update room statuses
        if ($oldRoomId != $validated['room_id']) {
            Room::find($oldRoomId)?->update(['status' => 'available']);
        }

        if (in_array($validated['status'], ['checked_out', 'cancelled'])) {
            $room->update(['status' => 'available']);
        } elseif (in_array($validated['status'], ['confirmed', 'checked_in'])) {
            $room->update(['status' => 'occupied']);
        }

        return redirect()->route('admin.reservations.show', $reservation)->with('success', 'Reservation updated.');
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
        ]);

        $oldStatus = $reservation->status;
        $newStatus = $validated['status'];

        $reservation->update(['status' => $newStatus]);

        if ($newStatus === 'checked_in') {
            $reservation->room->update(['status' => 'occupied']);
        } elseif (in_array($newStatus, ['checked_out', 'cancelled'])) {
            $reservation->room->update(['status' => 'available']);
        } elseif ($newStatus === 'confirmed') {
            $reservation->room->update(['status' => 'occupied']);
        }

        return back()->with('success', "Status changed from {$oldStatus} to {$newStatus}.");
    }

    public function recordPayment(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:cash,card,upi',
        ]);

        $grandTotal = $reservation->total_amount + $reservation->foodOrders()->where('status', 'delivered')->sum('total_price');
        $paidTotal = $reservation->payments()->where('status', 'completed')->sum('amount');
        $balanceDue = $grandTotal - $paidTotal;

        if ($balanceDue <= 0) {
            return back()->withErrors(['amount' => 'This reservation is already fully paid.']);
        }

        if ($validated['amount'] > $balanceDue) {
            return back()->withErrors(['amount' => 'Amount cannot exceed balance due of Rs. '.number_format($balanceDue, 2)]);
        }

        Payment::create([
            'reservation_id' => $reservation->id,
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'status' => 'completed',
        ]);

        return back()->with('success', 'Payment of Rs. '.number_format($validated['amount'], 2).' recorded.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->room->update(['status' => 'available']);
        $reservation->foodOrders()->delete();
        $reservation->payments()->delete();
        $reservation->delete();

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation deleted.');
    }

    public function generateInvoice(Reservation $reservation)
    {
        $reservation->load('user', 'room', 'foodOrders.food', 'payments');

        return view('admin.invoices.show', compact('reservation'));
    }
}
