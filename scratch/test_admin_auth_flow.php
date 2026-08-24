<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SysAdmin\User;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Requests\Admin\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

echo "=== STARTING ADMIN POS AUTHENTICATION TEST ===\n\n";

// 1. Ensure a default admin user exists
$user = User::updateOrCreate(
    ['email' => 'admin@gmail.com'],
    [
        'name' => 'Admin Kasir Utama',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]
);
echo "1. User Admin POS disiapkan: {$user->email} (ID: {$user->id})\n";

// 2. Test showLoginForm
$authCtrl = new AuthController();
$view = $authCtrl->showLoginForm();
$html = $view->render();
echo "2. ✅ Halaman Login POS (/login) berhasil di-render! (Panjang: " . strlen($html) . " bytes)\n";

// 3. Test Invalid Credentials
$invalidReq = new LoginRequest();
$invalidReq->merge([
    'email' => 'admin@gmail.com',
    'password' => 'wrongpassword',
]);
$invalidReq->headers->set('Accept', 'application/json');

$resInvalid = $authCtrl->login($invalidReq);
$dataInvalid = json_decode($resInvalid->getContent(), true);

if ($resInvalid->getStatusCode() === 422 && $dataInvalid['success'] === false) {
    echo "3. ✅ Login password salah berhasil ditolak (HTTP 422: {$dataInvalid['message']})\n";
} else {
    throw new Exception("❌ Proteksi password salah gagal!");
}

// 4. Test Valid Credentials
$validReq = new LoginRequest();
$validReq->merge([
    'email' => 'admin@gmail.com',
    'password' => 'password',
    'remember' => true,
]);
$validReq->headers->set('Accept', 'application/json');

$resValid = $authCtrl->login($validReq);
$dataValid = json_decode($resValid->getContent(), true);

if ($dataValid['success'] === true && Auth::guard('web')->check()) {
    echo "4. ✅ Login sukses! User aktif di guard web: " . Auth::guard('web')->user()->name . "\n";
    echo "   - Redirect URL: " . $dataValid['redirect_url'] . "\n";
} else {
    throw new Exception("❌ Login sukses gagal diverifikasi!");
}

// 5. Test Logout
$logoutReq = Request::create('/logout', 'POST');
$resLogout = $authCtrl->logout($logoutReq);

if (!Auth::guard('web')->check()) {
    echo "5. ✅ Logout berhasil! User telah keluar dari guard web.\n";
} else {
    throw new Exception("❌ Logout gagal membersihkan guard web!");
}

echo "\n=== ALL ADMIN POS AUTHENTICATION TESTS PASSED 100% ===\n";
