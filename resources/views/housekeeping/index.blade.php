@extends('layout')

@section('title', 'Housekeeping Tasks')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="fas fa-broom"></i> Housekeeping Management</h1>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('housekeeping.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Task
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card" style="border-top-color: #f39c12;">
                    <h6>Pending Tasks</h6>
                    <div class="value">{{ $pendingTasks }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="border-top-color: #3498db;">
                    <h6>In Progress</h6>
                    <div class="value">{{ $inProgressTasks }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="border-top-color: #27ae60;">
                    <h6>Completed Today</h6>
                    <div class="value">0</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Tasks</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Room</th>
                                <th>Task Type</th>
                                <th>Assigned To</th>
                                <th>Status</th>
                                <th>Scheduled</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                                <tr>
                                    <td><strong>{{ $task->room->room_number }}</strong></td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $task->task_type)) }}</td>
                                    <td>{{ $task->assigned_to ?? '-' }}</td>
                                    <td>
                                        <select class="form-select form-select-sm" style="width: 150px;"
                                            onchange="updateTaskStatus({{ $task->id }}, this.value)">
                                            <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>
                                                Pending</option>
                                            <option value="in_progress"
                                                {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>
                                                Completed</option>
                                            <option value="cancelled"
                                                {{ $task->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </td>
                                    <td>{{ $task->scheduled_at?->format('M d, Y H:i') ?? '-' }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('housekeeping.destroy', $task) }}"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <nav>{{ $tasks->links() }}</nav>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function updateTaskStatus(taskId, status) {
                fetch(`/housekeeping/${taskId}/update-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        status: status
                    })
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        </script>
    @endpush

@endsection
