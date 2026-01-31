<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Reservation;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('reservation.user')
            ->orderBy('issue_date', 'desc')
            ->paginate(15);

        return view('invoices.index', compact('invoices'));
    }

    public function create(Reservation $reservation)
    {
        $charges = $reservation->charges()->where('status', '!=', 'cancelled')->get();
        $subtotal = $charges->sum('amount');
        $tax = $subtotal * 0.1; // 10% tax
        $discount = 0;
        $total = $subtotal + $tax - $discount;

        return view('invoices.create', compact('reservation', 'charges', 'subtotal', 'tax', 'discount', 'total'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|in:cash,card,bank_transfer,check',
        ]);

        $validated['invoice_number'] = Invoice::generateInvoiceNumber();
        $validated['issue_date'] = now()->date();
        $validated['status'] = 'draft';

        $invoice = Invoice::create($validated);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created successfully');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('reservation.user', 'reservation.charges');

        return view('invoices.show', compact('invoice'));
    }

    public function markAsSent(Invoice $invoice)
    {
        $invoice->update(['status' => 'sent']);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice marked as sent');
    }

    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,bank_transfer,check',
            'paid_date' => 'required|date',
        ]);

        $invoice->update([
            'status' => 'paid',
            'payment_method' => $validated['payment_method'],
            'paid_date' => $validated['paid_date'],
        ]);

        // Mark all charges as paid
        $invoice->reservation->charges()->update(['status' => 'paid']);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice marked as paid');
    }

    public function generatePDF(Invoice $invoice)
    {
        $invoice->load('reservation.user', 'reservation.charges');

        // Generate PDF using TCPDF or similar
        // This is a simplified version
        return view('invoices.pdf', compact('invoice'))->render();
    }

    public function email(Invoice $invoice)
    {
        // Send invoice via email
        // Implement email sending here
        $invoice->update(['status' => 'sent']);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice sent via email');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->update(['status' => 'cancelled']);

        return redirect()->route('invoices.index')->with('success', 'Invoice cancelled');
    }
}
