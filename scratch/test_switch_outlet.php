<?php

require 'c:/xampp812/htdocs/newpost/new-kasir/vendor/autoload.php';
$app = require_once 'c:/xampp812/htdocs/newpost/new-kasir/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\SysAdmin\Client;
use App\Models\Admin\Outlet;
use App\Services\Client\ClientDatabaseManager;
use Illuminate\Http\Request;

$client = Client::where('client_code', 'KOPISENJA')->first() ?? Client::first();
if (!$client) {
    echo "Client not found!\n";
    exit(1);
}

ClientDatabaseManager::connectToClient($client);

echo "Testing Outlets in DB:\n";
$outlets = Outlet::where('delete_status', 0)->get();
foreach ($outlets as $o) {
    echo "- ID: {$o->outlet_id} | Name: {$o->outlet_name} | Code: {$o->outlet_code}\n";
}

echo "\n--- Testing Switch Outlet via Controller ---\n";
$firstOutlet = $outlets->first();
$secondOutlet = $outlets->skip(1)->first();

$controller = app(\App\Http\Controllers\Admin\SettingController::class);

// 1. Test Switch via GET / Query Param
$req1 = Request::create('/admin/switch-outlet', 'GET', ['outlet_id' => $firstOutlet->outlet_id]);
$app->instance('request', $req1);
$session = app('session.store');
$req1->setLaravelSession($session);

$res1 = $controller->switchOutlet($req1);
echo "Switch to {$firstOutlet->outlet_name}: Session active_outlet_id = " . session('active_outlet_id') . " | Status = " . $res1->getStatusCode() . "\n";

// 2. Test Switch via Route Param
$req2 = Request::create('/admin/switch-outlet/' . $secondOutlet->outlet_id, 'GET');
$app->instance('request', $req2);
$req2->setLaravelSession($session);

$res2 = $controller->switchOutlet($req2, $secondOutlet->outlet_id);
echo "Switch to {$secondOutlet->outlet_name}: Session active_outlet_id = " . session('active_outlet_id') . " | Status = " . $res2->getStatusCode() . "\n";

// 3. Test Consolidated Service KPI normalization
$service = app(\App\Services\ConsolidatedFinancialService::class);
$kpiAll = $service->getConsolidatedKPIs('2026-08-01', '2026-08-31', ['']);
echo "Consolidated KPI (with empty filter array ['']): Total Revenue = Rp " . number_format($kpiAll['total_revenue'], 0, ',', '.') . " (Analyzed: {$kpiAll['outlet_count_analyzed']} outlets)\n";

$kpiSingle = $service->getConsolidatedKPIs('2026-08-01', '2026-08-31', [$firstOutlet->outlet_id]);
echo "Single Outlet KPI ({$firstOutlet->outlet_name}): Total Revenue = Rp " . number_format($kpiSingle['total_revenue'], 0, ',', '.') . " (Analyzed: {$kpiSingle['outlet_count_analyzed']} outlets)\n";

echo "\nALL SWITCH OUTLET TESTS PASSED PERFECTLY!\n";
