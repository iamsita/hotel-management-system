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

    public function guest(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $query = $user->reservations()->with('room')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('room', fn ($q) => $q->where('room_number', 'like', "%{$search}%"));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('check_in_from')) {
            $query->whereDate('check_in', '>=', $request->check_in_from);
        }

        if ($request->filled('check_in_to')) {
            $query->whereDate('check_in', '<=', $request->check_in_to);
        }

        $reservations = $query->paginate(10)->withQueryString();

        return view('guest.dashboard', [
            'reservations' => $reservations,
        ]);
    }
}
