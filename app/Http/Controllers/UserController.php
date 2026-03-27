<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = ['admin', 'manager', 'staff', 'guest'];

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:8|confirmed',
            'type' => 'required|in:admin,manager,staff',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function show(User $user)
    {
        // Ensure this is not a guest user
        if ($user->type === 'guest') {
            abort(404);
        }

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Ensure this is not a guest user
        if ($user->type === 'guest') {
            abort(404);
        }

        $roles = ['admin', 'manager', 'staff', 'guest'];

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Ensure this is not a guest user
        if ($user->type === 'guest') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:8|confirmed',
            'type' => 'required|in:admin,manager,staff',
            'status' => 'required|in:active,inactive',
        ]);

        if (isset($validated['password']) && $validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.show', $user)->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        // Ensure this is not a guest user
        if ($user->type === 'guest') {
            abort(404);
        }

        // Prevent deleting the currently logged-in user
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
