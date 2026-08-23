<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin\Payment;
use App\Models\Admin\Transaction;
use App\Models\Admin\Order;

echo "Recent payments in database:\n";
$payments = Payment::latest()->take(10)->get();
foreach ($payments as $p) {
    echo "ID: {$p->payment_id} | Metode: {$p->payment_metode} | Amount: {$p->payment_amount} | Ref: {$p->payment_reference} | Remark: {$p->payment_remark}\n";
}

if ($payments->isEmpty()) {
    echo "No payments in database yet.\n";
}
