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

echo "=== STARTING PAYMENT FLOW TEST ===\n";

$company = Company::first();
if (!$company) {
    echo "No company found, creating test company...\n";
    $company = Company::create(['company_name' => 'Test Cafe']);
}

$product = Product::first();
$table = Table::first();

// 1. Create a dummy test order in_progress
$order = Order::create([
    'company_id' => $company->company_id,
    'order_type' => 'dine_in',
    'order_status' => 'in_progress',
    'order_grand_total' => 50000,
    'tax_percent' => 10,
    'tax_amount' => 4500,
    'tax_type' => 'exclusive',
    'service_charge_percent' => 5,
    'service_charge_amount' => 2250,
    'order_remark' => 'Test Order Payment Flow',
    'order_table_id' => $table?->table_id,
    'created_by' => 'tester',
]);

if ($product) {
    $order->products()->sync([
        $product->product_id => [
            'company_id' => $company->company_id,
            'quantity' => 2,
            'delete_status' => 0,
            'created_by' => 'tester'
        ]
    ]);
}

echo "1. Order created: ID #" . $order->order_id . " with status " . $order->order_status . "\n";

// 2. Test rendering the payment view
try {
    $orderController = app(\App\Http\Controllers\Admin\OrderController::class);
    $view = $orderController->payment($order);
    $html = $view->render();
    echo "2. Payment view rendered successfully! Length: " . strlen($html) . " bytes\n";
} catch (\Exception $e) {
    echo "❌ Error rendering payment view: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. Process Cash Payment
$request = new Request([
    'payment_metode' => 'cash',
    'payment_amount' => 100000,
    'payment_reference' => 'CASH-REF-TEST',
    'payment_remark' => 'Uang 100k kembalian 50k',
]);

try {
    $response = $orderController->processPayment($request, $order);
    echo "3. processPayment executed successfully!\n";
} catch (\Exception $e) {
    echo "❌ Error executing processPayment: " . $e->getMessage() . "\n";
    exit(1);
}

// 4. Verify database records
$order->refresh();
echo "4. Order status after payment: " . $order->order_status . " (Transaction ID: " . $order->order_transaction_id . ")\n";

$transaction = Transaction::with('payment', 'items')->find($order->order_transaction_id);
if (!$transaction) {
    echo "❌ Transaction not found!\n";
    exit(1);
}
echo "5. Transaction code: " . $transaction->transaction_code . ", grand_total: " . $transaction->transaction_grand_total . ", payment_id: " . $transaction->payment_id . "\n";

$payment = $transaction->payment;
if (!$payment) {
    echo "❌ Payment relation not found on transaction!\n";
    exit(1);
}

echo "6. Payment record verified:\n";
echo "   - Payment ID: " . $payment->payment_id . "\n";
echo "   - Company ID: " . $payment->company_id . "\n";
echo "   - Transaction ID: " . $payment->transaction_id . "\n";
echo "   - Metode: " . $payment->payment_metode . "\n";
echo "   - Amount: " . $payment->payment_amount . "\n";
echo "   - Reference: " . $payment->payment_reference . "\n";
echo "   - Status: " . $payment->payment_status . "\n";
echo "   - Grand Total: " . $payment->payment_grand_total . "\n";
echo "   - Remark: " . $payment->payment_remark . "\n";
echo "   - Payment Date: " . $payment->payment_date . "\n";
echo "   - Table ID: " . $payment->payment_table_id . "\n";
echo "   - Created By: " . $payment->created_by . "\n";

// Check if any critical field is null
$nullFields = [];
$fieldsToCheck = ['payment_id', 'company_id', 'transaction_id', 'payment_metode', 'payment_amount', 'payment_reference', 'payment_status', 'payment_grand_total', 'payment_remark', 'payment_date', 'created_by'];
foreach ($fieldsToCheck as $f) {
    if (is_null($payment->{$f})) {
        $nullFields[] = $f;
    }
}

if (count($nullFields) > 0) {
    echo "⚠️ Null fields detected: " . implode(', ', $nullFields) . "\n";
} else {
    echo "✅ ALL FIELDS FULLY POPULATED! No NULL values in required fields.\n";
}

// Clean up test order
$order->products()->detach();
$transaction->items()->delete();
$payment->delete();
$transaction->delete();
$order->delete();
echo "=== TEST COMPLETED SUCCESSFULLY ===\n";
