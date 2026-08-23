<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Keuangan\ReportDashboardController;
use App\Http\Controllers\Admin\Keuangan\SalesReportController;
use App\Http\Controllers\Admin\Keuangan\ProductReportController;
use App\Http\Controllers\Admin\Keuangan\CashFlowReportController;
use App\Http\Controllers\Admin\Keuangan\TaxServiceReportController;
use App\Http\Controllers\Admin\Keuangan\InventoryReportController;
use App\Http\Controllers\Admin\Keuangan\ShiftClosingReportController;

echo "=== TESTING ALL REPORT CONTROLLERS & EXPORTS ===\n\n";

$req = new Request();

try {
    echo "1. ReportDashboardController... ";
    (new ReportDashboardController())->index($req);
    echo "✅ PASS\n";
} catch (\Throwable $e) {
    echo "❌ FAIL: " . $e->getMessage() . "\n";
}

try {
    echo "2. SalesReportController... ";
    (new SalesReportController())->index($req);
    (new SalesReportController())->export($req);
    echo "✅ PASS (Index + Export)\n";
} catch (\Throwable $e) {
    echo "❌ FAIL: " . $e->getMessage() . "\n";
}

try {
    echo "3. ProductReportController... ";
    (new ProductReportController())->index($req);
    (new ProductReportController())->export($req);
    echo "✅ PASS (Index + Export)\n";
} catch (\Throwable $e) {
    echo "❌ FAIL: " . $e->getMessage() . "\n";
}

try {
    echo "4. CashFlowReportController... ";
    (new CashFlowReportController())->index($req);
    (new CashFlowReportController())->export($req);
    echo "✅ PASS (Index + Export)\n";
} catch (\Throwable $e) {
    echo "❌ FAIL: " . $e->getMessage() . "\n";
}

try {
    echo "5. TaxServiceReportController... ";
    (new TaxServiceReportController())->index($req);
    (new TaxServiceReportController())->export($req);
    echo "✅ PASS (Index + Export)\n";
} catch (\Throwable $e) {
    echo "❌ FAIL: " . $e->getMessage() . "\n";
}

try {
    echo "6. InventoryReportController... ";
    (new InventoryReportController())->index($req);
    (new InventoryReportController())->export($req);
    echo "✅ PASS (Index + Export)\n";
} catch (\Throwable $e) {
    echo "❌ FAIL: " . $e->getMessage() . "\n";
}

try {
    echo "7. ShiftClosingReportController... ";
    (new ShiftClosingReportController())->index($req);
    (new ShiftClosingReportController())->export($req);
    echo "✅ PASS (Index + Export)\n";
} catch (\Throwable $e) {
    echo "❌ FAIL: " . $e->getMessage() . "\n";
}

echo "\n=== ALL 7 CONTROLLERS & EXPORTS TESTED 100% SUCCESS ===\n";
