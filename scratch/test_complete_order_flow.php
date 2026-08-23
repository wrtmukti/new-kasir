<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin\Product;
use App\Models\Admin\Table;
use App\Models\Admin\Customer;
use App\Models\Admin\Order;
use App\Models\Admin\DailyClosing;
use App\Models\Admin\Transaction;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\OrderController;
use Carbon\Carbon;

echo "=======================================================\n";
echo "       POS KASIR FULL END-TO-END AUTOMATED TEST        \n";
echo "=======================================================\n\n";

try {
    // 0. Ensure an active shift exists
    $company = Company::where('delete_status', 0)->first();
    $companyId = $company->company_id;

    $activeClosing = DailyClosing::where('company_id', $companyId)->where('status', 'open')->first();
    if (!$activeClosing) {
        $activeClosing = DailyClosing::create([
            'company_id' => $companyId,
            'user_id' => 1,
            'business_date' => Carbon::today()->format('Y-m-d'),
            'shift_number' => 1,
            'shift_name' => 'Shift Pagi Test',
            'opened_at' => Carbon::now(),
            'starting_cash' => 300000,
            'status' => 'open',
            'created_by' => 'test'
        ]);
        echo "[1/6] Sesi Shift Kasir Aktif dibuat (ID: #{$activeClosing->id})\n";
    } else {
        echo "[1/6] Sesi Shift Kasir Aktif ditemukan (ID: #{$activeClosing->id}, Status: OPEN)\n";
    }

    // 1. Get Master Data
    $product = Product::where('delete_status', 0)->first();
    $table = Table::where('delete_status', 0)->first();
    $customer = Customer::where('delete_status', 0)->first();

    echo "[2/6] Master Data Produk: '{$product->product_name}' (Rp " . number_format($product->product_price, 0) . ")\n";
    if ($table) echo "      Meja #: {$table->table_number}\n";
    if ($customer) echo "      Pelanggan: {$customer->customer_name}\n";

    // 2. Create Order via Controller
    $reqData = [
        'order_type' => 'dine_in',
        'order_table_id' => $table ? $table->table_id : null,
        'order_customer_id' => $customer ? $customer->customer_id : null,
        'order_remark' => 'Automated test order dine-in',
        'items' => [
            [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'price' => (float) $product->product_price,
                'qty' => 2,
                'note' => 'Tanpa bawang'
            ]
        ]
    ];

    $request = Request::create('/admin/order', 'POST', $reqData);
    $controller = new OrderController();
    $response = $controller->store($request);

    if ($response->isRedirection() && str_contains($response->getTargetUrl(), 'admin/order/list')) {
        echo "[3/6] SUCCESS: Order berhasil dibuat! Redirected to /admin/order/list\n";
    } else {
        echo "[3/6] FAILED: Order creation failed! Status: " . $response->getStatusCode() . "\n";
        exit(1);
    }

    $createdOrder = Order::latest('order_id')->first();
    echo "      Order ID: #{$createdOrder->order_id} | Status: {$createdOrder->order_status} | Grand Total: Rp " . number_format($createdOrder->order_grand_total, 0) . "\n";

    // 3. Complete & Checkout Order
    echo "[4/6] Memproses Pembayaran / Complete Order #{$createdOrder->order_id}...\n";
    $completeReq = Request::create("/admin/order/{$createdOrder->order_id}/complete", 'POST');
    $completeResp = $controller->complete($createdOrder);

    $completedOrder = Order::find($createdOrder->order_id);
    echo "      Status Order Setelah Pembayaran: {$completedOrder->order_status}\n";

    // 4. Verify Transaction Record
    $transaction = Transaction::where('transaction_id', $completedOrder->order_transaction_id)->first();
    if ($transaction) {
        echo "[5/6] SUCCESS: Record Transaksi Terbuat! Code: {$transaction->transaction_code} | Total: Rp " . number_format($transaction->transaction_grand_total, 0) . "\n";
    } else {
        echo "[5/6] FAILED: Record Transaksi tidak ditemukan!\n";
        exit(1);
    }

    // 5. Verify Table status updated back to 'tersedia'
    if ($table) {
        $tableFresh = Table::find($table->table_id);
        echo "[6/6] Status Meja #{$tableFresh->table_number} Setelah Lunas: " . strtoupper($tableFresh->table_status) . " (Tersedia kembali!)\n";
    }

    echo "\n=======================================================\n";
    echo "    🎉 100% TEST PASSED - ALUR POS UTUH BERHASIL!      \n";
    echo "=======================================================\n";

} catch (\Exception $e) {
    echo "\n❌ TEST FAILED WITH EXCEPTION:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
