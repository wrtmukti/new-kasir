<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\DailyClosing;
use App\Models\Admin\Outlet;
use Carbon\Carbon;

class DailyClosingSeeder extends Seeder
{
    public function run(): void
    {
        $outlets = Outlet::all();
        if ($outlets->isEmpty()) {
            return;
        }

        $year = (int) date('Y');
        $month = (int) date('n');
        $currentDay = (int) date('j');
        $targetDays = max(26, min($currentDay, 28));

        foreach ($outlets as $outlet) {
            for ($day = 1; $day <= $targetDays; $day++) {
                $date = Carbon::create($year, $month, $day);

                // Shift 1 (Pagi 07.00 - 15.00)
                DailyClosing::create([
                    'outlet_id' => $outlet->outlet_id,
                    'cashier_id' => 1,
                    'shift_number' => 1,
                    'shift_name' => 'Shift Pagi',
                    'business_date' => $date->format('Y-m-d'),
                    'opened_at' => $date->copy()->setTime(7, 0, 0),
                    'closed_at' => $date->copy()->setTime(15, 0, 0),
                    'starting_cash' => 300000,
                    'system_cash_sales' => 0,
                    'system_non_cash_sales' => 0,
                    'cash_in_amount' => 0,
                    'cash_out_amount' => 0,
                    'system_expected_cash' => 300000,
                    'actual_cash_counted' => 300000,
                    'cash_difference' => 0,
                    'notes' => 'Shift pagi lancar',
                    'status' => 'closed',
                ]);

                // Shift 2 (Sore-Malam 15.00 - 23.00)
                DailyClosing::create([
                    'outlet_id' => $outlet->outlet_id,
                    'cashier_id' => 1,
                    'shift_number' => 2,
                    'shift_name' => 'Shift Malam',
                    'business_date' => $date->format('Y-m-d'),
                    'opened_at' => $date->copy()->setTime(15, 0, 0),
                    'closed_at' => $date->copy()->setTime(23, 0, 0),
                    'starting_cash' => 300000,
                    'system_cash_sales' => 0,
                    'system_non_cash_sales' => 0,
                    'cash_in_amount' => 0,
                    'cash_out_amount' => 0,
                    'system_expected_cash' => 300000,
                    'actual_cash_counted' => 300000,
                    'cash_difference' => 0,
                    'notes' => 'Shift malam lancar',
                    'status' => 'closed',
                ]);
            }
        }

        $this->command->info("✅ DailyClosingSeeder berhasil meng-seed sesi shift.");
    }
}
