@extends('layout')

@section('title', 'Create Housekeeping Task')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4"><i class="fas fa-plus"></i> Create Housekeeping Task</h1>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('housekeeping.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="room_id" class="form-label">Room *</label>
                                <select class="form-select @error('room_id') is-invalid @enderror" id="room_id"
                                    name="room_id" required>
                                    <option value="">Select Room</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}"
                                            {{ old('room_id') === (string) $room->id ? 'selected' : '' }}>
                                            {{ $room->room_number }} - Status:
                                            {{ ucfirst(str_replace('_', ' ', $room->housekeeping_status)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="task_type" class="form-label">Task Type *</label>
                                <select class="form-select @error('task_type') is-invalid @enderror" id="task_type"
                                    name="task_type" required>
                                    <option value="">Select Task Type</option>
                                    <option value="cleaning" {{ old('task_type') === 'cleaning' ? 'selected' : '' }}>
                                        Cleaning</option>
                                    <option value="maintenance" {{ old('task_type') === 'maintenance' ? 'selected' : '' }}>
                                        Maintenance</option>
                                    <option value="inspection" {{ old('task_type') === 'inspection' ? 'selected' : '' }}>
                                        Inspection</option>
                                    <option value="restocking" {{ old('task_type') === 'restocking' ? 'selected' : '' }}>
                                        Restocking</option>
                                </select>
                                @error('task_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="assigned_to" class="form-label">Assigned To</label>
                                <input type="text" class="form-control @error('assigned_to') is-invalid @enderror"
                                    id="assigned_to" name="assigned_to" value="{{ old('assigned_to') }}">
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="scheduled_at" class="form-label">Scheduled Date & Time</label>
                                <input type="datetime-local"
                                    class="form-control @error('scheduled_at') is-invalid @enderror" id="scheduled_at"
                                    name="scheduled_at" value="{{ old('scheduled_at') }}">
                                @error('scheduled_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Task
                                </button>
                                <a href="{{ route('housekeeping.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
