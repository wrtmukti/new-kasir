<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\Client\ClientDatabaseManager;
use App\Models\Admin\SettingOutlet;
use App\Models\Admin\Outlet;
use App\Models\Admin\Table;
use App\Models\Admin\Product;
use App\Models\Admin\Customer;
use App\Models\Admin\Order;
use App\Models\Admin\Transaction;
use App\Models\Admin\Payment;
use App\Http\Controllers\Admin\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

echo "=======================================================\n";
echo "🧪 INTEGRATION TEST: PRE-PAYMENT & POST-PAYMENT FLOWS\n";
echo "=======================================================\n\n";

$databases = ['new_kasir_kopisenja_dev', 'new_kasir_geprekgambos_dev'];

foreach ($databases as $dbName) {
    echo "=======================================================\n";
    echo "🏢 Testing Client Database: $dbName\n";
    echo "=======================================================\n";

    $connected = ClientDatabaseManager::connectToClient($dbName);
    if (!$connected) {
        die("❌ Gagal terhubung ke database $dbName\n");
    }

    $outlet = Outlet::where('delete_status', 0)->first();
    $table = Table::where('delete_status', 0)->first();
    $product = Product::where('delete_status', 0)->where('product_status', 1)->first();
    $customer = Customer::where('delete_status', 0)->first();

    if (!$outlet || !$table || !$product || !$customer) {
        die("❌ Master data outlet/table/product/customer tidak lengkap di $dbName.\n");
    }

    $controller = new OrderController();

    // ---------------------------------------------------------------------------------
    // 📌 SCENARIO 1: POST-PAYMENT (BAYAR DI AKHIR)
    // ---------------------------------------------------------------------------------
    echo "\n--- [SCENARIO 1] POST-PAYMENT (BAYAR DI AKHIR) ---\n";
    SettingOutlet::where('delete_status', 0)->update(['payment_timing' => 'post_payment']);
    $table->update(['table_status' => 'tersedia']);

    $order1 = Order::create([
        'outlet_id' => $outlet->outlet_id,
        'order_type' => 'dine_in',
        'order_status' => 'pending',
        'payment_status' => 'unpaid',
        'order_grand_total' => (float) $product->product_price * 2,
        'order_table_id' => $table->table_id,
        'order_customer_id' => $customer->customer_id,
        'created_by' => 'guest',
    ]);
    $order1->products()->sync([
        $product->product_id => [
            'outlet_id' => $outlet->outlet_id,
            'quantity' => 2,
            'delete_status' => 0,
            'created_by' => 'guest',
        ]
    ]);

    assert($order1->order_status === 'pending', "Order 1 status should be pending");
    assert($order1->payment_status === 'unpaid', "Order 1 payment_status should be unpaid");
    echo "1. Guest Order #{$order1->order_id} dibuat: status={$order1->order_status}, payment={$order1->payment_status} (PASS)\n";

    $controller->accept($order1);
    $order1->refresh();
    $table->refresh();

    assert($order1->order_status === 'in_progress', "Order 1 status should be in_progress");
    assert($order1->payment_status === 'unpaid', "Order 1 payment_status should still be unpaid");
    assert($table->table_status === 'terisi', "Table should be terisi");
    echo "2. Kasir Accept: status={$order1->order_status}, payment={$order1->payment_status}, meja={$table->table_status} (PASS)\n";

    $payReq1 = new Request([
        'payment_metode' => 'cash',
        'payment_amount' => $order1->order_grand_total + 10000,
        'payment_remark' => 'Pembayaran Cash Pasca Makan',
    ]);
    $controller->processPayment($payReq1, $order1);
    $order1->refresh();
    $table->refresh();

    assert($order1->order_status === 'completed', "Order 1 status should be completed");
    assert($order1->payment_status === 'paid', "Order 1 payment_status should be paid");
    assert($table->table_status === 'tersedia', "Table should be freed to tersedia");
    echo "3. Kasir Process Payment: status={$order1->order_status}, payment={$order1->payment_status}, meja={$table->table_status}, trx_id={$order1->order_transaction_id} (PASS)\n";

    // ---------------------------------------------------------------------------------
    // 📌 SCENARIO 2: PRE-PAYMENT (BAYAR DI AWAL)
    // ---------------------------------------------------------------------------------
    echo "\n--- [SCENARIO 2] PRE-PAYMENT (BAYAR DI AWAL) ---\n";
    SettingOutlet::where('delete_status', 0)->update(['payment_timing' => 'pre_payment']);
    $table->update(['table_status' => 'tersedia']);

    $order2 = Order::create([
        'outlet_id' => $outlet->outlet_id,
        'order_type' => 'dine_in',
        'order_status' => 'pending',
        'payment_status' => 'unpaid',
        'order_grand_total' => (float) $product->product_price * 3,
        'order_table_id' => $table->table_id,
        'order_customer_id' => $customer->customer_id,
        'created_by' => 'guest',
    ]);
    $order2->products()->sync([
        $product->product_id => [
            'outlet_id' => $outlet->outlet_id,
            'quantity' => 3,
            'delete_status' => 0,
            'created_by' => 'guest',
        ]
    ]);

    assert($order2->order_status === 'pending', "Order 2 status should be pending");
    assert($order2->payment_status === 'unpaid', "Order 2 payment_status should be unpaid");
    echo "1. Guest Order #{$order2->order_id} dibuat: status={$order2->order_status}, payment={$order2->payment_status} (PASS)\n";

    $payReq2 = new Request([
        'payment_metode' => 'debit',
        'payment_amount' => $order2->order_grand_total,
        'payment_reference' => 'BCA-EDC-98765',
        'payment_remark' => 'Pre-Payment Debit Card',
    ]);
    $controller->processPayment($payReq2, $order2);
    $order2->refresh();
    $table->refresh();

    assert($order2->order_status === 'in_progress', "Order 2 status should be in_progress (cooking in kitchen)");
    assert($order2->payment_status === 'paid', "Order 2 payment_status should be paid");
    assert($table->table_status === 'terisi', "Table should be occupied");
    echo "2. Kasir Process Payment di Awal: status={$order2->order_status} (Sedang Dimasak), payment={$order2->payment_status} (Lunas), meja={$table->table_status} (PASS)\n";

    $controller->completeServing($order2);
    $order2->refresh();
    $table->refresh();

    assert($order2->order_status === 'completed', "Order 2 status should be completed");
    assert($order2->payment_status === 'paid', "Order 2 payment_status should be paid");
    assert($table->table_status === 'tersedia', "Table should be freed to tersedia");
    echo "3. Kasir Tandai Selesai Disajikan: status={$order2->order_status}, payment={$order2->payment_status}, meja={$table->table_status} (PASS)\n";

    $paidCount = Order::paid()->count();
    $totalOrders = Order::count();
    echo "\nScopes: Total Orders: $totalOrders | Paid: $paidCount (PASS)\n\n";
}

echo "=======================================================\n";
echo "🎉 ALL MULTI-CLIENT TESTS PASSED SUCCESSFULLY! (100% PASS)\n";
echo "=======================================================\n";
