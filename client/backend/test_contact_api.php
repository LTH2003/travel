<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Contact;

// Get first user
$user = User::first();
if (!$user) {
    echo "No users found";
    exit(1);
}

echo "=== User Info ===" . PHP_EOL;
echo "Name: " . $user->name . PHP_EOL;
echo "Email: " . $user->email . PHP_EOL;
echo "ID: " . $user->id . PHP_EOL;

// Create token
$token = $user->createToken('test')->plainTextToken;
echo "Token: " . $token . PHP_EOL;

echo PHP_EOL . "=== Test API Request ===" . PHP_EOL;

// Use curl to test
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000/api/contacts");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer " . $token,
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'subject' => 'Test Subject from CLI',
    'message' => 'This is a test message from CLI testing',
]));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . PHP_EOL;
echo "Response: " . $response . PHP_EOL;

echo PHP_EOL . "=== Check Database ===" . PHP_EOL;
$contacts = Contact::all();
echo "Total contacts: " . $contacts->count() . PHP_EOL;
foreach ($contacts as $contact) {
    echo "- ID: " . $contact->id . ", User: " . $contact->name . ", Subject: " . $contact->subject . PHP_EOL;
}
