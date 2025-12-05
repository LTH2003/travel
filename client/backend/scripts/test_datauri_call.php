<?php
// Usage: php scripts/test_datauri_call.php 8
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

// Call datauri endpoint
$url = "http://127.0.0.1:8000/api/payment/vietqr/datauri/" . $orderId;
$opts = [
    'http' => [
        'method' => 'GET',
        'header' => "Authorization: Bearer $plain\r\nContent-Type: application/json\r\n",
        'ignore_errors' => true,
        'timeout' => 10,
    ],
];
$ctx = stream_context_create($opts);
$response = @file_get_contents($url, false, $ctx);
$status = $http_response_header[0] ?? 'NO_RESPONSE';
echo "HTTP: $status\n";
echo $response . "\n";

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
