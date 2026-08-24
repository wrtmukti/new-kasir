<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\ShiftSetting;
use App\Models\Admin\Shift;
use App\Models\Admin\Outlet;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $outlets = Outlet::all();
        if ($outlets->isEmpty()) {
            return;
        }

        foreach ($outlets as $outlet) {
            // Seed Default Setting Cut-Off Resto (03:00 AM Cut-off, Mode Auto Master Shift)
            ShiftSetting::updateOrCreate(
                ['outlet_id' => $outlet->outlet_id],
                [
                    'daily_cutoff_time' => '03:00:00',
                    'shift_mode' => 'auto_master',
                    'auto_lock_unclosed' => 1,
                ]
            );

            // Seed Master Shift 1 (Shift Pagi: 07:00 - 15:00)
            Shift::updateOrCreate(
                [
                    'outlet_id' => $outlet->outlet_id,
                    'shift_number' => 1,
                ],
                [
                    'shift_name' => 'Shift Pagi',
                    'start_time' => '07:00:00',
                    'end_time' => '15:00:00',
                    'default_starting_cash' => 300000,
                    'is_active' => 1,
                ]
            );

            // Seed Master Shift 2 (Shift Malam: 15:00 - 23:00)
            Shift::updateOrCreate(
                [
                    'outlet_id' => $outlet->outlet_id,
                    'shift_number' => 2,
                ],
                [
                    'shift_name' => 'Shift Malam',
                    'start_time' => '15:00:00',
                    'end_time' => '23:00:00',
                    'default_starting_cash' => 300000,
                    'is_active' => 1,
                ]
            );
        }

        $this->command->info("✅ ShiftSeeder berhasil meng-seed default Cut-off 03:00 AM & 2 Master Shift.");
    }
}
