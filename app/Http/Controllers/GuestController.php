<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index()
    {
        $guests = User::with('reservations')->paginate(15);

        return view('guests.index', compact('guests'));
    }

    public function create()
    {
        return view('guests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:guests',
            'phone' => 'nullable|string',
            'id_number' => 'nullable|string',
            'id_type' => 'nullable|in:passport,national_id,driving_license',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'guest_type' => 'required|in:individual,corporate',
        ]);

        User::create($validated);

        return redirect()->route('guests.index')->with('success', 'Guest created successfully');
    }

    public function show(User $user)
    {
        $user->load('reservations.room', 'reservations.charges');

        return view('guests.show', compact('guest'));
    }

    public function edit(User $user)
    {
        return view('guests.edit', compact('guest'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:guests,email,'.$user->id,
            'phone' => 'nullable|string',
            'id_number' => 'nullable|string',
            'id_type' => 'nullable|in:passport,national_id,driving_license',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'guest_type' => 'required|in:individual,corporate',
        ]);

        $user->update($validated);

        return redirect()->route('guests.show', $user)->with('success', 'Guest updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('guests.index')->with('success', 'Guest deleted successfully');
    }
}
