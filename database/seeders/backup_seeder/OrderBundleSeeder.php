<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderBundleSeeder extends Seeder
{
    public function run(): void
    {
        $orders = DB::table('orders')->where('delete_status', 0)->orderBy('created_at')->get();
        $bundles = DB::table('bundles')->where('delete_status', 0)->get();

        if ($bundles->isEmpty()) {
            $this->command->info('OrderBundleSeeder: skip (tidak ada bundle aktif).');
            return;
        }

        $now = now();
        $count = 0;

        // 1) Backfill order_bundle — attach bundle ke order yg produknya cocok (idempotent)
        foreach ($orders as $order) {
            $existing = DB::table('order_bundle')
                ->where('order_id', $order->order_id)
                ->exists();

            if ($existing) continue;

            $orderProductIds = DB::table('order_product')
                ->where('order_id', $order->order_id)
                ->where('delete_status', 0)
                ->pluck('product_id')
                ->toArray();

            $matched = null;
            foreach ($bundles as $bundle) {
                $bundleProductIds = DB::table('bundle_items')
                    ->where('bundle_id', $bundle->bundle_id)
                    ->where('delete_status', 0)
                    ->pluck('product_id')
                    ->toArray();

                if (!empty($bundleProductIds) && !array_diff($bundleProductIds, $orderProductIds)) {
                    $matched = $bundle;
                    break;
                }
            }

            if (!$matched) continue;

            $qty = 1;
            $price = $matched->bundle_price;
            DB::table('order_bundle')->insert([
                'outlet_id' => $order->outlet_id,
                'order_id' => $order->order_id,
                'transaction_id' => $order->order_transaction_id, // null kalo belum completed
                'bundle_id' => $matched->bundle_id,
                'bundle_name' => $matched->bundle_name,
                'bundle_price' => $price,
                'quantity' => $qty,
                'subtotal' => $price * $qty,
                'created_by' => 'seeder',
                'delete_status' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $count++;
        }

        // 2) Isi transaction_id utk order yg udah completed (idempotent)
        $updated = DB::table('order_bundle')
            ->whereNull('transaction_id')
            ->whereIn('order_id', DB::table('orders')->whereNotNull('order_transaction_id')->pluck('order_id'))
            ->update([
                'transaction_id' => DB::raw('(SELECT order_transaction_id FROM orders WHERE orders.order_id = order_bundle.order_id)'),
                'updated_at' => $now,
            ]);

        $this->command->info("OrderBundleSeeder: {$count} records created, {$updated} transaction_id updated.");
    }
}
