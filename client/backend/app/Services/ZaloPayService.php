<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZaloPayService
{
    protected $appId;
    protected $key1;
    protected $key2;
    protected $endpoint;

    public function __construct()
    {
        $this->appId = config('payments.zalopay.app_id');
        $this->key1 = config('payments.zalopay.key1');
        $this->key2 = config('payments.zalopay.key2');
        $this->endpoint = config('payments.zalopay.endpoint');
    }

    /**
     * Create ZaloPay order and return quicklink data (deeplink, checkout URL, QR)
     * Docs: https://docs.zalopay.vn/v2/
     */
    public function createOrderQuicklink($order, $amount, $description)
    {
        try {
            // Build unique transaction ID
            $apptransid = date('ymd') . '_' . $order->id;

            // Round current time to milliseconds
            $apptime = round(microtime(true) * 1000);

            // Prepare request body
            $body = [
                'appid' => $this->appId,
                'apptransid' => $apptransid,
                'appuser' => 'user_' . $order->user_id,
                'amount' => (int)$amount * 100, // ZaloPay expects amount in the smallest unit (cents/xu)
                'apptime' => $apptime,
                'embeddata' => json_encode([
                    'order_id' => $order->id,
                    'order_code' => $order->order_code,
                ]),
                'item' => json_encode([]),
                'description' => substr($description, 0, 100),
                'bankcode' => '', // optional: specific bank code
            ];

            // Compute MAC: mac = HMAC_SHA256("appid|apptransid|amount|apptime", key1)
            $macData = $body['appid'] . '|' . $body['apptransid'] . '|' . $body['amount'] . '|' . $body['apptime'];
            $mac = hash_hmac('sha256', $macData, $this->key1);
            $body['mac'] = $mac;

            Log::info('ZaloPay create order request', [
                'apptransid' => $apptransid,
                'amount' => $amount,
                'body' => $body,
            ]);

            // Make request to ZaloPay
            $response = Http::withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->post($this->endpoint, $body);

            $result = $response->json();

            Log::info('ZaloPay create order response', [
                'status' => $response->status(),
                'result' => $result,
            ]);

            if ($response->status() === 200 && !empty($result['return_code']) && $result['return_code'] == 1) {
                // Success: return_code 1 means OK
                // Expected response: {
                //   "return_code": 1,
                //   "return_message": "success",
                //   "sub_return_code": 1,
                //   "sub_return_message": "success",
                //   "zptranstoken": "...",
                //   "checkouturl": "...",
                //   "order_url": "..."
                // }

                return [
                    'status' => true,
                    'apptransid' => $apptransid,
                    'zptranstoken' => $result['zptranstoken'] ?? '',
                    'checkouturl' => $result['checkouturl'] ?? '',
                    'order_url' => $result['order_url'] ?? '',
                    'return_code' => $result['return_code'],
                    'return_message' => $result['return_message'] ?? 'success',
                ];
            } else {
                $message = $result['return_message'] ?? 'Unknown error from ZaloPay';
                Log::error('ZaloPay order creation failed', $result);
                return [
                    'status' => false,
                    'message' => $message,
                    'return_code' => $result['return_code'] ?? -1,
                ];
            }
        } catch (\Exception $e) {
            Log::error('ZaloPay service exception', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'status' => false,
                'message' => 'ZaloPay service error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify ZaloPay callback signature
     * Signature format: mac = HMAC_SHA256("data", key2)
     * where data = "appid|m_amount|appuser|app_transaction_id|embed_data|items|discount|bank_account_id|ref|status|type"
     */
    public function verifyCallback(array $payload, $signature)
    {
        try {
            // Reconstruct data string in exact order per ZaloPay docs
            $dataSign = implode('|', [
                $payload['appid'] ?? '',
                $payload['m_amount'] ?? 0,
                $payload['appuser'] ?? '',
                $payload['app_transaction_id'] ?? '',
                $payload['embed_data'] ?? '',
                $payload['items'] ?? '[]',
                $payload['discount'] ?? 0,
                $payload['bank_account_id'] ?? '',
                $payload['ref'] ?? '',
                $payload['status'] ?? '',
                $payload['type'] ?? '',
            ]);

            $mac = hash_hmac('sha256', $dataSign, $this->key2);

            Log::info('ZaloPay callback verification', [
                'signature' => $signature,
                'computed_mac' => $mac,
                'match' => hash_equals($mac, $signature),
            ]);

            return hash_equals($mac, $signature);
        } catch (\Exception $e) {
            Log::error('ZaloPay callback verification error', [
                'exception' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Query transaction status from ZaloPay
     */
    public function queryTransaction($apptransid)
    {
        try {
            $apptime = round(microtime(true) * 1000);

            // For query API: mac = HMAC_SHA256("appid|apptransid|apptime", key1)
            $macData = $this->appId . '|' . $apptransid . '|' . $apptime;
            $mac = hash_hmac('sha256', $macData, $this->key1);

            $body = [
                'appid' => $this->appId,
                'apptransid' => $apptransid,
                'apptime' => $apptime,
                'mac' => $mac,
            ];

            $response = Http::post($this->endpoint . '/query', $body);
            $result = $response->json();

            Log::info('ZaloPay query transaction response', $result);

            return $result;
        } catch (\Exception $e) {
            Log::error('ZaloPay query transaction error', [
                'exception' => $e->getMessage(),
            ]);
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Fetch QR image from ZaloPay checkout URL and return as data URI
     */
    public function getCheckoutQRDataUri($checkoutUrl)
    {
        try {
            if (empty($checkoutUrl)) {
                return ['status' => false, 'message' => 'No checkout URL provided'];
            }

            // Try to fetch the checkout page
            $response = Http::get($checkoutUrl);

            if ($response->status() !== 200) {
                return ['status' => false, 'message' => 'Failed to fetch checkout page'];
            }

            // For now, return the checkoutUrl as a QR code using a QR generator
            // In production, you might parse the HTML to extract the actual QR image
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' . urlencode($checkoutUrl);

            $qrResponse = Http::get($qrUrl);
            if ($qrResponse->status() === 200) {
                $dataUri = 'data:image/png;base64,' . base64_encode($qrResponse->body());
                return ['status' => true, 'dataUri' => $dataUri, 'qrUrl' => $qrUrl];
            }

            return ['status' => false, 'message' => 'Failed to generate QR code'];
        } catch (\Exception $e) {
            Log::error('ZaloPay QR generation error', [
                'exception' => $e->getMessage(),
            ]);
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
