<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin\Order;

echo "=== VERIFYING ORDER LIST TOP 5 SORTING ===\n";

$orders = Order::where('delete_status', 0)->orderBy('order_id', 'desc')->take(5)->get();

foreach ($orders as $o) {
    echo "ID: #{$o->order_id} | Type: {$o->order_type} | Status: {$o->order_status} | Total: Rp " . number_format($o->order_grand_total, 0) . " | Date: {$o->created_at}\n";
}
