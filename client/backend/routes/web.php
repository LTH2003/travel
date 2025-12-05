<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\HotelController as AdminHotelController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;

Route::get('/', function () {
    return view('admin.auth.login');
});

// ===== ADMIN ROUTES =====
// Login routes (không cần auth)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.store');
});

// Protected admin routes (cần auth và role=admin)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Users management
    Route::resource('users', UserController::class);

    // Tours management
    Route::resource('tours', TourController::class);

    // Blogs management
    Route::resource('blogs', BlogController::class);

    // Hotels management
    Route::resource('hotels', AdminHotelController::class);

    // Rooms management
    Route::resource('hotels.rooms', AdminRoomController::class);

    // Bookings management
    Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{id}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::put('bookings/{id}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.updateStatus');
});


