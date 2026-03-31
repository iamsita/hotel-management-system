# Guest Segmentation - Complete Implementation Guide

## Overview

The Guest Segmentation feature automatically categorizes hotel guests into 7 distinct segments based on their booking history, payment patterns, and spending behavior. This enables targeted marketing, personalized services, and better revenue management.

**Now includes:** Full frontend dashboard with visual analytics, guest detail pages, and segment management interface.

## Quick Start

### 1. Run the Migration

```bash
php artisan migrate
```

This adds three new columns to the `users` table:

- `segment` (ENUM): The guest's current segment
- `segment_metrics` (JSON): Calculated metrics used for segmentation
- `last_segmented_at` (TIMESTAMP): When the guest was last segmented

### 2. Access the Dashboard

Navigate to: `/segmentation` (requires authentication as admin/manager/staff)

### 3. Segment All Guests

Click **"Re-segment All Guests"** button on the dashboard, or run:

```bash
php artisan guests:segment --force
```

This command:

- Analyzes each guest's booking and payment history
- Assigns them to one of 7 segments
- Displays segmentation summary and statistics

## Frontend Routes

### Dashboard & Views

- **GET** `/segmentation` — Main dashboard with segment overview
- **GET** `/segmentation/segment/{segment}` — View guests in a specific segment
- **GET** `/segmentation/guest/{id}` — Detailed guest profile with metrics
- **GET** `/segmentation/segment-form` — Form to trigger re-segmentation

### Example URLs

```
/segmentation                           # Dashboard with analytics
/segmentation/segment/vip               # All VIP guests list
/segmentation/segment/loyal             # All Loyal guests list
/segmentation/guest/5                   # Guest details for user #5
/segmentation/segment-form              # Trigger re-segmentation
```

## The 7 Guest Segments

### 1. **VIP** (Very Important Person)

- **Criteria**: Lifetime value ≥ $50,000 AND ≥ 5 bookings
- **Characteristics**: Your most valuable guests with consistent high spending
- **Strategy**: Premium service, exclusive offers, dedicated support

### 2. **LOYAL** (Repeat Customers)

- **Criteria**: ≥ 10 bookings AND average stay ≥ 3 days
- **Characteristics**: Long-term repeat visitors who stay longer
- **Strategy**: Loyalty programs, exclusive perks, personalized experiences

### 3. **BUSINESS**

- **Criteria**: Average stay ≤ 2 days AND ≥ 4 bookings
- **Characteristics**: Short-stay frequent visitors (business travelers)
- **Strategy**: Fast check-in, business amenities, convenient locations

### 4. **LEISURE**

- **Criteria**: Average stay ≥ 4 days
- **Characteristics**: Vacation/holiday planner guests with longer stays
- **Strategy**: Leisure packages, tour arrangements, activity bookings

### 5. **BUDGET**

- **Criteria**: Below median guest spending
- **Characteristics**: Price-conscious guests
- **Strategy**: Promotions, value packages, seasonal offers

### 6. **RISK** (Potential Issues)

- **Criteria**: Cancellation rate > 30% OR payment reliability < 70%
- **Characteristics**: Unreliable guests with payment/cancellation issues
- **Strategy**: Stricter payment terms, deposit requirements, early confirmation

### 7. **REGULAR** (Default)

- **Criteria**: All other guests
- **Characteristics**: Occasional visitors with average metrics
- **Strategy**: Standard service, promotional opportunities

## API Endpoints

All endpoints are protected by `auth` middleware and require admin/manager/staff roles.

### Segment a Single Guest

**Request:**

```bash
POST /api/segmentation/segment-guest/{userId}
```

**Example:**

```bash
curl -X POST http://localhost/api/segmentation/segment-guest/5 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response:**

```json
{
    "success": true,
    "message": "Guest segmented successfully",
    "data": {
        "user_id": 5,
        "segment": "VIP",
        "metrics": {
            "lifetime_value": 55000.0,
            "total_bookings": 8,
            "avg_stay_duration": 3.5,
            "payment_reliability": 98.0,
            "cancellation_rate": 5.0,
            "avg_booking_value": 6875.0,
            "membership_years": 3
        },
        "last_segmented_at": "2024-01-15T10:30:00Z"
    }
}
```

### Segment All Guests

**Request:**

```bash
POST /api/segmentation/segment-all
```

**Example:**

```bash
curl -X POST http://localhost/api/segmentation/segment-all \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response:**

```json
{
    "success": true,
    "message": "All guests segmented successfully",
    "data": {
        "total": 150,
        "successful": 150,
        "failed": 0,
        "processing_time_seconds": 5.23
    }
}
```

### Get Segmentation Summary

**Request:**

```bash
GET /api/segmentation/summary
```

**Response:**

```json
{
    "success": true,
    "summary": {
        "total_guests": 150,
        "by_segment": {
            "vip": 8,
            "loyal": 15,
            "business": 22,
            "leisure": 18,
            "budget": 45,
            "risk": 12,
            "regular": 30
        },
        "average_lifetime_value": 5250.0,
        "highest_segment": "BUDGET",
        "lowest_segment": "VIP"
    }
}
```

### Get Guests by Segment

**Request:**

```bash
GET /api/segmentation/segment/{segment}
```

**Example:**

```bash
curl http://localhost/api/segmentation/segment/vip \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response:**

```json
{
    "success": true,
    "segment": "VIP",
    "guests": [
        {
            "id": 5,
            "name": "John Doe",
            "email": "john@example.com",
            "lifetime_value": 55000.0,
            "total_bookings": 8,
            "last_segmented_at": "2024-01-15T10:30:00Z"
        }
    ],
    "total": 8
}
```

### Get Guest Insights

**Request:**

```bash
GET /api/segmentation/insights/{userId}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "user_id": 5,
        "name": "John Doe",
        "segment": "VIP",
        "metrics": {
            "lifetime_value": 55000.0,
            "total_bookings": 8,
            "avg_stay_duration": 3.5,
            "payment_reliability": 98.0,
            "cancellation_rate": 5.0,
            "avg_booking_value": 6875.0,
            "membership_years": 3
        },
        "recommendations": [
            "VIP guests with high spending - offer exclusive packages",
            "Consider dedicated concierge service",
            "Invite to premium loyalty program"
        ]
    }
}
```

## Using the Service Directly in Code

You can also use the segmentation service directly in your code:

```php
<?php

use App\Services\GuestSegmentationEngine;
use App\Models\User;

class YourController
{
    public function segment(GuestSegmentationEngine $engine)
    {
        // Segment a single guest
        $user = User::find(5);
        $result = $engine->segmentGuest($user);

        echo "Guest {$user->name} is now: " . $user->segment;

        // Get all guests in a segment
        $vipGuests = $engine->getGuestsBySegment('vip');

        // Get segmentation summary
        $summary = $engine->getSegmentationSummary();
        echo "Total VIP guests: " . $summary['by_segment']['vip'];
    }
}
```

## Metrics Calculated

The algorithm calculates 7 metrics for each guest:

| Metric                    | Description                           | Source              |
| ------------------------- | ------------------------------------- | ------------------- |
| **Lifetime Value**        | Total amount paid across all bookings | Payment records     |
| **Total Bookings**        | Number of reservations made           | Reservation records |
| **Average Stay Duration** | Average nights per booking            | Reservation data    |
| **Payment Reliability**   | Percentage of on-time payments        | Payment records     |
| **Cancellation Rate**     | Percentage of cancelled bookings      | Reservation status  |
| **Average Booking Value** | Average amount spent per booking      | Payment records     |
| **Membership Years**      | Years since first booking             | User creation date  |

## Implementation Details

### File Structure

```
app/
├── Services/
│   └── GuestSegmentationEngine.php       (Core algorithm)
├── Http/Controllers/
│   └── GuestSegmentationController.php   (API endpoints)
└── Console/Commands/
    └── SegmentAllGuests.php              (Artisan command)

database/migrations/
└── 2026_03_13_add_segmentation_to_users.php  (Schema changes)

routes/
└── web.php                                (API routes)
```

### Architecture

**GuestSegmentationEngine** - Core service that handles:

- Guest segmentation logic
- Metrics calculation
- Segment determination
- Database updates

**GuestSegmentationController** - REST API layer that:

- Validates requests
- Calls the service
- Returns JSON responses
- Handles errors gracefully

**SegmentAllGuests** - Command that:

- Provides CLI interface
- Shows progress information
- Displays summary statistics

## Performance Considerations

- **Single Guest**: ~100ms (includes database queries)
- **All Guests (150)**: ~5-10 seconds (batched processing)
- **Database Impact**: Minimal (only updates users table)
- **Storage**: ~500 bytes per guest (JSON metrics + enum)

## Scheduled Execution

To automatically re-segment guests daily, add to your scheduler:

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('guests:segment --force')
        ->daily()
        ->at('02:00');  // 2 AM daily
}
```

## Customization

### Adjust Segment Thresholds

Edit `app/Services/GuestSegmentationEngine.php`:

```php
private function determineSegment(array $metrics): string
{
    // Modify conditions to adjust segment thresholds
    if ($metrics['lifetime_value'] >= 50000 && $metrics['total_bookings'] >= 5) {
        return 'VIP';  // Change 50000 or 5 as needed
    }
    // ...
}
```

### Add New Metrics

Extend `calculateGuestMetrics()` method to add custom metrics:

```php
private function calculateGuestMetrics(User $guest): array
{
    $metrics = [
        // ... existing metrics
        'custom_metric' => $this->calculateCustomMetric($guest),
    ];
    return $metrics;
}
```

## Frontend Features

The Guest Segmentation module includes a complete frontend dashboard for managing segments.

### Dashboard (`/segmentation`)

- Segment distribution overview with color-coded cards
- Visual bar chart showing percentage breakdown
- Total guests and average lifetime value metrics
- Quick action buttons to re-segment or manage guests
- Quick links to view each segment

### Segment Views (`/segmentation/segment/{segment}`)

Shows all guests in a specific segment:

- Segment criteria and recommended strategy
- Sortable table of guests with key metrics
    - Name, email, lifetime value
    - Booking count, average stay duration
    - Links to individual guest profiles
- Pagination support for large segments

### Guest Details (`/segmentation/guest/{id}`)

Comprehensive guest profile including:

- Current segment designation
- Segment info card with update timestamp
- 7 calculated metrics displayed clearly
- Payment reliability percentage with color coding
- Recent reservations (last 5 with dates and amounts)
- Recent payments (last 5 with method and status)
- Personalized recommendations based on segment

### Re-segmentation Form (`/segmentation/segment-form`)

Interactive form to trigger re-segmentation:

- Confirmation checkbox to prevent accidental runs
- Real-time progress bar during processing
- Results display with success/failure counts
- Error handling with user-friendly messages
- Navigation back to dashboard

### Views Structure

```
resources/views/segmentation/
├── dashboard.blade.php          # Main dashboard with analytics
├── segment.blade.php            # List guests by segment
├── guest-detail.blade.php       # Individual guest profile
└── segment-form.blade.php       # Re-segmentation trigger
```

## Troubleshooting

### Migration fails

- Ensure database connection is active
- Check if columns don't already exist on `users` table

### Segmentation runs slowly

- Check database query performance
- Ensure indexes on `reservations` and `payments` tables
- Consider running during off-peak hours

### No guests get segmented

- Verify migration has run: `php artisan migrate:status`
- Check that users have reservation/payment history
- Review error logs in `storage/logs/`

### Frontend views not loading

- Verify auth middleware is applied
- Check user role is admin/manager/staff
- Ensure layout file exists: `resources/views/layouts/app.blade.php`
- Review routes: `php artisan route:list | grep segmentation`

## Support

For issues or questions, check:

1. The algorithm documentation in `ALGORITHM_IMPLEMENTATION_GUIDE.md`
2. Controller error responses (JSON)
3. Application logs in `storage/logs/laravel.log`
