<?php
// Usage: php scripts/test_all_qr_methods.php 11
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orderId = $argv[1] ?? 8;
$order = \App\Models\Order::find($orderId);
if (!$order) {
    echo "NO_ORDER\n";
    exit(1);
}
$user = \App\Models\User::find($order->user_id);
if (!$user) {
    echo "NO_USER\n";
    exit(1);
}
// Create temporary token
$tokenObj = $user->createToken('tmp_debug');
$plain = $tokenObj->plainTextToken;
echo "TOKEN: $plain\n";

$base = "http://127.0.0.1:8000";
$endpoints = [
    'payload' => "/api/payment/vietqr/payload/{$orderId}",
    'image' => "/api/payment/vietqr/image/{$orderId}",
    'datauri' => "/api/payment/vietqr/datauri/{$orderId}",
];

$opts = [
    'http' => [
        'method' => 'GET',
        'header' => "Authorization: Bearer $plain\r\nContent-Type: application/json\r\n",
        'ignore_errors' => true,
        'timeout' => 15,
    ],
];

foreach ($endpoints as $name => $path) {
    echo "\n=== Calling: $name ($path) ===\n";
    $ctx = stream_context_create($opts);
    $url = $base . $path;
    $response = @file_get_contents($url, false, $ctx);
    $status = $http_response_header[0] ?? 'NO_RESPONSE';
    echo "HTTP: $status\n";
    if ($name === 'image') {
        // write binary to temp file if we got binary content
        $contentType = 'unknown';
        foreach ($http_response_header as $h) {
            if (stripos($h, 'Content-Type:') !== false) {
                $contentType = trim(substr($h, strpos($h, ':') + 1));
            }
        }
        echo "Content-Type: $contentType\n";
        if ($response !== false && strpos($status, '200') !== false) {
            $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "qr_image_{$orderId}.png";
            file_put_contents($tmp, $response);
            echo "Saved image to: $tmp\n";
        } else {
            echo "Image response:\n" . ($response ?? '') . "\n";
        }
    } else {
        echo "Body:\n" . ($response ?? '') . "\n";
    }
}

// Clean up: delete the personal access token record
$parts = explode('|', $plain, 2);
if (is_numeric($parts[0])) {
    $id = intval($parts[0]);
    try {
        $patClass = '\\Laravel\\Sanctum\\PersonalAccessToken';
        if (class_exists($patClass)) {
            $pat = $patClass::find($id);
            if ($pat) {
                $pat->delete();
                echo "Deleted token record id=$id\n";
            } else {
                echo "Token record id=$id not found\n";
            }
        } else {
            echo "PersonalAccessToken class not found\n";
        }
    } catch (Exception $e) {
        echo "Failed to delete token: " . $e->getMessage() . "\n";
    }
} else {
    echo "Could not parse token id\n";
}

echo "\nAll calls completed.\n";
