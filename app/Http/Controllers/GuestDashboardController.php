<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class GuestDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:guest');
    }

    public function index()
    {
        $guest = Auth::user();

        $activeBooking = $guest->reservations()
            ->where('status', 'checked_in')
            ->first();

        $totalBookings = $guest->reservations()->count();
        $totalSpent = $guest->reservations()->sum('total_amount');

        $recentOrders = $guest->foodOrders()
            ->latest()
            ->limit(5)
            ->get();

        return view('guest.dashboard', compact(
            'guest',
            'activeBooking',
            'totalBookings',
            'totalSpent',
            'recentOrders'
        ));
    }

    public function profile()
    {
        $guest = Auth::user();

        return view('guest.profile.edit', compact('guest'));
    }

    public function updateProfile(Request $request)
    {
        $guest = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
        ]);

        $guest->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}
