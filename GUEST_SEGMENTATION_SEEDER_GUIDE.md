# Guest Segmentation Test Data Seeder

## Overview

A comprehensive seeder class that creates realistic test data for the Guest Segmentation Algorithm. It generates multiple guests in each of the 7 segments with appropriate booking and payment histories.

## What Gets Created

### Test Data Structure

- **Rooms**: 10 different room types with varied pricing
- **Guest Types**: 28 total guests (4 per segment category)
    - 3 VIP guests (high spending, multiple bookings)
    - 3 Loyal guests (frequent, longer stays)
    - 5 Business guests (short stays, frequent)
    - 4 Leisure guests (longer stays)
    - 8 Budget guests (price-conscious)
    - 3 Risk guests (high cancellation/payment issues)
    - 10 Regular guests (average behavior)

### Per-Guest Data

- **Reservations**: 2-12 past bookings per guest
- **Payments**: Corresponding payments for each reservation
- **Dates**: Realistic dates spread over past 6-24 months
- **Amounts**: Varied pricing based on room type and stay length

## How to Run

### Option 1: Run All Seeders (Recommended for fresh database)

```bash
php artisan db:seed
```

This will run all seeders including GuestSegmentationSeeder.

### Option 2: Run Only Guest Segmentation Seeder

```bash
php artisan db:seed --class=GuestSegmentationSeeder
```

This creates only the test data for segmentation without affecting other data.

### Option 3: Seed with Fresh Database

```bash
php artisan migrate:fresh --seed
```

This will drop all tables, run migrations, and seed all data including test guests.

## Expected Outcome

After running the seeder, you'll have:

1. **VIP Guests** - Ready to be segmented as VIP
    - 8 completed bookings each
    - High lifetime value ($3,200-$6,400 per guest)
    - Hotel amenities bookings

2. **Loyal Guests** - Ready to be segmented as LOYAL
    - 12 completed bookings each
    - 3-5 night average stays
    - Consistent repeat customer pattern

3. **Business Guests** - Ready to be segmented as BUSINESS
    - 6 completed bookings each
    - 1-2 night average stays
    - High payment reliability
    - Business trip special requests

4. **Leisure Guests** - Ready to be segmented as LEISURE
    - 4 completed bookings each
    - 4-7 night stays
    - Family/group bookings
    - Vacation special requests

5. **Budget Guests** - Ready to be segmented as BUDGET
    - 3 completed bookings each
    - Low-price room selections
    - Below median spending

6. **Risk Guests** - Ready to be segmented as RISK
    - 5 bookings with mixed statuses
    - 30%+ cancellation rate
    - Payment failures (30-50% of bookings)

7. **Regular Guests** - Ready to be segmented as REGULAR
    - 2 completed bookings each
    - Average booking patterns
    - Standard payment methods

## Testing the Algorithm

Once seeded, test the segmentation:

### Via Web Dashboard

```bash
# Start the server
php artisan serve

# Navigate to http://localhost:8000/segmentation
```

You should see:

- Dashboard with segment distribution
- Click on segments to view guests
- Click on guests to see detailed profiles

### Via Artisan Command

```bash
php artisan guests:segment --force
```

This will:

- Analyze all 28 test guests
- Classify them into segments
- Show summary statistics
- Display results in command output

### Via API

```bash
# Get segmentation summary
curl http://localhost:8000/segmentation/api/summary

# Get VIP guests
curl http://localhost:8000/segmentation/api/segment/vip

# Get guest insights
curl http://localhost:8000/segmentation/api/insights/1
```

## Factory Methods

### User Factory

```php
// Guest user
User::factory()->guest()->create();

// Staff user
User::factory()->staff()->create();

// Manager user
User::factory()->manager()->create();

// Admin user
User::factory()->admin()->create();
```

### Room Factory

```php
// Create random room
Room::factory()->create();

// Create 10 rooms
Room::factory(10)->create();
```

### Reservation Factory

```php
// Create with random guest and room
Reservation::factory()->create();

// Create 5 reservations
Reservation::factory(5)->create();
```

### Payment Factory

```php
// Create payment for a reservation
Payment::factory()->create();

// Create 10 payments
Payment::factory(10)->create();
```

## Files Created/Modified

### New Files

- `database/factories/RoomFactory.php` - Room data factory
- `database/factories/ReservationFactory.php` - Reservation data factory
- `database/factories/PaymentFactory.php` - Payment data factory
- `database/seeders/GuestSegmentationSeeder.php` - Main seeder class

### Modified Files

- `database/factories/UserFactory.php` - Added guest/staff/manager/admin methods
- `database/seeders/DatabaseSeeder.php` - Added GuestSegmentationSeeder to call stack

## Validation

After seeding, verify the data:

```bash
# Check guest count
php artisan tinker
>>> User::where('type', 'guest')->count()
28  # Should show 28 guests

# Check reservations
>>> Reservation::count()
# Should show 100+ reservations

# Check payments
>>> Payment::count()
# Should match reservation count

# Check segment distribution before algorithm
>>> User::where('type', 'guest')
      ->groupBy('segment')
      ->selectRaw('segment, count(*) as count')
      ->get()
```

## Re-run Seeder

To clear and re-seed with fresh data:

```bash
# Option 1: Fresh database
php artisan migrate:fresh --seed

# Option 2: Clear existing guests and re-seed
php artisan db:seed --class=GuestSegmentationSeeder
```

## Notes

- Seeder creates realistic date patterns (past 6-24 months)
- Payment statuses are mixed (completed, failed, pending)
- Reservation statuses reflect segment types
- Room pricing varies by type
- Guest data is idempotent (safe to run multiple times)
- Uses factories for realistic data generation
