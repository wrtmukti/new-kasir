<?php

namespace Database\Seeders\Keuangan;

use Illuminate\Database\Seeder;
use App\Models\Admin\Keuangan\HppFinancialReport;
use App\Models\Admin\Outlet;

class HppFinancialReportSeeder extends Seeder
{
    public function run(): void
    {
        $outlet = Outlet::where('delete_status', 0)->first();
        $outletId = $outlet ? $outlet->outlet_id : null;

        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');

        $reports = [
            [
                'year' => 2026,
                'month' => 8,
                'total_labor_cost' => 8500000.00,
                'total_overhead_cost' => 3200000.00,
                'notes' => 'Gaji 3 staf dapur & 2 kasir + Listrik & WiFi bulan Agustus 2026',
            ],
            [
                'year' => $currentYear,
                'month' => $currentMonth,
                'total_labor_cost' => 9000000.00,
                'total_overhead_cost' => 3500000.00,
                'notes' => 'Catatan Operasional: Gaji Karyawan Rp 9.000.000 + Listrik & Sewa Rp 3.500.000',
            ]
        ];

        foreach ($reports as $r) {
            $report = HppFinancialReport::where('year', $r['year'])
                ->where('month', $r['month'])
                ->first();

            if (!$report) {
                $report = new HppFinancialReport();
            }

            $report->outlet_id = $outletId;
            $report->year = $r['year'];
            $report->month = $r['month'];
            $report->total_labor_cost = $r['total_labor_cost'];
            $report->total_overhead_cost = $r['total_overhead_cost'];
            $report->notes = $r['notes'];
            $report->created_by = 'seeder';
            $report->updated_by = 'seeder';
            $report->save();
        }
    }
}
