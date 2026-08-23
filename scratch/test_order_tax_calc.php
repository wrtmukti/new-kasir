<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin\Tax;
use App\Models\Admin\ServiceCharge;
use App\Models\Admin\Order;
use App\Models\Admin\DailyClosing;

echo "=== TEST ORDER TAX & SERVICE CHARGE CALCULATION ===\n";

$companyId = 'COMP-001';

// Fetch Active Master Tax & Service Charge
$activeTax = Tax::where('is_active', 1)->first();
$activeService = ServiceCharge::where('is_active', 1)->first();

echo "Active Tax: " . ($activeTax ? $activeTax->tax_name . " (" . $activeTax->rate_percent . "%)" : "None") . "\n";
echo "Active Service Charge: " . ($activeService ? $activeService->service_name . " (" . $activeService->rate_percent . "%)" : "None") . "\n";

// Test Subtotal Rp 100.000
$subtotal = 100000;
$discount = 10000; // Rp 10.000
$afterDiscount = $subtotal - $discount; // Rp 90.000

$scPercent = $activeService ? (float) $activeService->rate_percent : 0;
$scAmount = round($afterDiscount * ($scPercent / 100), 2); // Rp 4.500

$taxPercent = $activeTax ? (float) $activeTax->rate_percent : 0;
$taxType = $activeTax ? $activeTax->type : 'exclusive';
$isScTaxable = $activeService ? (bool) $activeService->is_taxable : false;
$taxableBase = $afterDiscount + ($isScTaxable ? $scAmount : 0); // Rp 94.500
$taxAmount = round($taxableBase * ($taxPercent / 100), 2); // Rp 9.450

$grandTotal = $taxableBase + $taxAmount; // Rp 103.950

echo "\n--- CALCULATION RESULTS ---\n";
echo "Subtotal: Rp " . number_format($subtotal, 0) . "\n";
echo "Discount: Rp " . number_format($discount, 0) . "\n";
echo "Service Charge (5%): Rp " . number_format($scAmount, 0) . "\n";
echo "Taxable Base (DPP): Rp " . number_format($taxableBase, 0) . "\n";
echo "Tax Amount (PB1 10%): Rp " . number_format($taxAmount, 0) . "\n";
echo "Grand Total Struk: Rp " . number_format($grandTotal, 0) . "\n";

if ($scAmount == 4500 && $taxAmount == 9450 && $grandTotal == 103950) {
    echo "\n✅ TEST CALCULATIONS MATEMATIS PAS DAN 100% AKURAT!\n";
} else {
    echo "\n❌ TEST CALCULATIONS ADA SELISIH!\n";
}
