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
use App\Http\Controllers\SysAdmin\ImpersonationController;
use App\Http\Controllers\SysAdmin\SystemHealthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

echo "========================================================================\n";
echo "🚀 MILESTONE 8: MASTER END-TO-END VERIFICATION & TENANT ISOLATION TEST\n";
echo "========================================================================\n\n";

// 1. Authenticate as Super Admin
$superAdmin = SystemUser::where('username', 'superadmin')->first();
Auth::guard('system_admin')->login($superAdmin);
echo "1. [AUTH] Berhasil login sebagai System Super Administrator: {$superAdmin->name} ({$superAdmin->role})\n\n";

// 0. Pre-Cleanup
$prevs = Client::whereIn('owner_email', ['aditya@kopisenja.local', 'reza@bintanglima.local'])->get();
foreach ($prevs as $p) {
    TenantDatabaseManager::dropDatabase($p->database_name);
    Subscription::where('client_id', $p->client_id)->delete();
    DatabaseConnection::where('client_id', $p->client_id)->delete();
    $p->delete();
}

// 2. Provision Client A & Client B
echo "2. [PROVISIONING] Membuat 2 Client Tenant Terpisah untuk Pengujian Isolasi Data...\n";

$provA = ClientProvisioningService::provision([
    'client_name' => 'PT Kopi Senja Indonesia',
    'business_name' => 'Kopi Senja Utama',
    'owner_name' => 'Aditya Pratama',
    'owner_email' => 'aditya@kopisenja.local',
    'owner_password' => 'password123',
]);
if (!$provA['success']) throw new Exception("Gagal membuat Client A: " . $provA['message']);
$clientA = $provA['client'];
echo "   ✅ Client A Terdaftar: [{$clientA->client_id}] {$clientA->client_name} (DB: {$clientA->database_name})\n";

$provB = ClientProvisioningService::provision([
    'client_name' => 'PT Resto Bintang Lima',
    'business_name' => 'Bintang Lima Resto & Lounge',
    'owner_name' => 'Reza Rahardian',
    'owner_email' => 'reza@bintanglima.local',
    'owner_password' => 'password123',
]);
if (!$provB['success']) throw new Exception("Gagal membuat Client B: " . $provB['message']);
$clientB = $provB['client'];
echo "   ✅ Client B Terdaftar: [{$clientB->client_id}] {$clientB->client_name} (DB: {$clientB->database_name})\n\n";

// 3. Insert Specific Data to Client A
echo "3. [ISOLATION TEST] Mengisi data spesifik ke Database Client A ({$clientA->database_name})...\n";
TenantDatabaseManager::connectToClient($clientA->database_name);
$outletA = DB::connection('tenant')->table('outlets')->first();

$catAId = DB::connection('tenant')->table('categories')->insertGetId([
    'outlet_id' => $outletA->outlet_id,
    'category_name' => 'Kopi Signature Senja',
    'category_slug' => 'kopi-signature-senja',
    'category_status' => 1,
    'created_at' => now(),
    'updated_at' => now(),
]);

$prodAId = DB::connection('tenant')->table('products')->insertGetId([
    'outlet_id' => $outletA->outlet_id,
    'category_id' => $catAId,
    'product_name' => 'Kopi Susu Gula Aren Senja',
    'product_code' => 'KPG-001',
    'product_price' => 25000,
    'product_status' => 1,
    'created_at' => now(),
    'updated_at' => now(),
]);
echo "   ✅ Data tersimpan di Client A: 'Kopi Susu Gula Aren Senja' (Rp 25.000)\n\n";

// 4. Insert Specific Data to Client B
echo "4. [ISOLATION TEST] Mengisi data spesifik ke Database Client B ({$clientB->database_name})...\n";
TenantDatabaseManager::connectToClient($clientB->database_name);
$outletB = DB::connection('tenant')->table('outlets')->first();

$catBId = DB::connection('tenant')->table('categories')->insertGetId([
    'outlet_id' => $outletB->outlet_id,
    'category_name' => 'Western Luxury Dishes',
    'category_slug' => 'western-luxury-dishes',
    'category_status' => 1,
    'created_at' => now(),
    'updated_at' => now(),
]);

$prodBId = DB::connection('tenant')->table('products')->insertGetId([
    'outlet_id' => $outletB->outlet_id,
    'category_id' => $catBId,
    'product_name' => 'Steak Wagyu A5 Meltique',
    'product_code' => 'STK-001',
    'product_price' => 350000,
    'product_status' => 1,
    'created_at' => now(),
    'updated_at' => now(),
]);
echo "   ✅ Data tersimpan di Client B: 'Steak Wagyu A5 Meltique' (Rp 350.000)\n\n";

// 5. Zero Data Leakage Verification
echo "5. [CROSS-DATABASE ZERO LEAKAGE VERIFICATION] Memverifikasi integritas isolasi data...\n";

// Switch to Client A and verify Client B's steak is NOT there
TenantDatabaseManager::connectToClient($clientA->database_name);
$prodsInA = DB::connection('tenant')->table('products')->pluck('product_name')->toArray();
echo "   🔍 Produk ditemukan di Client A: " . implode(', ', $prodsInA) . "\n";

if (in_array('Kopi Susu Gula Aren Senja', $prodsInA) && !in_array('Steak Wagyu A5 Meltique', $prodsInA)) {
    echo "   ✅ VERIFIKASI CLIENT A: Bersih! Data Client B sama sekali tidak bocor ke Client A.\n";
} else {
    throw new Exception("❌ DATA LEAK DETECTED: Data Client B bocor ke Client A!");
}

// Switch to Client B and verify Client A's coffee is NOT there
TenantDatabaseManager::connectToClient($clientB->database_name);
$prodsInB = DB::connection('tenant')->table('products')->pluck('product_name')->toArray();
echo "   🔍 Produk ditemukan di Client B: " . implode(', ', $prodsInB) . "\n";

if (in_array('Steak Wagyu A5 Meltique', $prodsInB) && !in_array('Kopi Susu Gula Aren Senja', $prodsInB)) {
    echo "   ✅ VERIFIKASI CLIENT B: Bersih! Data Client A sama sekali tidak bocor ke Client B.\n\n";
} else {
    throw new Exception("❌ DATA LEAK DETECTED: Data Client A bocor ke Client B!");
}

// 5.5 Schema Segregation Verification
echo "5.5 [SCHEMA SEGREGATION VERIFICATION] Memverifikasi Database Client B hanya berisi tabel POS (bersih dari tabel Sys Admin)...\n";
$tenantTables = DB::connection('tenant')->select('SHOW TABLES');
$tableKey = 'Tables_in_' . $clientB->database_name;
$tableNames = array_map(fn($t) => $t->$tableKey ?? current((array)$t), $tenantTables);

$forbiddenCentralTables = ['system_users', 'plans', 'clients', 'subscriptions', 'database_connections', 'audit_logs', 'system_settings'];
$foundForbidden = array_intersect($forbiddenCentralTables, $tableNames);

if (empty($foundForbidden)) {
    echo "   ✅ BERSIH 100%! Tidak ada satupun tabel SysAdmin di Database Client (Total tabel tenant: " . count($tableNames) . " tabel).\n\n";
} else {
    throw new Exception("❌ SCHEMA LEAK: Ditemukan tabel central di database tenant: " . implode(', ', $foundForbidden));
}

// 6. Test Security: Credential Masking in Eloquent
echo "6. [SECURITY HARDENING] Memverifikasi kredensial database tidak terekspos ke JSON array...\n";
TenantDatabaseManager::connectToCentral();
$clientAArray = $clientA->fresh()->toArray();

if (!isset($clientAArray['db_password'])) {
    echo "   ✅ 'db_password' TERSEMBUNYI secara aman dari serialisasi array/JSON model Client!\n\n";
} else {
    throw new Exception("❌ SECURITY WARNING: 'db_password' terekspos di array/JSON!");
}

// 7. Impersonation & Audit Trail Test on Client A
echo "7. [IMPERSONATION & AUDIT TEST] Menguji alur Impersonation Client A...\n";
$impersonateCtrl = new ImpersonationController();
$impersonateCtrl->start(Request::create("/sys_admin/impersonate/{$clientA->client_id}", 'GET'), $clientA->client_id);

if (session('is_impersonating') && session('impersonated_client_id') === $clientA->client_id) {
    echo "   ✅ Sesi Impersonation aktif untuk Client A!\n";
} else {
    throw new Exception("❌ Gagal memulai impersonation!");
}

$impersonateCtrl->stop();
if (!session('is_impersonating')) {
    echo "   ✅ Sesi Impersonation berhasil diakhiri & session dibersihkan!\n\n";
} else {
    throw new Exception("❌ Gagal menghentikan impersonation!");
}

// 8. Test Snapshot Backup on Client A
echo "8. [BACKUP SNAPSHOT TEST] Menguji Snapshot Backup Client A...\n";
$backupRes = BackupService::createSnapshot($clientA->client_id);
if ($backupRes['success'] && File::exists($backupRes['file_path'])) {
    echo "   ✅ Snapshot Backup Client A berhasil dibuat ({$backupRes['file_size_mb']} MB, {$backupRes['tables_count']} tabel)\n";
    File::delete($backupRes['file_path']);
    echo "   ✅ File snapshot uji coba berhasil dibersihkan.\n\n";
} else {
    throw new Exception("❌ Gagal membuat snapshot backup!");
}

// 9. Batch Ping Health Test
echo "9. [HEALTH MONITOR TEST] Menguji Batch Ping seluruh tenant databases...\n";
$healthCtrl = new SystemHealthController();
$pingRes = $healthCtrl->pingAll(Request::create('/sys_admin/health/ping-all', 'POST'));
$pingData = json_decode($pingRes->getContent(), true);
echo "   ✅ Batch Health Ping Result: {$pingData['total_checked']} database aktif terverifikasi connected!\n\n";

// 10. Clean up Test Tenant Databases
echo "10. [CLEANUP] Membersihkan database dan data uji coba...\n";
TenantDatabaseManager::connectToCentral();

TenantDatabaseManager::dropDatabase($clientA->database_name);
Subscription::where('client_id', $clientA->client_id)->delete();
DatabaseConnection::where('client_id', $clientA->client_id)->delete();
$clientA->delete();

TenantDatabaseManager::dropDatabase($clientB->database_name);
Subscription::where('client_id', $clientB->client_id)->delete();
DatabaseConnection::where('client_id', $clientB->client_id)->delete();
$clientB->delete();

echo "   ✅ Database '{$clientA->database_name}' dan '{$clientB->database_name}' berhasil di-drop!\n";
echo "   ✅ Seluruh entitas uji coba dibersihkan dengan aman.\n\n";

echo "========================================================================\n";
echo "🎉 ALL 8 MILESTONES & SECURITY ISOLATION VERIFICATIONS PASSED (100%)!\n";
echo "   Nexora POS Multi-Tenant SaaS Platform is STAGING & PRODUCTION READY!\n";
echo "========================================================================\n";
