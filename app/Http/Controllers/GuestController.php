<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index()
    {
        $guests = Guest::with('reservations')->paginate(15);

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

        Guest::create($validated);

        return redirect()->route('guests.index')->with('success', 'Guest created successfully');
    }

    public function show(Guest $guest)
    {
        $guest->load('reservations.room', 'reservations.charges');

        return view('guests.show', compact('guest'));
    }

    public function edit(Guest $guest)
    {
        return view('guests.edit', compact('guest'));
    }

    public function update(Request $request, Guest $guest)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:guests,email,'.$guest->id,
            'phone' => 'nullable|string',
            'id_number' => 'nullable|string',
            'id_type' => 'nullable|in:passport,national_id,driving_license',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'guest_type' => 'required|in:individual,corporate',
        ]);

        $guest->update($validated);

        return redirect()->route('guests.show', $guest)->with('success', 'Guest updated successfully');
    }

    public function destroy(Guest $guest)
    {
        $guest->delete();

        return redirect()->route('guests.index')->with('success', 'Guest deleted successfully');
    }
}
