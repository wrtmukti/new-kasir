<?php

namespace Database\Seeders;

use App\Models\Admin\Tax;
use App\Models\Admin\Outlet;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    public function run(): void
    {
        $outlet = Outlet::first();
        if (!$outlet) return;

        Tax::firstOrCreate(
            ['outlet_id' => $outlet->outlet_id, 'tax_name' => 'PBJT Restoran 10%'],
            [
                'rate_percent' => 10.00,
                'type' => 'exclusive',
                'is_active' => 1,
                'created_by' => 'seeder',
            ]
        );

        $this->command->info('✅ Master Tax (PBJT Restoran 10%) berhasil di-seed.');
    }
}
