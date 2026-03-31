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
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::get('edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    Route::resource('rooms', RoomController::class);
    Route::resource('reservations', ReservationController::class);
    Route::patch('reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservations.update-status');
    Route::post('reservations/{reservation}/pay', [ReservationController::class, 'recordPayment'])->name('reservations.pay');

    Route::get('guests', [GuestController::class, 'index'])->name('guests.index');
    Route::get('guests/create', [GuestController::class, 'create'])->name('guests.create');
    Route::post('guests', [GuestController::class, 'store'])->name('guests.store');

    Route::resource('foods', FoodController::class);

    Route::get('food-orders', [FoodOrderController::class, 'index'])->name('food-orders.index');
    Route::patch('food-orders/{order}/{status}', [FoodOrderController::class, 'updateStatus'])->name('food-orders.status');

    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');

    Route::get('algorithms', [AlgorithmDemoController::class, 'index'])->name('algorithms.index');
    Route::post('algorithms/segment', function () {
        \Artisan::call('guests:segment');
        return back()->with('success', 'Guest segmentation re-run successfully.');
    })->name('algorithms.segment');
});

// Guest Routes
Route::middleware('auth')->prefix('guest')->name('guest.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'guest'])->name('dashboard');
    Route::get('rooms', [GuestBookingController::class, 'rooms'])->name('rooms');
    Route::post('book', [GuestBookingController::class, 'book'])->name('book');
    Route::get('reservation/{reservation}', [GuestBookingController::class, 'show'])->name('reservation.show');
    Route::get('menu', [GuestBookingController::class, 'menu'])->name('menu');
    Route::post('order-food', [GuestBookingController::class, 'orderFood'])->name('order-food');
});
