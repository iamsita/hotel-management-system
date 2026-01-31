<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:guest');
    }

    public function showPaymentForm()
    {
        $guest = Auth::user();
        $reservations = $guest->reservations()->active()->get();

        return view('guest.payment.create', compact('reservations'));
    }

    public function processPayment(Request $request)
    {
        $guest = Auth::user();
        $reservation = $guest->reservations()->findOrFail($request->reservation_id);

        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,card,bank_transfer,online',
            'notes' => 'nullable|string',
        ]);

        $payment = Payment::create([
            'reservation_id' => $reservation->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'status' => 'completed',
            'notes' => $validated['notes'] ?? null,
            'transaction_id' => 'TXN-'.time().'-'.$reservation->id,
            'paid_at' => now(),
        ]);

        return redirect()->route('guest.payment.receipt', $payment)->with('success', 'Payment processed successfully!');
    }

    public function receipt(Payment $payment)
    {
        if ($payment->reservation->user_id !== Auth::id()) {
            abort(403);
        }

        return view('guest.payment.receipt', compact('payment'));
    }

    public function history()
    {
        $guest = Auth::user();
        $payments = Payment::whereIn('reservation_id', $guest->reservations->pluck('id'))
            ->latest()
            ->paginate(15);

        return view('guest.payment.history', compact('payments'));
    }
}
