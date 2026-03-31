@extends('layout')

@section('content')
<div class="container py-5" style="max-width: 600px;">
    <!-- Header -->
    <div class="mb-4">
        <a href="{{ route('segmentation.dashboard') }}" class="text-decoration-none small mb-2 d-block">← Back to Dashboard</a>
        <h1 class="fs-3 fw-bold">Segment All Guests</h1>
        <p class="text-muted small">Re-segment all guests based on their latest booking and payment history</p>
    </div>

    <form id="segmentForm" action="{{ route('segmentation.segment-all') }}" method="POST" class="border p-4">
        @csrf

        <div class="bg-light border p-3 mb-4 small">
            <p class="mb-0">
                This operation will recalculate segmentation for all guests in the system. It may take a few moments depending on the number of guests.
            </p>
        </div>

        <div class="mb-4">
            <label for="confirm" class="form-check-label">
                <input type="checkbox" id="confirm" name="confirm" class="form-check-input">
                I understand that this will update all guest segments
            </label>
        </div>

        <div class="mb-4">
            <button type="button" id="startBtn" disabled class="btn btn-primary w-100">
                Start Segmentation
            </button>
        </div>

        <!-- Progress Section (hidden until started) -->
        <div id="progressSection" class="d-none">
            <div class="mb-4">
                <p class="small fw-semibold mb-2">Progress</p>
                <div class="progress" style="height: 8px;">
                    <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                </div>
                <p id="progressText" class="small text-muted mt-2">Initializing...</p>
            </div>

            <!-- Results -->
            <div id="resultSection" class="d-none border p-4 mb-4">
                <h3 class="fw-semibold mb-3">Results</h3>
                <div class="row" id="resultContent">
                    <p class="text-muted">Processing...</p>
                </div>
            </div>

            <!-- Error -->
            <div id="errorSection" class="d-none border border-danger bg-danger bg-opacity-10 p-4 mb-4">
                <h3 class="fw-semibold text-danger mb-2">Error</h3>
                <p id="errorContent" class="small text-danger mb-0"></p>
            </div>
        </div>
    </form>

    <!-- Back Button -->
    <div class="mt-4">
        <a href="{{ route('segmentation.dashboard') }}" class="text-decoration-none fw-semibold small">
            ← Back to Dashboard
        </a>
    </div>
</div>

<script>
    // Enable button when checkbox is checked
    document.getElementById('confirm').addEventListener('change', function() {
        document.getElementById('startBtn').disabled = !this.checked;
        if (this.checked) {
            document.getElementById('startBtn').onclick = handleSegmentation;
        }
    });

    async function handleSegmentation() {
        const confirmBtn = document.getElementById('startBtn');
        const progressSection = document.getElementById('progressSection');
        const resultSection = document.getElementById('resultSection');
        const errorSection = document.getElementById('errorSection');
        const resultContent = document.getElementById('resultContent');
        const errorContent = document.getElementById('errorContent');

        // Reset and show progress section
        confirmBtn.disabled = true;
        progressSection.classList.remove('d-none');
        resultSection.classList.add('d-none');
        errorSection.classList.add('d-none');

        try {
            // Send the segmentation request
            const response = await fetch('{{ route('segmentation.segment-all') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': document.querySelector('input[name="_token"]').value,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            });

            const data = await response.json();

            if (response.ok && data.status === 'success') {
                // Show results
                document.getElementById('progressBar').style.width = '100%';
                document.getElementById('progressText').textContent = 'Segmentation completed!';

                resultContent.innerHTML = `
                    <div class="col-4 text-center">
                        <p class="text-muted text-uppercase small fw-semibold">Total Processed</p>
                        <p class="fs-5 fw-bold">${data.data.total_processed}</p>
                    </div>
                    <div class="col-4 text-center">
                        <p class="text-muted text-uppercase small fw-semibold">Successful</p>
                        <p class="fs-5 fw-bold text-success">${data.data.successful}</p>
                    </div>
                    <div class="col-4 text-center">
                        <p class="text-muted text-uppercase small fw-semibold">Failed</p>
                        <p class="fs-5 fw-bold text-danger">${data.data.failed}</p>
                    </div>
                `;
                resultSection.classList.remove('d-none');

                // Add redirect button after results
                setTimeout(() => {
                    window.location.href = '{{ route('segmentation.dashboard') }}';
                }, 3000);
            } else {
                throw new Error(data.message || 'An error occurred during segmentation');
            }
        } catch (error) {
            errorContent.textContent = error.message || 'An unexpected error occurred. Please try again.';
            errorSection.classList.remove('d-none');
            confirmBtn.disabled = false;
        }
    }
</script>
@endsection
