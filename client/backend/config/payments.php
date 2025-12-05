<?php

return [
    'momo' => [
        'partner_code' => env('MOMO_PARTNER_CODE', 'MOMO'),
        'access_key' => env('MOMO_ACCESS_KEY', ''),
        'secret_key' => env('MOMO_SECRET_KEY', ''),
        'endpoint' => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
    ],

    'vietqr' => [
        'bank_code' => env('VIETQR_BANK_CODE', 'BIDV'),
        'account_number' => env('VIETQR_ACCOUNT_NUMBER', ''),
        'account_name' => env('VIETQR_ACCOUNT_NAME', 'TRAVEL VN'),
        'api_key' => env('VIETQR_API_KEY', ''),
        'endpoint' => env('VIETQR_ENDPOINT', 'https://api.vietqr.io/api'),
    ],

    'stripe' => [
        'public_key' => env('STRIPE_PUBLIC_KEY', ''),
        'secret_key' => env('STRIPE_SECRET_KEY', ''),
    ],

    'zalopay' => [
        'app_id' => env('ZALOPAY_APP_ID', ''),
        'key1' => env('ZALOPAY_KEY1', ''),
        'key2' => env('ZALOPAY_KEY2', ''),
        'endpoint' => env('ZALOPAY_ENDPOINT', 'https://sandbox.zalopay.com.vn/api/v2/create'),
    ],

    'appota' => [
        'app_id' => env('APPOTA_APP_ID', ''),
        'app_secret' => env('APPOTA_APP_SECRET', ''),
        'endpoint' => env('APPOTA_ENDPOINT', 'https://test.appota.com/api/v3/order/create'),
    ],
];
