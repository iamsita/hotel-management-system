# Hotel Management System - Initial Seeder Command

## Overview

The `php artisan initial:seed` command seeded all necessary data for the Hotel Management System with diverse guest behaviors to test the guest segmentation algorithm.

## What Gets Seeded

### 1. **Admin User**

- Email: `admin@hms.com`
- Password: `password123`
- Role: Admin

### 2. **Guest Users (11 total)**

Designed to create different guest segments for algorithm testing:

#### VIP Guests (2 users)

- 6 completed stays each
- High spending: ₹15,000 - ₹32,000 total
- Restaurant orders: 2-4 per stay
- 0% cancellation rate
- Multiple payment methods

#### Loyal Guests (3 users)

- 4 completed stays each
- Moderate spending: ₹4,000 - ₹6,000 total
- Restaurant orders: 1-3 per stay
- 0% cancellation rate (reliable)
- Mix of cash and card payments

#### At Risk Guests (2 users)

- 3 completed stays each (in the past 10+ months)
- Low spending: ₹2,500 - ₹4,500 total
- Last visit: 90+ days ago
- Restaurant orders: 1-2 per stay
- Mostly cash payments

#### High Value New Guests (2 users)

- 1 completed stay (recent - 15 days ago)
- High spending: ₹16,500 - ₹23,000
- High-capacity rooms (suite/deluxe)
- 5 restaurant orders in single stay
- Card payments

#### Unreliable Guests (2 users)

- 4 reservations each (50% cancelled)
- Low spending: ₹2,000 - ₹3,500 total
- High cancellation rate: 50%
- Only food orders for completed stays
- Cash payments

#### Regular Guests (2 users)

- 2 completed stays each
- Moderate spending: ₹2,500 - ₹3,500 total
- No particular pattern
- Restaurant orders: 1-2 per stay
- Cash payments

### 3. **Rooms (8 total)**

- Single rooms (2): ₹2,000/night
- Double rooms (3): ₹3,500/night
- Suite rooms (2): ₹5,500/night
- Deluxe rooms (1): ₹8,000/night

### 4. **Foods (10 items)**

- Breakfast: Aloo Paratha (₹250)
- Lunch: Paneer Tikka (₹350), Butter Chicken (₹450)
- Dinner: Tandoori Chicken (₹500), Fish Curry (₹550)
- Snacks: Samosa (₹50), Momos (₹100)
- Beverages: Tea (₹50), Coffee (₹80), Orange Juice (₹120)

### 5. **Reservations (30+ total)**

- Status: Mix of checked_out and cancelled
- Dates: Spread across the last 10+ months
- Guests: 1-4 per reservation
- Total amount: Varies by room type and duration

### 6. **Food Orders (70+ total)**

- Quantity: 1-3 items per order
- Status: Mostly delivered
- Linked to reservations

### 7. **Payments (28+ total)**

- Only for completed (non-cancelled) reservations
- Methods: Cash, Card, UPI
- Status: All completed
- Amounts: Include room charges + food charges

## How to Run

### Fresh Database Setup

```bash
# Drop all tables and recreate them
php artisan migrate:fresh

# Seed initial data
php artisan initial:seed
```

### Run Segmentation Algorithm

After seeding, run the guest segmentation algorithm:

```bash
php artisan guests:segment
```

### Expected Output

When you run `php artisan initial:seed`, you should see:

```
Starting Hotel Management System initial seeding...
Seeding users...
  ✓ 11 guests seeded
Seeding rooms...
  ✓ 8 rooms seeded
Seeding foods...
  ✓ 10 foods seeded
Seeding reservations and related data...
  ✓ 30 reservations seeded
  ✓ 70 food orders seeded
  ✓ 28 payments seeded
✓ Seeding completed successfully!
You can now run: php artisan guests:segment
```

## Key Features

### Idempotent Seeding

The command uses `updateOrCreate()` instead of `create()`, making it safe to run multiple times without creating duplicates.

### Realistic Data Distribution

- Guest data is spread across 10+ months
- Different payment methods for different segment types
- Room reservations are random to create realistic scenarios
- Food orders vary in quantity and type

### Algorithm Data

The seeded data is specifically designed to create clear patterns for the segmentation algorithm:

- **VIP**: High spend + frequent visits
- **Loyal**: Multiple visits with no cancellations
- **At Risk**: Visited before but inactive (90+ days)
- **High Value New**: High first-time spend
- **Unreliable**: 50% cancellation rate
- **Regular**: Other patterns

### Statistics Generation

After` segmentation runs, you'll see a table like:

```
| Segment        | Guests |
|---|---|
| vip            | 2      |
| loyal          | 3      |
| at_risk        | 2      |
| high_value_new | 2      |
| unreliable     | 2      |
| regular        | 2      |
```

## Database Impact

### Tables Modified

- users (12 rows: 1 admin + 11 guests)
- rooms (8 rows)
- foods (10 rows)
- reservations (30 rows)
- food_orders (70 rows)
- payments (28 rows)

### No Data Loss

Uses `updateOrCreate()` so existing data is preserved. Safe for development and testing.

## Troubleshooting

### Command Not Found

If `php artisan initial:seed` is not recognized:

1. Run `php artisan list` to see all commands
2. Make sure the file exists at: `app/Console/Commands/InitialSeedCommand.php`
3. Run `php artisan cache:clear`

### Seeding Errors

If you get foreign key constraint errors:

1. Run `php artisan migrate:fresh` first
2. Then run `php artisan initial:seed`

### Models Not Found

If you get "Class not found" errors:

1. Verify all models exist in `app/Models/`
2. Run `composer dump-autoload`
3. Try again

## Next Steps

1. Run the segmentation algorithm: `php artisan guests:segment`
2. Check the admin dashboard to see guest distributions
3. Verify segment badges show correct categories
4. Test the algorithm logic with the seeded data
5. Adjust thresholds in the algorithm if needed

---

Created for: Hotel Management System - BCA Project
Date: April 6, 2026
