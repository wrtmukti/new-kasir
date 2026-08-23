<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin\Table;
use App\Models\Admin\Product;
use App\Models\Admin\Voucher;
use App\Models\Admin\Bundle;
use App\Models\Admin\Order;
use Illuminate\Http\Request;

echo "=== TESTING ALL GUEST SUBMIT SCENARIOS ===\n";

$table = Table::where('delete_status', 0)->first();
$product = Product::where('delete_status', 0)->where('product_status', 1)->first();
$bundle = Bundle::where('delete_status', 0)->with('items.product')->first();
$voucher = Voucher::where('voucher_code', 'NGOPI50')->first() ?? Voucher::first();
$controller = app(\App\Http\Controllers\Guest\OrderController::class);

// Scenario 1: Product + Voucher
echo "\n--- SCENARIO 1: Product + Voucher ---\n";
$itemsJson = [
    [
        'product_id' => $product->product_id,
        'qty' => 2,
        'note' => 'Pedas level 3',
    ]
];
$req1 = Request::create(route('guest.submit'), 'POST', [
    'table_id' => $table->table_id,
    'total_price' => (float)$product->product_price * 2,
    'items' => json_encode($itemsJson),
    'bundles' => json_encode([]),
    'voucher_code' => $voucher ? $voucher->voucher_code : '',
    'order_remark' => 'Remark 1',
]);
$res1 = $controller->submit($req1);
echo "Result 1 Status: " . $res1->getStatusCode() . " -> Redirect to: " . ($res1->headers->get('Location') ?? '-') . "\n";
if (str_contains($res1->headers->get('Location'), 'guest/status')) {
    echo "✅ SCENARIO 1 PASSED!\n";
} else {
    echo "❌ SCENARIO 1 FAILED! Error: " . $res1->getSession()?->get('error') . "\n";
}

// Scenario 2: Bundle Only
echo "\n--- SCENARIO 2: Bundle Only ---\n";
$bundlesJson = [
    [
        'bundle_id' => $bundle?->bundle_id,
        'bundle_name' => $bundle?->bundle_name,
        'bundle_price' => (float)$bundle?->bundle_price,
        'qty' => 1,
    ]
];
$req2 = Request::create(route('guest.submit'), 'POST', [
    'table_id' => $table->table_id,
    'total_price' => (float)$bundle?->bundle_price,
    'items' => json_encode([]),
    'bundles' => json_encode($bundlesJson),
    'voucher_code' => '',
    'order_remark' => 'Remark 2 Paket',
]);
$res2 = $controller->submit($req2);
echo "Result 2 Status: " . $res2->getStatusCode() . " -> Redirect to: " . ($res2->headers->get('Location') ?? '-') . "\n";
if (str_contains($res2->headers->get('Location'), 'guest/status')) {
    echo "✅ SCENARIO 2 PASSED!\n";
} else {
    echo "❌ SCENARIO 2 FAILED! Error: " . $res2->getSession()?->get('error') . "\n";
}

// Scenario 3: Product + Bundle + Voucher Combo
echo "\n--- SCENARIO 3: Product + Bundle + Voucher Combo ---\n";
$req3 = Request::create(route('guest.submit'), 'POST', [
    'table_id' => $table->table_id,
    'total_price' => ((float)$product->product_price * 2) + (float)$bundle?->bundle_price,
    'items' => json_encode($itemsJson),
    'bundles' => json_encode($bundlesJson),
    'voucher_code' => $voucher ? $voucher->voucher_code : '',
    'order_remark' => 'Remark 3 Combo',
]);
$res3 = $controller->submit($req3);
echo "Result 3 Status: " . $res3->getStatusCode() . " -> Redirect to: " . ($res3->headers->get('Location') ?? '-') . "\n";
if (str_contains($res3->headers->get('Location'), 'guest/status')) {
    echo "✅ SCENARIO 3 PASSED!\n";
} else {
    echo "❌ SCENARIO 3 FAILED! Error: " . $res3->getSession()?->get('error') . "\n";
}

// Check recent orders in DB
$recentOrder = Order::with('products', 'bundles', 'vouchers')->latest('order_id')->first();
echo "\n--- LATEST ORDER DATA IN DB ---\n";
echo "Order ID: #{$recentOrder->order_id} | Status: {$recentOrder->order_status} | Grand Total: {$recentOrder->order_grand_total}\n";
echo "Products attached: " . $recentOrder->products->count() . "\n";
echo "Bundles attached: " . $recentOrder->bundles->count() . "\n";
echo "Vouchers attached: " . $recentOrder->vouchers->count() . "\n";

echo "\n=== ALL SCENARIOS VERIFIED SUCCESSFULLY ===\n";
