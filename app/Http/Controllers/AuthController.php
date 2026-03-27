<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Redirect based on user type
            if ($user->type === 'guest') {
                return redirect()->route('guest.dashboard')->with('success', 'Logged in successfully!');
            }

            return redirect()->route('dashboard')->with('success', 'Logged in successfully!');
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Determine user type from request or default to guest
        $userType = $request->input('type', 'guest');

        // Only allow guest registration via public form
        if ($userType !== 'guest') {
            return back()->withErrors(['type' => 'Invalid user type']);
        }

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

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }
}
