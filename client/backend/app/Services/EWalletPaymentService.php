<?php

namespace App\Services;

class EWalletPaymentService
{
    /**
     * Danh sách các E-Wallet được hỗ trợ
     */
    public static $supportedWallets = [
        'momo' => 'MoMo Wallet',
        'zalopay' => 'ZaloPay',
        'appota' => 'Appota Pay',
        'airpay' => 'AirPay',
    ];

    /**
     * Tạo URL redirect cho ZaloPay
     */
    public function createZaloPayment($orderId, $amount, $description = '')
    {
        try {
            // ZaloPay config
            $app_id = config('payments.zalopay.app_id', '');
            $key1 = config('payments.zalopay.key1', '');
            $key2 = config('payments.zalopay.key2', '');
            $endpoint = config('payments.zalopay.endpoint', 'https://sandbox.zalopay.com.vn/api/v2/create');

            $transref = "ZALOPAY-" . date('YmdHis') . "-" . $orderId;

            $data = [
                "app_id" => $app_id,
                "app_time" => round(microtime(true) * 1000), // milliseconds
                "app_transaction_id" => $transref,
                "app_user" => "user_" . $orderId,
                "amount" => (int)$amount,
                "app_data" => "",
                "embed_data" => json_encode([
                    "order_id" => $orderId,
                    "description" => $description,
                ]),
                "item" => "1",
                "description" => substr($description, 0, 25),
                "bank_code" => "",
                "callback_url" => url('/api/payment/zalopay/callback'),
                "phone" => "",
                "email" => "",
                "address" => "",
            ];

            $data["mac"] = $this->generateZaloPayMac($data, $key1);

            $client = new \GuzzleHttp\Client();
            $response = $client->post($endpoint, [
                'json' => $data,
            ]);

            $result = json_decode($response->getBody(), true);

            if ($result['return_code'] == 1) {
                return [
                    'status' => true,
                    'redirectUrl' => $result['order_url'],
                    'transactionId' => $transref,
                    'zaloPayId' => $result['zp_trans_id'],
                ];
            } else {
                return [
                    'status' => false,
                    'message' => $result['return_message'] ?? 'Failed to create ZaloPay payment',
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate MAC cho ZaloPay
     */
    private function generateZaloPayMac($data, $key1)
    {
        $mac_raw = $data["app_id"] . "|" . $data["app_time"] . "|" . $data["app_transaction_id"] . "|" . $data["amount"] . "|" . $data["app_user"] . "|" . $data["embed_data"] . "|" . $data["item"];
        return hash_hmac('sha256', $mac_raw, $key1);
    }

    /**
     * Xác minh ZaloPay callback
     */
    public function verifyZaloPayCallback($data, $key2)
    {
        $mac = $data['mac'];
        unset($data['mac']);

        $mac_raw = $data["app_id"] . "|" . $data["app_time"] . "|" . $data["app_transaction_id"] . "|" . $data["amount"] . "|" . $data["app_user"] . "|" . $data["embed_data"] . "|" . $data["item"];
        $calculatedMac = hash_hmac('sha256', $mac_raw, $key2);

        return $mac === $calculatedMac;
    }

    /**
     * Tạo URL redirect cho Appota Pay
     */
    public function createAppotaPayment($orderId, $amount, $customerEmail, $description = '')
    {
        try {
            $appId = config('payments.appota.app_id', '');
            $appSecret = config('payments.appota.app_secret', '');
            $endpoint = config('payments.appota.endpoint', 'https://test.appota.com/api/v3/order/create');

            $transactionId = "APPOTA-" . date('YmdHis') . "-" . $orderId;

            $data = [
                'app_id' => $appId,
                'app_trans_id' => $transactionId,
                'app_user' => "user_" . $orderId,
                'app_time' => time() * 1000,
                'amount' => (int)$amount,
                'description' => substr($description, 0, 50),
                'return_url' => url('/api/payment/appota/return'),
                'notify_url' => url('/api/payment/appota/callback'),
                'lang' => 'vi',
            ];

            // Sort và create signature
            ksort($data);
            $signature = '';
            foreach ($data as $key => $value) {
                $signature .= $key . '=' . $value . '&';
            }
            $signature = rtrim($signature, '&');
            $data['checksum'] = hash_hmac('sha256', $signature, $appSecret);

            return [
                'status' => true,
                'endpoint' => $endpoint,
                'data' => $data,
                'transactionId' => $transactionId,
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Lấy danh sách E-Wallet hỗ trợ
     */
    public function getSupportedWallets()
    {
        return self::$supportedWallets;
    }
}
