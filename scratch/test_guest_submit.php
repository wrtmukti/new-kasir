<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin\Table;
use App\Models\Admin\Product;
use App\Models\Admin\Voucher;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;

$table = Table::where('delete_status', 0)->first();
$product = Product::where('delete_status', 0)->where('product_status', 1)->first();
$voucher = Voucher::active()->first();

echo "Table ID: " . $table->table_id . " (Meja " . $table->table_number . ")\n";
echo "Product ID: " . $product->product_id . " (" . $product->product_name . ")\n";
echo "Voucher: " . ($voucher ? $voucher->voucher_code : 'none') . "\n";

// Simulate itemsJson exactly as constructed in review.blade.php
$itemsJson = [
    [
        'product_id' => $product->product_id,
        'qty' => 1,
        'note' => 'Pedas banget',
    ]
];

$postData = [
    'table_id' => $table->table_id,
    'total_price' => (float)$product->product_price,
    'items' => json_encode($itemsJson),
    'bundles' => json_encode([]),
    'voucher_code' => $voucher ? $voucher->voucher_code : '',
    'order_remark' => 'Test Catatan Guest',
];

$request = Request::create(route('guest.submit'), 'POST', $postData);
$controller = app(\App\Http\Controllers\Guest\OrderController::class);

try {
    $response = $controller->submit($request);
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response target URL: " . ($response->headers->get('Location') ?? 'no redirect') . "\n";
    if ($response->getSession()) {
        echo "Session errors: " . json_encode($response->getSession()->get('errors')?->all()) . "\n";
        echo "Session error message: " . $response->getSession()->get('error') . "\n";
        echo "Session success message: " . $response->getSession()->get('success') . "\n";
    }
} catch (\Illuminate\Validation\ValidationException $ve) {
    echo "❌ ValidationException: " . json_encode($ve->errors()) . "\n";
} catch (\Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
