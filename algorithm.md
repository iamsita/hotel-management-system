# Algorithm Used in Hotel Management System

## 1. Introduction

This document describes the algorithm implemented in the Hotel Management System developed as part of the college minor project. The system is built using the Laravel framework (PHP) with a MySQL database. The algorithm implemented is a **Rule-Based Decision Tree** for automatic guest segmentation, which classifies hotel guests into behavioral categories based on their reservation history, spending patterns, and cancellation behavior.

---

## 2. Rule-Based Decision Tree Algorithm (Guest Segmentation)

### 2.1 Purpose

The Rule-Based Decision Tree Algorithm automatically classifies hotel guests into distinct behavioral segments based on their historical booking data, total spending, and cancellation patterns. This enables the hotel administration to understand their guest base and provide personalized service to different types of guests.

### 2.2 Problem Statement

Not all hotel guests behave the same way. Some guests are frequent, high-spending visitors who deserve premium service. Others have not returned in months and may need re-engagement. Some guests repeatedly cancel reservations, which creates operational challenges. The system needs an automated method to categorize guests so that administrators can identify each type at a glance and take appropriate action — without manually analyzing individual guest records.

### 2.3 Algorithm Logic

The algorithm implements a **Rule-Based Decision Tree** — a hierarchical series of IF-THEN conditions evaluated sequentially in order of priority. Each guest is assigned to the first segment whose conditions they satisfy. The rules are applied to four features derived from the existing database tables.

### 2.4 Mathematical Formulation

Let **G** be the set of all guests in the system, and let each guest be represented as **g ∈ G**.

For each guest g, four feature values are computed from the database:

---

**Feature 1 — Total Spend (M)**

    M(g) = Σ  payment.amount
             ∀ payments linked to g where payment.status = "completed"

This is the sum of all completed payment amounts made by guest g across all reservations.

---

**Feature 2 — Visit Count (F)**

    F(g) = | { r ∈ Reservations : r.user_id = g.id } |

This is the total number of reservations made by guest g (the cardinality of the reservation set).

---

**Feature 3 — Days Since Last Visit (R)**

    R(g) = D_today  −  max({ r.check_out : r.user_id = g.id })

Where D_today is the current date. R(g) measures how many days have passed since the guest's most recent checkout. A smaller value of R means the guest visited more recently.

---

**Feature 4 — Cancellation Rate (C)**

    C(g) =  | { r : r.user_id = g.id  AND  r.status = "cancelled" } |
            ─────────────────────────────────────────────────────────
                   | { r : r.user_id = g.id } |

This is the proportion of reservations that were cancelled. C(g) ∈ [0, 1], where 0 means no cancellations and 1 means all reservations were cancelled.

---

**Classification Function**

The classification function φ maps each guest's feature vector to a segment label:

    φ : (M, F, R, C)  →  { vip, loyal, at_risk, high_value_new, unreliable, regular }

The function is defined as a priority-ordered piecewise rule:

              ⎧  "vip"            if  M(g) ≥ 10000  AND  F(g) ≥ 5
              ⎪  "loyal"          if  F(g) ≥ 3       AND  C(g) < 0.2
    φ(g)  =   ⎨  "at_risk"        if  R(g) > 90      AND  F(g) ≥ 2
              ⎪  "high_value_new" if  F(g) = 1        AND  M(g) ≥ 5000
              ⎪  "unreliable"     if  C(g) ≥ 0.5
              ⎩  "regular"        (default)

Rules are evaluated strictly from top to bottom. The guest is assigned to the segment of the **first rule that evaluates to true**. If no rule matches, the default "regular" segment is assigned.

---

**Segmentation Procedure**

The full segmentation over all guests can be expressed as:

    S = { (g, φ(g))  :  g ∈ G }

Where S is the complete mapping of every guest to their computed segment. This mapping is stored by updating the segment field in the users table for each guest g.

---

### 2.5 Features Used from the Database

| Feature | Source Table | Derivation |
|---|---|---|
| Total Spend | payments | SUM(amount) WHERE status = 'completed' |
| Visit Count | reservations | COUNT(id) per guest |
| Days Since Last Visit | reservations | DATEDIFF(today, MAX(check_out)) |
| Cancellation Rate | reservations | cancelled reservations ÷ total reservations |

### 2.5 Decision Tree

```
INPUT: guest features (total_spend, visit_count, days_since_last_visit, cancellation_rate)

├── IF total_spend >= 10000 AND visit_count >= 5
│       └── ASSIGN segment = "VIP"
│
├── ELSE IF visit_count >= 3 AND cancellation_rate < 0.2
│       └── ASSIGN segment = "Loyal"
│
├── ELSE IF days_since_last_visit > 90 AND visit_count >= 2
│       └── ASSIGN segment = "At Risk"
│
├── ELSE IF visit_count = 1 AND total_spend >= 5000
│       └── ASSIGN segment = "High Value New"
│
├── ELSE IF cancellation_rate >= 0.5
│       └── ASSIGN segment = "Unreliable"
│
└── ELSE
        └── ASSIGN segment = "Regular"

OUTPUT: segment label stored in users.segment
```

### 2.6 Segment Definitions

| Segment | Description | Recommended Admin Action |
|---|---|---|
| VIP | High-spending guest with 5 or more completed stays | Complimentary room upgrade, priority service, loyalty rewards |
| Loyal | Repeat visitor with at least 3 stays and low cancellation rate | Discount on next booking, loyalty acknowledgement |
| At Risk | Previously active guest who has not visited in over 90 days | Re-engagement promotional offer via email |
| High Value New | First-time guest with a high-value booking | Assign dedicated staff, personalized welcome |
| Unreliable | Guest who cancels 50% or more of their bookings | Require advance deposit on future reservations |
| Regular | Guest who does not meet any of the above criteria | Standard hotel service |

### 2.7 Pseudocode

```
FUNCTION classifyGuest(total_spend, visit_count, days_since_last_visit, cancellation_rate):

    IF total_spend >= 10000 AND visit_count >= 5:
        RETURN "vip"

    IF visit_count >= 3 AND cancellation_rate < 0.2:
        RETURN "loyal"

    IF days_since_last_visit > 90 AND visit_count >= 2:
        RETURN "at_risk"

    IF visit_count = 1 AND total_spend >= 5000:
        RETURN "high_value_new"

    IF cancellation_rate >= 0.5:
        RETURN "unreliable"

    RETURN "regular"

END FUNCTION


PROCEDURE runSegmentation():

    guests = SQL query to aggregate features for all guest-role users
             from reservations, payments tables

    FOR EACH guest IN guests:
        segment = classifyGuest(guest.total_spend, guest.visit_count,
                                guest.days_since_last_visit, guest.cancellation_rate)
        UPDATE users SET segment = segment WHERE id = guest.user_id

END PROCEDURE
```

### 2.8 Step-by-Step Process

1. A single SQL query joins the `users`, `reservations`, and `payments` tables to compute the four feature values for each guest.
2. For each guest record, the `classifyGuest` function evaluates the rules in order from top to bottom.
3. The guest is assigned the segment of the first rule that evaluates to true.
4. If no rule matches, the guest is assigned the "regular" segment by default.
5. The computed segment label is saved to the `segment` column of the `users` table.
6. This process is scheduled to run automatically every day at midnight via Laravel's task scheduler.
7. The Admin Guest Segmentation page reads the `segment` field and displays a color-coded badge next to each guest's name along with the feature values used for classification.

### 2.9 Example

```
Guest: Alice
  total_spend          = Rs. 12,000
  visit_count          = 6
  days_since_last_visit = 10
  cancellation_rate    = 0.0

Rule 1: total_spend >= 10000 AND visit_count >= 5 → TRUE
Result: segment = "VIP"

---

Guest: Eve
  total_spend          = Rs. 800
  visit_count          = 4
  days_since_last_visit = 20
  cancellation_rate    = 0.50

Rule 1: 800 >= 10000 AND 4 >= 5 → FALSE
Rule 2: 4 >= 3 AND 0.50 < 0.2  → FALSE  (cancellation rate too high)
Rule 3: 20 > 90 AND 4 >= 2     → FALSE  (visited recently)
Rule 4: visit_count = 1        → FALSE
Rule 5: 0.50 >= 0.5            → TRUE
Result: segment = "Unreliable"

---

Guest: Frank
  total_spend          = Rs. 250
  visit_count          = 1
  days_since_last_visit = 15
  cancellation_rate    = 0.0

Rule 1 → FALSE
Rule 2 → FALSE (visit_count < 3)
Rule 3 → FALSE (visited recently)
Rule 4 → FALSE (total_spend < 5000)
Rule 5 → FALSE
Result: segment = "Regular"
```

### 2.10 Implementation

**Feature Extraction Query**

File: `app/Console/Commands/SegmentGuests.php`

```php
$guests = DB::select("
    SELECT
        u.id AS user_id,
        COUNT(r.id)                                                        AS visit_count,
        COALESCE(SUM(p.amount), 0)                                        AS total_spend,
        COALESCE(DATEDIFF(CURDATE(), MAX(r.check_out)), 9999)             AS days_since_last_visit,
        COALESCE(
            SUM(CASE WHEN r.status = 'cancelled' THEN 1 ELSE 0 END)
            / NULLIF(COUNT(r.id), 0), 0
        )                                                                  AS cancellation_rate
    FROM users u
    LEFT JOIN reservations r  ON r.user_id = u.id
    LEFT JOIN payments p      ON p.reservation_id = r.id AND p.status = 'completed'
    WHERE u.role = 'guest'
    GROUP BY u.id
");
```

**Classification Function**

```php
private function classify(float $spend, int $visits, int $daysSince, float $cancellationRate): string
{
    if ($spend >= 10000 && $visits >= 5)          return 'vip';
    if ($visits >= 3 && $cancellationRate < 0.2)  return 'loyal';
    if ($daysSince > 90 && $visits >= 2)          return 'at_risk';
    if ($visits == 1 && $spend >= 5000)           return 'high_value_new';
    if ($cancellationRate >= 0.5)                 return 'unreliable';
    return 'regular';
}
```

**Daily Schedule**

File: `routes/console.php`

```php
Schedule::command('guests:segment')->daily();
```

### 2.11 Where It Is Used

- **Artisan command** `php artisan guests:segment` — can be run manually to classify all guests immediately.
- **Laravel Task Scheduler** — runs the command automatically every day so segments stay up to date as guest behavior changes over time.
- **Admin Guest Segmentation Page** (`/admin/algorithms`) — displays a color-coded segment badge for every guest along with the feature values (visits, spend, days since visit, cancellation rate) used for classification. Includes a **Re-run Segmentation** button to trigger the algorithm from the UI.

### 2.12 Database Change

A `segment` column was added to the existing `users` table migration:

```php
$table->enum('segment', ['vip', 'loyal', 'at_risk', 'high_value_new', 'unreliable', 'regular'])
      ->nullable();
```

### 2.13 Time Complexity

- **O(g)** where g is the total number of guests — each guest is evaluated against a constant number of rules (6 rules), so the classification itself is O(1) per guest.
- The underlying SQL aggregation is **O(r + p)** where r is the number of reservation rows and p is the number of payment rows — all computed in a single database query.
- Overall the algorithm scales linearly with the size of the guest database.

---

## 3. Summary

| Property | Detail |
|---|---|
| Algorithm Type | Rule-Based Decision Tree |
| Input | Guest behavioral features from reservations and payments tables |
| Output | Segment label stored in users.segment |
| Number of Rules | 5 priority rules + 1 default |
| Number of Segments | 6 (VIP, Loyal, At Risk, High Value New, Unreliable, Regular) |
| Time Complexity | O(g) — linear in number of guests |
| Execution | Manual (artisan command) or automatic (daily scheduler) |
| File Location | app/Console/Commands/SegmentGuests.php |
