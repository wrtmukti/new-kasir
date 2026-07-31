<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistoryStockSeeder extends Seeder
{
    public function run(): void
    {
        $stocks = DB::table('stocks')->where('delete_status', 0)->get();

        $now = now();
        $count = 0;

        foreach ($stocks as $stock) {
            // Cek apakah udah ada history buat stock ini (idempotent)
            $existing = DB::table('stock_histories')
                ->where('stock_id', $stock->stock_id)
                ->where('action_type', 'create')
                ->exists();

            if ($existing) continue;

            DB::table('stock_histories')->insert([
                'stock_id' => $stock->stock_id,
                'company_id' => $stock->company_id,
                'stock_code' => $stock->stock_code,
                'stock_name' => $stock->stock_name,
                'stock_slug' => $stock->stock_slug,
                'stock_description' => $stock->stock_description,
                'stock_type' => $stock->stock_type,
                'stock_unit' => $stock->stock_unit,
                'stock_counted' => $stock->stock_counted ?? 1,
                'stock_amount' => $stock->stock_amount ?? 0,
                'stock_price' => $stock->stock_price,
                'stock_status' => $stock->stock_status,
                'stock_image' => $stock->stock_image,
                'effective_date' => $stock->created_at ?? $now,
                'action_type' => 'create',
                'changed_by' => $stock->created_by ?? 'seeder',
                'created_by' => $stock->created_by,
                'delete_status' => 0,
                'created_at' => $stock->created_at ?? $now,
                'updated_at' => $now,
            ]);
            $count++;
        }

        $this->command->info("HistoryStockSeeder: {$count} records created.");
    }
}
