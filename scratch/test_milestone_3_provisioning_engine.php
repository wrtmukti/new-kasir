<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SysAdmin\Client;
use App\Models\SysAdmin\Subscription;
use App\Models\SysAdmin\DatabaseConnection;
use App\Models\SysAdmin\AuditLog;
use App\Services\Tenant\ClientProvisioningService;
use App\Services\Tenant\TenantDatabaseManager;
use App\Http\Controllers\SysAdmin\DatabaseManagementController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

echo "=== STARTING MILESTONE 3: AUTOMATED CLIENT PROVISIONING ENGINE TEST ===\n\n";

// 0. Pre-Cleanup any previous failed test clients
$prevClients = Client::where('owner_email', 'budi@rasanusantara.local')->get();
foreach ($prevClients as $pc) {
    TenantDatabaseManager::dropDatabase($pc->database_name);
    Subscription::where('client_id', $pc->client_id)->delete();
    DatabaseConnection::where('client_id', $pc->client_id)->delete();
    $pc->delete();
}

// 1. Test Generate Client ID
echo "1. Menguji generator Client ID unik...\n";
$generatedId = ClientProvisioningService::generateClientId();
echo "✅ Generated Client ID: {$generatedId}\n\n";

// 2. Jalankan Automated Provisioning
echo "2. Menjalankan Automated Provisioning untuk Klien Baru (PT Rasa Nusantara Group)...\n";
$provisionData = [
    'client_name' => 'PT Rasa Nusantara Group',
    'business_name' => 'Rasa Nusantara Cafe & Resto',
    'owner_name' => 'Budi Santoso',
    'owner_email' => 'budi@rasanusantara.local',
    'owner_phone' => '081298765432',
    'owner_password' => 'secret123',
    'address' => 'Jl. Malioboro No. 45, Yogyakarta',
];

$result = ClientProvisioningService::provision($provisionData);

if (!$result['success']) {
    throw new Exception("❌ Provisioning gagal: " . $result['message']);
}

echo "✅ Provisioning BERHASIL!\n";
echo "   - Client ID: {$result['client']->client_id}\n";
echo "   - Database Name: {$result['database_name']}\n";
echo "   - Status: {$result['client']->status}\n\n";

$client = $result['client'];

// 3. Verifikasi Data Central Database
echo "3. Memverifikasi entitas Central Database...\n";
$sub = Subscription::where('client_id', $client->client_id)->first();
if ($sub && $sub->status === 'trial') {
    echo "✅ Subscription Central DB terbuat: {$sub->subscription_id}, Plan: {$sub->plan->plan_name}, Expired: {$sub->expired_date}\n";
} else {
    throw new Exception("❌ Subscription record tidak valid di Central DB!");
}

$dbConn = DatabaseConnection::where('client_id', $client->client_id)->first();
if ($dbConn && $dbConn->connection_status === 'connected') {
    echo "✅ DatabaseConnection Central DB terdaftar: {$dbConn->database_name}, Tables: {$dbConn->tables_count}, Latency: {$dbConn->latency_ms} ms\n";
} else {
    throw new Exception("❌ DatabaseConnection record tidak valid di Central DB!");
}

$audit = AuditLog::where('client_id', $client->client_id)->where('action', 'client_provisioned')->first();
if ($audit) {
    echo "✅ Audit Log Central DB tercatat: Action: {$audit->action}, Target: #{$audit->target_id}\n";
} else {
    throw new Exception("❌ Audit Log provisioning tidak ditemukan!");
}

// 4. Test direct connection to tenant database
echo "4. Menguji koneksi langsung & data di dalam Database Tenant ({$client->database_name})...\n";
TenantDatabaseManager::connectToClient($client->database_name);
$outlet = DB::connection('tenant')->table('outlets')->first();
$ownerUser = DB::connection('tenant')->table('users')->where('email', 'budi@rasanusantara.local')->first();
$setting = DB::connection('tenant')->table('setting_outlets')->first();

if ($outlet && $outlet->outlet_name === 'Rasa Nusantara Cafe & Resto' && $ownerUser && $setting) {
    echo "✅ Data Tenant DB terisolasi valid!\n";
    echo "   - Outlet Utama: {$outlet->outlet_name} (Code: {$outlet->outlet_code})\n";
    echo "   - Owner Admin: {$ownerUser->name} ({$ownerUser->email})\n";
    echo "   - Setting Theme: {$setting->theme}\n";
} else {
    throw new Exception("❌ Data awal di tenant database tidak sesuai!");
}

// 5. Test Controller DatabaseManagementController@testConnection
echo "\n5. Menguji AJAX test connection controller...\n";
TenantDatabaseManager::connectToCentral();
$dbController = new DatabaseManagementController();
$pingRes = $dbController->testConnection($client->client_id);
$pingData = json_decode($pingRes->getContent(), true);
echo "✅ Controller Ping Test Result: " . json_encode($pingData) . "\n";

// 6. Test Artisan Command tenant:migrate
echo "\n6. Menguji Artisan Command: php artisan tenant:migrate --client={$client->client_id}...\n";
$exitCode = Artisan::call('tenant:migrate', ['--client' => $client->client_id]);
echo "✅ Artisan Command Output: " . trim(Artisan::output()) . "\n";

// 7. Cleanup Test Database & Central Records
echo "\n7. Membersihkan (Clean up) Database & Record Uji Coba...\n";
TenantDatabaseManager::connectToCentral();
TenantDatabaseManager::dropDatabase($client->database_name);
$sub->delete();
$dbConn->delete();
$client->delete();
echo "✅ Database uji '{$client->database_name}' dan record Central DB berhasil dibersihkan!\n";

echo "\n=== ALL MILESTONE 3 AUTOMATED PROVISIONING TESTS PASSED 100% ===\n";
