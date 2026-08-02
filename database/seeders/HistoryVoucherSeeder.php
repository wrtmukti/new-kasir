<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistoryVoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = DB::table('vouchers')->where('delete_status', 0)->get();

        $now = now();
        $count = 0;

        foreach ($vouchers as $voucher) {
            // Idempotent — cek udah ada history 'create' buat voucher ini
            $existing = DB::table('voucher_histories')
                ->where('voucher_id', $voucher->voucher_id)
                ->where('action', 'create')
                ->exists();

            if ($existing) continue;

            DB::table('voucher_histories')->insert([
                'voucher_id' => $voucher->voucher_id,
                'company_id' => $voucher->company_id,
                'voucher_code' => $voucher->voucher_code,
                'voucher_name' => $voucher->voucher_name,
                'voucher_description' => $voucher->voucher_description,
                'voucher_type' => $voucher->voucher_type,
                'voucher_value' => $voucher->voucher_value,
                'voucher_max_discount' => $voucher->voucher_max_discount,
                'voucher_min_purchase' => $voucher->voucher_min_purchase,
                'voucher_applicable_to' => $voucher->voucher_applicable_to,
                'voucher_usage_limit' => $voucher->voucher_usage_limit,
                'voucher_usage_per_customer' => $voucher->voucher_usage_per_customer,
                'voucher_start_date' => $voucher->voucher_start_date,
                'voucher_end_date' => $voucher->voucher_end_date,
                'voucher_status' => $voucher->voucher_status,
                'action' => 'create',
                'user_id' => $voucher->created_by ?? 'seeder',
                'created_by' => $voucher->created_by,
                'delete_status' => 0,
                'created_at' => $voucher->created_at ?? $now,
                'updated_at' => $now,
            ]);
            $count++;
        }

        $this->command->info("HistoryVoucherSeeder: {$count} records created.");
    }
}
