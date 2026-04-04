# Use Case Diagram Legend & Interpretation Guide

## 📖 How to Read the Use Case Diagram

### Diagram Components

#### 1. **System Boundary**

- Large rounded box containing all use cases
- Represents the **Hotel Management System (HMS)**
- Everything inside is a system operation/function

#### 2. **Actors (Left Side)**

Three distinct stick figures representing:

- **🔴 Orange Figure** - Guest (Unauthenticated)
- **🟢 Green Figure** - Guest (Authenticated/Authorized)
- **🔵 Blue Figure** - Admin

#### 3. **Use Cases (Ellipses)**

Represent system functions. Color indicates primary actor:

- **Yellow (#ffe5b4)** - Authentication functions (used by all actors)
- **Green (#d5f5e3)** - Guest-level functions
- **Light Blue (#dceefb)** - Admin-only functions

#### 4. **Associations (Lines)**

Lines connecting actors to use cases show:

- Which users can perform which actions
- Solid lines = direct interaction
- Actor can initiate the use case

---

## 🎯 Layer-by-Layer Breakdown

### AUTH LAYER (Top)

```
Register → Login → Logout
```

- **Register:** Unauthenticated guest creates new account
- **Login:** Unauthenticated guest authenticates
- **Logout:** Any authenticated user ends session

**Routes:**

- POST /register
- POST /login
- POST /logout

---

### PROFILE & DASHBOARD LAYER

```
View Profile → Edit Profile → View Dashboard
```

- **View Profile:** User sees their account info
- **Edit Profile:** User updates name, phone, email
- **View Dashboard:** Summary of personal/system data

**Database:** users table
**Routes:**

- GET /profile
- GET /profile/edit
- PUT /profile

---

### ROOM MANAGEMENT LAYER

```
Search Rooms → Filter by Price/Type → View Room Details → Manage Rooms [CRUD]
```

**Guest Functions:**

- **Search Rooms:** Find available rooms in date range (uses interval overlap algorithm)
- **Filter by Price/Type:** Apply multiple filter criteria
- **View Room Details:** See full room information including availability

**Admin Function:**

- **Manage Rooms:** Create, Read, Update, Delete room records

**Database:** rooms table (7 columns)
**Connected Algorithm:** Interval Overlap Detection for availability

---

### RESERVATION MANAGEMENT LAYER

```
Book a Room [Reserve] → View My Reservation → Cancel Reservation
         ↓
Manage Reservations ← Check-In/Check-Out
```

**Guest Functions:**

- **Book a Room:** Create new reservation with check-in/check-out dates
- **View My Reservation:** See all personal bookings and details
- **Cancel Reservation:** Change status to cancelled

**Admin Functions:**

- **Manage Reservations:** Full CRUD operations on all reservations
- **Check-In/Check-Out:** Process guest arrival/departure, update status

**Database:** reservations table (7 columns)
**Business Rules:**

- Check-in date < Check-out date
- Guest count ≤ room capacity
- Room must be available (no overlapping reservations)

---

### FOOD & DINING LAYER

```
View Food Menu → Order Food from Menu → View Food Orders
       ↓
Manage Food Menu [CRUD] → Update Food Order Status
```

**Guest Functions:**

- **View Food Menu:** Browse available dishes and categories
- **Order Food from Menu:** Select items and quantities
- **View Food Orders:** Track status of placed orders

**Admin Functions:**

- **Manage Food Menu:** Add/edit/delete menu items
- **Update Food Order Status:** Change order from pending → preparing → ready → served

**Database:**

- foods (5 columns)
- food_orders (6 columns)
  **Connections:**
- Reservation (1) → (many) FoodOrders
- Food (1) → (many) FoodOrders

---

### PAYMENT & BILLING LAYER

```
View Billing Summary → Make Payment → View Payment History
       ↓
Record Payment → View All Payments
```

**Guest Functions:**

- **View Billing Summary:** Room charges + Food charges breakdown
- **Make Payment:** Process payment with method selection
- **View Payment History:** See all previous transactions

**Admin Functions:**

- **Record Payment:** Manually enter guest payment
- **View All Payments:** Complete payment ledger for reporting

**Database:** payments table (5 columns)
**States:**

- pending → completed
- pending → failed

---

### GUEST MANAGEMENT LAYER (Admin Only)

```
Manage Guests [CRUD] → View Guest Details
```

**Admin Only:**

- **Manage Guests:** Create/update/delete guest accounts
- **View Guest Details:** See profile, history, and segment

**Database:** users table (with role = 'guest')

---

### DATA ANALYSIS LAYER (Admin Only)

```
Admin Dashboard & Reports ← Guest Segmentation
```

**Admin Only:**

- **Admin Dashboard & Reports:** System overview, metrics, analytics
- **Guest Segmentation:** Behavioral analysis, categorization

**Algorithm:** Custom segmentation algorithm
**Data Sources:** Aggregation from all tables

---

## 🔀 Association Types in Use Case Diagrams

### Direct Association

```
Actor ─── Use Case
```

- Actor directly interacts with the use case
- Actor initiates the action
- Example: Guide can access the museum

### Extension Association

```
Use Case ─────|> Base Use Case
              ‹extend›
```

Indicates an optional extension (not shown in this diagram)

### Inclusion Association

```
Use Case ────|> Included Use Case
             ‹include›
```

One use case always calls another (not shown in this diagram)

**In our diagram:** All associations are direct associations (solid lines)

---

## 📊 System Statistics

| Metric                       | Count |
| ---------------------------- | ----- |
| **Total Actors**             | 3     |
| **Total Use Cases**          | 27+   |
| **Authentication Use Cases** | 3     |
| **Guest Use Cases**          | 11    |
| **Admin-Only Use Cases**     | 14+   |
| **Database Tables**          | 6     |
| **1:N Relationships**        | 6     |

---

## 🔗 Relationships Explained

### User → Reservation Relationship

- One user can have **many** reservations
- Cardinality: **1:N**
- Meaning: A guest books multiple rooms over time

### Room → Reservation Relationship

- One room can have **many** reservations
- Cardinality: **1:N**
- Meaning: A room is booked by different guests at different times

### Reservation → FoodOrder Relationship

- One reservation can have **many** food orders
- Cardinality: **1:N**
- Meaning: During a stay, guest orders multiple food items

### Reservation → Payment Relationship

- One reservation can have **many** payments
- Cardinality: **1:N**
- Meaning: Guest can make partial/multiple payments

### Food → FoodOrder Relationship

- One food item can be in **many** orders
- Cardinality: **1:N**
- Meaning: Popular dish ordered by multiple guests

---

## 🛡️ Security Flow

```
UNAUTHENTICATED
      ↓
   [Register/Login]
      ↓
AUTHENTICATED (Guest Role)
      ↓
   [Profile + Browsing]
      ↓
   [Can perform guest operations]

=== OR ===

AUTHENTICATED (Admin Role)
      ↓
   [All Admin Operations]
      ↓
   [Full System Access]
```

---

## 🔑 Key Observations

### 1. **Layered Architecture**

- Diagram uses horizontal layers
- Each layer represents functional area
- Layers build on each other

### 2. **Actor Distribution**

- **Unauthenticated:** 2 use cases (Register/Login)
- **Authenticated Guest:** 11 use cases
- **Admin:** 14+ use cases (including guest's)

### 3. **Shared Flows**

- Many use cases accessed by multiple actors
- Admin has extended access to all features
- Security enforced at route/middleware level

### 4. **Data Dependencies**

- Reservations depend on Users and Rooms
- FoodOrders depend on Reservations and Foods
- Payments depend on Reservations
- This ensures referential integrity

### 5. **Status Workflows**

- Each major entity has state transitions
- Room availability checked dynamically
- Order progresses through workflow states

---

## 🎓 Business Logic Examples

### Example 1: Room Booking Flow

```
1. Guest [Search Rooms] using dates
2. System checks [View Room Details] availability
3. Guest views [Filter by Price/Type] options
4. Guest [Book a Room]
5. System creates Reservation record
6. Status set to 'pending'
```

### Example 2: Food Ordering Flow

```
1. Guest in active reservation
2. Guest [View Food Menu]
3. Guest [Order Food from Menu]
4. System creates FoodOrder record
5. Admin [Update Food Order Status]
6. Guest [View Food Orders] for status
```

### Example 3: Payment Processing

```
1. Guest [View Billing Summary]
2. Guest [Make Payment]
3. System creates Payment record (pending)
4. Admin [Record Payment] if manual
5. System updates Payment (completed)
6. Guest [View Payment History]
```

---

## 🚀 System Capabilities

### What Guests Can Do

✓ Browse available rooms  
✓ Search by date, price, type  
✓ Make reservations  
✓ Order food during stay  
✓ Make payments  
✓ View history/profile

### What Admin Can Do

✓ Everything guests can do  
✓ Manage all rooms  
✓ Process all reservations  
✓ Check in/check out guests  
✓ Manage menu items  
✓ Record payments  
✓ View all data  
✓ Run analytics/reports

---

## 📋 Validation Rules

### Room Booking Validation

```
IF (check_in < check_out) AND
   (room available for dates) AND
   (guests ≤ room capacity)
THEN allow booking
ELSE show error
```

### Payment Validation

```
IF (amount > 0) AND
   (valid payment method) AND
   (linked to valid reservation)
THEN process payment
ELSE reject
```

### Food Order Validation

```
IF (reservation active) AND
   (food available) AND
   (quantity > 0)
THEN create order
ELSE reject
```

---

## 📈 Scalability Notes

This use case diagram supports:

- ✅ Multiple CRUD operations
- ✅ Status workflow management
- ✅ Complex queries (search, filter)
- ✅ Multi-table transactions
- ✅ Role-based access control
- ✅ Future extensions (ratings, loyalty, etc.)

---

## 🔍 How to Trace a User Action

**Example: Guest books a room**

1. Start at guest actor (green)
2. Find "Book a Room [Reserve]" use case
3. Trace the connection line
4. Line touches the use case = action is allowed
5. Database involved: users, rooms, reservations
6. Algorithm used: Interval Overlap Detection
7. Result: New reservation created

---

**Last Updated:** 2024  
**Generated from:** Complete system analysis (DB + Routes + Models)
