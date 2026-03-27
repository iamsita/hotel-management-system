<?php

namespace App\Http\Controllers;

use App\Models\User;
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string|max:20',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['type'] = 'guest';
        $validated['status'] = 'active';

        $user = User::create($validated);

        Auth::login($user);

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
            $user = Auth::user();

            // Only allow guest users
            if ($user->type !== 'guest') {
                Auth::logout();

                return back()->withErrors(['email' => 'Staff users must use staff login'])->onlyInput('email');
            }

            return redirect()->route('guest.dashboard')->with('success', 'Logged in successfully!');
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }
}
