<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin\Order;
use App\Models\Admin\Product;
use App\Models\Admin\Table;
use App\Models\Admin\Payment;
use App\Models\Admin\Transaction;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;

echo "=== TESTING DEBIT & CASH PAYMENT FLOW ===\n";

$company = Company::first();
$product = Product::first();
$table = Table::first();
$orderController = app(\App\Http\Controllers\Admin\OrderController::class);

// TEST 1: DEBIT PAYMENT
echo "\n--- TEST 1: DEBIT CARD PAYMENT ---\n";
$orderDebit = Order::create([
    'company_id' => $company->company_id,
    'order_type' => 'dine_in',
    'order_status' => 'in_progress',
    'order_grand_total' => 75000,
    'tax_percent' => 10,
    'tax_amount' => 6750,
    'tax_type' => 'exclusive',
    'service_charge_percent' => 5,
    'service_charge_amount' => 3375,
    'order_remark' => 'Test Debit Order',
    'order_table_id' => $table?->table_id,
    'created_by' => 'tester',
]);

$debitRequest = new Request([
    'payment_metode' => 'debit',
    'payment_amount' => 75000,
    'payment_reference' => 'EDC-BCA-889911',
    'payment_remark' => 'Debit BCA Kasir 1',
]);

$responseDebit = $orderController->processPayment($debitRequest, $orderDebit);
$orderDebit->refresh();

$trxDebit = Transaction::with('payment')->find($orderDebit->order_transaction_id);
$pmDebit = $trxDebit?->payment;

echo "Order #{$orderDebit->order_id} status: {$orderDebit->order_status}\n";
echo "Trx Code: {$trxDebit->transaction_code} | Remark: {$trxDebit->transaction_remark}\n";
echo "Payment ID: {$pmDebit->payment_id} | Metode: {$pmDebit->payment_metode} | Amount: {$pmDebit->payment_amount} | Ref: {$pmDebit->payment_reference} | Remark: {$pmDebit->payment_remark}\n";

if ($pmDebit->payment_metode === 'debit' && $pmDebit->payment_reference === 'EDC-BCA-889911' && str_contains($pmDebit->payment_remark, 'Debit')) {
    echo "✅ DEBIT PAYMENT TEST PASSED!\n";
} else {
    echo "❌ DEBIT PAYMENT TEST FAILED!\n";
}

// Render show view for debit
$showView = $orderController->show($orderDebit);
$showHtml = $showView->render();
if (str_contains($showHtml, 'Debit Card') && str_contains($showHtml, 'EDC-BCA-889911')) {
    echo "✅ SHOW BLADE DISPLAYS DEBIT CARD INFO CORRECTLY!\n";
} else {
    echo "❌ SHOW BLADE MISSING DEBIT CARD INFO!\n";
}

// Clean up
$pmDebit->delete();
$trxDebit->delete();
$orderDebit->delete();

echo "\n=== ALL TESTS FINISHED ===\n";
