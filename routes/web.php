<?php

use App\Http\Controllers\ChargeController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HousekeepingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ReportController::class, 'dashboard'])->name('dashboard');

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

// Reports
Route::get('reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');
Route::get('reports/occupancy', [ReportController::class, 'occupancyReport'])->name('reports.occupancy');
Route::get('reports/revenue', [ReportController::class, 'revenueReport'])->name('reports.revenue');
Route::get('reports/guests', [ReportController::class, 'guestReport'])->name('reports.guests');
Route::get('reports/services', [ReportController::class, 'serviceReport'])->name('reports.services');
