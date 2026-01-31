<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentManagementController extends Controller
{
    public function index()
    {
        $payments = Payment::with('reservation.guest', 'reservation.room')
            ->latest()
            ->paginate(20);

        return view('staff.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        return view('staff.payments.show', compact('payment'));
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded',
        ]);

        $payment->update([
            'status' => $validated['status'],
            'paid_at' => $validated['status'] === 'completed' ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Payment status updated!');
    }

    public function report()
    {
        $payments = Payment::with('reservation.guest', 'processedBy')
            ->latest()
            ->get();

        $totalPayments = $payments->count();
        $totalAmount = $payments->sum('amount');
        $completedPayments = $payments->where('status', 'completed')->count();
        $pendingPayments = $payments->where('status', 'pending')->count();

        $paymentsByMethod = [];
        foreach ($payments->groupBy('payment_method') as $method => $methodPayments) {
            $paymentsByMethod[$method] = [
                'count' => $methodPayments->count(),
                'amount' => $methodPayments->sum('amount'),
            ];
        }

        $paymentsByStatus = [];
        foreach ($payments->groupBy('status') as $status => $statusPayments) {
            $paymentsByStatus[$status] = [
                'count' => $statusPayments->count(),
                'amount' => $statusPayments->sum('amount'),
            ];
        }

        return view('staff.payments.report', compact(
            'totalPayments',
            'totalAmount',
            'completedPayments',
            'pendingPayments',
            'paymentsByMethod',
            'paymentsByStatus',
        ));
    }
}
