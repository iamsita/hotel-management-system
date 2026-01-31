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
        $reservations = Reservation::with('guest', 'room')
            ->orderBy('check_in_date', 'desc')
            ->paginate(15);

        return view('reservations.index', compact('reservations'));
    }

    public function create()
    {
        $guests = User::all();
        $availableRooms = Room::where('status', 'available')->get();

        return view('reservations.create', compact('guests', 'availableRooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:guests,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ]);

        $checkInDate = Carbon::parse($validated['check_in_date']);
        $checkOutDate = Carbon::parse($validated['check_out_date']);
        $nights = $checkOutDate->diffInDays($checkInDate);
        $pricePerNight = Room::find($validated['room_id'])->price_per_night;
        $totalAmount = $nights * $pricePerNight;

        $validated['total_amount'] = $totalAmount;
        $validated['status'] = 'confirmed';

        $reservation = Reservation::create($validated);

        // Update room status
        Room::find($validated['room_id'])->update(['status' => 'reserved']);

        return redirect()->route('reservations.show', $reservation)->with('success', 'Reservation created successfully');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load('guest', 'room', 'charges');

        return view('reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $guests = User::all();
        $rooms = Room::all();

        return view('reservations.edit', compact('reservation', 'guests', 'rooms'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:guests,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ]);

        $checkInDate = Carbon::parse($validated['check_in_date']);
        $checkOutDate = Carbon::parse($validated['check_out_date']);
        $nights = $checkOutDate->diffInDays($checkInDate);
        $pricePerNight = Room::find($validated['room_id'])->price_per_night;
        $totalAmount = $nights * $pricePerNight;

        $validated['total_amount'] = $totalAmount;

        $reservation->update($validated);

        return redirect()->route('reservations.show', $reservation)->with('success', 'Reservation updated successfully');
    }

    public function checkIn(Reservation $reservation)
    {
        if ($reservation->status !== 'confirmed') {
            return back()->with('error', 'Only confirmed reservations can be checked in');
        }

        $reservation->update(['status' => 'checked_in']);
        $reservation->room->update(['status' => 'occupied']);

        return redirect()->route('reservations.show', $reservation)->with('success', 'Guest checked in successfully');
    }

    public function checkOut(Reservation $reservation)
    {
        if ($reservation->status !== 'checked_in') {
            return back()->with('error', 'Only checked-in reservations can be checked out');
        }

        $reservation->update(['status' => 'checked_out']);
        $reservation->room->update([
            'status' => 'available',
            'housekeeping_status' => 'dirty',
        ]);

        return redirect()->route('reservations.show', $reservation)->with('success', 'Guest checked out successfully');
    }

    public function getAvailableRooms(Request $request)
    {
        $checkInDate = Carbon::parse($request->check_in_date);
        $checkOutDate = Carbon::parse($request->check_out_date);

        $bookedRooms = Reservation::whereBetween('check_in_date', [$checkInDate, $checkOutDate])
            ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
            ->pluck('room_id');

        $availableRooms = Room::whereNotIn('id', $bookedRooms)
            ->where('status', '!=', 'maintenance')
            ->get();

        return response()->json($availableRooms);
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->update(['status' => 'cancelled']);
        $reservation->room->update(['status' => 'available']);

        return redirect()->route('reservations.index')->with('success', 'Reservation cancelled successfully');
    }
}
