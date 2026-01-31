<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GuestAuthController extends Controller
{
    public function showRegister()
    {
        return view('guest.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:guests',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string|max:20',
            'id_type' => 'required|string',
            'id_number' => 'required|string|unique:guests',
            'address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['guest_type'] = 'individual';

        $guest = Guest::create($validated);

        Auth::login($guest);

        return redirect()->route('guest.dashboard')->with('success', 'Account created successfully!');
    }

    public function showLogin()
    {
        return view('guest.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            return redirect()->route('guest.dashboard')->with('success', 'Logged in successfully!');
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('guest.login')->with('success', 'Logged out successfully!');
    }
}
