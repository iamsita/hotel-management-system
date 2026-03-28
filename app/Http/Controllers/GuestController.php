<?php

namespace App\Http\Controllers;

use App\Models\User;

class GuestController extends Controller
{
    public function index()
    {
        $guests = User::where('role', 'guest')->withCount('reservations')->paginate(15);

        return view('admin.guests.index', compact('guests'));
    }
}
