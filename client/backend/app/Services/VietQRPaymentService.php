<?php

namespace App\Services;

use Illuminate\Support\Str;

class VietQRPaymentService
{
    private $bankCode;
    private $accountNumber;
    private $accountName;
    private $apiKey;
    private $endpoint;

    public function __construct()
    {
        $this->bankCode = config('payments.vietqr.bank_code', 'BIDV');
        $this->accountNumber = config('payments.vietqr.account_number', '');
        $this->accountName = config('payments.vietqr.account_name', 'TRAVEL VN');
        $this->apiKey = config('payments.vietqr.api_key', '');
        $this->endpoint = config('payments.vietqr.endpoint', 'https://api.vietqr.io/api');
    }

    /**
     * Tạo mã QR thanh toán VietQR
     */
    public function generateQRCode($orderId, $amount, $description = 'Thanh toán đơn hàng')
    {
        try {
            // Build EMV (VietQR) payload - this is what generates the actual QR
            $emv = $this->buildEmvPayload([
                'bank' => $this->bankCode,
                'account' => $this->accountNumber,
                'amount' => (int)$amount,
                'description' => substr($description . ' ' . $orderId, 0, 25),
                'accountName' => $this->accountName,
                'orderId' => $orderId,
            ]);

            // Alternative: Use public VietQR API if you have an API key
            $qrUrl = null;
            if (!empty($this->apiKey)) {
                $payload = [
                    'bank' => $this->bankCode,
                    'account' => $this->accountNumber,
                    'template' => 'compact2',
                    'amount' => (int)$amount,
                    'description' => substr($description . ' ' . $orderId, 0, 25),
                    'accountName' => $this->accountName,
                ];
                $qrUrl = $this->endpoint . '/generate?' . http_build_query($payload);
            }

            return [
                'status' => true,
                'emvPayload' => $emv, // This is what bank apps scan
                'qrUrl' => $qrUrl, // Fallback if API available
                'bankCode' => $this->bankCode,
                'accountNumber' => $this->accountNumber,
                'accountName' => $this->accountName,
                'amount' => $amount,
                'orderId' => $orderId,
                'description' => substr($description . ' ' . $orderId, 0, 25),
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build EMV (VietQR) payload from qr data array.
     * This creates TLV fields and appends CRC16 (CCITT) as required by EMVCo/VietQR.
     */
    private function buildEmvPayload(array $data)
    {
        // Helper: build TLV element
        $tlv = function ($id, $value) {
            $len = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
            return $id . $len . $value;
        };

        // Merchant Account Information (using GUI A000000727 as common for VietQR)
        $mai = '';
        $mai .= $tlv('00', 'A000000727'); // GUI
        // put account number in subfield '01'
        $mai .= $tlv('01', $this->accountNumber);

        $payload = '';
        $payload .= $tlv('00', '01'); // Payload Format Indicator
        $payload .= $tlv('01', '12'); // Point of Initiation Method (12 = dynamic)
        $payload .= $tlv('26', $mai); // Merchant Account Information
        $payload .= $tlv('52', '0000'); // Merchant Category Code (0000 generic)
        $payload .= $tlv('53', '704'); // Transaction Currency (704 = VND)

        // Amount - optional, use integer or formatted string without separators
        if (!empty($data['amount'])) {
            $amountStr = (string) (int) $data['amount'];
            $payload .= $tlv('54', $amountStr);
        }

        $payload .= $tlv('58', 'VN'); // Country Code
        $payload .= $tlv('59', $this->accountName); // Merchant Name
        $payload .= $tlv('60', ''); // Merchant City (optional)

        // Additional data field template - put order reference in subfield 01
        $adf = '';
        if (!empty($data['orderId'])) {
            $adf .= $tlv('01', $data['orderId']);
        }
        if ($adf !== '') {
            $payload .= $tlv('62', $adf);
        }

        // Append CRC placeholder (tag 63 + length 04) then compute CRC16-CCITT
        $payloadForCrc = $payload . '63' . '04';
        $crc = $this->crc16($payloadForCrc);
        $payload .= $tlv('63', $crc);

        return $payload;
    }

    /**
     * CRC16 CCITT (0x1021) - returns uppercase hex string of 4 chars
     */
    private function crc16($data)
    {
        $crc = 0xFFFF;
        $len = strlen($data);
        for ($i = 0; $i < $len; $i++) {
            $crc ^= ord($data[$i]) << 8;
            for ($j = 0; $j < 8; $j++) {
                if (($crc & 0x8000) !== 0) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }
        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }

    /**
     * Tạo transaction ID cho theo dõi
     */
    public function generateTransactionId($orderId)
    {
        return 'VQR-' . date('YmdHis') . '-' . $orderId;
    }

    /**
     * Tạo manual transfer reference
     */
    public function createManualTransferInfo($orderId, $amount)
    {
        return [
            'bankCode' => $this->bankCode,
            'accountNumber' => $this->accountNumber,
            'accountName' => $this->accountName,
            'amount' => $amount,
            'reference' => $orderId, // Mã tham chiếu
            'description' => "Thanh toán don hang {$orderId}",
        ];
    }

    /**
     * Lấy thông tin ngân hàng VietQR
     */
    public function getBankInfo()
    {
        return [
            'bankCode' => $this->bankCode,
            'accountNumber' => $this->accountNumber,
            'accountName' => $this->accountName,
        ];
    }
}
