<?php

namespace Database\Seeders;

use App\Models\Admin\ServiceCharge;
use App\Models\SysAdmin\Company;
use Illuminate\Database\Seeder;

class ServiceChargeSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company) return;

        ServiceCharge::firstOrCreate(
            ['company_id' => $company->company_id, 'service_name' => 'Service Charge 5%'],
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
