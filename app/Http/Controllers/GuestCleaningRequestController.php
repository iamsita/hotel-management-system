<?php

namespace App\Http\Controllers;

use App\Models\CleaningRequest;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestCleaningRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:guest');
    }

    public function create()
    {
        $reservations = Auth::user()->reservations()
            ->whereIn('status', ['checked_in'])
            ->get();

        return view('guest.cleaning.create', compact('reservations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'request_type' => 'required|string',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $reservation = Reservation::findOrFail($validated['reservation_id']);
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        CleaningRequest::create([
            'reservation_id' => $validated['reservation_id'],
            'room_id' => $reservation->room_id,
            'request_type' => $validated['request_type'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'requested',
        ]);

        return redirect()->route('guest.dashboard')->with('success', 'Cleaning request submitted!');
    }

    public function myRequests()
    {
        $guest = Auth::user();
        $requests = CleaningRequest::whereIn('reservation_id', $guest->reservations->pluck('id'))
            ->latest()
            ->paginate(15);

        return view('guest.cleaning.index', compact('requests'));
    }
}
