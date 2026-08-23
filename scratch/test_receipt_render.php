<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$order = App\Models\Admin\Order::where('order_status', 'completed')->with('vouchers', 'bundles.bundle.items.product')->first();
if (!$order) {
    $order = App\Models\Admin\Order::first();
}
$transaction = App\Models\Admin\Transaction::with('items', 'payment')->where('transaction_id', $order?->order_transaction_id)->first();
$table = null;
$company = App\Models\SysAdmin\Company::first();

echo app('view')->make('admin.order.receipt', compact('order', 'transaction', 'table', 'company'))->render();
echo "\n\n✅ RECEIPT RENDERED 100% SUCCESSFULLY WITHOUT ANY SYNTAX ERROR!\n";
