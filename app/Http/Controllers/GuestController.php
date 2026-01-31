<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index()
    {
        $guests = User::where('type', 'guest')->with('reservations')->paginate(15);

        return view('guests.index', compact('guests'));
    }

    public function create()
    {
        return view('guests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string',
            'password' => 'required|min:8',
        ]);

        // Set user type to guest
        $validated['type'] = 'guest';
        $validated['status'] = 'active';

        User::create($validated);

        return redirect()->route('guests.index')->with('success', 'Guest created successfully');
    }

    public function show(User $user)
    {
        // Ensure this is a guest user
        if ($user->type !== 'guest') {
            abort(404);
        }

        $user->load('reservations.room', 'reservations.charges');

        return view('guests.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Ensure this is a guest user
        if ($user->type !== 'guest') {
            abort(404);
        }

        return view('guests.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Ensure this is a guest user
        if ($user->type !== 'guest') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string',
        ]);

        $user->update($validated);

        return redirect()->route('guests.show', $user)->with('success', 'Guest updated successfully');
    }

    public function destroy(User $user)
    {
        // Ensure this is a guest user
        if ($user->type !== 'guest') {
            abort(404);
        }

        $user->delete();

        return redirect()->route('guests.index')->with('success', 'Guest deleted successfully');
    }
}
