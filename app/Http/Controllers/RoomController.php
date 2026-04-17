<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::all();

        return view('admin.rooms.index', [
            'rooms',
        ]);
    }

    public function create()
    {
        return view('admin.rooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|unique:rooms',
            'type' => 'required|in:single,double,suite,deluxe',
            'capacity' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'floor' => 'required|integer|min:1',
        ]);

        Room::create($validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Room added successfully.');
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.edit', [
            'room' => $room,
        ]);
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number' => 'required|unique:rooms,room_number,'.$room->id,
            'type' => 'required|in:single,double,suite,deluxe',
            'capacity' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'status' => 'required|in:available,occupied,maintenance',
            'floor' => 'required|integer|min:1',
        ]);

        $room->update($validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return redirect()->route('admin.rooms.index')->with('success', 'Room deleted.');
    }
}
