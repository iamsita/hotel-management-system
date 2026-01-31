<?php

namespace App\Http\Controllers;

use App\Models\HousekeepingTask;
use App\Models\Room;
use Illuminate\Http\Request;

class HousekeepingController extends Controller
{
    public function index()
    {
        $tasks = HousekeepingTask::with('room')
            ->orderBy('scheduled_at', 'desc')
            ->paginate(20);

        $pendingTasks = HousekeepingTask::where('status', 'pending')->count();
        $inProgressTasks = HousekeepingTask::where('status', 'in_progress')->count();

        return view('housekeeping.index', compact('tasks', 'pendingTasks', 'inProgressTasks'));
    }

    public function create()
    {
        $rooms = Room::all();

        return view('housekeeping.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'task_type' => 'required|in:cleaning,maintenance,inspection,restocking',
            'assigned_to' => 'nullable|string',
            'notes' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
        ]);

        HousekeepingTask::create($validated);

        return redirect()->route('housekeeping.index')->with('success', 'Task created successfully');
    }

    public function updateStatus(Request $request, HousekeepingTask $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        if ($validated['status'] === 'in_progress') {
            $task->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        } elseif ($validated['status'] === 'completed') {
            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            // Update room housekeeping status
            $task->room->update(['housekeeping_status' => 'clean']);
        } else {
            $task->update(['status' => $validated['status']]);
        }

        return response()->json(['success' => true]);
    }

    public function getRoomStatus()
    {
        $dirtyRooms = Room::where('housekeeping_status', 'dirty')->count();
        $cleanRooms = Room::where('housekeeping_status', 'clean')->count();
        $inProgressRooms = Room::where('housekeeping_status', 'in_progress')->count();
        $inspectedRooms = Room::where('housekeeping_status', 'inspected')->count();

        $pendingTasks = HousekeepingTask::where('status', 'pending')->count();
        $inProgressTasks = HousekeepingTask::where('status', 'in_progress')->count();

        return response()->json([
            'room_status' => [
                'dirty' => $dirtyRooms,
                'clean' => $cleanRooms,
                'in_progress' => $inProgressRooms,
                'inspected' => $inspectedRooms,
            ],
            'task_status' => [
                'pending' => $pendingTasks,
                'in_progress' => $inProgressTasks,
            ],
        ]);
    }

    public function destroy(HousekeepingTask $task)
    {
        $task->delete();

        return redirect()->route('housekeeping.index')->with('success', 'Task deleted successfully');
    }
}
