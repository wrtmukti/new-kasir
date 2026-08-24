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
use App\Http\Controllers\SysAdmin\ImpersonationController;
use App\Http\Controllers\SysAdmin\AuditLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=== STARTING MILESTONE 7: IMPERSONATION & AUDIT LOGS TEST ===\n\n";

// 1. Authenticate as Super Admin
$superAdmin = SystemUser::where('username', 'superadmin')->first();
Auth::guard('system_admin')->login($superAdmin);

// 2. Provision a Test Tenant
echo "1. Menyiapkan Client Baru untuk Pengujian Impersonation...\n";
$prov = ClientProvisioningService::provision([
    'client_name' => 'PT Impersonate Mandiri Utama',
    'business_name' => 'Impersonate Resto & Lounge',
    'owner_name' => 'Indra Impersonate',
    'owner_email' => 'indra@impersonate.local',
    'owner_password' => 'password123',
]);

if (!$prov['success']) {
    throw new Exception("❌ Gagal membuat test client: " . $prov['message']);
}

$client = $prov['client'];
echo "✅ Test Client terdaftar: {$client->client_id} (DB: {$client->database_name})\n\n";

// 3. Test ImpersonationController@start
echo "2. Menguji ImpersonationController@start ('Login as Client')...\n";
$impersonateCtrl = new ImpersonationController();
$startRes = $impersonateCtrl->start(Request::create("/sys_admin/impersonate/{$client->client_id}", 'GET'), $client->client_id);

if (session('is_impersonating') && session('impersonated_client_id') === $client->client_id) {
    echo "✅ Sesi Impersonation AKTIF!\n";
    echo "   - Impersonator: " . session('impersonator_name') . " (" . session('impersonator_role') . ")\n";
    echo "   - Impersonated Client: " . session('impersonated_client_name') . " ({$client->client_id})\n";
    echo "   - Impersonated User Email: " . session('impersonated_user_email') . "\n";
} else {
    throw new Exception("❌ Session impersonation tidak tersimpan!");
}

$auditStart = AuditLog::where('client_id', $client->client_id)->where('action', 'impersonation_start')->first();
if ($auditStart) {
    echo "✅ Audit Log 'impersonation_start' tercatat di Central DB!\n\n";
} else {
    throw new Exception("❌ Audit Log impersonation_start tidak ditemukan!");
}

// 4. Test ImpersonationController@stop
echo "3. Menguji ImpersonationController@stop ('Kembali ke System Admin')...\n";
$stopRes = $impersonateCtrl->stop();

if (!session('is_impersonating') && !session('impersonated_client_id')) {
    echo "✅ Sesi Impersonation berhasil dihentikan & session dibersihkan!\n";
} else {
    throw new Exception("❌ Session impersonation masih tertinggal!");
}

$auditStop = AuditLog::where('client_id', $client->client_id)->where('action', 'impersonation_stop')->first();
if ($auditStop) {
    echo "✅ Audit Log 'impersonation_stop' tercatat di Central DB!\n\n";
} else {
    throw new Exception("❌ Audit Log impersonation_stop tidak ditemukan!");
}

// 5. Test AuditLogController@index (HTML View & AJAX Filter)
echo "4. Menguji AuditLogController@index...\n";
$auditCtrl = new AuditLogController();
$auditView = $auditCtrl->index(Request::create('/sys_admin/audit-logs', 'GET'));
$auditHtml = $auditView->render();
echo "✅ Audit Logs HTML rendered! Panjang: " . strlen($auditHtml) . " bytes\n";

$ajaxAudit = $auditCtrl->index(Request::create('/sys_admin/audit-logs?action=impersonation_start', 'GET', [], [], [], [
    'HTTP_ACCEPT' => 'application/json',
    'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
]));
$ajaxData = json_decode($ajaxAudit->getContent(), true);
if ($ajaxData['success'] && $ajaxData['total'] >= 1) {
    echo "✅ AJAX Filter Audit Log (action=impersonation_start) berhasil: Total {$ajaxData['total']} log tercatat.\n\n";
} else {
    throw new Exception("❌ AJAX filter audit log gagal!");
}

// 6. Clean up Test Tenant Database
echo "5. Membersihkan (Clean up) Database & Record Uji Coba...\n";
TenantDatabaseManager::connectToCentral();
TenantDatabaseManager::dropDatabase($client->database_name);
Subscription::where('client_id', $client->client_id)->delete();
DatabaseConnection::where('client_id', $client->client_id)->delete();
$client->delete();
echo "✅ Clean up selesai!\n";

echo "\n=== ALL MILESTONE 7 IMPERSONATION & AUDIT TESTS PASSED 100% ===\n";
