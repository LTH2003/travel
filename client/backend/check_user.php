<?php

// Load environment
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    $request = \Illuminate\Http\Request::capture()
);

// Check user
$user = \App\Models\User::where('email', 'huyhoahien85@gmail.com')->first();

if ($user) {
    echo "Email: " . $user->email . "\n";
    echo "Role: " . $user->role . "\n";
    echo "Name: " . $user->name . "\n";
    echo "ID: " . $user->id . "\n";
} else {
    echo "User not found\n";
}
