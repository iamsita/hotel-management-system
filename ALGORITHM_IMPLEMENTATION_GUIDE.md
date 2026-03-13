# Algorithm Implementation Quick Reference

## 1. Dynamic Pricing Algorithm

### File Structure

```
app/
  Services/
    PricingEngine.php           # Main pricing logic
    PriceCalculator.php         # Mathematical calculations
  Models/
    PricingHistory.php          # Track price changes
  Http/Controllers/
    PricingController.php       # API endpoints
database/
  migrations/
    create_pricing_history_table.php
    add_pricing_to_rooms_table.php
```

### Core Logic Pseudocode

```php
class PricingEngine {
    public function calculateDynamicPrice(Room $room, Carbon $date): float {
        $basePrice = $room->price_per_night;

        // Factor 1: Occupancy Rate (0 to 1.5x)
        $occupancyRate = $this->calculateOccupancyRate($date);
        $occupancyFactor = $this->getOccupancyMultiplier($occupancyRate);

        // Factor 2: Seasonal Adjustment (0.8 to 1.3x)
        $seasonalFactor = $this->getSeasonalMultiplier($date);

        // Factor 3: Demand Forecast (0.9 to 1.4x)
        $demandFactor = $this->calculateDemandFactor($date, $room->room_type);

        // Factor 4: Lead Time (booking advance days)
        $leadTimeFactor = $this->getLeadTimeMultiplier($daysUntilBooking);

        // Factor 5: Day of Week (weekday vs weekend)
        $dayOfWeekFactor = $this->getDayOfWeekMultiplier($date->dayOfWeek);

        // Combine factors (can use multiplication or weighted sum)
        $finalPrice = $basePrice
            * $occupancyFactor
            * $seasonalFactor
            * $demandFactor
            * $leadTimeFactor
            * $dayOfWeekFactor;

        // Apply min/max price bounds
        return $this->applyPriceBounds($finalPrice, $room);
    }
}
```

### Database Change Needed

```sql
ALTER TABLE rooms ADD COLUMN base_price_per_night DECIMAL(10,2);
ALTER TABLE rooms ADD COLUMN min_price DECIMAL(10,2);
ALTER TABLE rooms ADD COLUMN max_price DECIMAL(10,2);

CREATE TABLE pricing_history (
    id BIGINT PRIMARY KEY,
    room_id BIGINT,
    date DATE,
    calculated_price DECIMAL(10,2),
    occupancy_factor FLOAT,
    seasonal_factor FLOAT,
    demand_factor FLOAT,
    created_at TIMESTAMP
);
```

---

## 2. Smart Room Assignment Algorithm

### File Structure

```
app/
  Services/
    RoomMatchingEngine.php      # Main matching logic
    RoomScoringService.php      # Calculate match scores
  Models/
    RoomFeature.php             # Room additional features
    GuestPreference.php         # Stored guest preferences
database/
  migrations/
    create_room_features_table.php
    create_guest_preferences_table.php
```

### Core Logic Pseudocode

```php
class RoomMatchingEngine {
    public function findBestRooms(
        Reservation $reservation,
        ?array $guestPreferences = null
    ): Collection {

        // Get available rooms
        $availableRooms = $this->getAvailableRooms(
            $reservation->check_in_date,
            $reservation->check_out_date,
            $reservation->number_of_guests
        );

        // Score each room
        $scoredRooms = $availableRooms->map(function (Room $room)
            use ($reservation, $guestPreferences) {

            $score = 0;

            // 1. Preference Match (40% weight)
            $preferenceScore = $this->calculatePreferenceMatch(
                $room,
                $guestPreferences
            ); // 0-100
            $score += $preferenceScore * 0.4;

            // 2. Guest History Compatibility (20% weight)
            $historyScore = $this->calculateHistoryMatch(
                $room,
                $reservation->user
            ); // 0-100
            $score += $historyScore * 0.2;

            // 3. Availability Quality (30% weight)
            // Earlier checkout = available longer = better
            $availabilityScore = $this->calculateAvailabilityScore($room); // 0-100
            $score += $availabilityScore * 0.3;

            // 4. Proximity to Amenities (10% weight)
            $proximityScore = $this->calculateProximityScore($room); // 0-100
            $score += $proximityScore * 0.1;

            return [
                'room' => $room,
                'score' => $score,
                'breakdown' => [
                    'preference' => $preferenceScore,
                    'history' => $historyScore,
                    'availability' => $availabilityScore,
                    'proximity' => $proximityScore,
                ]
            ];
        });

        // Sort by score and return top N
        return $scoredRooms->sortByDesc('score')->take(5);
    }
}
```

---

## 3. Guest Segmentation Algorithm

### File Structure

```
app/
  Services/
    GuestSegmentationEngine.php # Main segmentation logic
    SegmentationAnalyzer.php    # Data analysis
  Models/
    GuestSegment.php            # Segment model
    GuestSegmentHistory.php     # Audit trail
database/
  migrations/
    create_guest_segments_table.php
    add_segment_id_to_users_table.php
```

### Segmentation Logic Pseudocode

```php
class GuestSegmentationEngine {
    // Segment IDs: VIP, LOYAL, BUDGET, BUSINESS, LEISURE, RISK

    public function segmentGuest(User $guest): string {
        $metrics = $this->calculateGuestMetrics($guest);

        // Risk guests (must check first)
        if ($this->isRiskGuest($metrics)) {
            return 'RISK'; // High cancellation rate, defaults, etc
        }

        // VIP guests (highest value)
        if ($metrics['lifetime_value'] > 50000 && $metrics['booking_count'] >= 5) {
            return 'VIP';
        }

        // Loyal guests (repeat customers)
        if ($metrics['booking_count'] >= 10 && $metrics['avg_stay_days'] >= 3) {
            return 'LOYAL';
        }

        // Business travelers
        if ($metrics['avg_stay_days'] < 2 && $metrics['booking_count'] >= 4) {
            return 'BUSINESS';
        }

        // Leisure (longer stays)
        if ($metrics['avg_stay_days'] >= 4) {
            return 'LEISURE';
        }

        // Budget guests (lower spending)
        if ($metrics['avg_room_price'] < $this->medianPrice && $metrics['services_count'] < 2) {
            return 'BUDGET';
        }

        return 'REGULAR'; // Default segment
    }

    private function calculateGuestMetrics(User $guest): array {
        $reservations = $guest->reservations()
            ->where('status', '!=', 'cancelled')
            ->get();

        $payments = Payment::where('reservation_id', $guest->id)->get();

        return [
            'lifetime_value' => $payments->sum('amount'),
            'booking_count' => $reservations->count(),
            'total_nights' => $reservations->sum(function($r) {
                return $r->check_out_date->diffInDays($r->check_in_date);
            }),
            'avg_stay_days' => $reservations->avg(function($r) {
                return $r->check_out_date->diffInDays($r->check_in_date);
            }),
            'avg_room_price' => $reservations->avg('total_amount'),
            'services_count' => $guest->serviceOrders()->count(),
            'cancellation_rate' => $this->calculateCancellationRate($guest),
            'payment_reliability' => $this->assessPaymentReliability($guest),
        ];
    }
}
```

---

## 4. Automatic Discount Engine

### File Structure

```
app/
  Services/
    DiscountCalculator.php      # Main calculation
    DiscountRuleEvaluator.php   # Rule checking
  Models/
    DiscountRule.php            # Discount configurations
    AppliedDiscount.php         # Audit trail
database/
  migrations/
    create_discount_rules_table.php
    create_applied_discounts_table.php
```

### Discount Rules Example

```php
class DiscountCalculator {

    public function calculateTotalDiscount(Reservation $reservation): array {
        $rules = DiscountRule::where('active', true)->get();
        $applicableDiscounts = [];
        $maxCombinableDiscount = 0;

        foreach ($rules as $rule) {
            if ($this->isEligible($reservation, $rule)) {
                $discountAmount = $this->calculateRuleDiscount($reservation, $rule);

                $applicableDiscounts[] = [
                    'rule_id' => $rule->id,
                    'name' => $rule->name,
                    'amount' => $discountAmount,
                    'percentage' => $rule->discount_type === 'percentage'
                        ? $rule->discount_value
                        : ($discountAmount / $reservation->total_amount * 100),
                    'combinable' => $rule->combinable,
                ];

                if ($rule->combinable) {
                    $maxCombinableDiscount += $discountAmount;
                }
            }
        }

        // Rules for combination
        $totalDiscount = min($maxCombinableDiscount, $reservation->total_amount * 0.3); // Max 30%

        return [
            'total_discount' => $totalDiscount,
            'rules_applied' => $applicableDiscounts,
            'final_amount' => $reservation->total_amount - $totalDiscount,
        ];
    }

    private function isEligible(Reservation $reservation, DiscountRule $rule): bool {
        switch ($rule->type) {
            case 'LOYALTY':
                return $reservation->user->reservations()->count() >= $rule->min_bookings;

            case 'GROUP_BOOKING':
                // Check if part of group reservation
                return $this->isGroupReservation($reservation, $rule->min_rooms);

            case 'LONG_STAY':
                $nights = $reservation->check_out_date
                    ->diffInDays($reservation->check_in_date);
                return $nights >= $rule->min_nights;

            case 'EARLY_BIRD':
                $daysBeforeCheckIn = now()
                    ->diffInDays($reservation->check_in_date);
                return $daysBeforeCheckIn >= $rule->days_advance;

            case 'OFF_SEASON':
                return $this->isOffSeason($reservation->check_in_date);

            default:
                return false;
        }
    }
}
```

---

## 5. Payment Risk Assessment

### File Structure

```
app/
  Services/
    PaymentRiskCalculator.php   # Risk scoring
    FraudDetectionEngine.php    # Fraud detection
  Models/
    RiskAssessment.php          # Risk records
    FraudLog.php                # Fraud incidents
database/
  migrations/
    create_risk_assessments_table.php
    create_fraud_logs_table.php
```

### Risk Scoring Logic

```php
class PaymentRiskCalculator {

    const LOW_RISK = 0.3;
    const MEDIUM_RISK = 0.6;
    const HIGH_RISK = 0.8;

    public function calculatePaymentRisk(Payment $payment): array {
        $riskScore = 0.0;
        $riskFactors = [];

        // Factor 1: Guest Payment History (30% weight)
        $historyRisk = $this->assessPaymentHistory($payment->reservation->user);
        $riskScore += $historyRisk * 0.3;
        $riskFactors['history'] = $historyRisk;

        // Factor 2: Booking Pattern Anomaly (25% weight)
        $patternRisk = $this->assessBookingPatternAnomaly($payment);
        $riskScore += $patternRisk * 0.25;
        $riskFactors['pattern'] = $patternRisk;

        // Factor 3: Amount Anomaly (20% weight)
        $amountRisk = $this->assessAmountAnomaly($payment);
        $riskScore += $amountRisk * 0.2;
        $riskFactors['amount'] = $amountRisk;

        // Factor 4: Geographic Risk (15% weight)
        $geoRisk = $this->assessGeographicRisk($payment);
        $riskScore += $geoRisk * 0.15;
        $riskFactors['geographic'] = $geoRisk;

        // Factor 5: Device/IP Risk (10% weight)
        $deviceRisk = $this->assessDeviceRisk($payment);
        $riskScore += $deviceRisk * 0.1;
        $riskFactors['device'] = $deviceRisk;

        return [
            'risk_score' => $riskScore, // 0.0 to 1.0
            'risk_level' => $this->getRiskLevel($riskScore),
            'factors' => $riskFactors,
            'recommendation' => $this->getRecommendation($riskScore),
            'requires_review' => $riskScore > self::MEDIUM_RISK,
        ];
    }

    private function assessPaymentHistory(User $user): float {
        $payments = Payment::where('user_id', $user->id)->get();

        if ($payments->isEmpty()) {
            return 0.7; // New user = medium-high risk
        }

        $failedCount = $payments->where('status', 'failed')->count();
        $refundedCount = $payments->where('status', 'refunded')->count();

        $failureRate = ($failedCount + $refundedCount) / $payments->count();

        return min($failureRate * 1.5, 1.0);
    }
}
```

---

## Start Implementation Now!

Choose one algorithm and start with this structure:

1. **Create Service Class**

    ```bash
    php artisan make:service YourAlgorithmService
    ```

2. **Create Model & Migration** (if needed)

    ```bash
    php artisan make:model YourModel -m
    ```

3. **Create Controller**

    ```bash
    php artisan make:controller YourAlgorithmController
    ```

4. **Write Tests**

    ```bash
    php artisan make:test YourAlgorithmTest
    ```

5. **Add Routes** in `routes/web.php`

6. **Add to Middleware** if staff-only feature

---

## Database Query Optimization Tips

For these algorithms, ensure proper indexing:

```sql
-- Frequently queried in algorithms
CREATE INDEX idx_reservations_dates ON reservations(check_in_date, check_out_date);
CREATE INDEX idx_reservations_user ON reservations(user_id);
CREATE INDEX idx_payments_user ON payments(user_id, status);
CREATE INDEX idx_charges_reservation ON charges(reservation_id, status);
CREATE INDEX idx_rooms_status ON rooms(status);
CREATE INDEX idx_rooms_type ON rooms(room_type);
```

---

## Performance Optimization

These algorithms will require Redis caching:

```php
// Cache expensive calculations
Cache::remember('pricing_' . $room->id . '_' . $date,
    3600, // 1 hour
    function() use ($room, $date) {
        return $this->calculateDynamicPrice($room, $date);
    }
);
```

Use Laravel Jobs for heavy computations:

```php
// Queue heavy calculation
dispatch(new CalculateDynamicPrices())->onQueue('pricing');
```
