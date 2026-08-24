<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SysAdmin\Client;
use App\Models\SysAdmin\Subscription;
use App\Models\SysAdmin\DatabaseConnection;
use App\Models\SysAdmin\SystemUser;
use App\Models\SysAdmin\AuditLog;
use App\Services\Tenant\ClientProvisioningService;
use App\Services\Tenant\TenantDatabaseManager;
use App\Services\Tenant\BackupService;
use App\Http\Controllers\SysAdmin\SystemHealthController;
use App\Http\Controllers\SysAdmin\BackupController;
use App\Http\Controllers\SysAdmin\SystemToolsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

echo "=== STARTING MILESTONE 6: INFRASTRUCTURE, BACKUPS & SYSTEM TOOLS TEST ===\n\n";

// 1. Authenticate as Super Admin
$superAdmin = SystemUser::where('username', 'superadmin')->first();
Auth::guard('system_admin')->login($superAdmin);

// 2. Test SystemHealthController@index
echo "1. Menguji SystemHealthController@index...\n";
$healthCtrl = new SystemHealthController();
$healthView = $healthCtrl->index();
$healthHtml = $healthView->render();
echo "✅ System Health View rendered! Panjang HTML: " . strlen($healthHtml) . " bytes\n";
echo "   - Central Status: " . $healthView->getData()['central']['status'] . " ({$healthView->getData()['central']['latency_ms']} ms)\n";
echo "   - Disk Free: " . $healthView->getData()['disk']['free_gb'] . " GB\n\n";

// 3. Test SystemHealthController@pingAll
echo "2. Menguji AJAX Batch Ping Semua Database Tenant...\n";
$pingRes = $healthCtrl->pingAll(Request::create('/sys_admin/health/ping-all', 'POST'));
$pingData = json_decode($pingRes->getContent(), true);
echo "✅ Batch Ping Output: " . $pingData['message'] . " (Checked: {$pingData['total_checked']} databases)\n\n";

// 4. Provisioning Test Client for Backup Snapshot
echo "3. Menyiapkan database client uji coba untuk pengujian Snapshot Backup...\n";
$prov = ClientProvisioningService::provision([
    'client_name' => 'PT Backup Test Mandiri',
    'business_name' => 'Backup Test Cafe',
    'owner_name' => 'Bambang Backup',
    'owner_email' => 'bambang@backuptest.local',
    'owner_password' => 'password123',
]);

if (!$prov['success']) {
    throw new Exception("❌ Gagal membuat test client: " . $prov['message']);
}

$client = $prov['client'];
echo "✅ Test Client terdaftar: {$client->client_id} (DB: {$client->database_name})\n\n";

// 5. Test BackupService::createSnapshot
echo "4. Menguji eksekusi Snapshot SQL Dump via BackupService::createSnapshot...\n";
$backupResult = BackupService::createSnapshot($client->client_id);
if (!$backupResult['success']) {
    throw new Exception("❌ Gagal membuat snapshot backup: " . $backupResult['message']);
}

echo "✅ Snapshot SQL berhasil dibuat!\n";
echo "   - File Name: {$backupResult['file_name']}\n";
echo "   - File Size: {$backupResult['file_size_mb']} MB\n";
echo "   - Tables: {$backupResult['tables_count']} tabel\n";

if (File::exists($backupResult['file_path']) && File::size($backupResult['file_path']) > 0) {
    echo "✅ File fisik SQL terverifikasi ada di storage disk!\n\n";
} else {
    throw new Exception("❌ File fisik SQL tidak ditemukan di storage!");
}

// 6. Test BackupController@index & Delete
echo "5. Menguji BackupController@index & Hapus Snapshot Backup...\n";
$backupCtrl = new BackupController();
$bkIndexView = $backupCtrl->index();
echo "✅ Total Backups terdaftar di UI: " . $bkIndexView->getData()['totalBackups'] . " file\n";

// Delete backup
$backupCtrl->destroy($client->client_id, $backupResult['file_name']);
if (!File::exists($backupResult['file_path'])) {
    echo "✅ File snapshot backup berhasil dihapus dan dibersihkan dari storage!\n\n";
} else {
    throw new Exception("❌ File backup masih tertinggal!");
}

// 7. Test SystemToolsController (Clear Cache, Config, Route, View, Optimize)
echo "6. Menguji SystemToolsController pemeliharaan framework...\n";
$toolsCtrl = new SystemToolsController();

$toolsToTest = ['clear_cache', 'clear_config', 'clear_route', 'clear_view', 'optimize', 'queue_restart'];
foreach ($toolsToTest as $tool) {
    $req = Request::create('/sys_admin/tools/run', 'POST', ['tool' => $tool], [], [], [
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
    ]);
    $res = $toolsCtrl->runTool($req);
    $resData = json_decode($res->getContent(), true);
    if ($resData && $resData['success']) {
        echo "✅ Tool '{$tool}': " . $resData['message'] . "\n";
    } else {
        throw new Exception("❌ Gagal menjalankan tool '{$tool}'!");
    }
}

// 8. Clean up Test Client Database
echo "\n7. Membersihkan (Clean up) Database & Record Uji Coba...\n";
TenantDatabaseManager::connectToCentral();
TenantDatabaseManager::dropDatabase($client->database_name);
Subscription::where('client_id', $client->client_id)->delete();
DatabaseConnection::where('client_id', $client->client_id)->delete();
$client->delete();
echo "✅ Clean up selesai!\n";

echo "\n=== ALL MILESTONE 6 INFRASTRUCTURE & BACKUP TESTS PASSED 100% ===\n";
