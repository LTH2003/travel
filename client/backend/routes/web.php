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
use App\Http\Controllers\Admin\TourReviewController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\ReceptionistController;

Route::get('/', function () {
    return view('admin.auth.login');
});

// ===== LOGIN & LOGOUT ROUTES (không cần admin_or_manager) =====
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.store');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

// ===== PROTECTED ADMIN ROUTES (cần auth + admin/tour_manager/hotel_manager role) =====
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin_or_manager'])->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
        Route::get('bookings/export-pdf', [AdminBookingController::class, 'exportPdf'])->name('bookings.exportPdf');
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

    // Tour Reviews management (chỉ admin)
    Route::middleware('admin')->group(function () {
        Route::get('tour-reviews', [TourReviewController::class, 'index'])->name('tour-reviews.index');
        Route::get('tour-reviews/{tourReview}', [TourReviewController::class, 'show'])->name('tour-reviews.show');
        Route::post('tour-reviews/{tourReview}/approve', [TourReviewController::class, 'approve'])->name('tour-reviews.approve');
        Route::post('tour-reviews/{tourReview}/reject', [TourReviewController::class, 'reject'])->name('tour-reviews.reject');
        Route::delete('tour-reviews/{tourReview}', [TourReviewController::class, 'destroy'])->name('tour-reviews.destroy');
        Route::post('tour-reviews/approve-bulk', [TourReviewController::class, 'approveBulk'])->name('tour-reviews.approve-bulk');
        Route::post('tour-reviews/reject-bulk', [TourReviewController::class, 'rejectBulk'])->name('tour-reviews.reject-bulk');
        Route::post('tour-reviews/delete-bulk', [TourReviewController::class, 'deleteBulk'])->name('tour-reviews.delete-bulk');
    });

    // Payment management (chỉ admin)
    Route::middleware('admin')->group(function () {
        Route::get('payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/export-pdf', [AdminPaymentController::class, 'exportPdf'])->name('payments.exportPdf');
        Route::get('payments/statistics', [AdminPaymentController::class, 'statistics'])->name('payments.statistics');
        Route::get('payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
        Route::post('payments/{payment}/confirm', [AdminPaymentController::class, 'confirm'])->name('payments.confirm');
        Route::post('payments/{payment}/mark-failed', [AdminPaymentController::class, 'markFailed'])->name('payments.markFailed');
        Route::delete('payments/{payment}', [AdminPaymentController::class, 'destroy'])->name('payments.destroy');
        Route::get('payments/{payment}/verify', [AdminPaymentController::class, 'verifyTransactionStatus'])->name('payments.verify');
    });
});

// ===== RECEPTIONIST ROUTES (chỉ dành cho lễ tân) =====
Route::middleware(['auth', 'receptionist'])->group(function () {
    Route::get('receptionist', [ReceptionistController::class, 'dashboard'])->name('receptionist.dashboard');
    Route::post('receptionist/check-in', [ReceptionistController::class, 'checkIn'])->name('receptionist.checkIn');
    Route::get('receptionist/checked-in-list', [ReceptionistController::class, 'getCheckedInList'])->name('receptionist.getCheckedInList');
    Route::get('receptionist/history', [ReceptionistController::class, 'history'])->name('receptionist.history');
    Route::get('receptionist/export-pdf', [ReceptionistController::class, 'exportPDF'])->name('receptionist.exportPDF');
    Route::get('receptionist/export-history-pdf', [ReceptionistController::class, 'exportHistoryPDF'])->name('receptionist.exportHistoryPDF');
});

