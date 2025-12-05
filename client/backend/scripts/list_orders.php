<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orders = \App\Models\Order::latest()->take(5)->get(['id','order_code','user_id','total_amount','status','created_at']);
foreach ($orders as $o) {
    echo sprintf("%d | %s | user:%d | %s | %s\n", $o->id, $o->order_code, $o->user_id, $o->total_amount, $o->status);
}
if ($orders->isEmpty()) echo "NO_ORDERS\n";
