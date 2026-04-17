<?php

use App\Http\Controllers\AlgorithmDemoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\FoodOrderController;
use App\Http\Controllers\GuestBookingController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Auth (guests only)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Profile Routes (all authenticated users)
Route::middleware('auth')->name('profile.')->group(function () {
    Route::get('profile', [ProfileController::class, 'show'])->name('show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('update');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('admin/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    // Rooms
    Route::get('admin/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('admin/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('admin/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('admin/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::get('admin/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::put('admin/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('admin/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

    // Reservations
    Route::get('admin/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('admin/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('admin/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('admin/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::get('admin/reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
    Route::put('admin/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
    Route::delete('admin/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
    Route::patch('admin/reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservations.update-status');
    Route::post('admin/reservations/{reservation}/pay', [ReservationController::class, 'recordPayment'])->name('reservations.pay');
    Route::get('admin/reservations/{reservation}/invoice', [ReservationController::class, 'generateInvoice'])->name('reservations.generate-invoice');

    // Guests
    Route::get('admin/guests', [GuestController::class, 'index'])->name('guests.index');
    Route::get('admin/guests/create', [GuestController::class, 'create'])->name('guests.create');
    Route::post('admin/guests', [GuestController::class, 'store'])->name('guests.store');

    // Foods
    Route::get('admin/foods', [FoodController::class, 'index'])->name('foods.index');
    Route::get('admin/foods/create', [FoodController::class, 'create'])->name('foods.create');
    Route::post('admin/foods', [FoodController::class, 'store'])->name('foods.store');
    Route::get('admin/foods/{food}', [FoodController::class, 'show'])->name('foods.show');
    Route::get('admin/foods/{food}/edit', [FoodController::class, 'edit'])->name('foods.edit');
    Route::put('admin/foods/{food}', [FoodController::class, 'update'])->name('foods.update');
    Route::delete('admin/foods/{food}', [FoodController::class, 'destroy'])->name('foods.destroy');

    Route::get('admin/food-orders', [FoodOrderController::class, 'index'])->name('food-orders.index');
    Route::patch('admin/food-orders/{order}/{status}', [FoodOrderController::class, 'updateStatus'])->name('food-orders.status');

    Route::get('admin/payments', [PaymentController::class, 'index'])->name('payments.index');

    Route::get('admin/algorithms', [AlgorithmDemoController::class, 'index'])->name('algorithms.index');
    Route::post('admin/algorithms/segment', function () {
        Artisan::call('guests:segment');

        return back()->with('success', 'Guest segmentation re-run successfully.');
    })->name('algorithms.segment');
});

// Guest Routes
Route::middleware('auth')->name('guest.')->group(function () {
    Route::get('guest/dashboard', [DashboardController::class, 'guest'])->name('dashboard');
    Route::get('guest/rooms', [GuestBookingController::class, 'rooms'])->name('rooms');
    Route::post('guest/book', [GuestBookingController::class, 'book'])->name('book');
    Route::get('guest/reservation/{reservation}', [GuestBookingController::class, 'show'])->name('reservation.show');
    Route::get('guest/menu', [GuestBookingController::class, 'menu'])->name('menu');
    Route::post('guest/order-food', [GuestBookingController::class, 'orderFood'])->name('order-food');
});
