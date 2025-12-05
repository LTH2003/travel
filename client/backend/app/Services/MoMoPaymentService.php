<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MoMoPaymentService
{
    private $partnerCode;
    private $accessKey;
    private $secretKey;
    private $endpoint;

    public function __construct()
    {
        $this->partnerCode = config('payments.momo.partner_code', 'MOMO');
        $this->accessKey = config('payments.momo.access_key', '');
        $this->secretKey = config('payments.momo.secret_key', '');
        $this->endpoint = config('payments.momo.endpoint', 'https://test-payment.momo.vn/v2/gateway/api/create');
    }

    /**
     * Tạo yêu cầu thanh toán MoMo
     */
    public function createPayment($orderId, $amount, $orderInfo = 'Thanh toán đơn hàng', $returnUrl = null)
    {
        try {
            $requestId = $this->partnerCode . date('YmdHis');
            $extraData = base64_encode(json_encode(['orderId' => $orderId]));
            
            $rawSignature = "accessKey=" . $this->accessKey 
                . "&amount=" . $amount 
                . "&extraData=" . $extraData 
                . "&ipnUrl=" . ($returnUrl ?? url('/api/payment/momo/callback')) 
                . "&orderId=" . $orderId 
                . "&orderInfo=" . urlencode($orderInfo) 
                . "&partnerCode=" . $this->partnerCode 
                . "&redirectUrl=" . ($returnUrl ?? url('/api/payment/momo/return')) 
                . "&requestId=" . $requestId 
                . "&requestType=captureWallet";

            $signature = hash_hmac('sha256', $rawSignature, $this->secretKey);

            $payload = [
                'partnerCode' => $this->partnerCode,
                'partnerName' => 'TravelVN',
                'partnerUserID' => 'user-' . $orderId,
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'redirectUrl' => $returnUrl ?? url('/api/payment/momo/return'),
                'ipnUrl' => $returnUrl ?? url('/api/payment/momo/callback'),
                'lang' => 'vi',
                'requestType' => 'captureWallet',
                'signature' => $signature,
                'extraData' => $extraData,
            ];

            return [
                'status' => true,
                'requestId' => $requestId,
                'payload' => $payload,
                'endpoint' => $this->endpoint,
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Xác minh callback từ MoMo
     */
    public function verifyCallback($signature, $data)
    {
        $rawSignature = "accessKey=" . $this->accessKey
            . "&amount=" . $data['amount']
            . "&extraData=" . $data['extraData']
            . "&ipnUrl=" . $data['ipnUrl']
            . "&orderId=" . $data['orderId']
            . "&orderInfo=" . urlencode($data['orderInfo'])
            . "&partnerCode=" . $this->partnerCode
            . "&redirectUrl=" . $data['redirectUrl']
            . "&requestId=" . $data['requestId']
            . "&requestType=" . $data['requestType'];

        $verifySignature = hash_hmac('sha256', $rawSignature, $this->secretKey);
        return $signature === $verifySignature;
    }

    /**
     * Kiểm tra trạng thái giao dịch
     */
    public function checkTransaction($requestId, $orderId)
    {
        try {
            $rawSignature = "accessKey=" . $this->accessKey
                . "&orderId=" . $orderId
                . "&partnerCode=" . $this->partnerCode
                . "&requestId=" . $requestId;

            $signature = hash_hmac('sha256', $rawSignature, $this->secretKey);

            $response = Http::post('https://test-payment.momo.vn/v2/gateway/api/query', [
                'partnerCode' => $this->partnerCode,
                'requestId' => $requestId,
                'orderId' => $orderId,
                'signature' => $signature,
                'lang' => 'vi',
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return [
                'resultCode' => -1,
                'message' => $e->getMessage(),
            ];
        }
    }
}
