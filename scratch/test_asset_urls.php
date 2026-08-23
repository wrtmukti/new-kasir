<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "APP_URL: " . config('app.url') . "\n";
echo "GUEST_TEMPLATE: " . config('app.guest_template') . "\n";
echo "Asset test (admin): " . asset('nexora-assets/css/main.css') . "\n";
echo "Asset test (guest): " . asset('guest/ignite_spice/css/ignite_spice.css') . "\n";
echo "File exists main.css: " . (file_exists(public_path('nexora-assets/css/main.css')) ? 'YES' : 'NO') . "\n";
echo "File exists ignite_spice.css: " . (file_exists(public_path('guest/ignite_spice/css/ignite_spice.css')) ? 'YES' : 'NO') . "\n";
echo "File exists guest.css: " . (file_exists(public_path('guest/css/guest.css')) ? 'YES' : 'NO') . "\n";
