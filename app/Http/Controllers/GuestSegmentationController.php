<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\GuestSegmentationEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class GuestSegmentationController extends Controller
{
    private GuestSegmentationEngine $engine;

    public function __construct(GuestSegmentationEngine $engine)
    {
        $this->engine = $engine;
    }

    // ==================== VIEW METHODS ====================

    /**
     * Display the guest segmentation dashboard
     */
    public function dashboard(): View
    {
        $summary = $this->engine->getSegmentationSummary();

        return view('segmentation.dashboard', [
            'summary' => $summary,
        ]);
    }

    /**
     * Display guests by segment
     */
    public function showSegment(string $segment): View
    {
        $validSegments = ['VIP', 'LOYAL', 'BUSINESS', 'LEISURE', 'BUDGET', 'RISK', 'REGULAR'];
        $segment = strtoupper($segment);

        if (!in_array($segment, $validSegments)) {
            abort(404);
        }

        $guests = $this->engine->getGuestsBySegment($segment);
        $summary = $this->engine->getSegmentationSummary();

        return view('segmentation.segment', [
            'segment' => $segment,
            'guests' => $guests,
            'summary' => $summary,
        ]);
    }

    /**
     * Display guest details and insights
     */
    public function showGuest(User $user): View
    {
        if ($user->type !== 'guest') {
            abort(404);
        }

        $reservations = $user->reservations()->get();
        $payments = $user->payments()->get();

        return view('segmentation.guest-detail', [
            'user' => $user,
            'reservations' => $reservations,
            'payments' => $payments,
        ]);
    }

    /**
     * Show the segment all guests form
     */
    public function segmentForm(): View
    {
        return view('segmentation.segment-form');
    }

    // ==================== API METHODS ====================
    public function segmentGuest(User $user): JsonResponse
    {
        if ($user->type !== 'guest') {
            return response()->json(['status' => 'error', 'message' => 'User is not a guest'], 400);
        }

        $guest = $this->engine->segmentGuest($user);

        return response()->json([
            'status' => 'success',
            'data' => [
                'user_id' => $guest->id,
                'segment' => $guest->segment,
                'metrics' => $guest->segment_metrics,
                'last_segmented' => $guest->last_segmented_at,
            ],
        ]);
    }

    /**
     * Segment all guests
     */
    public function segmentAllGuests(): JsonResponse
    {
        $results = $this->engine->segmentAllGuests();
        $successful = collect($results)->where('status', 'success')->count();
        $failed = collect($results)->where('status', 'failed')->count();

        return response()->json([
            'status' => 'success',
            'message' => "Segmented {$successful} guests, {$failed} failed",
            'data' => [
                'total_processed' => count($results),
                'successful' => $successful,
                'failed' => $failed,
            ],
        ]);
    }

    /**
     * Get segmentation summary
     */
    public function getSummary(): JsonResponse
    {
        $summary = $this->engine->getSegmentationSummary();

        return response()->json([
            'status' => 'success',
            'data' => $summary,
        ]);
    }

    /**
     * Get guests by segment
     */
    public function getBySegment(string $segment): JsonResponse
    {
        $validSegments = ['VIP', 'LOYAL', 'BUSINESS', 'LEISURE', 'BUDGET', 'RISK', 'REGULAR'];

        if (!in_array(strtoupper($segment), $validSegments)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid segment',
            ], 400);
        }

        $guests = $this->engine->getGuestsBySegment($segment);

        return response()->json([
            'status' => 'success',
            'segment' => strtoupper($segment),
            'count' => $guests->count(),
            'data' => $guests,
        ]);
    }

    /**
     * Get guest insights
     */
    public function getInsights(User $user): JsonResponse
    {
        if ($user->type !== 'guest') {
            return response()->json(['status' => 'error', 'message' => 'User is not a guest'], 400);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'guest' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'segment' => $user->segment,
                'metrics' => $user->segment_metrics,
                'last_segmented' => $user->last_segmented_at,
            ],
        ]);
    }
}
