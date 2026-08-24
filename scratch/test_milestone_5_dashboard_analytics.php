<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\SysAdmin\DashboardController;
use App\Models\SysAdmin\SystemUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=== STARTING MILESTONE 5: DASHBOARD & MULTI-TENANT ANALYTICS TEST ===\n\n";

// 1. Authenticate as Super Admin
$superAdmin = SystemUser::where('username', 'superadmin')->first();
Auth::guard('system_admin')->login($superAdmin);
echo "✅ Authenticated as System Admin: {$superAdmin->name} ({$superAdmin->role})\n\n";

$dashboardCtrl = new DashboardController();

// 2. Test Dashboard HTML View Rendering
echo "2. Menguji render halaman Dashboard Executive (HTML View)...\n";
$htmlReq = Request::create('/sys_admin/dashboard', 'GET');
$view = $dashboardCtrl->index($htmlReq);
$html = $view->render();

echo "✅ Dashboard View rendered! Ukuran HTML: " . strlen($html) . " bytes\n";
if (str_contains($html, 'chartGrowth') && str_contains($html, 'chartPlans') && str_contains($html, 'Live Audit Log Stream')) {
    echo "✅ Seluruh elemen Chart.js, KPI Bento Grid, dan Live Audit Feed terverifikasi ada di HTML!\n\n";
} else {
    throw new Exception("❌ Elemen penting dashboard tidak ditemukan di HTML!");
}

// 3. Test Dashboard AJAX Date Range Filtering
echo "3. Menguji AJAX Endpoint Filtering untuk berbagai rentang waktu...\n";
$ranges = ['today', '7days', '30days', 'this_month', 'all_time'];

foreach ($ranges as $range) {
    $ajaxReq = Request::create("/sys_admin/dashboard?range={$range}", 'GET', [], [], [], [
        'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
        'HTTP_ACCEPT' => 'application/json',
    ]);

    $jsonRes = $dashboardCtrl->index($ajaxReq);
    $data = json_decode($jsonRes->getContent(), true);

    if ($data['success'] && isset($data['kpi']) && isset($data['chart_growth']) && isset($data['chart_plans'])) {
        echo "✅ Filter '{$range}': Success! Total Clients: {$data['kpi']['total_clients']}, MRR: Rp {$data['kpi']['mrr']}, Growth Points: " . count($data['chart_growth']['data']) . "\n";
    } else {
        throw new Exception("❌ Filter '{$range}' mengembalikan response JSON tidak valid!");
    }
}

echo "\n=== ALL MILESTONE 5 DASHBOARD & ANALYTICS TESTS PASSED 100% ===\n";
