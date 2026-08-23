<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin\Voucher;

$vouchers = Voucher::all();
foreach ($vouchers as $v) {
    echo "ID: {$v->voucher_id} | Code: {$v->voucher_code} | Name: {$v->voucher_name} | Type: {$v->voucher_type} | Val: {$v->voucher_value} | MinPurchase: {$v->voucher_min_purchase} | MaxDisc: {$v->voucher_max_discount} | Start: {$v->voucher_start_date} | End: {$v->voucher_end_date} | Status: {$v->voucher_status} | ActiveScope: " . (Voucher::active()->byCode($v->voucher_code)->exists() ? 'YES' : 'NO') . "\n";
}
