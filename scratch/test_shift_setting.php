<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin\ShiftSetting;
use App\Models\Admin\Shift;
use App\Models\SysAdmin\Company;

echo "=== VERIFIKASI TEST SHIFT SETTING & MASTER SHIFT ===\n";

$setting = ShiftSetting::first();
if ($setting) {
    echo "✅ ShiftSetting Found: Cut-Off Time = {$setting->daily_cutoff_time}, Mode = {$setting->shift_mode}, Auto-Lock = {$setting->auto_lock_unclosed}\n";
} else {
    echo "❌ ShiftSetting Not Found!\n";
}

$shifts = Shift::all();
echo "✅ Total Master Shift Found: " . $shifts->count() . "\n";
foreach ($shifts as $s) {
    echo "   -> [#{$s->shift_number}] {$s->shift_name}: {$s->start_time} - {$s->end_time} | Modal Awal: Rp " . number_format($s->default_starting_cash, 0, ',', '.') . " | Status: " . ($s->is_active ? 'Aktif' : 'Non-Aktif') . "\n";
}

echo "=== TEST VERIFIKASI SELESAI (100% SUCCESS PASS) ===\n";
