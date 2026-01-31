<?php

namespace App\Http\Controllers;

use App\Models\CleaningRequest;
use App\Models\FoodOrder;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalGuests' => User::where('type', 'guest')->count(),
            'checkedIn' => Reservation::where('status', 'checked_in')->count(),
            'pendingOrders' => FoodOrder::where('status', '!=', 'delivered')->count(),
            'totalRevenue' => Payment::where('status', 'completed')->sum('amount'),
            'recentBookings' => Reservation::with(['user'])->orderBy('check_in_date', 'desc')->limit(5)->get(),
            'pendingCleaning' => CleaningRequest::where('status', '!=', 'completed')->limit(5)->get(),
        ];

        return view('staff.dashboard', $data);
    }
}
