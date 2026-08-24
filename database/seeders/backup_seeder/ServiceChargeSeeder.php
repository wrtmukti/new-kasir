<?php

namespace Database\Seeders;

use App\Models\Admin\ServiceCharge;
use App\Models\Admin\Outlet;
use Illuminate\Database\Seeder;

class ServiceChargeSeeder extends Seeder
{
    public function run(): void
    {
        $outlet = Outlet::first();
        if (!$outlet) return;

        ServiceCharge::firstOrCreate(
            ['outlet_id' => $outlet->outlet_id, 'service_name' => 'Service Charge 5%'],
            [
                'rate_percent' => 5.00,
                'is_taxable' => 1,
                'is_active' => 1,
                'created_by' => 'seeder',
            ]
        );

        $this->command->info('✅ Master Service Charge (5%) berhasil di-seed.');
    }
}
