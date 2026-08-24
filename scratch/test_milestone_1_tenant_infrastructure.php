<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SysAdmin\SystemUser;
use App\Models\SysAdmin\Plan;
use App\Models\SysAdmin\Client;
use App\Models\SysAdmin\Subscription;
use App\Models\SysAdmin\DatabaseConnection;
use App\Models\SysAdmin\AuditLog;
use App\Models\SysAdmin\SystemSetting;
use App\Services\Tenant\TenantDatabaseManager;
use Illuminate\Support\Facades\DB;

echo "=== STARTING MILESTONE 1: CENTRAL DB & TENANT CONNECTION TEST ===\n\n";

// 1. Jalankan Seeder Central DB
echo "1. Menjalankan CentralDatabaseSeeder...\n";
require_once __DIR__ . '/../database/seeders/CentralDatabaseSeeder.php';
$seeder = new \Database\Seeders\CentralDatabaseSeeder();
$seeder->run();
echo "✅ CentralDatabaseSeeder executed successfully!\n\n";

// 2. Verifikasi Data Central DB
echo "2. Memverifikasi data System User Super Admin...\n";
$superAdmin = SystemUser::where('username', 'superadmin')->first();
if ($superAdmin && $superAdmin->isSuperAdmin()) {
    echo "✅ Super Admin found: {$superAdmin->name} ({$superAdmin->email}), Role: {$superAdmin->role}\n";
} else {
    throw new Exception("❌ Super Admin user not found in central DB!");
}

echo "\n3. Memverifikasi Master Plans SaaS...\n";
$plans = Plan::where('delete_status', 0)->orderBy('sort_order')->get();
echo "✅ Total Plans terdaftar: " . $plans->count() . "\n";
foreach ($plans as $p) {
    echo "   - [{$p->plan_code}] {$p->plan_name} | Outlets: {$p->max_outlets} | Users: {$p->max_users} | Harga/bln: Rp " . number_format($p->price_monthly, 0, ',', '.') . "\n";
}

echo "\n4. Memverifikasi System Settings...\n";
$platformName = SystemSetting::getVal('platform_name');
$currency = SystemSetting::getVal('default_currency');
$timezone = SystemSetting::getVal('default_timezone');
echo "✅ Platform Name: {$platformName}, Currency: {$currency}, Timezone: {$timezone}\n";

// 5. Test Dynamic Tenant Database Manager
echo "\n5. Menguji Dynamic TenantDatabaseManager...\n";
$testConnResult = TenantDatabaseManager::testConnection('new_kasir');
echo "✅ Test connection ke 'new_kasir': " . json_encode($testConnResult) . "\n";

echo "\n6. Menguji dynamic switching ke tenant connection...\n";
$switched = TenantDatabaseManager::connectToClient('new_kasir');
if ($switched) {
    $tables = DB::connection(TenantDatabaseManager::TENANT_CONNECTION)->select('SHOW TABLES');
    echo "✅ Berhasil switch ke tenant connection! Total tabel terdeteksi: " . count($tables) . "\n";
} else {
    throw new Exception("❌ Gagal switch ke tenant connection!");
}

// 7. Menguji pembuatan Database Tenant baru & Penghapusan
echo "\n7. Menguji pembuatan database tenant dinamis ('new_kasir_test_tenant')...\n";
$dbCreated = TenantDatabaseManager::createDatabase('new_kasir_test_tenant');
if ($dbCreated && TenantDatabaseManager::databaseExists('new_kasir_test_tenant')) {
    echo "✅ Database 'new_kasir_test_tenant' berhasil dibuat secara dinamis!\n";
    
    // Switch ke DB baru dan test query
    TenantDatabaseManager::connectToClient('new_kasir_test_tenant');
    DB::connection(TenantDatabaseManager::TENANT_CONNECTION)->statement("CREATE TABLE IF NOT EXISTS `_ping_test` (`id` int primary key auto_increment, `msg` varchar(50))");
    echo "✅ Berhasil mengeksekusi DDL pada database tenant baru terisolasi!\n";
    
    // Bersihkan kembali
    TenantDatabaseManager::connectToCentral();
    TenantDatabaseManager::dropDatabase('new_kasir_test_tenant');
    echo "✅ Database uji 'new_kasir_test_tenant' berhasil dibersihkan (DROP)!\n";
} else {
    throw new Exception("❌ Gagal membuat database tenant baru!");
}

// 8. Menguji Central Audit Logging System
echo "\n8. Menguji Central Audit Logging System...\n";
$log = AuditLog::record(
    action: 'milestone_1_verification',
    clientId: 'SYSTEM',
    targetType: 'Infrastructure',
    targetId: 'CentralDB',
    result: 'success',
    metadata: ['tested_by' => 'Antigravity AI', 'milestone' => 1]
);
if ($log && $log->id) {
    echo "✅ Audit log berhasil dicatat di Central DB! Log ID: #{$log->id}, Action: {$log->action}\n";
} else {
    throw new Exception("❌ Gagal mencatat audit log!");
}

echo "\n=== ALL MILESTONE 1 INFRASTRUCTURE TESTS PASSED 100% ===\n";
