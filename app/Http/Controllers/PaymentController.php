<?php

namespace App\Http\Controllers;

use App\Models\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('reservation.user')->latest()->paginate(15);

        return view('admin.payments.index', compact('payments'));
    }
}
