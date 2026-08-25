<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\Client\ClientDatabaseManager;
use App\Models\Admin\Order;

foreach (['new_kasir_kopisenja_dev', 'new_kasir_geprekgambos_dev'] as $db) {
    if (ClientDatabaseManager::connectToClient($db)) {
        $count = Order::where('order_status', 'completed')->update(['payment_status' => 'paid']);
        echo "DB $db: $count orders set to paid\n";
    }
}
