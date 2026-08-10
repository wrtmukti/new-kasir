<?php

namespace Database\Seeders\Keuangan;

use Illuminate\Database\Seeder;
use App\Models\Admin\Keuangan\CogsRawMaterial;
use App\Models\Admin\Keuangan\CogsWasteHistory;
use App\Models\Admin\Keuangan\CogsWasteLog;
use App\Models\SysAdmin\Company;

class CogsWasteLogSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('delete_status', 0)->first();
        $companyId = $company ? $company->company_id : null;

        $ayam = CogsRawMaterial::where('raw_material_code', 'RAW-AYAM01')->first();
        $cabe = CogsRawMaterial::where('raw_material_code', 'RAW-CABE01')->first();
        $gelas = CogsRawMaterial::where('raw_material_code', 'RAW-GLN01')->first();

        $wastes = [];

        if ($ayam) {
            $wastes[] = [
                'material' => $ayam,
                'qty_lost' => 1.5,
                'reason' => 'Basi/Rotten',
                'notes' => 'Ayam tidak habis terpakai di kulkas pendingin',
                'date' => now()->subDays(2),
            ];
        }

        if ($cabe) {
            $wastes[] = [
                'material' => $cabe,
                'qty_lost' => 0.5,
                'reason' => 'Expired',
                'notes' => 'Cabai rawit busuk karena lembab',
                'date' => now()->subDays(5),
            ];
        }

        if ($gelas) {
            $wastes[] = [
                'material' => $gelas,
                'qty_lost' => 10,
                'reason' => 'Tumpah/Rusak',
                'notes' => 'Gelas plastik penyok/pecah saat pengemasan',
                'date' => now()->subDays(1),
            ];
        }

        foreach ($wastes as $w) {
            $raw = $w['material'];
            $wasteCost = $w['qty_lost'] * (float) $raw->effective_price;

            $log = CogsWasteLog::create([
                'company_id' => $companyId,
                'cogs_raw_material_id' => $raw->cogs_raw_material_id,
                'qty_lost' => $w['qty_lost'],
                'waste_cost' => $wasteCost,
                'reason' => $w['reason'],
                'loss_date' => $w['date'],
                'notes' => $w['notes'],
                'created_by' => 'seeder',
            ]);

            // Deduct raw material stock for the waste
            $raw->amount = max(0, (float)$raw->amount - $w['qty_lost']);
            $raw->save();

            CogsWasteHistory::create([
                'cogs_waste_log_id' => $log->cogs_waste_log_id,
                'company_id' => $companyId,
                'cogs_raw_material_id' => $raw->cogs_raw_material_id,
                'qty_lost' => $w['qty_lost'],
                'waste_cost' => $wasteCost,
                'reason' => $w['reason'],
                'loss_date' => $log->loss_date,
                'action_type' => 'create',
                'changed_by' => 'Seeder',
                'history_remark' => 'Pencatatan awal bahan terbuang (waste log)',
                'created_by' => 'seeder',
            ]);
        }
    }
}
