# Algorithm Used in Hotel Management System

## 3.3 Algorithm Details

The Hotel Management System implements a **Rule-Based Decision Tree Algorithm** to automatically classify hotel guests into behavioral segments based on their booking history and transaction data. The algorithm analyzes guest data from the reservations and payments tables and assigns each guest a segment label which is stored in the users table. This helps hotel administrators quickly identify which guests need special attention without manually reviewing individual records.

The algorithm evaluates four features for each guest and applies five priority-based rules in order. The guest is placed in the first category whose condition matches. If no rule matches, the guest is marked as "Regular" by default.

---

## Features Used

| Feature                   | Source Table | How it is Calculated                 |
| ------------------------- | ------------ | ------------------------------------ |
| Total Spend (M)           | payments     | Sum of all completed payment amounts |
| Visit Count (F)           | reservations | Total number of bookings made        |
| Days Since Last Visit (R) | reservations | Days passed since last checkout      |
| Cancellation Rate (C)     | reservations | Cancelled bookings ÷ total bookings  |

---

## Classification Rules

| Priority | Condition               | Segment        |
| -------- | ----------------------- | -------------- |
| 1        | M ≥ Rs.10,000 AND F ≥ 5 | VIP            |
| 2        | F ≥ 3 AND C < 0.2       | Loyal          |
| 3        | R > 90 AND F ≥ 2        | At Risk        |
| 4        | F = 1 AND M ≥ Rs.5,000  | High Value New |
| 5        | C ≥ 0.5                 | Unreliable     |
| 6        | Default                 | Regular        |

---

## Segment Definitions

| Segment        | Description                           | Recommended Action             |
| -------------- | ------------------------------------- | ------------------------------ |
| VIP            | High-spending frequent guest          | Room upgrade, priority service |
| Loyal          | Repeat visitor with low cancellations | Discount, loyalty reward       |
| At Risk        | Has not visited in over 90 days       | Send re-engagement offer       |
| High Value New | First-time guest with high spending   | Assign dedicated staff         |
| Unreliable     | Cancels 50% or more of bookings       | Require advance deposit        |
| Regular        | Does not meet any above criteria      | Standard service               |

---

## Steps of the Algorithm

### Step i — Data Retrieval

A single SQL query joins the users, reservations, payments, and food_orders tables to compute all feature values for every guest at once. The HMS also tracks food orders to enhance segmentation insights:

```sql
SELECT
    u.id AS user_id,
    COUNT(r.id) AS visit_count,
    COALESCE(SUM(p.amount), 0) AS total_spend,
    COALESCE(DATEDIFF(CURDATE(), MAX(r.check_out)), 9999) AS days_since_last_visit,
    COALESCE(
        SUM(CASE WHEN r.status = 'cancelled' THEN 1 ELSE 0 END)
        / NULLIF(COUNT(r.id), 0), 0
    ) AS cancellation_rate,
    COUNT(fo.id) AS food_order_count
FROM users u
LEFT JOIN reservations r ON r.user_id = u.id
LEFT JOIN payments p ON p.reservation_id = r.id AND p.status = 'completed'
LEFT JOIN food_orders fo ON fo.reservation_id = r.id AND fo.status = 'delivered'
WHERE u.role = 'guest'
GROUP BY u.id;
```

### Step ii — Feature Vector Creation

For each guest, a feature vector is formed as:

**v = (M, F, R, C)**

Where M = Total Spend, F = Visit Count, R = Days Since Last Visit, C = Cancellation Rate.

### Step iii — Classification

The PHP implementation uses the `classify()` method in the Command class. It evaluates rules from top to bottom and returns the segment of the first matching rule. Comments explain each rule's business logic:

```php
private function classify(float $spend, int $visits, int $daysSince, float $cancellationRate): string
{
    // Rule 1: VIP — high spend + frequent visitor
    if ($spend >= 10000 && $visits >= 5) {
        return 'vip';
    }

    // Rule 2: Loyal — repeat visitor with low cancellation rate
    if ($visits >= 3 && $cancellationRate < 0.2) {
        return 'loyal';
    }

    // Rule 3: At Risk — used to visit but now inactive
    if ($daysSince > 90 && $visits >= 2) {
        return 'at_risk';
    }

    // Rule 4: High Value New — first-time but high spend
    if ($visits == 1 && $spend >= 5000) {
        return 'high_value_new';
    }

    // Rule 5: Unreliable — cancels often
    if ($cancellationRate >= 0.5) {
        return 'unreliable';
    }

    // Default
    return 'regular';
}
```

### Step iv — Database Update & Statistics

Once the segment is determined for each guest, it is saved to the users table. The command also maintains segment statistics:

```php
$counts = ['vip' => 0, 'loyal' => 0, 'at_risk' => 0, 'high_value_new' => 0, 'unreliable' => 0, 'regular' => 0];

foreach ($guests as $guest) {
    $segment = $this->classify(
        (float) $guest->total_spend,
        (int) $guest->visit_count,
        (int) $guest->days_since_last_visit,
        (float) $guest->cancellation_rate
    );

    DB::table('users')->where('id', $guest->user_id)->update(['segment' => $segment]);
    $counts[$segment]++;
}

// Display results as a table
$this->table(
    ['Segment', 'Guests'],
    collect($counts)->map(fn ($n, $s) => [$s, $n])->values()->toArray()
);

$this->info('Segmentation complete. '.array_sum($counts).' guests classified.');
```

This shows administrators how many guests fall into each segment category, making it easy to monitor guest composition and adjust strategies accordingly.

### Step v — Execution

The algorithm is implemented in `app/Console/Commands/SegmentGuests.php` with the signature `guests:segment`. It runs in two ways:

- **Manual:** Admin can trigger it anytime using `php artisan guests:segment`
    - Outputs a table showing segment counts
    - Displays total number of guests classified

- **Automatic:** Runs every day at midnight via Laravel's task scheduler (configured in `routes/console.php`):

```php
Schedule::command('guests:segment')->daily();
```

When executed, the command:

1. Logs "Running guest segmentation..."
2. Fetches all guest data from database
3. Classifies each guest using the `classify()` method
4. Updates the users table with segment labels
5. Displays a statistics table
6. Logs completion message with total count

```php
Schedule::command('guests:segment')->daily();
```

---

## Complexity Analysis

- **Time Complexity:** O(n) — linear in the number of guests. The SQL query runs in O(r + p) and classification per guest is O(1) since only 6 rules are checked.
- **Space Complexity:** O(n) — for storing the feature vectors and results.

---

## Advantages and Limitations

**Advantages:**

- Fully automated, no manual work needed
- Rules are simple and easy to understand
- Efficient and scales well as guest count grows
- Thresholds can be adjusted easily if business needs change

**Limitations:**

- Rule thresholds are fixed and may need periodic update
- Does not consider qualitative factors like guest feedback
- No machine learning component to adapt over time
- Assumes past behavior predicts future behavior

---

## System Integration

- Admin Dashboard shows guest distribution across all segments
- Guest Management Page shows a color-coded badge next to each guest's name
- Admin can trigger re-segmentation manually from the dashboard
- Automatically updates every night so segments stay current
