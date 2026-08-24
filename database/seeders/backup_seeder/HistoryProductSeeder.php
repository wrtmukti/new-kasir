<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistoryProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = DB::table('products')->where('delete_status', 0)->get();

        $now = now();
        $count = 0;

        foreach ($products as $product) {
            // Idempotent — cek udah ada history 'create' buat produk ini
            $existing = DB::table('product_histories')
                ->where('product_id', $product->product_id)
                ->where('action_type', 'create')
                ->exists();

            if ($existing) continue;

            DB::table('product_histories')->insert([
                'product_id' => $product->product_id,
                'outlet_id' => $product->outlet_id,
                'category_id' => $product->category_id,
                'history_code' => $product->product_code,
                'history_name' => $product->product_name,
                'history_slug' => $product->product_slug,
                'history_description' => $product->product_description,
                'history_price' => $product->product_price,
                'history_discount' => $product->product_discount_value ?? 0,
                'history_grand_total' => $product->product_price,
                'history_status' => $product->product_status,
                'history_image' => $product->product_image,
                'category_remark' => $product->category_remark,
                'effective_date' => $product->created_at ?? $now,
                'action_type' => 'create',
                'changed_by' => $product->created_by ?? 'seeder',
                'created_by' => $product->created_by,
                'delete_status' => 0,
                'created_at' => $product->created_at ?? $now,
                'updated_at' => $now,
            ]);
            $count++;
        }

        $this->command->info("HistoryProductSeeder: {$count} records created.");
    }
}
