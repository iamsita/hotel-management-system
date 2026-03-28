<?php

namespace App\Http\Controllers;

use App\Models\FoodOrder;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('admin.dashboard', [
            'totalRooms' => Room::count(),
            'availableRooms' => Room::where('status', 'available')->count(),
            'totalGuests' => User::where('role', 'guest')->count(),
            'totalReservations' => Reservation::count(),
            'checkedIn' => Reservation::where('status', 'checked_in')->count(),
            'pendingOrders' => FoodOrder::where('status', 'pending')->count(),
            'totalRevenue' => Payment::where('status', 'completed')->sum('amount'),
            'recentReservations' => Reservation::with('user', 'room')->latest()->limit(5)->get(),
        ]);
    }

    public function guest()
    {
        $user = auth()->user();

        return view('guest.dashboard', [
            'reservations' => $user->reservations()->with('room')->latest()->get(),
        ]);
    }
}
