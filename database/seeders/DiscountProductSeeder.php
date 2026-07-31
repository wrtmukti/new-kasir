<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountProductSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua produk yg punya discount_id (data lama)
        $products = DB::table('products')
            ->whereNotNull('product_discount_id')
            ->where('delete_status', 0)
            ->get(['product_id', 'company_id', 'product_discount_id', 'created_at']);

        $now = now();
        $count = 0;

        foreach ($products as $product) {
            // Cek apakah udah ada pivot buat produk ini (biar idempotent)
            $existing = DB::table('discount_product')
                ->where('product_id', $product->product_id)
                ->where('discount_id', $product->product_discount_id)
                ->where('delete_status', 0)
                ->exists();

            if ($existing) {
                continue;
            }

            DB::table('discount_product')->insert([
                'company_id' => $product->company_id,
                'product_id' => $product->product_id,
                'discount_id' => $product->product_discount_id,
                'start_date' => $product->created_at ?? $now,
                'end_date' => null,
                'created_by' => 'seeder',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $count++;
        }

        echo "DiscountProductSeeder: {$count} pivot records created.\n";
    }
}
