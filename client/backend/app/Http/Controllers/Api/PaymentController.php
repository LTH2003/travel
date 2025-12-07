<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\BookingDetail;
use App\Models\Payment;
use App\Services\MoMoPaymentService;
use App\Services\VietQRPaymentService;
use App\Services\ZaloPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $momoService;
    protected $vietqrService;
    protected $zalopayService;

    public function __construct(
        MoMoPaymentService $momoService,
        VietQRPaymentService $vietqrService,
        ZaloPayService $zalopayService
    ) {
        $this->momoService = $momoService;
        $this->vietqrService = $vietqrService;
        $this->zalopayService = $zalopayService;
    }

    /**
     * 📝 Tạo đơn hàng mới
     */
    public function createOrder(Request $request)
    {
        \DB::beginTransaction();
        try {
            // Log the incoming request for debugging
            \Log::info('createOrder request:', [
                'body' => $request->all(),
                'content_type' => $request->header('Content-Type'),
            ]);

            // Handle both JSON string and array formats for items
            $items = $request->input('items');
            if (is_string($items)) {
                $items = json_decode($items, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    \DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Items JSON không hợp lệ: ' . json_last_error_msg(),
                    ], 422);
                }
            } elseif (!is_array($items)) {
                \DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Items phải là mảng hoặc JSON string',
                ], 422);
            }

            $totalAmount = $request->input('total_amount');
            $notes = $request->input('notes');

            if (!$items || empty($items)) {
                \DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Danh sách sản phẩm không được để trống',
                ], 422);
            }

            if (!$totalAmount || $totalAmount < 1000) {
                \DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Số tiền phải lớn hơn 1000',
                ], 422);
            }

            $user = $request->user();
            if (!$user) {
                \DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Người dùng không được xác thực',
                ], 401);
            }

            $orderCode = 'ORD-' . date('YmdHis') . '-' . $user->id;

            \Log::info('Creating order with code: ' . $orderCode);

            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => $orderCode,
                'total_amount' => $totalAmount,
                'items' => $items,
                'notes' => $notes,
                'status' => 'pending',
            ]);

            \Log::info('Order created with ID: ' . $order->id);

            // Create BookingDetail records from items
            foreach ($items as $item) {
                // Convert type to proper class name (tour -> Tour, hotel -> Hotel)
                $itemType = $item['type'] ?? 'tour';
                $bookableType = ucfirst(strtolower($itemType));
                
                BookingDetail::create([
                    'order_id' => $order->id,
                    'bookable_id' => $item['id'] ?? null,
                    'bookable_type' => $bookableType,
                    'quantity' => $item['quantity'] ?? 1,
                    'price' => $item['price'] ?? 0,
                    'booking_info' => [
                        'name' => $item['name'] ?? 'Unknown',
                        'quantity' => $item['quantity'] ?? 1,
                        'price' => $item['price'] ?? 0,
                        'totalPrice' => ($item['quantity'] ?? 1) * ($item['price'] ?? 0),
                    ],
                ]);
            }

            \Log::info('BookingDetails created for order: ' . $order->id);

            \DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Tạo đơn hàng thành công',
                'order' => $order,
                'id' => $order->id,
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Order creation error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->user()->id ?? null,
                'request' => $request->all(),
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Lỗi tạo đơn hàng: ' . $e->getMessage(),
                'debug' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 422);
        }
    }

    /**
     * 👤 Lấy thông tin khách hàng để checkout
     */
    public function getCheckoutInfo(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'status' => true,
                'customer' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                    'address' => $user->address ?? '',
                    'avatar' => $user->avatar ?? '',
                    'bio' => $user->bio ?? '',
                    'created_at' => $user->created_at,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * 💳 Tạo thanh toán MoMo
     */
    public function initiateMoMoPayment(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|numeric',
        ]);

        try {
            $order = Order::find($validated['order_id']);
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Đơn hàng không tồn tại',
                    'details' => 'Order ID: ' . $validated['order_id'],
                ], 422);
            }
            
            // Kiểm tra owner
            if ($order->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không có quyền truy cập đơn hàng này',
                ], 403);
            }

            // Tạo payment record
            $transactionId = 'MOM-' . date('YmdHis') . '-' . $order->id;
            
            $payment = Payment::create([
                'order_id' => $order->id,
                'transaction_id' => $transactionId,
                'amount' => $order->total_amount,
                'payment_method' => 'momo',
                'status' => 'pending',
            ]);

            // Tạo request MoMo
            $momoResult = $this->momoService->createPayment(
                $order->id,
                $order->total_amount,
                'Thanh toán đơn hàng ' . $order->order_code
            );

            if (!$momoResult['status']) {
                return response()->json([
                    'status' => false,
                    'message' => $momoResult['message'],
                ], 422);
            }

            // Lưu request ID
            $payment->update(['request_id' => $momoResult['requestId']]);

            return response()->json([
                'status' => true,
                'message' => 'Khởi tạo thanh toán MoMo thành công',
                'payment' => $payment,
                'momoData' => $momoResult,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * 📱 Tạo thanh toán VietQR
     */
    public function initiateVietQRPayment(Request $request)
    {
        // First validate that order_id is provided
        $validated = $request->validate([
            'order_id' => 'required|numeric',
        ]);

        try {
            // Check if order exists
            $order = Order::find($validated['order_id']);
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Đơn hàng không tồn tại',
                    'details' => 'Order ID: ' . $validated['order_id'],
                ], 422);
            }
            
            // Kiểm tra owner
            if ($order->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không có quyền truy cập đơn hàng này',
                ], 403);
            }

            // Tạo payment record
            $transactionId = $this->vietqrService->generateTransactionId($order->id);
            
            $payment = Payment::create([
                'order_id' => $order->id,
                'transaction_id' => $transactionId,
                'amount' => $order->total_amount,
                'payment_method' => 'vietqr',
                'status' => 'pending',
            ]);

            // Tạo QR code
            $qrResult = $this->vietqrService->generateQRCode(
                $order->order_code,
                $order->total_amount,
                'Thanh toán don hang ' . $order->order_code
            );

            if (!$qrResult['status']) {
                return response()->json([
                    'status' => false,
                    'message' => $qrResult['message'],
                ], 422);
            }

            return response()->json([
                'status' => true,
                'message' => 'Khởi tạo thanh toán VietQR thành công',
                'payment' => $payment,
                'qrData' => $qrResult,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * ✅ MoMo Callback (Webhook)
     */
    public function momoCallback(Request $request)
    {
        try {
            $data = $request->all();
            
            // Xác minh signature
            $signature = $request->header('X-Signature');
            if (!$this->momoService->verifyCallback($signature, $data)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid signature',
                ], 403);
            }

            $payment = Payment::where('request_id', $data['requestId'])->firstOrFail();
            $order = $payment->order;

            if ($data['resultCode'] == 0) {
                // Thanh toán thành công
                $payment->update([
                    'status' => 'success',
                    'paid_at' => now(),
                    'response_data' => $data,
                ]);

                $order->update([
                    'status' => 'completed',
                    'payment_method' => 'momo',
                    'completed_at' => now(),
                ]);

                return response()->json(['status' => true]);
            } else {
                // Thanh toán thất bại
                $payment->update([
                    'status' => 'failed',
                    'error_message' => $data['message'] ?? 'Payment failed',
                    'response_data' => $data,
                ]);

                return response()->json(['status' => false]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * ✅ VietQR Manual Verification
     * Người dùng tự kiểm tra sau khi chuyển khoản
     */
    public function verifyVietQRPayment(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'transaction_ref' => 'required|string',
        ]);

        try {
            $order = Order::findOrFail($validated['order_id']);
            
            if ($order->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không có quyền truy cập đơn hàng này',
                ], 403);
            }

            $payment = $order->lastPayment;
            
            if ($payment && $payment->payment_method === 'vietqr') {
                // Cập nhật trạng thái thanh toán
                $payment->update([
                    'status' => 'success',
                    'paid_at' => now(),
                ]);

                $order->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Xác nhận thanh toán thành công',
                    'order' => $order,
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy đơn hàng chờ thanh toán',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * 💳 ZaloPay: Khởi tạo thanh toán QuickLink
     */
    public function initiateZaloPayPayment(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|numeric',
        ]);

        try {
            $order = Order::find($validated['order_id']);
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Đơn hàng không tồn tại',
                    'details' => 'Order ID: ' . $validated['order_id'],
                ], 422);
            }
            
            // Kiểm tra owner
            if ($order->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không có quyền truy cập đơn hàng này',
                ], 403);
            }

            // Tạo payment record
            $transactionId = 'ZALOPAY-' . date('YmdHis') . '-' . $order->id;
            
            $payment = Payment::create([
                'order_id' => $order->id,
                'transaction_id' => $transactionId,
                'amount' => $order->total_amount,
                'payment_method' => 'zalopay',
                'status' => 'pending',
            ]);

            // Gọi ZaloPay service để tạo order QuickLink
            $result = $this->zalopayService->createOrderQuicklink(
                $order,
                $order->total_amount,
                'Thanh toán đơn hàng ' . $order->order_code
            );

            if (!isset($result['status']) || $result['status'] !== 1) {
                return response()->json([
                    'status' => false,
                    'message' => $result['message'] ?? 'Không thể tạo đơn hàng ZaloPay',
                    'details' => $result,
                ], 422);
            }

            // Lưu apptransid để xác minh callback
            $payment->update([
                'request_id' => $result['apptransid'] ?? null,
                'response_data' => $result,
            ]);

            // Lấy QR data URI từ checkout URL (server proxy)
            $qrResult = [
                'checkoutUrl' => $result['checkouturl'] ?? '',
                'orderUrl' => $result['order_url'] ?? '',
                'zptranstoken' => $result['zptranstoken'] ?? '',
            ];

            // Cố gắng lấy QR dưới dạng data URI
            if (!empty($result['checkouturl'])) {
                try {
                    $qrDataUri = $this->zalopayService->getCheckoutQRDataUri($result['checkouturl']);
                    $qrResult['qrDataUri'] = $qrDataUri;
                } catch (\Exception $e) {
                    \Log::warning('Failed to fetch ZaloPay QR image: ' . $e->getMessage());
                    // Không fail, vẫn có checkouturl để redirect
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Khởi tạo thanh toán ZaloPay thành công',
                'payment' => $payment,
                'qrData' => $qrResult,
                'checkoutUrl' => $result['checkouturl'] ?? '',
            ]);
        } catch (\Exception $e) {
            \Log::error('ZaloPay initiation error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * ✅ ZaloPay Callback (Webhook)
     */
    public function zalopayCallback(Request $request)
    {
        try {
            $data = $request->all();
            \Log::info('ZaloPay callback received', ['data' => $data]);

            // Xác minh callback signature
            $signature = $data['mac'] ?? '';
            if (!$this->zalopayService->verifyCallback($data, $signature)) {
                \Log::warning('ZaloPay callback verification failed');
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid signature',
                ], 403);
            }

            // Tìm payment record bằng apptransid
            $apptransid = $data['apptransid'] ?? null;
            if (!$apptransid) {
                return response()->json([
                    'status' => false,
                    'message' => 'Missing apptransid',
                ], 422);
            }

            $payment = Payment::where('request_id', $apptransid)->firstOrFail();
            $order = $payment->order;

            // Kiểm tra return_code từ ZaloPay
            $returnCode = (int)($data['return_code'] ?? 0);
            
            if ($returnCode === 1) {
                // Thanh toán thành công
                $payment->update([
                    'status' => 'success',
                    'paid_at' => now(),
                    'response_data' => $data,
                ]);

                $order->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                \Log::info('ZaloPay payment success', ['order_id' => $order->id, 'payment_id' => $payment->id]);

                return response()->json([
                    'status' => true,
                    'message' => 'Xác nhận thanh toán thành công',
                ]);
            } else {
                // Thanh toán thất bại
                $payment->update([
                    'status' => 'failed',
                    'response_data' => $data,
                ]);

                \Log::warning('ZaloPay payment failed', [
                    'order_id' => $order->id,
                    'return_code' => $returnCode,
                    'data' => $data,
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Thanh toán không thành công',
                    'return_code' => $returnCode,
                ], 422);
            }
        } catch (\Exception $e) {
            \Log::error('ZaloPay callback error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * 📊 Lấy chi tiết đơn hàng
     */
    public function getOrder(Request $request, $orderId)
    {
        try {
            $order = Order::with('payments')->findOrFail($orderId);
            
            if ($order->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không có quyền truy cập đơn hàng này',
                ], 403);
            }

            return response()->json([
                'status' => true,
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * 📋 Lấy danh sách đơn hàng của user
     */
    public function getUserOrders(Request $request)
    {
        try {
            $orders = Order::where('user_id', $request->user()->id)
                ->with('payments')
                ->latest()
                ->paginate(10);

            return response()->json([
                'status' => true,
                'orders' => $orders,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * 💳 Tạo thanh toán bằng Thẻ (Stripe)
     */
    public function initiateCardPayment(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|numeric',
        ]);

        try {
            $order = Order::find($validated['order_id']);
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Đơn hàng không tồn tại',
                    'details' => 'Order ID: ' . $validated['order_id'],
                ], 422);
            }
            
            if ($order->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không có quyền truy cập đơn hàng này',
                ], 403);
            }

            // Tạo payment record
            $transactionId = 'CARD-' . date('YmdHis') . '-' . $order->id;
            
            $payment = Payment::create([
                'order_id' => $order->id,
                'transaction_id' => $transactionId,
                'amount' => $order->total_amount,
                'payment_method' => 'card',
                'status' => 'pending',
            ]);

            // Tạo Payment Intent
            $cardResult = $this->cardService->createPaymentIntent(
                $order->id,
                $order->total_amount,
                $request->user()->email,
                'Order ' . $order->order_code
            );

            if (!$cardResult['status']) {
                return response()->json([
                    'status' => false,
                    'message' => $cardResult['message'],
                ], 422);
            }

            // Lưu Stripe Payment Intent ID
            $payment->update(['request_id' => $cardResult['paymentIntentId']]);

            return response()->json([
                'status' => true,
                'message' => 'Khởi tạo thanh toán thẻ thành công',
                'payment' => $payment,
                'stripeData' => $cardResult,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * ✅ Xác nhận thanh toán thẻ (sau khi Stripe callback)
     */
    public function verifyCardPayment(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_intent_id' => 'required|string',
        ]);

        try {
            $order = Order::findOrFail($validated['order_id']);
            
            if ($order->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không có quyền truy cập đơn hàng này',
                ], 403);
            }

            $payment = $order->lastPayment;
            
            if (!$payment || $payment->payment_method !== 'card') {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy giao dịch thẻ',
                ], 404);
            }

            // Kiểm tra trạng thái Payment Intent
            $verifyResult = $this->cardService->verifyPaymentIntent($validated['payment_intent_id']);

            if (!$verifyResult['status']) {
                return response()->json([
                    'status' => false,
                    'message' => $verifyResult['message'],
                ], 422);
            }

            if ($verifyResult['paymentStatus'] === 'succeeded') {
                $payment->update([
                    'status' => 'success',
                    'paid_at' => now(),
                ]);

                $order->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Thanh toán thẻ thành công',
                    'order' => $order,
                ]);
            } else {
                $payment->update([
                    'status' => 'failed',
                    'error_message' => 'Payment status: ' . $verifyResult['paymentStatus'],
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Thanh toán thẻ chưa hoàn tất: ' . $verifyResult['paymentStatus'],
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * 📱 Tạo thanh toán E-Wallet (ZaloPay, Appota, AirPay)
     */
    public function initiateEWalletPayment(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|numeric',
            'wallet_type' => 'required|string|in:zalopay,appota,momo,airpay',
        ]);

        try {
            $order = Order::find($validated['order_id']);
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Đơn hàng không tồn tại',
                    'details' => 'Order ID: ' . $validated['order_id'],
                ], 422);
            }
            
            if ($order->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không có quyền truy cập đơn hàng này',
                ], 403);
            }

            $walletType = $validated['wallet_type'];
            $transactionId = strtoupper($walletType) . '-' . date('YmdHis') . '-' . $order->id;

            // Tạo payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'transaction_id' => $transactionId,
                'amount' => $order->total_amount,
                'payment_method' => 'ewallet',
                'status' => 'pending',
                'request_id' => $walletType, // Lưu loại e-wallet
            ]);

            $result = null;

            if ($walletType === 'zalopay') {
                $result = $this->ewalletService->createZaloPayment(
                    $order->id,
                    $order->total_amount,
                    'Order ' . $order->order_code
                );
            } elseif ($walletType === 'appota') {
                $result = $this->ewalletService->createAppotaPayment(
                    $order->id,
                    $order->total_amount,
                    $request->user()->email,
                    'Order ' . $order->order_code
                );
            } elseif ($walletType === 'momo') {
                // MoMo đã có endpoint riêng
                return response()->json([
                    'status' => false,
                    'message' => 'Vui lòng sử dụng endpoint /payment/momo/initiate',
                ], 400);
            }

            if (!$result['status']) {
                return response()->json([
                    'status' => false,
                    'message' => $result['message'],
                ], 422);
            }

            $payment->update(['request_id' => $result['transactionId'] ?? $walletType]);

            return response()->json([
                'status' => true,
                'message' => "Khởi tạo thanh toán {$walletType} thành công",
                'payment' => $payment,
                'paymentData' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * 📊 Lấy danh sách phương thức thanh toán
     */
    public function getPaymentMethods(Request $request)
    {
        return response()->json([
            'status' => true,
            'methods' => [
                [
                    'id' => 'momo',
                    'name' => 'MoMo',
                    'description' => 'Ví điện tử MoMo',
                    'icon' => 'momo',
                ],
                [
                    'id' => 'vietqr',
                    'name' => 'VietQR',
                    'description' => 'Chuyển khoản ngân hàng qua QR Code',
                    'icon' => 'vietqr',
                ],
                [
                    'id' => 'card',
                    'name' => 'Thẻ Tín Dụng / Ghi Nợ',
                    'description' => 'Visa, Mastercard, JCB',
                    'icon' => 'card',
                ],
                [
                    'id' => 'zalopay',
                    'name' => 'ZaloPay',
                    'description' => 'Ví điện tử ZaloPay',
                    'icon' => 'zalopay',
                ],
                [
                    'id' => 'appota',
                    'name' => 'Appota Pay',
                    'description' => 'Ví điện tử Appota',
                    'icon' => 'appota',
                ],
            ],
        ]);
    }
}
