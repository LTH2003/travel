<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogCommentController;
use App\Http\Controllers\Admin\HotelController as AdminHotelController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;

Route::get('/', function () {
    return view('admin.auth.login');
});

// ===== LOGIN ROUTES (không cần auth) =====
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.store');
});

// ===== PROTECTED ADMIN ROUTES (cần auth + admin/tour_manager/hotel_manager role) =====
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin_or_manager'])->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Users management (chỉ admin)
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Tours management (admin và tour_manager)
    Route::middleware('admin_or_manager')->group(function () {
        Route::resource('tours', TourController::class);
    });

    // Blogs management (chỉ admin)
    Route::middleware('admin')->group(function () {
        Route::resource('blogs', BlogController::class);
        Route::resource('blog-comments', BlogCommentController::class);
        Route::post('blog-comments/approve-bulk', [BlogCommentController::class, 'approveBulk'])->name('blog-comments.approve-bulk');
        Route::post('blog-comments/reject-bulk', [BlogCommentController::class, 'rejectBulk'])->name('blog-comments.reject-bulk');
        Route::post('blog-comments/delete-bulk', [BlogCommentController::class, 'deleteBulk'])->name('blog-comments.delete-bulk');
    });

    // Hotels management (admin và hotel_manager)
    Route::middleware('admin_or_manager')->group(function () {
        Route::resource('hotels', AdminHotelController::class);
    });

    // Rooms management (admin và hotel_manager)
    Route::middleware('admin_or_manager')->group(function () {
        Route::resource('hotels.rooms', AdminRoomController::class);
    });

    // Bookings management (chỉ admin)
    Route::middleware('admin')->group(function () {
        Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/{id}', [AdminBookingController::class, 'show'])->name('bookings.show');
        Route::put('bookings/{id}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.updateStatus');
        Route::delete('bookings/{id}', [AdminBookingController::class, 'destroy'])->name('bookings.destroy');
    });

    // Contacts management (chỉ admin)
    Route::middleware('admin')->group(function () {
        Route::resource('contacts', AdminContactController::class);
        Route::post('contacts/{contact}/send-cancellation-email', [AdminContactController::class, 'sendCancellationEmail'])->name('contacts.send-cancellation-email');
        Route::post('contacts/{contact}/send-reply-email', [AdminContactController::class, 'sendReplyEmail'])->name('contacts.send-reply-email');
    });
});

