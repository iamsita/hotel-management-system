<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'guest')->withCount('reservations');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('segment')) {
            $query->where('segment', $request->segment);
        }

        $guests = $query->paginate(15)->withQueryString();

        return view('admin.guests.index', [
            'guests' => $guests,
        ]);
    }

    public function create()
    {
        return view('admin.guests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:6',
        ]);

        $validated['role'] = 'guest';

        User::create($validated);

        return redirect()->route('admin.guests.index')->with('success', 'Guest created successfully.');
    }
}
