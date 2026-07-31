<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistoryDiscountSeeder extends Seeder
{
    public function run(): void
    {
        $discounts = DB::table('discounts')->where('delete_status', 0)->get();

        $now = now();
        $count = 0;

        foreach ($discounts as $discount) {
            $existing = DB::table('discount_histories')
                ->where('discount_id', $discount->discount_id)
                ->where('reason', 'create')
                ->exists();

            if ($existing) continue;

            DB::table('discount_histories')->insert([
                'discount_id' => $discount->discount_id,
                'company_id' => $discount->company_id,
                'discount_name' => $discount->discount_name,
                'discount_type' => $discount->discount_type,
                'discount_value' => $discount->discount_value,
                'discount_max_amount' => $discount->discount_max_amount,
                'start_date' => $discount->start_date ?? $now,
                'end_date' => $discount->end_date,
                'reason' => 'create',
                'changed_by' => $discount->created_by ?? 'seeder',
                'created_by' => $discount->created_by,
                'delete_status' => 0,
                'created_at' => $discount->created_at ?? $now,
                'updated_at' => $now,
            ]);
            $count++;
        }

        $this->command->info("HistoryDiscountSeeder: {$count} records created.");
    }
}
