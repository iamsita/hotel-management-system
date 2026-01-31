<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\FoodOrderManagementController;
use App\Http\Controllers\GuestAuthController;
use App\Http\Controllers\GuestBookingController;
use App\Http\Controllers\GuestCleaningRequestController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\GuestDashboardController;
use App\Http\Controllers\GuestFoodOrderController;
use App\Http\Controllers\GuestPaymentController;
use App\Http\Controllers\HousekeepingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentManagementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use App\Http\Middleware\UserTypeMiddleware;
use App\Models\CleaningRequest;
use App\Models\FoodOrder;
use App\Models\Guest;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// ==================== GUEST ROUTES (No Auth Required) ====================
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::prefix('guest')->name('guest.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('dashboard', [GuestDashboardController::class, 'index'])->name('dashboard');
        Route::get('profile', [GuestDashboardController::class, 'profile'])->name('profile');
        Route::post('profile', [GuestDashboardController::class, 'updateProfile'])->name('profile.update');

        Route::get('booking', [GuestBookingController::class, 'showBooking'])->name('booking.create');
        Route::post('booking/check-availability', [GuestBookingController::class, 'checkAvailability'])->name('booking.check-availability');
        Route::post('booking', [GuestBookingController::class, 'store'])->name('booking.store');
        Route::get('bookings', [GuestBookingController::class, 'myBookings'])->name('bookings');
        Route::get('booking/{reservation}', [GuestBookingController::class, 'show'])->name('booking.show');

        Route::get('food/menu', [GuestFoodOrderController::class, 'showMenu'])->name('food.menu');
        Route::post('food/order', [GuestFoodOrderController::class, 'placeOrder'])->name('food.order');
        Route::get('orders', [GuestFoodOrderController::class, 'orderHistory'])->name('orders');
        Route::post('orders/{order}/cancel', [GuestFoodOrderController::class, 'cancelOrder'])->name('orders.cancel');

        Route::get('cleaning', [GuestCleaningRequestController::class, 'create'])->name('cleaning.create');
        Route::post('cleaning', [GuestCleaningRequestController::class, 'store'])->name('cleaning.store');
        Route::get('cleaning-requests', [GuestCleaningRequestController::class, 'myRequests'])->name('cleaning.requests');

        Route::get('payment', [GuestPaymentController::class, 'showPaymentForm'])->name('payment.create');
        Route::post('payment', [GuestPaymentController::class, 'processPayment'])->name('payment.process');
        Route::get('payment/{payment}/receipt', [GuestPaymentController::class, 'receipt'])->name('payment.receipt');
        Route::get('payments', [GuestPaymentController::class, 'history'])->name('payment.history');

        Route::post('logout', [GuestAuthController::class, 'logout'])->name('logout');
    });

    Route::middleware(UserTypeMiddleware::class.':guest')->group(function () {
        Route::get('register', [GuestAuthController::class, 'showRegister'])->name('register');
        Route::post('register', [GuestAuthController::class, 'register'])->name('register.store');
        Route::get('login', [GuestAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [GuestAuthController::class, 'login'])->name('login.store');
    });
});

// ==================== HMS STAFF ROUTES ====================
Route::middleware('auth.staff')->group(function () {
    Route::get('staff/dashboard', function () {
        // Show staff dashboard
        return view('staff.dashboard', [
            'totalGuests' => User::where('type', 'guest')->count(),
            'checkedIn' => Reservation::where('status', 'checked_in')->count(),
            'pendingOrders' => FoodOrder::where('status', '!=', 'delivered')->count(),
            'totalRevenue' => Payment::where('status', 'completed')->sum('amount'),
            'recentBookings' => Reservation::orderBy('check_in_date', 'desc')->limit(5)->get(),
            'pendingCleaning' => CleaningRequest::where('status', '!=', 'completed')->limit(5)->get(),
        ]);
    })->name('staff.dashboard');

    // Auth
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('staff')->name('staff.')->group(function () {
        // Rooms Management
        Route::resource('rooms', RoomController::class);
        Route::patch('rooms/{room}/update-status', [RoomController::class, 'updateStatus'])->name('rooms.update-status');
        Route::get('api/rooms/status', [RoomController::class, 'getRoomStatus'])->name('rooms.status');

        // Guest Management
        Route::resource('guests', GuestController::class);

        // Reservations Management
        Route::resource('reservations', ReservationController::class);
        Route::post('reservations/{reservation}/check-in', [ReservationController::class, 'checkIn'])->name('reservations.check-in');
        Route::post('reservations/{reservation}/check-out', [ReservationController::class, 'checkOut'])->name('reservations.check-out');
        Route::get('api/reservations/available-rooms', [ReservationController::class, 'getAvailableRooms'])->name('reservations.available-rooms');

        // Housekeeping Management
        Route::resource('housekeeping', HousekeepingController::class);
        Route::patch('housekeeping/{task}/update-status', [HousekeepingController::class, 'updateStatus'])->name('housekeeping.update-status');
        Route::get('api/housekeeping/room-status', [HousekeepingController::class, 'getRoomStatus'])->name('housekeeping.room-status');

        // Charges Management
        Route::post('charges', [ChargeController::class, 'store'])->name('charges.store');
        Route::get('api/charges/{reservation}', [ChargeController::class, 'getReservationCharges'])->name('charges.get');
        Route::patch('charges/{charge}/mark-paid', [ChargeController::class, 'markAsPaid'])->name('charges.mark-paid');
        Route::delete('charges/{charge}', [ChargeController::class, 'destroy'])->name('charges.destroy');

        // Invoices Management
        Route::resource('invoices', InvoiceController::class);
        Route::post('invoices/{invoice}/mark-sent', [InvoiceController::class, 'markAsSent'])->name('invoices.mark-sent');
        Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'generatePDF'])->name('invoices.pdf');
        Route::post('invoices/{invoice}/email', [InvoiceController::class, 'email'])->name('invoices.email');

        // Food Management
        Route::resource('food', FoodController::class);
        Route::get('food-orders', [FoodOrderManagementController::class, 'index'])->name('food-orders.index');
        Route::get('food-orders/{order}', [FoodOrderManagementController::class, 'show'])->name('food-orders.show');
        Route::put('food-orders/{order}/status', [FoodOrderManagementController::class, 'updateStatus'])->name('food-orders.update-status');

        // Payment Management
        Route::get('payments', [PaymentManagementController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [PaymentManagementController::class, 'show'])->name('payments.show');
        Route::put('payments/{payment}/status', [PaymentManagementController::class, 'updateStatus'])->name('payments.update-status');
        Route::get('payments-report', [PaymentManagementController::class, 'report'])->name('payments.report');

        // Reports
        Route::get('reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');
        Route::get('reports/occupancy', [ReportController::class, 'occupancyReport'])->name('reports.occupancy');
        Route::get('reports/revenue', [ReportController::class, 'revenueReport'])->name('reports.revenue');
        Route::get('reports/guests', [ReportController::class, 'guestReport'])->name('reports.guests');
        Route::get('reports/food', [ReportController::class, 'foodReport'])->name('reports.food');
    });
});

// ==================== AUTH ROUTES ====================
Route::middleware(UserTypeMiddleware::class)->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.store');
});

Route::get('reports/services', [ReportController::class, 'serviceReport'])->name('reports.services');
