<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\Client\ClientDatabaseManager;
use App\Models\Admin\Outlet;
use App\Models\Admin\SettingOutlet;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Http\Request;

echo "=======================================================\n";
echo "🧪 TESTING SETTING SAVE FLOW (ALL TABS)\n";
echo "=======================================================\n\n";

$databases = ['new_kasir_kopisenja_dev', 'new_kasir_geprekgambos_dev'];

foreach ($databases as $dbName) {
    echo "🏢 Testing Client Database: $dbName\n";
    $connected = ClientDatabaseManager::connectToClient($dbName);
    if (!$connected) {
        die("❌ Gagal terhubung ke $dbName\n");
    }

    $controller = new SettingController();

    // 1. Test Index View Rendering
    $view = $controller->index();
    echo "  1. Setting Index page renders: OK\n";

    // 2. Test Save Company Profile
    $profileReq = new Request([
        'outlet_name' => 'Kopi Senja Utama',
        'outlet_code' => 'KPS',
        'outlet_branch' => 'Pusat',
        'outlet_email' => 'kopisenja@example.com',
        'outlet_phone' => '081234567890',
        'outlet_address' => 'Jl. Malioboro No. 45, Jogja',
    ]);
    $resProfile = $controller->updateCompanyProfile($profileReq);
    $dataProfile = $resProfile->getData(true);
    assert($dataProfile['success'] === true, "Company profile update failed");
    echo "  2. Update Company Profile: " . $dataProfile['message'] . " (PASS)\n";

    // 3. Test Save Payment Setting
    $payReq = new Request([
        'payment_timing' => 'pre_payment',
    ]);
    $resPay = $controller->updatePaymentSetting($payReq);
    $dataPay = $resPay->getData(true);
    assert($dataPay['success'] === true, "Payment timing update failed");
    echo "  3. Update Payment Setting: " . $dataPay['message'] . " (PASS)\n";

    // 4. Test Save Theme Setting
    $themeReq = new Request([
        'theme' => 'spicy_bites',
    ]);
    $resTheme = $controller->updateThemeSetting($themeReq);
    $dataTheme = $resTheme->getData(true);
    assert($dataTheme['success'] === true, "Theme update failed");
    echo "  4. Update Theme Setting: " . $dataTheme['message'] . " (PASS)\n\n";
}

echo "=======================================================\n";
echo "🎉 ALL SETTING SAVE TESTS PASSED (100% SUCCESS)\n";
echo "=======================================================\n";
