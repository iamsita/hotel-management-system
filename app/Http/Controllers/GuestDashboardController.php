<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestDashboardController extends Controller
{
    public static function middleware(): array
    {
        return [
            'type:guest',
        ];
    }

    public function index()
    {
        $user = Auth::user();

        $activeBooking = $user->reservations()
            ->where('status', 'checked_in')
            ->first();

        $totalBookings = $user->reservations()->count();
        $totalSpent = $user->reservations()->sum('total_amount');

        $recentOrders = $user->foodOrders()
            ->latest()
            ->limit(5)
            ->get();

        return view('guest.dashboard', compact(
            'user',
            'activeBooking',
            'totalBookings',
            'totalSpent',
            'recentOrders'
        ));
    }

    public function profile()
    {
        $user = Auth::user();

        return view('guest.profile.edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}
