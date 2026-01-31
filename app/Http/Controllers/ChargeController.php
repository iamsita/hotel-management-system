<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ChargeController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'service_id' => 'nullable|exists:services,id',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'charge_type' => 'required|in:room,service,extra,deposit',
        ]);

        $charge = Charge::create([
            ...$validated,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Charge added successfully',
            'charge' => $charge,
        ]);
    }

    public function getReservationCharges(Reservation $reservation)
    {
        $charges = $reservation->charges()->get();
        $total = $charges->where('status', '!=', 'cancelled')->sum('amount');

        return response()->json([
            'charges' => $charges,
            'total' => $total,
        ]);
    }

    public function markAsPaid(Charge $charge)
    {
        $charge->update(['status' => 'paid']);

        return response()->json(['success' => true, 'message' => 'Charge marked as paid']);
    }

    public function destroy(Charge $charge)
    {
        $charge->update(['status' => 'cancelled']);

        return response()->json(['success' => true, 'message' => 'Charge cancelled']);
    }
}
