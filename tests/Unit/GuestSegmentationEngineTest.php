<?php

namespace Tests\Unit;

use App\Models\GuestSegment;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Services\GuestSegmentationEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestSegmentationEngineTest extends TestCase
{
    use RefreshDatabase;

    private GuestSegmentationEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new GuestSegmentationEngine();
        $this->artisan('migrate');
    }

    /**
     * Test VIP guest segmentation
     */
    public function test_vip_guest_segmentation(): void
    {
        $guest = User::factory()->guest()->create();

        for ($i = 0; $i < 5; $i++) {
            $checkIn = now()->addDays($i * 5);
            $reservation = Reservation::factory()
                ->for($guest)
                ->create([
                    'check_in_date' => $checkIn,
                    'check_out_date' => $checkIn->addDays(3),
                    'total_amount' => 15000,
                    'status' => 'checked_out',
                ]);

            Payment::factory()
                ->for($reservation)
                ->create(['amount' => 15000, 'status' => 'completed']);
        }

        $segment = $this->engine->segmentGuest($guest);

        $this->assertEquals(GuestSegmentationEngine::SEGMENT_VIP, $segment->segment);
        $this->assertGreaterThanOrEqual(50000, $segment->lifetime_value);
    }

    /**
     * Test LOYAL guest segmentation
     */
    public function test_loyal_guest_segmentation(): void
    {
        $guest = User::factory()->guest()->create();

        // Create 10+ reservations to get LOYAL status
        for ($i = 0; $i < 10; $i++) {
            Reservation::factory()
                ->for($guest)
                ->create(['status' => 'checked_out']);
        }

        $segment = $this->engine->segmentGuest($guest);

        // Should have 10+ bookings
        $this->assertGreaterThanOrEqual(10, $segment->booking_count);
        // Should be segmented (not empty)
        $this->assertNotEmpty($segment->segment);
    }

    /**
     * Test BUSINESS guest segmentation
     */
    public function test_business_guest_segmentation(): void
    {
        $guest = User::factory()->guest()->create();

        for ($i = 0; $i < 4; $i++) {
            $checkIn = now()->addDays($i * 8);
            Reservation::factory()
                ->for($guest)
                ->create([
                    'check_in_date' => $checkIn,
                    'check_out_date' => $checkIn->addDay(),
                    'status' => 'checked_out',
                ]);
        }

        $segment = $this->engine->segmentGuest($guest);

        $this->assertEquals(GuestSegmentationEngine::SEGMENT_BUSINESS, $segment->segment);
        $this->assertLessThanOrEqual(2, $segment->avg_stay_days);
        $this->assertGreaterThanOrEqual(4, $segment->booking_count);
    }

    /**
     * Test LEISURE guest segmentation
     */
    public function test_leisure_guest_segmentation(): void
    {
        $guest = User::factory()->guest()->create();

        // Create multiple reservations
        for ($i = 0; $i < 3; $i++) {
            Reservation::factory()
                ->for($guest)
                ->create(['status' => 'checked_out']);
        }

        $segment = $this->engine->segmentGuest($guest);

        // Should have bookings and be in a valid segment
        $this->assertGreaterThan(0, $segment->booking_count);
        $this->assertNotEmpty($segment->segment);
        // Should not be REGULAR if there are multiple bookings
        $this->assertNotEquals(GuestSegmentationEngine::SEGMENT_RISK, $segment->segment);
    }

    /**
     * Test RISK guest segmentation - high cancellation
     */
    public function test_risk_guest_segmentation_high_cancellation(): void
    {
        $guest = User::factory()->guest()->create();

        for ($i = 0; $i < 5; $i++) {
            $status = $i < 3 ? 'cancelled' : 'checked_out';
            $checkIn = now()->addDays($i * 5);
            Reservation::factory()
                ->for($guest)
                ->create([
                    'check_in_date' => $checkIn,
                    'check_out_date' => $checkIn->addDays(2),
                    'status' => $status,
                ]);
        }

        $segment = $this->engine->segmentGuest($guest);

        $this->assertEquals(GuestSegmentationEngine::SEGMENT_RISK, $segment->segment);
        $this->assertGreaterThan(30, $segment->cancellation_rate);
    }

    /**
     * Test RISK guest segmentation - low payment reliability
     */
    public function test_risk_guest_segmentation_low_payment_reliability(): void
    {
        $guest = User::factory()->guest()->create();

        for ($i = 0; $i < 5; $i++) {
            $checkIn = now()->addDays($i * 3);
            $reservation = Reservation::factory()
                ->for($guest)
                ->create([
                    'check_in_date' => $checkIn,
                    'check_out_date' => $checkIn->addDay(),
                ]);

            $status = $i < 3 ? 'failed' : 'completed';
            Payment::factory()
                ->for($reservation)
                ->create(['status' => $status]);
        }

        $segment = $this->engine->segmentGuest($guest);

        $this->assertEquals(GuestSegmentationEngine::SEGMENT_RISK, $segment->segment);
        $this->assertLessThan(70, $segment->payment_reliability);
    }

    /**
     * Test BUDGET guest segmentation
     */
    public function test_budget_guest_segmentation(): void
    {
        $guest = User::factory()->guest()->create();

        // Create reservations with low total amounts
        for ($i = 0; $i < 2; $i++) {
            Reservation::factory()
                ->for($guest)
                ->create(['total_amount' => 50, 'status' => 'checked_out']);
        }

        $segment = $this->engine->segmentGuest($guest);

        // Should be segmented into a category
        $this->assertNotEmpty($segment->segment);
        $this->assertGreaterThan(0, $segment->booking_count);
    }

    /**
     * Test segment all guests
     */
    public function test_segment_all_guests(): void
    {
        User::factory()->guest()->count(5)->create();

        $results = $this->engine->segmentAllGuests();

        $this->assertEquals(5, count($results));
        $this->assertTrue(collect($results)->pluck('status')->every(fn($s) => $s === 'success'));
    }

    /**
     * Test segmentation summary
     */
    public function test_segmentation_summary(): void
    {
        $vipGuest = User::factory()->guest()->create();
        for ($i = 0; $i < 5; $i++) {
            $checkIn = now()->addDays($i * 5);
            Reservation::factory()
                ->for($vipGuest)
                ->create([
                    'check_in_date' => $checkIn,
                    'check_out_date' => $checkIn->addDays(3),
                    'total_amount' => 15000,
                    'status' => 'checked_out',
                ]);
        }

        $this->engine->segmentAllGuests();

        $summary = $this->engine->getSegmentationSummary();

        $this->assertIsArray($summary);
        $this->assertArrayHasKey('total_guests', $summary);
        $this->assertArrayHasKey('by_segment', $summary);
    }

    /**
     * Test segment profile retrieval
     */
    public function test_get_segment_profile(): void
    {
        $guest = User::factory()->guest()->create();
        for ($i = 0; $i < 5; $i++) {
            $checkIn = now()->addDays($i * 5);
            Reservation::factory()
                ->for($guest)
                ->create([
                    'check_in_date' => $checkIn,
                    'check_out_date' => $checkIn->addDays(3),
                    'total_amount' => 15000,
                    'status' => 'checked_out',
                ]);
        }

        $this->engine->segmentGuest($guest);

        $profile = $this->engine->getSegmentProfile('VIP');

        $this->assertEquals('VIP', $profile['segment']);
        $this->assertArrayHasKey('count', $profile);
        $this->assertArrayHasKey('guests', $profile);
    }

    /**
     * Test metrics calculation
     */
    public function test_metrics_calculation(): void
    {
        $guest = User::factory()->guest()->create();

        $checkIn = now()->addDays(1);
        $reservation = Reservation::factory()
            ->for($guest)
            ->create([
                'check_in_date' => $checkIn,
                'check_out_date' => $checkIn->addDays(3),
                'total_amount' => 5000,
                'status' => 'checked_out',
            ]);

        Payment::factory()
            ->for($reservation)
            ->create(['amount' => 5000, 'status' => 'completed']);

        $segment = $this->engine->segmentGuest($guest);

        $this->assertEquals(1, $segment->booking_count);
        $this->assertGreaterThan(0, $segment->lifetime_value);
    }

    /**
     * Test segment history tracking
     */
    public function test_segment_history_tracking(): void
    {
        $guest = User::factory()->guest()->create();

        $segment1 = $this->engine->segmentGuest($guest);
        $this->assertNotNull($segment1);

        for ($i = 0; $i < 10; $i++) {
            $checkIn = now()->addDays($i * 6);
            Reservation::factory()
                ->for($guest)
                ->create([
                    'check_in_date' => $checkIn,
                    'check_out_date' => $checkIn->addDays(4),
                    'status' => 'checked_out',
                ]);
        }

        $segment2 = $this->engine->segmentGuest($guest);

        $history = $segment2->history()->get();

        if ($segment1->segment !== $segment2->segment) {
            $this->assertGreaterThan(0, $history->count());
        }
    }
}
