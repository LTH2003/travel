<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\BlogCommentController;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\RecommendationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\BookingManagementController;
use App\Http\Controllers\Api\ContactController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Các route này sẽ được tự động gắn prefix `/api` 
| (VD: http://127.0.0.1:8000/api/login)
| và sẽ qua middleware "api" (được cấu hình trong Kernel.php)
|--------------------------------------------------------------------------
*/

// 🌐 Public routes (không cần token)
Route::get('/blog', [BlogController::class, 'index']);
Route::get('/blog/{id}', [BlogController::class, 'show']);
Route::post('/blog/{id}/increment-view', [BlogController::class, 'incrementView']); // 📈 Tăng view
Route::post('/blog/slug/{slug}/increment-view', [BlogController::class, 'incrementViewBySlug']); // 📈 Tăng view theo slug

// 📝 Blog Comments routes (public GET, protected POST/PUT/DELETE)
Route::get('/blog-comments/{blogId}', [BlogCommentController::class, 'getComments']);
Route::get('/blog-comments/slug/{slug}', [BlogCommentController::class, 'getCommentsBySlug']);

Route::get('/tours', [TourController::class, 'index']);
Route::get('/tours/{id}', [TourController::class, 'show']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 2FA routes (public)
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/auth/resend-otp', [AuthController::class, 'resendOtp']);

// Note: Development-only debug routes (test-token, debug-order) removed.
// These were gated by APP_DEBUG and have been deleted to clean the codebase.

// 📧 Contact routes (requires authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/contacts', [ContactController::class, 'store']);
});


// 🔐 Protected routes (cần đăng nhập)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/profile', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    
    // 2FA routes (protected)
    Route::post('/auth/enable-2fa', [AuthController::class, 'enableTwoFactor']);
    Route::post('/auth/confirm-2fa', [AuthController::class, 'confirmTwoFactor']);
    Route::post('/auth/disable-2fa', [AuthController::class, 'disableTwoFactor']);
    
    // Cart routes
    Route::get('/cart', [CartController::class, 'show']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::delete('/cart', [CartController::class, 'destroy']);
    
    // Favorite routes
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites', [FavoriteController::class, 'destroy']);
    Route::post('/favorites/check', [FavoriteController::class, 'check']);
    
    // 📝 Blog Comments routes (protected)
    Route::post('/blog-comments/{blogId}', [BlogCommentController::class, 'store']);
    Route::put('/blog-comments/{commentId}', [BlogCommentController::class, 'update']);
    Route::delete('/blog-comments/{commentId}', [BlogCommentController::class, 'destroy']);
    
    // Recommendation routes
    Route::get('/recommendations', [RecommendationController::class, 'getRecommendations']);
    
    // 💳 Payment routes
    Route::post('/orders', [PaymentController::class, 'createOrder']); // ✅ Tạo đơn hàng (cần auth)
    Route::get('/checkout-info', [PaymentController::class, 'getCheckoutInfo']);
    Route::get('/payment-methods', [PaymentController::class, 'getPaymentMethods']);
    
    // MoMo
        // POST-only API. Add GET handler to return JSON 405 for accidental browser visits.
        Route::get('/payment/momo/initiate', function () {
            return response()->json([
                'status' => false,
                'message' => 'Method Not Allowed. Use POST to initiate payment.',
            ], 405);
        });
        Route::post('/payment/momo/initiate', [PaymentController::class, 'initiateMoMoPayment']);
    
    // VietQR
    // Add GET handlers to return JSON 405 for accidental browser navigation
    Route::get('/payment/vietqr/initiate', function () {
        return response()->json([
            'status' => false,
            'message' => 'Method Not Allowed. Use POST to initiate VietQR payment.',
        ], 405);
    });
    Route::post('/payment/vietqr/initiate', [PaymentController::class, 'initiateVietQRPayment']);
    // Server-side proxy to fetch QR image (avoids CORS/404 for client)
    Route::get('/payment/vietqr/proxy/{orderId}', function ($orderId, Request $request) {
        // Require auth and owner check
        $user = $request->user();
        $order = \App\Models\Order::find($orderId);
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }
        if ($order->user_id !== $user->id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        // Use the VietQR service to build the qrUrl
        $vietqr = app(\App\Services\VietQRPaymentService::class);
        $qrResult = $vietqr->generateQRCode($order->order_code, $order->total_amount, 'Thanh toán don hang ' . $order->order_code);

        if (empty($qrResult['qrUrl'])) {
            return response()->json(['status' => false, 'message' => 'QR URL not available'], 422);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::get($qrResult['qrUrl']);
            if ($response->status() !== 200) {
                return response()->json(['status' => false, 'message' => 'Failed to fetch QR image', 'status_code' => $response->status()], 422);
            }

            $contentType = $response->header('Content-Type') ?? 'image/png';
            return response($response->body(), 200)->header('Content-Type', $contentType);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    });
    // Server-side generator: create QR image via api.qrserver.com and return binary
    Route::get('/payment/vietqr/image/{orderId}', function ($orderId, Request $request) {
        $user = $request->user();
        $order = \App\Models\Order::find($orderId);
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }
        if ($order->user_id !== $user->id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $vietqr = app(\App\Services\VietQRPaymentService::class);
        $qrResult = $vietqr->generateQRCode($order->order_code, $order->total_amount, 'Thanh toán don hang ' . $order->order_code);

        // Choose data to encode: prefer EMV payload (emvPayload) for bank apps,
        // otherwise fall back to qrUrl or encoded qrData JSON
        $dataToEncode = $qrResult['emvPayload'] ?? $qrResult['qrUrl'] ?? json_encode($qrResult['qrData'] ?? $qrResult);

        try {
            // Generate PNG locally using Endroid
            $payload = $dataToEncode;
            $builder = \Endroid\QrCode\Builder\Builder::create()
                ->data($payload)
                ->size(400)
                ->margin(0)
                ->build();

            $png = $builder->getString();
            return response($png, 200)->header('Content-Type', 'image/png');
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    });
    // Server-side Data-URL generator: return JSON with base64 data URI for frontend embedding
    Route::get('/payment/vietqr/datauri/{orderId}', function ($orderId, Request $request) {
        $user = $request->user();
        $order = \App\Models\Order::find($orderId);
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }
        if ($order->user_id !== $user->id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $vietqr = app(\App\Services\VietQRPaymentService::class);
        $qrResult = $vietqr->generateQRCode($order->order_code, $order->total_amount, 'Thanh toán don hang ' . $order->order_code);

        if (empty($qrResult['qrUrl'])) {
            return response()->json(['status' => false, 'message' => 'QR URL not available'], 422);
        }

        try {
            // Generate QR locally using endroid/qr-code
            // Prefer EMV payload if available so bank apps receive correct format
            $payload = $qrResult['emvPayload'] ?? $qrResult['qrUrl'];
            $builder = \Endroid\QrCode\Builder\Builder::create()
                ->data($payload)
                ->size(400)
                ->margin(0)
                ->build();

            $png = $builder->getString();
            $contentType = 'image/png';
            $dataUri = 'data:' . $contentType . ';base64,' . base64_encode($png);

            return response()->json([
                'status' => true,
                'dataUri' => $dataUri,
                'qrData' => $qrResult,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    });
    // Server-side QuickLink generator (builds img.vietqr.io URL and returns dataUri)
    Route::get('/payment/vietqr/quicklink/{orderId}', function ($orderId, Request $request) {
        $user = $request->user();
        $order = \App\Models\Order::find($orderId);
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }
        if ($order->user_id !== $user->id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $vietqr = app(\App\Services\VietQRPaymentService::class);
        $qrResult = $vietqr->generateQRCode($order->order_code, $order->total_amount, 'Thanh toán don hang ' . $order->order_code);

        // Build QuickLink URL using available data. Note: ensure your config provides the correct bank identifier expected by vietqr (e.g., numeric code like 970415)
        $bankId = $qrResult['bankCode'] ?? $vietqr->getBankInfo()['bankCode'] ?? '';
        $accountNo = $qrResult['accountNumber'] ?? $vietqr->getBankInfo()['accountNumber'] ?? '';
        $template = 'compact2';
        $amount = (int) $order->total_amount;
        $description = substr('Thanh toán don hang ' . $order->order_code, 0, 120);
        $accountName = $qrResult['accountName'] ?? $vietqr->getBankInfo()['accountName'] ?? '';

        if (empty($bankId) || empty($accountNo)) {
            return response()->json(['status' => false, 'message' => 'Insufficient bank info for QuickLink'], 422);
        }

        $base = "https://img.vietqr.io/image/" . rawurlencode($bankId) . "-" . rawurlencode($accountNo) . "-" . rawurlencode($template) . ".jpg";
        $params = [];
        if (!empty($amount)) $params['amount'] = (string)$amount;
        if (!empty($description)) $params['addInfo'] = $description;
        if (!empty($accountName)) $params['accountName'] = $accountName;

        $quicklinkUrl = $base . (empty($params) ? '' : ('?' . http_build_query($params)));

        try {
            $response = \Illuminate\Support\Facades\Http::get($quicklinkUrl);
            if ($response->status() !== 200) {
                return response()->json(['status' => false, 'message' => 'Failed to fetch QuickLink image', 'status_code' => $response->status()], 422);
            }

            $contentType = $response->header('Content-Type') ?? 'image/png';
            $body = $response->body();
            $dataUri = 'data:' . $contentType . ';base64,' . base64_encode($body);

            return response()->json([
                'status' => true,
                'quicklinkUrl' => $quicklinkUrl,
                'dataUri' => $dataUri,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    });
    // Server-side payload endpoint: return a payload string that frontend can encode into a QR
    Route::get('/payment/vietqr/payload/{orderId}', function ($orderId, Request $request) {
        $user = $request->user();
        $order = \App\Models\Order::find($orderId);
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }
        if ($order->user_id !== $user->id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $vietqr = app(\App\Services\VietQRPaymentService::class);
        $qrResult = $vietqr->generateQRCode($order->order_code, $order->total_amount, 'Thanh toán don hang ' . $order->order_code);

        if (empty($qrResult['qrUrl']) && empty($qrResult['qrData'])) {
            return response()->json(['status' => false, 'message' => 'QR data not available'], 422);
        }

        // Prefer returning a compact payload string (EMV payload) for client-side QR generation
        $payload = $qrResult['emvPayload'] ?? $qrResult['qrUrl'] ?? json_encode($qrResult['qrData']);

        return response()->json([
            'status' => true,
            'payload' => $payload,
            'qrData' => $qrResult,
        ]);
    });
    Route::get('/payment/vietqr/verify', function () {
        return response()->json([
            'status' => false,
            'message' => 'Method Not Allowed. Use POST to verify VietQR payment.',
        ], 405);
    });
    Route::post('/payment/vietqr/verify', [PaymentController::class, 'verifyVietQRPayment']);
    
    // ZaloPay
    Route::get('/payment/zalopay/initiate', function () {
        return response()->json([
            'status' => false,
            'message' => 'Method Not Allowed. Use POST to initiate ZaloPay payment.',
        ], 405);
    });
    Route::post('/payment/zalopay/initiate', [PaymentController::class, 'initiateZaloPayPayment']);
    Route::get('/payment/zalopay/quicklink/{orderId}', [PaymentController::class, 'initiateZaloPayPayment']);
    
    // Orders
    Route::get('/orders/{orderId}', [PaymentController::class, 'getOrder']);
    Route::get('/orders', [PaymentController::class, 'getUserOrders']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('users', UserController::class);
});

Route::get('/hotels', [HotelController::class, 'index']);
Route::get('/hotels/{id}', [HotelController::class, 'show']);

Route::get('/rooms/{id}', [RoomController::class, 'show']);

// 🔐 Public Payment Callbacks (Webhooks)
Route::post('/payment/momo/callback', [PaymentController::class, 'momoCallback']);
Route::post('/payment/zalopay/callback', [PaymentController::class, 'zalopayCallback']);

// 🔐 Admin routes for Hotels and Rooms
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Hotel admin routes
    Route::post('/hotels', [HotelController::class, 'store']);
    Route::put('/hotels/{id}', [HotelController::class, 'update']);
    Route::delete('/hotels/{id}', [HotelController::class, 'destroy']);

    // Room admin routes
    Route::get('/hotels/{hotelId}/rooms', [RoomController::class, 'indexByHotel']);
    Route::post('/hotels/{hotelId}/rooms', [RoomController::class, 'store']);
    Route::put('/hotels/{hotelId}/rooms/{roomId}', [RoomController::class, 'update']);
    Route::delete('/hotels/{hotelId}/rooms/{roomId}', [RoomController::class, 'destroy']);
});

// 📦 Booking routes (authenticated users)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/bookings/complete/{orderId}', [BookingController::class, 'completeBooking']);
    Route::get('/bookings/history', [BookingController::class, 'getPurchaseHistory']);
    Route::get('/bookings/history/{purchaseId}', [BookingController::class, 'getPurchaseDetail']);
    Route::get('/bookings/all', [BookingController::class, 'getAllBookings']); // Get all orders (pending + completed)
});

// 📧 Contact management (admin only)
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/contacts', [ContactController::class, 'index']);
    Route::get('/admin/contacts/{contact}', [ContactController::class, 'show']);
    Route::put('/admin/contacts/{contact}', [ContactController::class, 'update']);
    Route::delete('/admin/contacts/{contact}', [ContactController::class, 'destroy']);
});

// 📊 Booking management (admin only)
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/bookings', [App\Http\Controllers\Admin\BookingController::class, 'index']);
    Route::get('/admin/bookings/{id}', [App\Http\Controllers\Admin\BookingController::class, 'show']);
    Route::put('/admin/bookings/{id}/status', [App\Http\Controllers\Admin\BookingController::class, 'updateStatus']);
    Route::put('/admin/bookings/{id}/notes', [App\Http\Controllers\Admin\BookingController::class, 'updateNotes']);
    Route::get('/admin/bookings-stats', [App\Http\Controllers\Admin\BookingController::class, 'statistics']);
    Route::get('/admin/bookings-export', [App\Http\Controllers\Admin\BookingController::class, 'export']);
});

// 🔓 Public booking verification (no auth needed)
Route::get('/bookings/verify/{orderId}', [BookingController::class, 'verifyBooking']);

