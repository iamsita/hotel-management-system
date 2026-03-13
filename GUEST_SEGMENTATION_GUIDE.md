# Guest Segmentation Algorithm - Complete Implementation Guide

## Overview

The Guest Segmentation Algorithm is a comprehensive system that classifies hotel guests into meaningful segments based on their booking history, payment patterns, and behavioral metrics. This enables targeted marketing, personalized service delivery, and risk management.

---

## Segment Types

### 1. **VIP Guests** 👑

- **Criteria**: Lifetime value ≥ $50,000 AND ≥ 5 bookings
- **Characteristics**: High-value guests with strong loyalty
- **Marketing**: Premium services, exclusive offers, dedicated account manager
- **Benefits**: Priority room selection, complimentary upgrades, reserved rates

### 2. **LOYAL Guests** 🔄

- **Criteria**: ≥ 10 bookings AND average stay ≥ 3 days
- **Characteristics**: Regular repeat customers with reasonable stays
- **Marketing**: Loyalty rewards, anniversary offers, customized packages
- **Benefits**: Loyalty points, special discounts, extended loyalty programs

### 3. **BUSINESS Guests** 💼

- **Criteria**: Average stay ≤ 2 days AND ≥ 4 bookings
- **Characteristics**: Frequent short-stay travelers
- **Marketing**: Extended stay discounts, business packages, WiFi bundles
- **Benefits**: Fast check-in/out, business center access, meeting rooms

### 4. **LEISURE Guests** 🏖️

- **Criteria**: Average stay ≥ 4 days
- **Characteristics**: Vacation/family travelers with longer stays
- **Marketing**: All-inclusive packages, family bundles, activity packages
- **Benefits**: Discounted rates for long stays, family amenities, recreational packages

### 5. **BUDGET Guests** 💰

- **Criteria**: Below median room price AND < 2 services used
- **Characteristics**: Price-sensitive, minimal service consumption
- **Marketing**: Value packages, early-bird discounts, last-minute deals
- **Benefits**: Off-season discounts, package deals, promotional rates

### 6. **RISK Guests** ⚠️

- **Criteria**: Cancellation rate > 30% OR payment reliability < 70%
- **Characteristics**: Unreliable payment or high cancellation patterns
- **Actions**: Require deposits before booking, payment verification needed
- **Caution**: Monitor for fraud, stricter cancellation policies

### 7. **REGULAR Guests** 👤

- **Default**: All guests not matching above criteria
- **Characteristics**: One-time or occasional visitors
- **Marketing**: General promotions, seasonal offers

---

## Database Schema

### `guest_segments` Table

```sql
┌─────────────────────────┬──────────────┬─────────────────┐
│ Column                  │ Type         │ Description     │
├─────────────────────────┼──────────────┼─────────────────┤
│ id                      │ BIGINT       │ Primary Key     │
│ user_id                 │ BIGINT       │ FK to users     │
│ segment                 │ ENUM         │ VIP/LOYAL/...   │
│ metrics                 │ JSON         │ Calculated data │
│ lifetime_value          │ INTEGER      │ Total spent     │
│ booking_count           │ INTEGER      │ # of bookings   │
│ avg_stay_days           │ DECIMAL      │ Avg stay length │
│ avg_room_price          │ DECIMAL      │ Avg room price  │
│ cancellation_rate       │ DECIMAL      │ % cancelled     │
│ services_count          │ INTEGER      │ Services used   │
│ payment_reliability     │ DECIMAL      │ 0-100 score     │
│ total_nights            │ INTEGER      │ Total nights    │
│ last_calculated_at      │ TIMESTAMP    │ Last update     │
│ created_at              │ TIMESTAMP    │ Created at      │
│ updated_at              │ TIMESTAMP    │ Updated at      │
└─────────────────────────┴──────────────┴─────────────────┘
```

### `guest_segment_history` Table

```sql
┌──────────────────────┬──────────────┬──────────────────────┐
│ Column               │ Type         │ Description          │
├──────────────────────┼──────────────┼──────────────────────┤
│ id                   │ BIGINT       │ Primary Key          │
│ guest_segment_id     │ BIGINT       │ FK to guest_segments │
│ previous_segment     │ ENUM         │ Previous segment     │
│ new_segment          │ ENUM         │ New segment          │
│ reason               │ TEXT         │ Why changed          │
│ created_at           │ TIMESTAMP    │ When changed         │
│ updated_at           │ TIMESTAMP    │ Updated at           │
└──────────────────────┴──────────────┴──────────────────────┘
```

---

## Installation & Setup

### Step 1: Run Migration

```bash
php artisan migrate
```

This creates the `guest_segments` and `guest_segment_history` tables with proper indexes.

### Step 2: Service Provider Registration

The service is already available for dependency injection. No additional registration needed.

### Step 3: Verify Installation

```bash
php artisan tinker
> App\Models\GuestSegment::count()
# Should return 0 until guests are segmented
```

---

## API Endpoints

All endpoints are prefixed with `/api/segmentation/` and require admin authentication.

### 1. Segment a Single Guest

**POST** `/api/segmentation/segment-guest/{user}`

```bash
curl -X POST http://localhost:8000/api/segmentation/segment-guest/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response**:

```json
{
    "status": "success",
    "data": {
        "user_id": 1,
        "segment": "VIP",
        "segment_description": "High-value guest with excellent lifetime value and loyalty",
        "metrics": {
            "lifetime_value": 75000,
            "booking_count": 8,
            "avg_stay_days": 3.5,
            "avg_room_price": 9375,
            "cancellation_rate": "0%",
            "payment_reliability": "100%",
            "services_used": 12,
            "total_nights": 28
        },
        "last_calculated": "2026-03-13T10:30:00Z"
    }
}
```

### 2. Segment All Guests

**POST** `/api/segmentation/segment-all`

```bash
curl -X POST http://localhost:8000/api/segmentation/segment-all \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response**:

```json
{
    "status": "success",
    "message": "Segmented 45 guests successfully, 2 failed",
    "data": {
        "total_processed": 47,
        "successful": 45,
        "failed": 2,
        "details": {
            "1": { "status": "success", "segment": "VIP" },
            "2": { "status": "success", "segment": "LOYAL" }
        }
    }
}
```

### 3. Get Segmentation Summary

**GET** `/api/segmentation/summary`

```bash
curl http://localhost:8000/api/segmentation/summary \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response**:

```json
{
    "status": "success",
    "data": {
        "total_guests": 47,
        "by_segment": {
            "vip": 8,
            "loyal": 12,
            "business": 10,
            "leisure": 5,
            "budget": 7,
            "risk": 2,
            "regular": 3
        },
        "vip_revenue": 600000,
        "total_revenue": 1200000,
        "avg_lifetime_value": 25531.91
    }
}
```

### 4. Get Segment Profile

**GET** `/api/segmentation/segment/{segment}`

```bash
curl http://localhost:8000/api/segmentation/segment/VIP \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response**:

```json
{
    "status": "success",
    "data": {
        "segment": "VIP",
        "count": 8,
        "total_revenue": 600000,
        "avg_lifetime_value": 75000,
        "avg_stay_days": 3.75,
        "avg_booking_count": 8.5,
        "avg_payment_reliability": 98.5,
        "guests": [
            {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com",
                "lifetime_value": 75000,
                "bookings": 8,
                "avg_stay": 3.5
            }
        ]
    }
}
```

### 5. List Segmented Guests

**GET** `/api/segmentation/guests` or **GET** `/api/segmentation/guests/{segment}`

```bash
curl http://localhost:8000/api/segmentation/guests/VIP?page=1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 6. Get Guest Insights

**GET** `/api/segmentation/insights/{user}`

```bash
curl http://localhost:8000/api/segmentation/insights/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 7. Export Segment Data

**GET** `/api/segmentation/export/{segment}`

```bash
curl http://localhost:8000/api/segmentation/export/VIP \
  -H "Authorization: Bearer YOUR_TOKEN" > vip_guests.json
```

---

## Artisan Command

### Segment All Guests

```bash
# Interactive (with confirmation)
php artisan guests:segment

# Force execution (no confirmation)
php artisan guests:segment --force
```

**Output Example**:

```
Starting guest segmentation...
✓ Guest segmentation completed!

╭───────────────────────────────┬───────╮
│ Metric                        │ Value │
├───────────────────────────────┼───────┤
│ Total Guests Processed        │ 47    │
│ Successfully Segmented        │ 45    │
│ Failed                        │ 2     │
│ Time Taken                    │ 5s    │
│ Average Time per Guest        │ 0.11s │
╰───────────────────────────────┴───────╯

Segmentation Summary:
╭─────────┬───────┬──────────────╮
│ Segment │ Count │ Total Revenue│
├─────────┼───────┼──────────────┤
│ VIP     │ 8     │ $600000      │
│ LOYAL   │ 12    │ $450000      │
│ BUSINESS│ 10    │ $250000      │
│ LEISURE │ 5     │ $180000      │
│ BUDGET  │ 7     │ $120000      │
│ RISK    │ 2     │ $5000        │
│ REGULAR │ 3     │ $15000       │
╰─────────┴───────┴──────────────╯
```

---

## Usage Examples

### Programmatic Usage

```php
<?php

use App\Services\GuestSegmentationEngine;
use App\Models\User;

$engine = app(GuestSegmentationEngine::class);

// Segment a single guest
$guest = User::find(1);
$segment = $engine->segmentGuest($guest);
echo "Guest segment: " . $segment->segment;

// Segment all guests
$results = $engine->segmentAllGuests();
echo "Processed: " . count($results) . " guests";

// Get summary
$summary = $engine->getSegmentationSummary();
echo "VIP guests: " . $summary['by_segment']['vip'];

// Get segment profile
$profile = $engine->getSegmentProfile('VIP');
foreach ($profile['guests'] as $guest) {
    echo $guest['name'] . ": $" . $guest['lifetime_value'] . "\n";
}
```

### Controller Usage

```php
class MyController extends Controller
{
    public function __construct(private GuestSegmentationEngine $engine)
    {}

    public function dashboard()
    {
        $summary = $this->engine->getSegmentationSummary();

        return view('dashboard', [
            'total_guests' => $summary['total_guests'],
            'by_segment' => $summary['by_segment'],
            'total_revenue' => $summary['total_revenue'],
        ]);
    }
}
```

---

## Testing

### Run All Tests

```bash
php artisan test
```

### Run Only Segmentation Tests

```bash
php artisan test --filter GuestSegmentation
```

### Run Unit Tests

```bash
php artisan test tests/Unit/GuestSegmentationEngineTest.php
```

### Run Feature Tests

```bash
php artisan test tests/Feature/GuestSegmentationFeatureTest.php
```

### Test Coverage

```bash
php artisan test --coverage
```

---

## Configuration

Adjust thresholds in `app/Services/GuestSegmentationEngine.php`:

```php
private array $config = [
    'vip_lifetime_value' => 50000,        // Modify for different currency
    'vip_min_bookings' => 5,              // Minimum VIP bookings
    'loyal_min_bookings' => 10,           // Minimum loyal bookings
    'loyal_min_stay' => 3,                // Minimum average stay days
    'business_max_stay' => 2,             // Maximum business stay
    'business_min_bookings' => 4,         // Minimum business bookings
    'leisure_min_stay' => 4,              // Minimum leisure stay
    'risk_cancellation_threshold' => 0.3, // 30% cancellation rate
    'risk_payment_reliability_threshold' => 70, // Below 70%
];
```

---

## Performance Optimization

### Caching

Cache the segmentation summary for better performance:

```php
use Illuminate\Support\Facades\Cache;

$summary = Cache::remember('segmentation_summary', 3600, function () {
    return $engine->getSegmentationSummary();
});
```

### Batch Processing

Use queues for batch segmentation:

```php
// In a controller or command
dispatch(new SegmentAllGuestsJob())->onQueue('segmentation');
```

### Database Indexes

The migration automatically creates indexes on:

- `user_id`
- `segment`
- `lifetime_value`
- `booking_count`

---

## Business Intelligence Integration

### Revenue Analysis by Segment

```php
$segments = GuestSegment::select('segment')
    ->selectRaw('SUM(lifetime_value) as total_revenue')
    ->selectRaw('COUNT(*) as guest_count')
    ->selectRaw('AVG(lifetime_value) as avg_revenue')
    ->groupBy('segment')
    ->get();
```

### Segment Trends

```php
$history = GuestSegmentHistory::where('created_at', '>=', now()->subMonth())
    ->groupBy('new_segment')
    ->selectRaw('new_segment, COUNT(*) as changes')
    ->get();
```

---

## Best Practices

### 1. **Regular Recalculation**

Schedule segmentation to run weekly or monthly:

```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('guests:segment --force')
        ->weekly(Carbon::SUNDAY)
        ->at('02:00');
}
```

### 2. **Monitor Changes**

Review segment changes regularly with the history table to identify churn or upgrades.

### 3. **Use Segments in Marketing**

Integrate with email campaigns, SMS notifications, and personalized offers based on segment:

```php
// Example: VIP-only promotion
$vips = GuestSegment::vip()->with('user')->get();
foreach ($vips as $vip) {
    Mail::to($vip->user->email)->send(new VipExclusiveOffer());
}
```

### 4. **Risk Management**

Monitor RISK segment closely and implement additional verification:

```php
$riskGuests = GuestSegment::risk()->get();
foreach ($riskGuests as $risk) {
    // Require deposit, payment verification, etc.
}
```

### 5. **Data Quality**

Ensure accurate booking and payment data:

- Validate dates during reservation creation
- Verify payments are marked correctly
- Clean up test/demo records regularly

---

## Troubleshooting

### Issue: Guests not segmenting correctly

**Solution**: Check metrics calculation:

```bash
php artisan tinker
> $guest = App\Models\User::find(1);
> $metrics = app(GuestSegmentationEngine::class)->calculateGuestMetrics($guest);
> dd($metrics);
```

### Issue: Slow segmentation

**Solution**: Ensure indexes exist:

```sql
SHOW INDEX FROM reservations;
SHOW INDEX FROM payments;
```

### Issue: High memory usage for large dataset

**Solution**: Use batch processing:

```php
User::where('type', 'guest')
    ->chunk(100, function ($users) {
        foreach ($users as $user) {
            $engine->segmentGuest($user);
        }
    });
```

---

## Future Enhancements

1. **Machine Learning**: Use historical data for predictive segmentation
2. **Real-time Updates**: Segment guests automatically on each booking/payment
3. **Custom Segments**: Allow admin to define custom segment criteria
4. **Segment Lifecycle**: Track guest movement between segments over time
5. **Seasonal Adjustments**: Adjust thresholds based on season
6. **A/B Testing**: Test different segment strategies and measure impact

---

## Files Created

```
app/
├── Services/
│   └── GuestSegmentationEngine.php          # Core algorithm
├── Models/
│   ├── GuestSegment.php                     # Main model
│   └── GuestSegmentHistory.php              # History tracking
├── Http/Controllers/
│   └── GuestSegmentationController.php      # API endpoints
├── Console/Commands/
│   └── SegmentAllGuests.php                 # Artisan command
database/
└── migrations/
    └── 2026_03_13_000001_create_guest_segments_table.php
tests/
├── Unit/
│   └── GuestSegmentationEngineTest.php      # Unit tests
└── Feature/
    └── GuestSegmentationFeatureTest.php     # Feature tests
```

---

## Support & Documentation

For more information:

- Review `PROJECT_ANALYSIS.md` for algorithm theory
- Check `ALGORITHM_IMPLEMENTATION_GUIDE.md` for coding patterns
- See test files for usage examples

---

**Version**: 1.0.0  
**Last Updated**: March 13, 2026  
**Status**: Production Ready ✅
