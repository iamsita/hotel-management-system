<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Invoice;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function dashboard()
    {
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $availableRooms = Room::where('status', 'available')->count();
        $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

        $totalGuests = User::count();
        $activeReservations = Reservation::where('status', 'checked_in')->count();
        $totalReservations = Reservation::count();

        $totalRevenue = Invoice::where('status', 'paid')->sum('total_amount');
        $pendingInvoices = Invoice::where('status', '!=', 'paid')->sum('total_amount');

        return view('reports.dashboard', compact(
            'totalRooms',
            'occupiedRooms',
            'availableRooms',
            'occupancyRate',
            'totalGuests',
            'activeReservations',
            'totalReservations',
            'totalRevenue',
            'pendingInvoices'
        ));
    }

    public function occupancyReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));

        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $reservations = Reservation::whereBetween('check_in_date', [$startDate, $endDate])
            ->with('room', 'guest')
            ->get();

        $totalNights = 0;
        $occupiedNights = 0;

        foreach ($reservations as $reservation) {
            if ($reservation->status !== 'cancelled') {
                $nights = $reservation->check_out_date->diffInDays($reservation->check_in_date);
                $occupiedNights += $nights;
            }
        }

        $totalNights = Room::count() * 30; // 30 days
        $occupancyRate = $totalNights > 0 ? ($occupiedNights / $totalNights) * 100 : 0;

        return view('reports.occupancy', compact('reservations', 'occupancyRate', 'startDate', 'endDate'));
    }

    public function revenueReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));

        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $invoices = Invoice::whereBetween('issue_date', [$startDate, $endDate])
            ->with('reservation.guest')
            ->get();

        $roomRevenue = 0;
        $serviceRevenue = 0;
        $totalRevenue = 0;

        foreach ($invoices as $invoice) {
            $charges = $invoice->reservation->charges;
            foreach ($charges as $charge) {
                if ($charge->charge_type === 'room') {
                    $roomRevenue += $charge->amount;
                } elseif ($charge->charge_type === 'service') {
                    $serviceRevenue += $charge->amount;
                }
            }
            $totalRevenue += $invoice->total_amount;
        }

        $byPaymentMethod = Invoice::whereBetween('issue_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->groupBy('payment_method')
            ->selectRaw('payment_method, SUM(total_amount) as amount')
            ->get();

        return view('reports.revenue', compact('invoices', 'roomRevenue', 'serviceRevenue', 'totalRevenue', 'byPaymentMethod', 'startDate', 'endDate'));
    }

    public function guestReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));

        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $guests = User::whereHas('reservations', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('check_in_date', [$startDate, $endDate]);
        })
            ->with('reservations')
            ->get();

        $newGuests = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalGuests = User::count();
        $repeatGuests = User::whereHas('reservations', function ($query) {
            $query->where('status', '!=', 'cancelled');
        })
            ->having('reservation_count', '>', 1)
            ->selectRaw('guests.*, count(*) as reservation_count')
            ->groupBy('guests.id')
            ->get();

        return view('reports.guest', compact('guests', 'newGuests', 'totalGuests', 'repeatGuests', 'startDate', 'endDate'));
    }

    public function serviceReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));

        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $charges = Charge::whereBetween('created_at', [$startDate, $endDate])
            ->with('service', 'reservation')
            ->get()
            ->groupBy('service_id');

        $serviceBreakdown = [];
        foreach ($charges as $serviceId => $serviceCharges) {
            $service = $serviceCharges->first()->service;
            $serviceBreakdown[] = [
                'name' => $service?->name ?? 'Extra Charge',
                'count' => $serviceCharges->count(),
                'total' => $serviceCharges->sum('amount'),
                'average' => $serviceCharges->average('amount'),
            ];
        }

        return view('reports.service', compact('serviceBreakdown', 'startDate', 'endDate'));
    }
}
