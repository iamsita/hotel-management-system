<!DOCTYPE html>
<html>

<head>
    <title>My Cleaning Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>My Cleaning Requests</h2>
            <a href="{{ route('guest.cleaning.create') }}" class="btn btn-primary">New Request</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
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
                                <span
                                    class="badge bg-{{ $request->status === 'completed' ? 'success' : ($request->status === 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                    data-bs-target="#detailModal{{ $request->id }}">
                                    Details
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
                                        <p><strong>Status:</strong>
                                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}</p>
                                        <p><strong>Description:</strong></p>
                                        <p>{{ $request->description ?? 'No additional details' }}</p>
                                        <p><strong>Requested:</strong> {{ $request->created_at->format('M d, Y H:i') }}
                                        </p>
                                        @if ($request->completed_at)
                                            <p><strong>Completed:</strong>
                                                {{ $request->completed_at->format('M d, Y H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No requests found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $requests->links() }}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
