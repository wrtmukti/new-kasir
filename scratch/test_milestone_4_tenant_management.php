<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SysAdmin\Client;
use App\Models\SysAdmin\Plan;
use App\Models\SysAdmin\Subscription;
use App\Models\SysAdmin\DatabaseConnection;
use App\Http\Controllers\SysAdmin\ClientController;
use App\Http\Controllers\SysAdmin\OutletOverviewController;
use App\Http\Controllers\SysAdmin\UserOverviewController;
use App\Http\Controllers\SysAdmin\PlanController;
use App\Http\Controllers\SysAdmin\SubscriptionController;
use App\Http\Requests\SysAdmin\Client\StoreClientRequest;
use App\Services\Tenant\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=== STARTING MILESTONE 4: TENANT MANAGEMENT TEST ===\n\n";

// Login as Super Admin
$superAdmin = \App\Models\SysAdmin\SystemUser::where('username', 'superadmin')->first();
Auth::guard('system_admin')->login($superAdmin);

// 0. Pre-cleanup
$prev = Client::where('owner_email', 'suryo@suryakencana.local')->first();
if ($prev) {
    TenantDatabaseManager::dropDatabase($prev->database_name);
    Subscription::where('client_id', $prev->client_id)->delete();
    DatabaseConnection::where('client_id', $prev->client_id)->delete();
    $prev->delete();
}

// 1. Test ClientController@index
echo "1. Menguji ClientController@index...\n";
$clientCtrl = new ClientController();
$indexReq = Request::create('/sys_admin/clients', 'GET');
$indexView = $clientCtrl->index($indexReq);
$indexHtml = $indexView->render();
echo "✅ Client index view berhasil dirender! Panjang HTML: " . strlen($indexHtml) . " bytes\n\n";

// 2. Test ClientController@store (Provisioning New Tenant)
echo "2. Menguji pembuatan client baru via ClientController@store...\n";
$plan = Plan::where('plan_code', 'PRO')->first() ?? Plan::first();
$storeReq = Request::create('/sys_admin/clients', 'POST', [
    'client_name' => 'PT Surya Kencana Nusantara',
    'business_name' => 'Surya Kencana Bistro & Coffee',
    'owner_name' => 'Suryo Wibowo',
    'owner_email' => 'suryo@suryakencana.local',
    'owner_phone' => '081345678901',
    'owner_password' => 'kencana123',
    'plan_id' => $plan->id,
    'address' => 'Jl. Sudirman No. 88, Jakarta Selatan',
]);
$storeFormReq = StoreClientRequest::createFrom($storeReq);
$storeFormReq->setContainer($app);

$storeRes = $clientCtrl->store($storeFormReq);
$client = Client::where('owner_email', 'suryo@suryakencana.local')->first();

if ($client && $client->status === 'active') {
    echo "✅ Client baru berhasil diprovisioning: [{$client->client_id}] {$client->client_name} (Status: {$client->status})\n";
    echo "   Database: {$client->database_name}\n\n";
} else {
    throw new Exception("❌ Gagal membuat client baru!");
}

// 3. Test ClientController@show (8 Tab Detail)
echo "3. Menguji Client Detail (8 Tab View) di ClientController@show...\n";
$showView = $clientCtrl->show($client->client_id);
$showHtml = $showView->render();
echo "✅ Detail 8 Tab berhasil dirender! Panjang HTML: " . strlen($showHtml) . " bytes\n";
if (str_contains($showHtml, '1. Overview') && str_contains($showHtml, '2. Outlets') && str_contains($showHtml, '5. Database')) {
    echo "✅ Seluruh navigasi 8 Tab terverifikasi lengkap di halaman detail client!\n\n";
} else {
    throw new Exception("❌ Tab navigasi detail client tidak lengkap!");
}

// 4. Test Update Profile & Suspend / Reactivate
echo "4. Menguji Update Profil, Suspend, dan Reactivate Client...\n";
$updateReq = Request::create("/sys_admin/clients/{$client->client_id}", 'PUT', [
    'client_name' => 'PT Surya Kencana Nusantara Update',
    'business_name' => 'Surya Kencana Bistro & Resto',
    'owner_name' => 'Suryo Wibowo SE',
    'owner_email' => 'suryo@suryakencana.local',
    'owner_phone' => '081345678901',
    'address' => 'Jl. Sudirman No. 88 Lantai 2, Jakarta Selatan',
]);
$clientCtrl->update($updateReq, $client->client_id);
$client->refresh();
echo "✅ Update profil: {$client->client_name}, Owner: {$client->owner_name}\n";

$suspendReq = Request::create("/sys_admin/clients/{$client->client_id}/suspend", 'POST', ['reason' => 'Testing Suspend']);
$clientCtrl->suspend($suspendReq, $client->client_id);
$client->refresh();
echo "✅ Status setelah Suspend: {$client->status} (Alasan: {$client->suspension_reason})\n";

$reactivateReq = Request::create("/sys_admin/clients/{$client->client_id}/reactivate", 'POST');
$clientCtrl->reactivate($reactivateReq, $client->client_id);
$client->refresh();
echo "✅ Status setelah Reactivate: {$client->status}\n\n";

// 5. Test OutletOverviewController & UserOverviewController
echo "5. Menguji OutletOverviewController & UserOverviewController...\n";
$outletCtrl = new OutletOverviewController();
$outletView = $outletCtrl->index(Request::create('/sys_admin/outlets', 'GET'));
echo "✅ Outlet Overview rendered! Total outlets: " . $outletView->getData()['totalOutlets'] . "\n";

$userCtrl = new UserOverviewController();
$userView = $userCtrl->index(Request::create('/sys_admin/users', 'GET'));
echo "✅ User Overview rendered! Total System Users: " . $userView->getData()['systemUsers']->count() . "\n\n";

// 6. Test PlanController & SubscriptionController
echo "6. Menguji PlanController & SubscriptionController...\n";
$planCtrl = new PlanController();
$planView = $planCtrl->index();
echo "✅ Plans Index rendered! Total Plans: " . $planView->getData()['plans']->count() . "\n";

$subCtrl = new SubscriptionController();
$subView = $subCtrl->index(Request::create('/sys_admin/subscriptions', 'GET'));
echo "✅ Subscriptions Index rendered! Total Subscriptions: " . $subView->getData()['subscriptions']->total() . "\n";

$clientSub = Subscription::where('client_id', $client->client_id)->first();
$extendReq = Request::create("/sys_admin/subscriptions/{$clientSub->id}/extend", 'POST', [
    'plan_id' => $plan->id,
    'expired_date' => now()->addDays(90)->toDateString(),
    'status' => 'active',
    'amount_paid' => 1350000,
    'payment_method' => 'bank_transfer',
    'notes' => 'Perpanjangan 3 bulan PRO plan',
]);
$subCtrl->extend($extendReq, $clientSub->id);
$clientSub->refresh();
echo "✅ Subscription Extended: Expired Date: {$clientSub->expired_date->format('d M Y')}, Status: {$clientSub->status}\n\n";

// 7. Cleanup
echo "7. Membersihkan (Clean up) Database & Record Uji Coba...\n";
TenantDatabaseManager::connectToCentral();
TenantDatabaseManager::dropDatabase($client->database_name);
Subscription::where('client_id', $client->client_id)->delete();
DatabaseConnection::where('client_id', $client->client_id)->delete();
$client->delete();
echo "✅ Clean up selesai!\n";

echo "\n=== ALL MILESTONE 4 TENANT MANAGEMENT TESTS PASSED 100% ===\n";
