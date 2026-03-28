<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\FoodOrderController;
use App\Http\Controllers\GuestBookingController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
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

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    Route::resource('rooms', RoomController::class);
    Route::resource('reservations', ReservationController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::patch('reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservations.update-status');
    Route::post('reservations/{reservation}/check-in', [ReservationController::class, 'checkIn'])->name('reservations.checkin');
    Route::post('reservations/{reservation}/check-out', [ReservationController::class, 'checkOut'])->name('reservations.checkout');

    Route::get('guests', [GuestController::class, 'index'])->name('guests.index');

    Route::resource('foods', FoodController::class);

    Route::get('food-orders', [FoodOrderController::class, 'index'])->name('food-orders.index');
    Route::patch('food-orders/{order}/{status}', [FoodOrderController::class, 'updateStatus'])->name('food-orders.status');

    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
});

// Guest Routes
Route::middleware('auth')->prefix('guest')->name('guest.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'guest'])->name('dashboard');
    Route::get('rooms', [GuestBookingController::class, 'rooms'])->name('rooms');
    Route::post('book', [GuestBookingController::class, 'book'])->name('book');
    Route::get('menu', [GuestBookingController::class, 'menu'])->name('menu');
    Route::post('order-food', [GuestBookingController::class, 'orderFood'])->name('order-food');
    Route::post('pay', [GuestBookingController::class, 'pay'])->name('pay');
});
