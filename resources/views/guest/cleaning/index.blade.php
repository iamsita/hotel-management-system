@extends('guest-layout')

@section('title', 'My Cleaning Requests')
@section('page-title', 'My Cleaning Requests')

@section('content')
    <div class="mb-3">
        <a href="{{ route('guest.cleaning.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Request
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Service Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td>Room {{ $request->room->room_number }}</td>
                            <td>{{ ucfirst($request->request_type) }}</td>
                            <td>
                                <span
                                    class="badge bg-{{ $request->priority === 'urgent' ? 'danger' : ($request->priority === 'high' ? 'warning' : 'info') }}">
                                    {{ ucfirst($request->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ str_replace('_', '-', $request->status) }}">
                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                    data-bs-target="#detailModal{{ $request->id }}">
                                    <i class="fas fa-eye"></i> Details
                                </a>
                            </td>
                        </tr>

                        <!-- Detail Modal -->
                        <div class="modal fade" id="detailModal{{ $request->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Request Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Service Type:</strong> {{ ucfirst($request->request_type) }}</p>
                                        <p><strong>Priority:</strong> {{ ucfirst($request->priority) }}</p>
                                        <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                        </p>
                                        <p><strong>Description:</strong></p>
                                        <p>{{ $request->description ?? 'No additional details' }}</p>
                                        <p><strong>Requested:</strong> {{ $request->created_at->format('M d, Y H:i') }}</p>
                                        @if ($request->completed_at)
                                            <p><strong>Completed:</strong>
                                                {{ $request->completed_at?->format('M d, Y H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <p class="text-muted">No requests found</p>
                                <a href="{{ route('guest.cleaning.create') }}" class="btn btn-primary btn-sm">Create Your
                                    First Request</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($requests->hasPages())
        <div class="mt-3">
            {{ $requests->links() }}
        </div>
    @endif
@endsection
