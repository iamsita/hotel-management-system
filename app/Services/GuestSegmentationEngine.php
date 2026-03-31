<?php

namespace App\Services;

use App\Models\User;
use App\Models\GuestSegmentHistory;
use Illuminate\Support\Facades\DB;

class GuestSegmentationEngine
{
    /**
     * Segment constants
     */
    const SEGMENT_VIP = 'VIP';
    const SEGMENT_LOYAL = 'LOYAL';
    const SEGMENT_BUDGET = 'BUDGET';
    const SEGMENT_BUSINESS = 'BUSINESS';
    const SEGMENT_LEISURE = 'LEISURE';
    const SEGMENT_RISK = 'RISK';
    const SEGMENT_REGULAR = 'REGULAR';

    /**
     * Configuration thresholds
     */
    private array $config = [
        'vip_lifetime_value' => 50000,
        'vip_min_bookings' => 5,
        'loyal_min_bookings' => 10,
        'loyal_min_stay' => 3,
        'business_max_stay' => 2,
        'business_min_bookings' => 4,
        'leisure_min_stay' => 4,
        'risk_cancellation_threshold' => 0.3,
        'risk_payment_reliability_threshold' => 70,
    ];

    /**
     * Segment a single guest
     */
    public function segmentGuest(User $guest): User
    {
        // Capture the current segment before update
        $previousSegment = $guest->segment;

        // Calculate metrics
        $metrics = $this->calculateGuestMetrics($guest);

        // Determine segment
        $segment = $this->determineSegment($metrics);

        // Update user with segment and metrics
        $guest->update([
            'segment' => $segment,
            'segment_metrics' => $metrics,
            'last_segmented_at' => now(),
        ]);

        // Record history if segment changed
        if ($previousSegment && $previousSegment !== $segment) {
            GuestSegmentHistory::create([
                'user_id' => $guest->id,
                'segment' => $previousSegment,
                'metrics' => $metrics,
            ]);
        }

        return $guest->fresh();
    }

    /**
     * Segment all guests
     */
    public function segmentAllGuests(): array
    {
        $guests = User::where('type', 'guest')->get();
        $results = [];

        foreach ($guests as $guest) {
            try {
                $segmented = $this->segmentGuest($guest);
                $results[$guest->id] = [
                    'status' => 'success',
                    'segment' => $segmented->segment,
                ];
            } catch (\Exception $e) {
                $results[$guest->id] = [
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Calculate comprehensive metrics
     */
    private function calculateGuestMetrics(User $guest): array
    {
        $reservations = $guest->reservations()
            ->where('status', '!=', 'cancelled')
            ->get();

        $payments = DB::table('payments')
            ->whereIn('reservation_id', $reservations->pluck('id'))
            ->where('status', 'completed')
            ->get();

        // Calculate metrics
        $lifetimeValue = (int) $payments->sum('amount');
        $bookingCount = $reservations->count();

        $totalNights = $reservations->sum(function ($r) {
            $checkIn = \Carbon\Carbon::parse($r->check_in_date);
            $checkOut = \Carbon\Carbon::parse($r->check_out_date);
            return abs($checkOut->diffInDays($checkIn));
        });

        $avgStayDays = $bookingCount > 0 ? round($totalNights / $bookingCount, 2) : 0;
        $avgRoomPrice = $reservations->avg('total_amount') ?? 0;

        // Cancellation rate
        $allReservations = $guest->reservations()->get();
        $cancelledCount = $allReservations->where('status', 'cancelled')->count();
        $cancellationRate = $allReservations->count() > 0
            ? round(($cancelledCount / $allReservations->count()) * 100, 2)
            : 0;

        // Payment reliability
        $paymentReliability = $this->assessPaymentReliability($guest, $payments);

        return [
            'lifetime_value' => $lifetimeValue,
            'booking_count' => $bookingCount,
            'total_nights' => $totalNights,
            'avg_stay_days' => $avgStayDays,
            'avg_room_price' => round($avgRoomPrice, 2),
            'cancellation_rate' => $cancellationRate,
            'payment_reliability' => $paymentReliability,
        ];
    }

    /**
     * Determine segment based on metrics
     */
    private function determineSegment(array $metrics): string
    {
        // Check RISK first
        if ($this->isRiskGuest($metrics)) {
            return self::SEGMENT_RISK;
        }

        // Check VIP
        if ($this->isVipGuest($metrics)) {
            return self::SEGMENT_VIP;
        }

        // Check LOYAL
        if ($this->isLoyalGuest($metrics)) {
            return self::SEGMENT_LOYAL;
        }

        // Check BUSINESS
        if ($this->isBusinessGuest($metrics)) {
            return self::SEGMENT_BUSINESS;
        }

        // Check LEISURE
        if ($this->isLeisureGuest($metrics)) {
            return self::SEGMENT_LEISURE;
        }

        // Check BUDGET
        if ($this->isBudgetGuest($metrics)) {
            return self::SEGMENT_BUDGET;
        }

        return self::SEGMENT_REGULAR;
    }

    /**
     * Segment classification checks
     */
    private function isVipGuest(array $metrics): bool
    {
        return $metrics['lifetime_value'] >= $this->config['vip_lifetime_value']
            && $metrics['booking_count'] >= $this->config['vip_min_bookings'];
    }

    private function isLoyalGuest(array $metrics): bool
    {
        return $metrics['booking_count'] >= $this->config['loyal_min_bookings']
            && $metrics['avg_stay_days'] >= $this->config['loyal_min_stay'];
    }

    private function isBusinessGuest(array $metrics): bool
    {
        return $metrics['avg_stay_days'] <= $this->config['business_max_stay']
            && $metrics['booking_count'] >= $this->config['business_min_bookings'];
    }

    private function isLeisureGuest(array $metrics): bool
    {
        return $metrics['avg_stay_days'] >= $this->config['leisure_min_stay'];
    }

    private function isBudgetGuest(array $metrics): bool
    {
        $medianPrice = $this->getMedianRoomPrice();
        return $metrics['avg_room_price'] < $medianPrice;
    }

    private function isRiskGuest(array $metrics): bool
    {
        $hasHighCancellation = $metrics['cancellation_rate'] > ($this->config['risk_cancellation_threshold'] * 100);
        $hasLowPaymentReliability = $metrics['payment_reliability'] < $this->config['risk_payment_reliability_threshold'];
        return $hasHighCancellation || $hasLowPaymentReliability;
    }

    /**
     * Assess payment reliability (0-100)
     */
    private function assessPaymentReliability($guest, $payments): int
    {
        if ($payments->isEmpty()) {
            return 75;
        }

        $totalPayments = $payments->count();
        $allPayments = DB::table('payments')
            ->whereIn('reservation_id', $guest->reservations()->pluck('id'))
            ->get();

        $successCount = $allPayments->where('status', 'completed')->count();
        $failedCount = $allPayments->where('status', 'failed')->count();

        $successRate = ($successCount / $allPayments->count()) * 100;
        $reliability = $successRate - ($failedCount * 5);

        return max(0, min(100, (int) $reliability));
    }

    /**
     * Get median room price
     */
    private function getMedianRoomPrice(): float
    {
        $prices = DB::table('reservations')
            ->where('status', '!=', 'cancelled')
            ->pluck('total_amount')
            ->sort()
            ->values();

        if ($prices->isEmpty()) {
            return 0;
        }

        $count = $prices->count();
        $middle = (int) ($count / 2);

        if ($count % 2 === 0) {
            return ($prices[$middle - 1] + $prices[$middle]) / 2;
        }

        return $prices[$middle];
    }

    /**
     * Get segmentation summary
     */
    public function getSegmentationSummary(): array
    {
        return [
            'total_guests' => User::where('type', 'guest')->count(),
            'by_segment' => [
                'vip' => User::where('segment', 'VIP')->count(),
                'loyal' => User::where('segment', 'LOYAL')->count(),
                'business' => User::where('segment', 'BUSINESS')->count(),
                'leisure' => User::where('segment', 'LEISURE')->count(),
                'budget' => User::where('segment', 'BUDGET')->count(),
                'risk' => User::where('segment', 'RISK')->count(),
                'regular' => User::where('segment', 'REGULAR')->count(),
            ],
            'vip_revenue' => User::where('segment', 'VIP')->sum(
                DB::raw("JSON_EXTRACT(segment_metrics, '$.lifetime_value')")
            ),
            'total_revenue' => User::where('type', 'guest')->sum(
                DB::raw("JSON_EXTRACT(segment_metrics, '$.lifetime_value')")
            ),
        ];
    }

    /**
     * Get guests by segment
     */
    public function getGuestsBySegment(string $segment)
    {
        return User::where('segment', strtoupper($segment))
            ->where('type', 'guest')
            ->select('id', 'name', 'email', 'phone', 'segment', 'segment_metrics')
            ->get();
    }

    /**
     * Get segment profile (alias for getGuestsBySegment with additional metadata)
     */
    public function getSegmentProfile(string $segment): array
    {
        $guests = $this->getGuestsBySegment($segment);

        return [
            'segment' => strtoupper($segment),
            'count' => $guests->count(),
            'guests' => $guests,
        ];
    }
}
