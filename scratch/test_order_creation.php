<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin\Product;
use App\Models\Admin\Order;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\OrderController;

echo "=== TESTING ORDER CREATION END-TO-END ===\n";

try {
    $company = Company::where('delete_status', 0)->first();
    echo "1. Company ID: " . ($company->company_id ?? 'NONE') . "\n";

    $product = Product::where('delete_status', 0)->first();
    if (!$product) {
        echo "ERROR: Tidak ada produk di database!\n";
        exit(1);
    }
    echo "2. Product ID: {$product->product_id}, Name: {$product->product_name}, Price: {$product->product_price}\n";

    // Simulate Request
    $reqData = [
        'order_type' => 'dine_in',
        'order_remark' => 'Test order dari scratch script',
        'items' => [
            [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'price' => (float) $product->product_price,
                'qty' => 1,
                'note' => 'Pedas sedikit'
            ]
        ]
    ];

    $request = Request::create('/admin/order', 'POST', $reqData);
    $controller = new OrderController();
    $response = $controller->store($request);

    echo "3. Response Status Code: " . $response->getStatusCode() . "\n";
    if ($response->isRedirection()) {
        echo "4. Target Redirect URL: " . $response->getTargetUrl() . "\n";
        if (session('error')) {
            echo "   [!] Session Error Message: " . session('error') . "\n";
        } else {
            echo "   [✓] Session Success Message: " . session('success') . "\n";
        }
    }

    $latestOrder = Order::latest('order_id')->first();
    if ($latestOrder) {
        echo "5. Latest Order ID in DB: #" . $latestOrder->order_id . " (Grand Total: Rp " . number_format($latestOrder->order_grand_total, 0) . ")\n";
    } else {
        echo "5. WARNING: Belum ada order terdaftar di database!\n";
    }

} catch (\Exception $e) {
    echo "EXCEPTIONS CAUGHT: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
