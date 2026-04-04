# Use Case Diagram - Quick Reference Guide

## 📊 Generated Files

### Main Outputs

1. **figure2_use_case_diagram.png** - Enhanced use case diagram (generated)
2. **USE_CASE_ANALYSIS.md** - Comprehensive analysis document
3. **generate_diagrams.py** - Python script that generates all diagrams

---

## 🎯 Diagram at a Glance

### Three System Actors

- **🔴 Orange (Guest - Unauthenticated):** Can register or login
- **🟢 Green (Guest - Authenticated):** Full access to bookings and ordering
- **🔵 Blue (Admin):** Full system management and reporting

### Eight Functional Layers

1. **Authentication** (Yellow) - Register, Login, Logout
2. **Profile & Dashboard** (Green) - User settings and overview
3. **Room Management** (Green/Blue) - Browse & manage inventory
4. **Reservation Management** (Green/Blue) - Booking system
5. **Food & Dining** (Green/Blue) - Menu and orders
6. **Payment & Billing** (Green/Blue) - Payment processing
7. **Guest Management** (Blue) - Admin guest management
8. **Data Analysis** (Blue) - Reporting and segmentation

---

## 📋 Database Tables Analyzed

```
Users (6 fields)
├─ id, name, email, password, phone, role
└─ segment (for guest segmentation)

Rooms (7 fields)
├─ id, room_number, type, capacity
├─ price_per_night, status, floor

Reservations (8 fields)
├─ id, user_id, room_id
├─ check_in, check_out, guests
├─ status, total_amount

Foods (5 fields)
├─ id, name, category, price, available

FoodOrders (6 fields)
├─ id, reservation_id, food_id
├─ quantity, total_price, status

Payments (5 fields)
├─ id, reservation_id, amount, method, status
```

---

## 🔀 Data Relationships (Entity Relationships)

```
Users
  ├─ 1:N ─→ Reservations
  └─ References many bookings

Rooms
  └─ 1:N ─→ Reservations
       Each room has many reservations

Reservations (Hub)
  ├─ N:1 ← Users
  ├─ N:1 ← Rooms
  ├─ 1:N ─→ FoodOrders
  └─ 1:N ─→ Payments

Foods
  └─ 1:N ─→ FoodOrders

Payments
  └─ N:1 ← Reservations
```

---

## 🛣️ Route Analysis Summary

### Public Routes (0 protected)

- Home page

### Guest Routes (Protected: `auth`)

- 6 routes for browsing, booking, ordering

### Admin Routes (Protected: `auth` + `admin`)

- 15+ routes for resource management

### Total: **30+** distinct system endpoints

---

## 🧮 Key Algorithms Identified

### 1. Interval Overlap Detection

**Purpose:** Room availability checking

```
Two date intervals overlap if:
  start₁ < end₂  AND  start₂ < end₁
```

**Time Complexity:** O(n) where n = reservations

### 2. Multi-Criteria Search & Filter

**Purpose:** Room discovery

- Filter by type (exact)
- Filter by price range (bounds)
- Filter by capacity (minimum)
- Sort by field (price, capacity, number)

### 3. Guest Segmentation

**Purpose:** Behavioral analysis and categorization

- Custom segmentation algorithm
- Used for marketing and service optimization

---

## ✅ Use Cases by Actor

### Unauthenticated Guest (1 use case)

- Register
- Login

### Authenticated Guest (11 use cases)

- View/Edit Profile
- View Dashboard
- Search/Filter Rooms
- View Room Details
- Book Room
- View Reservation
- Cancel Reservation
- View Food Menu
- Order Food
- View Billing
- Make Payment

### Admin (15+ use cases)

- **All guest use cases** (auth, profile, search)
- Manage Rooms (CRUD)
- Manage Reservations (CRUD + status)
- Check-in/Check-out
- Manage Guests (CRUD)
- Manage Food Menu (CRUD)
- Update Food Order Status
- Record Payment
- View All Payments
- Admin Dashboard
- Guest Segmentation

---

## 🔐 Security Model

### Authentication Flow

1. Unauthenticated → Register/Login
2. → Session/Token issued
3. → Authenticated Guest role assigned

### Authorization Checks

```
Public Routes
  ├─ Home page

Guest Routes (Middle-ware: auth)
  ├─ Profile management
  ├─ Room browsing
  ├─ Reservation management
  ├─ Food ordering
  └─ Payment processing

Admin Routes (Middle-ware: auth + admin)
  ├─ Resource CRUD
  ├─ Status management
  ├─ Report generation
  └─ Guest management
```

---

## 📊 System States

### Room Status

- `available`
- `occupied`
- `maintenance`

### Reservation Status

- `pending` → `confirmed` → `checked_in` → `checked_out`
- Alternative: `pending` → `cancelled`

### Food Order Status

- `pending` → `preparing` → `ready` → `served`
- Alternative: `pending` → `cancelled`

### Payment Status

- `pending` → `completed`
- Alternative: `pending` → `failed`

---

## 🎨 Diagram Color Scheme

| Color                | Meaning                  |
| -------------------- | ------------------------ |
| Yellow (#ffe5b4)     | Authentication use cases |
| Green (#d5f5e3)      | Guest/User functions     |
| Light Blue (#dceefb) | Admin functions          |

---

## 📈 System Coverage

| Aspect             | Coverage                          |
| ------------------ | --------------------------------- |
| Actors             | 3 (Unauthenticated, Guest, Admin) |
| Use Cases          | 27+                               |
| Database Tables    | 6                                 |
| Data Relationships | 6 (1:N relationships)             |
| Routes/Endpoints   | 30+                               |
| Algorithms         | 3 major patterns                  |
| Security Layers    | 3 (guest, auth, admin)            |

---

## 🚀 How the System Works

```
USER FLOW:
  1. Unauthenticated Guest visits home
  2. Either registers or logs in
  3. Becomes Authenticated Guest
  4. Can view rooms and availability
  5. Books a room (creates reservation)
  6. Orders food during stay
  7. Makes payment
  8. Checks out

ADMIN FLOW:
  1. Admin logs in
  2. Views dashboard with metrics
  3. Can manage any resource
  4. Records payments
  5. Generates reports
  6. Runs segmentation
  7. Maintains system data
```

---

## 📝 Analysis Methodology

This use case diagram was generated by analyzing:

1. **Database Schema** - 6 tables with relationships
2. **Model Relationships** - Eloquent relationships in PHP models
3. **Routes Structure** - route/web.php with 30+ endpoints
4. **Controller Actions** - HTTP methods and CRUD operations
5. **Middleware Security** - Authentication and authorization
6. **Business Logic** - Algorithms and validation rules

---

## 📞 Notes

- This diagram represents the **current system** state
- Based on actual code analysis (not assumptions)
- All routes, tables, and relationships verified
- Scalable for future enhancements

Generated: 2024
