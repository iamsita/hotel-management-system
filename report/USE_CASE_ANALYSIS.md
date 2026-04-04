# Hotel Management System - Use Case Diagram Analysis

## System Overview

The Hotel Management System (HMS) is a comprehensive web-based application designed to manage hotel operations including room bookings, guest reservations, food orders, and payment processing.

---

## Actors (Users)

### 1. **Guest (Unauthenticated)**

- Visitors who haven't logged into the system
- Limited access: Can only register or login
- **Primary Goals:**
    - Create a new account
    - Access the system

### 2. **Guest (Authenticated)**

- Registered guests/customers who have logged in
- Full access to booking and ordering features
- **Primary Goals:**
    - Browse available rooms
    - Make reservations
    - Manage personal profile
    - Order food items
    - Make payments
    - View booking history

### 3. **Admin**

- System administrator with full system access
- Manages all resources and operations
- **Primary Goals:**
    - Manage hotel inventory (rooms)
    - Manage reservations and guest check-ins/check-outs
    - Manage food menu and inventory
    - Process and record payments
    - Manage guest accounts
    - Generate reports and analytics

---

## Use Cases by Layer

### Layer 1: Authentication (All Actors)

| Use Case     | Actor              | Description                                         |
| ------------ | ------------------ | --------------------------------------------------- |
| **Register** | Unauth Guest       | New user creates account with email, password, name |
| **Login**    | Unauth Guest       | Existing user authenticates with credentials        |
| **Logout**   | Auth Guest / Admin | User terminates session                             |

**Database Tables Involved:** `users`

---

### Layer 2: Profile & Dashboard (Auth Guest + Admin)

| Use Case           | Actor              | Database                       |
| ------------------ | ------------------ | ------------------------------ |
| **View Profile**   | Auth Guest         | `users`                        |
| **Edit Profile**   | Auth Guest         | `users` (name, phone, email)   |
| **View Dashboard** | Auth Guest / Admin | Multiple tables (summary view) |

---

### Layer 3: Room Management

#### Guest Functions

| Use Case                 | Description            | Details                              |
| ------------------------ | ---------------------- | ------------------------------------ |
| **Search Rooms**         | Find rooms by criteria | Query optimization: O(n) search      |
| **Filter by Price/Type** | Apply multiple filters | Supports dynamic filtering           |
| **View Room Details**    | Full room information  | Room attributes, availability, price |

**Algorithm Used:**

- Interval Overlap Detection for availability checking
- Search, Filter & Sort for room discovery

#### Admin Functions

| Use Case         | Description          | CRUD Operation               |
| ---------------- | -------------------- | ---------------------------- |
| **Manage Rooms** | Full CRUD operations | Create, Read, Update, Delete |

**Database Table:** `rooms` (room_number, type, capacity, price_per_night, status, floor)

---

### Layer 4: Reservation Management

#### Guest Functions

| Use Case                | Description                    | Validation                                      |
| ----------------------- | ------------------------------ | ----------------------------------------------- |
| **Book a Room**         | Create new reservation         | Check room availability using interval overlap  |
| **View My Reservation** | List all personal reservations | Shows check-in, check-out, status, total amount |
| **Cancel Reservation**  | Cancel booking                 | Update status to 'cancelled'                    |

#### Admin Functions

| Use Case                 | Description             | Details                      |
| ------------------------ | ----------------------- | ---------------------------- |
| **Manage Reservations**  | CRUD operations         | Create, Read, Update, Delete |
| **Check-In / Check-Out** | Guest arrival/departure | Update reservation status    |

**Database Tables:** `reservations`, `users` (1:N relationship)

**Reservation States:**

- `pending` → `confirmed` → `checked_in` → `checked_out`
- Alternative: `pending` → `cancelled`

---

### Layer 5: Food & Dining Management

#### Guest Functions

| Use Case             | Description             | Triggered By              |
| -------------------- | ----------------------- | ------------------------- |
| **View Food Menu**   | Browse available dishes | During active reservation |
| **Order Food**       | Select items from menu  | From dining menu page     |
| **View Food Orders** | Track placed orders     | Personal order history    |

#### Admin Functions

| Use Case                     | Description          | CRUD Operation                               |
| ---------------------------- | -------------------- | -------------------------------------------- |
| **Manage Food Menu**         | Full CRUD operations | Add, edit, delete menu items                 |
| **Update Food Order Status** | Change order status  | `pending` → `preparing` → `ready` → `served` |

**Database Tables:**

- `foods` (name, category, price, available)
- `food_orders` (reservation_id, food_id, quantity, total_price, status)

**Relationships:**

- Food (1) → (many) FoodOrders
- Reservation (1) → (many) FoodOrders

---

### Layer 6: Payment & Billing

#### Guest Functions

| Use Case                 | Description             | Data Shown                  |
| ------------------------ | ----------------------- | --------------------------- |
| **View Billing Summary** | Total charges breakdown | Room charges + Food charges |
| **Make Payment**         | Process payment         | Payment method, amount      |
| **View Payment History** | Previous transactions   | Paid amount, date, method   |

#### Admin Functions

| Use Case              | Description          | Details                           |
| --------------------- | -------------------- | --------------------------------- |
| **Record Payment**    | Manual payment entry | Admin records guest payment       |
| **View All Payments** | Payment ledger       | All system payments for reporting |

**Database Table:** `payments` (reservation_id, amount, method, status)

**Payment Methods:** `credit_card`, `debit_card`, `cash`, `wallet`

**Payment States:**

- `pending` → `completed`
- `pending` → `failed`

---

### Layer 7: Guest Management (Admin Only)

| Use Case               | Description                       | Details                                   |
| ---------------------- | --------------------------------- | ----------------------------------------- |
| **Manage Guests**      | CRUD operations on guest accounts | Create, Read, Update, Delete              |
| **View Guest Details** | Customer profile information      | Name, email, phone, segment, reservations |

**Database Table:** `users` (with role = 'guest')

---

### Layer 8: Data Analysis & Reporting (Admin Only)

| Use Case                      | Description                   | Algorithm                       |
| ----------------------------- | ----------------------------- | ------------------------------- |
| **Admin Dashboard & Reports** | System overview and analytics | Aggregated data from all tables |
| **Guest Segmentation**        | Categorize guests by behavior | Custom segmentation algorithm   |

**Data Sources:** All tables aggregated

---

## Data Flow & Relationships

### Entity Relationships (Cardinality)

```
USERS
  ├── 1:N → RESERVATIONS
  └── (1:N → FOOD_ORDERS via RESERVATIONS)

ROOMS
  └── 1:N → RESERVATIONS

RESERVATIONS
  ├── N:1 ← USERS
  ├── N:1 ← ROOMS
  ├── 1:N → FOOD_ORDERS
  └── 1:N → PAYMENTS

FOODS
  └── 1:N → FOOD_ORDERS

PAYMENTS
  └── N:1 ← RESERVATIONS
```

---

## Key Algorithms & Business Logic

### 1. **Interval Overlap Detection**

Used in: **Book a Room** use case

**Formula:**

```
Two intervals [A_start, A_end] and [B_start, B_end] overlap if:
    A_start < B_end  AND  B_start < A_end
```

**Time Complexity:** O(n) where n = active reservations for the room

---

### 2. **Search, Filter & Sort**

Used in: **Search Rooms**, **Filter by Price/Type** use cases

**Criteria:**

1. Room type (exact match)
2. Price range (min/max bounds)
3. Capacity (minimum guests)
4. Sort by: price, capacity, or room_number

---

### 3. **Guest Segmentation**

Used in: **Guest Segmentation** use case (admin)

**Purpose:** Categorize guests based on behavior for marketing and service optimization

---

## System Routes Summary

### Public Routes

- `GET /` - Home page

### Authentication Routes

- `POST /login` - User login
- `POST /register` - User registration
- `POST /logout` - User logout

### Guest Routes (Protected by `auth` middleware)

- `GET /guest/dashboard` - Guest dashboard
- `GET /guest/rooms` - View available rooms
- `POST /guest/book` - Create reservation
- `GET /guest/reservation/{id}` - View reservation details
- `GET /guest/menu` - View food menu
- `POST /guest/order-food` - Order food

### Admin Routes (Protected by `auth` + `admin` middleware)

- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/rooms` - Manage rooms
- `GET /admin/reservations` - Manage reservations
- `PATCH /admin/reservations/{id}/status` - Update reservation status
- `POST /admin/reservations/{id}/pay` - Record payment
- `GET /admin/guests` - Manage guests
- `GET /admin/foods` - Manage food menu
- `GET /admin/food-orders` - View food orders
- `PATCH /admin/food-orders/{id}` - Update order status
- `GET /admin/payments` - View all payments

---

## Security & Access Control

### Middleware Layers

1. **guest** - Only unauthenticated users
2. **auth** - Only authenticated users
3. **admin** - Only authenticated users with role = 'admin'

### Permission Model

- **Unauthenticated:** Register, Login, Home
- **Authenticated Guest:** Profile, Dashboard, Rooms, Reservations, Food Menu, Payments
- **Admin:** All data management, reporting, guest management

---

## Validation & Business Rules

### Room Booking Validation

- Check room availability (interval overlap detection)
- Validate check-in < check-out dates
- Verify guest count ≤ room capacity

### Payment Validation

- Amount > 0
- Valid payment method
- Linked to valid reservation

### Food Order Validation

- Must have active reservation
- Food item must be available
- Quantity > 0

---

## Future Extensions

Potential additional use cases for future versions:

1. **Cancellation with Refund Processing**
2. **Multi-language Support**
3. **Email Notifications**
4. **SMS Reminders**
5. **Loyalty Program**
6. **Rating & Review System**
7. **Room Service Management**
8. **Housekeeping Management**
9. **Revenue Analytics**
10. **Booking Analytics**

---

## Summarized Database Tables

| Table            | Fields                                                                  | Purpose                     |
| ---------------- | ----------------------------------------------------------------------- | --------------------------- |
| **users**        | id, name, email, password, phone, role, segment                         | User accounts (admin/guest) |
| **rooms**        | id, room_number, type, capacity, price_per_night, status, floor         | Hotel inventory             |
| **reservations** | id, user_id, room_id, check_in, check_out, guests, status, total_amount | Bookings                    |
| **foods**        | id, name, category, price, available                                    | Menu items                  |
| **food_orders**  | id, reservation_id, food_id, quantity, total_price, status              | Guest orders                |
| **payments**     | id, reservation_id, amount, method, status                              | Payment records             |

---

**Generated From:** Database schema analysis, Route structure analysis, Model relationships analysis
**Date Generated:** 2024
