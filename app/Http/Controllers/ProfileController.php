<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show user profile
     */
    public function show()
    {
        $user = auth()->user();

        return view('profile.show', [
            'user' => $user,
        ]);
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        $user = auth()->user();

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update basic info
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        // Update password if provided
        if (isset($validated['password']) && $validated['password']) {
            if (! Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }
}
