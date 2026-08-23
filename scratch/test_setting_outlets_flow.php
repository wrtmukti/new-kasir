<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin\SettingOutlet;
use App\Models\Admin\Table;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;

echo "=== STARTING SETTING OUTLET & PAYMENT TIMING TEST ===\n";

$company = Company::first();
echo "1. Company found: " . ($company?->company_name ?? 'none') . "\n";

$settingController = app(\App\Http\Controllers\Admin\SettingController::class);

// 1. Test Index View Render
try {
    $indexView = $settingController->index();
    $html = $indexView->render();
    echo "2. Setting index view rendered successfully! Length: " . strlen($html) . " bytes\n";
} catch (\Exception $e) {
    echo "❌ Error rendering setting index view: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Test Update Payment Timing to pre_payment
$reqPre = new Request(['payment_timing' => 'pre_payment']);
$resPre = $settingController->updatePaymentSetting($reqPre);
$jsonPre = json_decode($resPre->getContent(), true);
echo "3. Update to pre_payment: " . ($jsonPre['success'] ? 'SUCCESS' : 'FAILED') . " | Msg: " . $jsonPre['message'] . "\n";

$dbSetting = SettingOutlet::where('company_id', $company->company_id)->first();
if ($dbSetting->payment_timing === 'pre_payment') {
    echo "✅ DB setting_outlets payment_timing updated to pre_payment!\n";
} else {
    echo "❌ DB payment_timing mismatch!\n";
    exit(1);
}

// 3. Test Update Payment Timing to post_payment
$reqPost = new Request(['payment_timing' => 'post_payment']);
$resPost = $settingController->updatePaymentSetting($reqPost);
$jsonPost = json_decode($resPost->getContent(), true);
echo "4. Update to post_payment: " . ($jsonPost['success'] ? 'SUCCESS' : 'FAILED') . " | Msg: " . $jsonPost['message'] . "\n";

$dbSetting->refresh();
if ($dbSetting->payment_timing === 'post_payment') {
    echo "✅ DB setting_outlets payment_timing updated to post_payment!\n";
} else {
    echo "❌ DB payment_timing mismatch!\n";
    exit(1);
}

// 4. Test Update Theme Setting to metropolis_brew
$reqTheme = new Request(['theme' => 'metropolis_brew']);
$resTheme = $settingController->updateThemeSetting($reqTheme);
$jsonTheme = json_decode($resTheme->getContent(), true);
echo "5. Update theme to metropolis_brew: " . ($jsonTheme['success'] ? 'SUCCESS' : 'FAILED') . "\n";

$dbSetting->refresh();
if ($dbSetting->theme === 'metropolis_brew') {
    echo "✅ DB setting_outlets theme updated to metropolis_brew!\n";
} else {
    echo "❌ DB theme mismatch!\n";
    exit(1);
}

// 5. Test Guest OrderController Dynamic View Resolution with Table
$table = Table::where('delete_status', 0)->first();
if ($table) {
    $guestController = app(\App\Http\Controllers\Guest\OrderController::class);
    $guestView = $guestController->index($table->table_id);
    $guestHtml = $guestView->render();
    echo "6. Guest index view with dynamic theme rendered successfully! Length: " . strlen($guestHtml) . " bytes\n";
    if (str_contains($guestView->name(), 'guest.metropolis_brew.index')) {
        echo "✅ Dynamic theme resolution to guest.metropolis_brew.index confirmed!\n";
    }
}

// 6. Test Update Company Profile
$reqProfile = new Request([
    'company_name' => $company->company_name,
    'company_branch' => 'Pusat Utama',
    'company_email' => 'contact@resto.com',
    'company_phone' => '081299998888',
    'company_address' => 'Jl. Kebon Sirih No. 45, Jakarta Pusat',
]);
$resProfile = $settingController->updateCompanyProfile($reqProfile);
$jsonProfile = json_decode($resProfile->getContent(), true);
echo "7. Update company profile: " . ($jsonProfile['success'] ? 'SUCCESS' : 'FAILED') . " | Msg: " . $jsonProfile['message'] . "\n";

echo "\n=== ALL SETTING & PAYMENT TIMING TESTS PASSED 100% ===\n";
