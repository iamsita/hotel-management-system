<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::all();
        $totalRooms = $rooms->count();
        $availableRooms = $rooms->where('status', 'available')->count();
        $occupiedRooms = $rooms->where('status', 'occupied')->count();
        $maintenanceRooms = $rooms->where('status', 'maintenance')->count();

        return view('rooms.index', compact('rooms', 'totalRooms', 'availableRooms', 'occupiedRooms', 'maintenanceRooms'));
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|unique:rooms',
            'room_type' => 'required|in:single,double,suite,deluxe',
            'capacity' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'floor' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        Room::create($validated);

        return redirect()->route('rooms.index')->with('success', 'Room created successfully');
    }

    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number' => 'required|unique:rooms,room_number,'.$room->id,
            'room_type' => 'required|in:single,double,suite,deluxe',
            'capacity' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'status' => 'required|in:available,occupied,maintenance,reserved',
            'housekeeping_status' => 'required|in:clean,dirty,in_progress,inspected',
            'floor' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $room->update($validated);

        return redirect()->route('rooms.index')->with('success', 'Room updated successfully');
    }

    public function updateStatus(Request $request, Room $room)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,maintenance,reserved',
            'housekeeping_status' => 'nullable|in:clean,dirty,in_progress,inspected',
        ]);

        $room->update($validated);

        return response()->json(['success' => true, 'message' => 'Room status updated']);
    }

    public function getRoomStatus()
    {
        $rooms = Room::all();

        return response()->json([
            'total' => $rooms->count(),
            'available' => $rooms->where('status', 'available')->count(),
            'occupied' => $rooms->where('status', 'occupied')->count(),
            'maintenance' => $rooms->where('status', 'maintenance')->count(),
            'reserved' => $rooms->where('status', 'reserved')->count(),
            'rooms' => $rooms->map(function ($room) {
                return [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'status' => $room->status,
                    'housekeeping_status' => $room->housekeeping_status,
                    'current_guest' => $room->current_reservation?->guest?->full_name,
                ];
            }),
        ]);
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully');
    }
}
