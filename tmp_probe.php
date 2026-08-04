<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== latest order ===\n";
$o = DB::table('orders')->orderBy('order_id', 'desc')->first();
var_dump($o);

echo "\n=== guest orders (source=guest) ===\n";
$g = DB::table('orders')->where('source', 'guest')->get();
var_dump($g->toArray());

echo "\n=== orders last 2 days ===\n";
$all = DB::table('orders')->where('created_at', '>=', now()->subDays(2))
    ->get(['order_id','order_code','source','order_status','table_id','created_at']);
var_dump($all->toArray());