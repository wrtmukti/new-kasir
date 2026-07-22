<?php

namespace Database\Seeders;

use App\Models\Admin\Table as Meja;
use App\Models\SysAdmin\Company;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $companyIds = Company::pluck('company_id')->toArray();
        $cid = $companyIds[0] ?? null;
        if (!$cid) return;

        $tables = [
            ['number' => 1, 'capacity' => 2, 'status' => 'tersedia'],
            ['number' => 2, 'capacity' => 2, 'status' => 'tersedia'],
            ['number' => 3, 'capacity' => 4, 'status' => 'tersedia'],
            ['number' => 4, 'capacity' => 4, 'status' => 'tersedia'],
            ['number' => 5, 'capacity' => 4, 'status' => 'tersedia'],
            ['number' => 6, 'capacity' => 6, 'status' => 'tersedia'],
            ['number' => 7, 'capacity' => 6, 'status' => 'tersedia'],
            ['number' => 8, 'capacity' => 8, 'status' => 'tersedia'],
            ['number' => 9, 'capacity' => 8, 'status' => 'tersedia'],
            ['number' => 10, 'capacity' => 4, 'status' => 'tersedia'],
            ['number' => 11, 'capacity' => 2, 'status' => 'tersedia'],
            ['number' => 12, 'capacity' => 2, 'status' => 'tersedia'],
            ['number' => 13, 'capacity' => 4, 'status' => 'tersedia'],
            ['number' => 14, 'capacity' => 6, 'status' => 'tersedia'],
            ['number' => 15, 'capacity' => 10, 'status' => 'tersedia'],
        ];

        foreach ($tables as $data) {
            Meja::create([
                'company_id' => $cid,
                'table_number' => $data['number'],
                'table_status' => $data['status'],
                'table_capacity' => $data['capacity'],
                'table_description' => 'Meja ' . $data['number'] . ' (' . $data['capacity'] . ' kursi)',
            ]);
        }

        $this->command->info('✅ ' . count($tables) . ' meja berhasil di-seed.');
    }
}
