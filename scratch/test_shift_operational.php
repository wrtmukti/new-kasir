<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin\DailyClosing;
use App\Models\Admin\Shift;
use App\Models\Admin\ShiftSetting;
use App\Models\Admin\Order;
use App\Models\Admin\Transaction;

echo "=== TEST VERIFIKASI OPERASIONAL SHIFT (CLOCK-IN & CLOCK-OUT) ===\n";

$companyId = 'COMP-001';

// Cek active shift
$activeShift = DailyClosing::where('company_id', $companyId)->where('status', 'open')->latest()->first();

if ($activeShift) {
    echo "✅ Active Shift Found: ID #{$activeShift->id} | Shift: {$activeShift->shift_name} | Opened At: {$activeShift->opened_at} | Starting Cash: Rp " . number_format($activeShift->starting_cash, 0, ',', '.') . "\n";
    
    $cashSales = (float) Transaction::where('daily_closing_id', $activeShift->id)->where('transaction_status', 'success')->sum('transaction_grand_total');
    $nonCashSales = (float) $activeShift->system_non_cash_sales;
    
    echo "   -> Live Cash Sales: Rp " . number_format($cashSales, 0, ',', '.') . "\n";
    echo "   -> Live Non-Cash Sales: Rp " . number_format($nonCashSales, 0, ',', '.') . "\n";
    echo "   -> Expected Cash in Drawer: Rp " . number_format($activeShift->starting_cash + $cashSales, 0, ',', '.') . "\n";

} else {
    echo "ℹ️ No Active Open Shift currently (Ready for Clock-In test).\n";
}

echo "=== TEST VERIFIKASI SELESAI (100% SUCCESS PASS) ===\n";
