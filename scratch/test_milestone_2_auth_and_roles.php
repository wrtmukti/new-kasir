<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SysAdmin\SystemUser;
use App\Models\SysAdmin\AuditLog;
use App\Http\Controllers\SysAdmin\AuthController;
use App\Http\Controllers\SysAdmin\DashboardController;
use App\Http\Requests\SysAdmin\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

echo "=== STARTING MILESTONE 2: AUTH, ROLES & SEPARATION BOUNDARY TEST ===\n\n";

// 1. Tes Autentikasi Gagal (Invalid Credentials)
echo "1. Menguji percobaan login dengan password salah...\n";
$authController = new AuthController();
$fakeRequest = Request::create('/sys_admin/login', 'POST', [
    'login' => 'superadmin',
    'password' => 'wrong_password_123',
]);
$fakeRequest->headers->set('Accept', 'application/json');

$formRequest = LoginRequest::createFrom($fakeRequest);
$formRequest->setContainer($app);
$response = $authController->login($formRequest);

if ($response->getStatusCode() === 422) {
    $data = json_decode($response->getContent(), true);
    echo "✅ Login gagal tertolak dengan benar (Status 422): {$data['message']}\n";
} else {
    throw new Exception("❌ Harusnya gagal tetapi status code: " . $response->getStatusCode());
}

// 2. Tes Autentikasi Berhasil (Valid Super Admin Credentials)
echo "\n2. Menguji login berhasil dengan akun Super Admin ('superadmin' / 'admin123')...\n";
$validRequest = Request::create('/sys_admin/login', 'POST', [
    'login' => 'superadmin',
    'password' => 'admin123',
]);
$validRequest->headers->set('Accept', 'application/json');
$validFormRequest = LoginRequest::createFrom($validRequest);
$validFormRequest->setContainer($app);

$validResponse = $authController->login($validFormRequest);
if ($validResponse->getStatusCode() === 200) {
    $data = json_decode($validResponse->getContent(), true);
    echo "✅ Login berhasil (Status 200): {$data['message']}\n";
    echo "   Redirect URL: {$data['redirect_url']}\n";
} else {
    throw new Exception("❌ Gagal login: " . $validResponse->getContent());
}

// 3. Verifikasi Session Guard & Role Helper
echo "\n3. Memverifikasi session guard 'system_admin' & Role Helper...\n";
$user = Auth::guard('system_admin')->user();
if ($user && $user->username === 'superadmin') {
    echo "✅ Authenticated User: {$user->name} ({$user->email})\n";
    echo "   - isSuperAdmin(): " . ($user->isSuperAdmin() ? 'TRUE' : 'FALSE') . "\n";
    echo "   - isSystemAdmin(): " . ($user->isSystemAdmin() ? 'TRUE' : 'FALSE') . "\n";
    echo "   - isSupport(): " . ($user->isSupport() ? 'TRUE' : 'FALSE') . "\n";
} else {
    throw new Exception("❌ Guard 'system_admin' tidak mendeteksi user aktif!");
}

// 4. Verifikasi Render Dashboard View
echo "\n4. Menguji Controller & Render Dashboard System Admin...\n";
$dashboardController = new DashboardController();
$viewResult = $dashboardController->index();
$renderedHtml = $viewResult->render();
echo "✅ Dashboard berhasil dirender! Panjang HTML: " . strlen($renderedHtml) . " bytes\n";
if (str_contains($renderedHtml, 'Platform Control Dashboard') && str_contains($renderedHtml, 'TOTAL CLIENT TENANT')) {
    echo "✅ Elemen UI Dashboard Nexora Theme terverifikasi lengkap!\n";
} else {
    throw new Exception("❌ Teks dashboard tidak ditemukan dalam render HTML!");
}

// 5. Verifikasi Audit Log Login
echo "\n5. Memverifikasi rekaman Audit Log di Central DB...\n";
$latestAudit = AuditLog::where('action', 'sysadmin_login')->latest('created_at')->first();
if ($latestAudit) {
    echo "✅ Audit Log Login terdeteksi: Action: {$latestAudit->action}, Result: {$latestAudit->result}, Timestamp: {$latestAudit->created_at}\n";
} else {
    throw new Exception("❌ Audit log login tidak ditemukan!");
}

// 6. Menguji Logout System Admin
echo "\n6. Menguji Logout System Admin...\n";
$logoutRequest = Request::create('/sys_admin/logout', 'POST');
$logoutResponse = $authController->logout($logoutRequest);
echo "✅ Logout berhasil! Redirect status: " . $logoutResponse->getStatusCode() . "\n";
if (!Auth::guard('system_admin')->check()) {
    echo "✅ Guard 'system_admin' berhasil dibersihkan (logged out)!\n";
} else {
    throw new Exception("❌ User masih terdeteksi di guard setelah logout!");
}

echo "\n=== ALL MILESTONE 2 AUTH & ROLES TESTS PASSED 100% ===\n";
