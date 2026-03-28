# Algorithms Used in Hotel Management System


## 1. Interval Overlap Detection Algorithm (Room Availability Check)

### Purpose
To prevent double booking of rooms by checking whether a requested date range conflicts with any existing reservation for the same room.

### Problem Statement
When a guest or admin tries to book a room for a specific check-in and check-out date, the system must verify that no other active reservation exists for that room during the same time period. If an overlap is detected, the booking must be rejected.

### Algorithm Logic
The algorithm uses the mathematical **Interval Overlap Formula** to detect conflicts between two date ranges:

Two date intervals [A_start, A_end] and [B_start, B_end] overlap if and only if:

    A_start < B_end AND B_start < A_end

Where:
- A_start, A_end = Check-in and Check-out dates of the existing reservation
- B_start, B_end = Check-in and Check-out dates of the new booking request

If both conditions are true, the intervals overlap, meaning the room is NOT available.

### Step-by-Step Process
1. User selects a room and enters desired check-in and check-out dates.
2. The system queries all existing reservations for that room where:
   - The reservation status is NOT "cancelled" or "checked_out" (only active bookings are considered).
3. For each existing reservation, the system checks:
   - Is the existing check-in date BEFORE the requested check-out date? (existing_start < new_end)
   - Is the existing check-out date AFTER the requested check-in date? (existing_end > new_start)
4. If any reservation satisfies both conditions, an overlap is detected and the booking is rejected.
5. If no overlaps are found, the room is available and the booking proceeds.

### Example Scenario

    Existing Reservation: Room 101, Check-in: Jan 5, Check-out: Jan 10
    New Booking Request:  Room 101, Check-in: Jan 8, Check-out: Jan 12

    Check: Jan 5 < Jan 12? YES
    Check: Jan 10 > Jan 8? YES
    Result: OVERLAP DETECTED - Booking Rejected

    Another Request:      Room 101, Check-in: Jan 11, Check-out: Jan 15

    Check: Jan 5 < Jan 15? YES
    Check: Jan 10 > Jan 11? NO
    Result: NO OVERLAP - Booking Allowed

### Implementation Code (app/Models/Room.php)

```php
public function isAvailable($checkIn, $checkOut, $excludeReservationId = null)
{
    $query = $this->reservations()
        ->whereNotIn('status', ['cancelled', 'checked_out'])
        ->where('check_in', '<', $checkOut)    // existing start < new end
        ->where('check_out', '>', $checkIn);   // existing end > new start

    if ($excludeReservationId) {
        $query->where('id', '!=', $excludeReservationId);
    }

    return $query->count() === 0;
}
```

### Where It Is Used
- Guest booking (GuestBookingController@book) - when a guest books a room from the Browse Rooms page.
- Admin creating reservation (ReservationController@store) - when admin creates a new reservation.
- Admin updating reservation (ReservationController@update) - when admin changes the room or dates of an existing reservation. The current reservation is excluded from the overlap check using the excludeReservationId parameter.

### Time Complexity
- O(n) where n is the number of active reservations for that specific room.
- In practice, each room has very few active reservations at any time, making this nearly O(1).


---


## 2. Multi-Criteria Search, Filter and Sort Algorithm

### Purpose
To allow users to find rooms that match their specific requirements by applying multiple filters simultaneously and sorting the results in a desired order.

### Problem Statement
The hotel has multiple rooms of different types, capacities, floors, and prices. When a guest wants to browse available rooms, they should be able to narrow down the results by specifying criteria such as room type, price range, and minimum capacity, and then sort the results to find the best option.

### Algorithm Logic
The algorithm applies a **sequential filtering pipeline** followed by a **comparison-based sort**:

1. Start with all available rooms (base dataset).
2. Apply each filter one by one, eliminating rooms that do not match.
3. Sort the remaining rooms by the chosen field.

This follows the **Filter-Map-Sort** pattern commonly used in data processing.

### Step-by-Step Process
1. Begin with all rooms where status = "available".
2. If room type is specified, filter rooms where type matches exactly (e.g., "single", "double", "suite", "deluxe").
3. If minimum price is specified, filter rooms where price_per_night >= min_price.
4. If maximum price is specified, filter rooms where price_per_night <= max_price.
5. If minimum capacity is specified, filter rooms where capacity >= requested capacity.
6. Sort the filtered results by the chosen field (price, capacity, room number, or floor) in ascending or descending order.
7. Return the final sorted and filtered list.

### Example Scenario

    Total Available Rooms: 15 rooms

    User Filters:
    - Type: "double"
    - Min Price: Rs. 100
    - Max Price: Rs. 200
    - Min Capacity: 2
    - Sort By: Price (Ascending)

    Step 1: Start with 15 available rooms
    Step 2: Filter by type "double" -> 5 rooms remain
    Step 3: Filter by price >= 100 -> 5 rooms remain
    Step 4: Filter by price <= 200 -> 4 rooms remain
    Step 5: Filter by capacity >= 2 -> 4 rooms remain
    Step 6: Sort by price ascending -> [Rs.120, Rs.120, Rs.150, Rs.180]
    Result: 4 rooms displayed in order of price

### Implementation Code (app/Models/Room.php)

```php
public static function searchAndFilter($filters)
{
    $query = static::where('status', 'available');

    // Filter by type
    if (!empty($filters['type'])) {
        $query->where('type', $filters['type']);
    }

    // Filter by price range
    if (!empty($filters['min_price'])) {
        $query->where('price_per_night', '>=', $filters['min_price']);
    }
    if (!empty($filters['max_price'])) {
        $query->where('price_per_night', '<=', $filters['max_price']);
    }

    // Filter by minimum capacity
    if (!empty($filters['capacity'])) {
        $query->where('capacity', '>=', $filters['capacity']);
    }

    // Sort results
    $sortBy = $filters['sort_by'] ?? 'price_per_night';
    $sortOrder = $filters['sort_order'] ?? 'asc';
    $allowedSorts = ['price_per_night', 'capacity', 'room_number', 'floor'];

    if (in_array($sortBy, $allowedSorts)) {
        $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
    }

    return $query->get();
}
```

### Where It Is Used
- Guest Browse Rooms page (GuestBookingController@rooms) - the search and filter form at the top of the Browse Rooms page sends filter parameters via GET request, and the controller passes them to this algorithm.

### Time Complexity
- Filtering: O(n) where n is the total number of available rooms (each filter scans through the dataset).
- Sorting: O(n log n) using comparison-based sort (database ORDER BY clause).
- Overall: O(n log n) dominated by the sorting step.


---


## 3. Billing Aggregation Algorithm

### Purpose
To calculate the complete bill for a reservation by aggregating room charges and delivered food order charges, then computing the balance due after subtracting payments already made.

### Problem Statement
At the time of checkout or payment, the system must present an accurate bill that includes the room charge based on the number of nights stayed and the per-night rate, plus the total cost of all food items that were ordered and successfully delivered during the stay. The system must also track partial payments and show the remaining balance.

### Algorithm Logic
The algorithm uses **summation and aggregation** across multiple related database tables:

    Room Charge  = Number of Nights x Price Per Night
    Food Charge  = SUM(total_price) of all food orders WHERE status = "delivered"
    Grand Total  = Room Charge + Food Charge
    Paid Amount  = SUM(amount) of all payments WHERE status = "completed"
    Balance Due  = Grand Total - Paid Amount

Only food orders with status "delivered" are included. Orders that are pending, preparing, or cancelled are excluded from the bill.

### Step-by-Step Process
1. Calculate the number of nights: check_out_date - check_in_date.
2. Multiply nights by the room's price per night to get the room charge.
3. Query all food orders linked to this reservation where status is "delivered".
4. Sum the total_price field of all delivered food orders to get the food charge.
5. Add room charge and food charge to get the grand total.
6. Query all payments linked to this reservation where status is "completed".
7. Sum the amount field of all completed payments to get the paid amount.
8. Subtract paid amount from grand total to get the balance due.
9. If balance due is less than or equal to zero, the reservation is fully paid.

### Example Scenario

    Reservation: Room 203, Check-in: Mar 1, Check-out: Mar 4 (3 nights)
    Room Price: Rs. 120 per night

    Room Charge = 3 x Rs. 120 = Rs. 360

    Food Orders:
    - Grilled Chicken x2 = Rs. 29.98  (Status: Delivered)  -> Counted
    - Coffee x1          = Rs. 3.99   (Status: Delivered)  -> Counted
    - Pasta x1           = Rs. 12.99  (Status: Cancelled)  -> NOT Counted
    - Steak x1           = Rs. 24.99  (Status: Preparing)  -> NOT Counted

    Food Charge = Rs. 29.98 + Rs. 3.99 = Rs. 33.97

    Grand Total = Rs. 360 + Rs. 33.97 = Rs. 393.97

    Payments Made:
    - Rs. 200 via Cash (Status: Completed) -> Counted

    Paid Amount = Rs. 200
    Balance Due = Rs. 393.97 - Rs. 200 = Rs. 193.97

### Implementation Code

In the Blade views (guest/reservation.blade.php and admin/reservations/show.blade.php):

```php
$nights     = $reservation->check_in->diffInDays($reservation->check_out);
$roomTotal  = $reservation->total_amount;
$foodTotal  = $reservation->foodOrders->where('status', 'delivered')->sum('total_price');
$grandTotal = $roomTotal + $foodTotal;
$paidTotal  = $reservation->payments->where('status', 'completed')->sum('amount');
$balanceDue = $grandTotal - $paidTotal;
```

In the payment validation (ReservationController@recordPayment):

```php
$grandTotal = $reservation->total_amount
    + $reservation->foodOrders()->where('status', 'delivered')->sum('total_price');
$paidTotal  = $reservation->payments()->where('status', 'completed')->sum('amount');
$balanceDue = $grandTotal - $paidTotal;

if ($balanceDue <= 0) {
    // Reservation is fully paid - reject payment
}
if ($validated['amount'] > $balanceDue) {
    // Payment exceeds balance - reject to prevent overpayment
}
```

### Where It Is Used
- Guest Reservation Detail page - shows the billing summary to the customer (view only).
- Admin Reservation Detail page - shows the billing summary and allows admin to record payments.
- Payment validation in both ReservationController and GuestBookingController - ensures payments do not exceed the balance due.

### Time Complexity
- O(m + p) where m is the number of food orders and p is the number of payments for that reservation.
- In practice, both m and p are small numbers per reservation, making this effectively O(1).


---


## 4. Finite State Machine Algorithm (Reservation Lifecycle)

### Purpose
To manage the lifecycle of a reservation through a defined set of states with controlled transitions, ensuring that reservations follow a valid workflow and room statuses are automatically updated.

### Problem Statement
A reservation goes through multiple stages from creation to completion. The system must enforce valid transitions (e.g., a guest cannot be checked in before the reservation is confirmed) and automatically update the room's availability status when the reservation state changes.

### Algorithm Logic
The algorithm implements a **Finite State Machine (FSM)** where:

- **States**: pending, confirmed, checked_in, checked_out, cancelled
- **Transitions**: Each state can only move to specific next states
- **Side Effects**: Room status is automatically updated based on the reservation state

### State Transition Diagram

    pending ---------> confirmed ---------> checked_in ---------> checked_out
       |                  |
       |                  |
       v                  v
    cancelled          cancelled

### Transition Rules and Side Effects

    From State    -> To State      | Room Status Change
    -----------------------------------------------
    pending       -> confirmed     | Room -> occupied
    confirmed     -> checked_in    | Room -> occupied
    checked_in    -> checked_out   | Room -> available
    pending       -> cancelled     | Room -> available
    confirmed     -> cancelled     | Room -> available

### Implementation Code (app/Http/Controllers/ReservationController.php)

```php
public function updateStatus(Request $request, Reservation $reservation)
{
    $validated = $request->validate([
        'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
    ]);

    $oldStatus = $reservation->status;
    $newStatus = $validated['status'];

    $reservation->update(['status' => $newStatus]);

    // Side effects: automatically update room status
    if ($newStatus === 'checked_in') {
        $reservation->room->update(['status' => 'occupied']);
    } elseif (in_array($newStatus, ['checked_out', 'cancelled'])) {
        $reservation->room->update(['status' => 'available']);
    } elseif ($newStatus === 'confirmed') {
        $reservation->room->update(['status' => 'occupied']);
    }

    return back()->with('success', "Status changed from {$oldStatus} to {$newStatus}.");
}
```

### Where It Is Used
- Admin Reservation Index page - quick action buttons (Confirm, Check In, Check Out, Cancel) trigger state transitions.
- Admin Reservation Detail page - status dropdown allows changing to any valid state.
- The room status is automatically synchronized with the reservation status throughout the system.

### Time Complexity
- O(1) for each state transition - it is a constant-time operation involving one update to the reservation and one update to the room.


---


## Summary Table

| Algorithm | Purpose | Time Complexity | File Location |
|---|---|---|---|
| Interval Overlap Detection | Prevent double booking of rooms | O(n) | app/Models/Room.php |
| Multi-Criteria Search, Filter & Sort | Find rooms matching user preferences | O(n log n) | app/Models/Room.php |
| Billing Aggregation | Calculate total bill with room + food charges | O(m + p) | Views + Controllers |
| Finite State Machine | Manage reservation lifecycle and room status | O(1) | ReservationController.php |
