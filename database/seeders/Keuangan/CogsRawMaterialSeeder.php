<?php

namespace Database\Seeders\Keuangan;

use Illuminate\Database\Seeder;
use App\Models\Admin\Keuangan\CogsRawMaterial;
use App\Models\Admin\Keuangan\CogsRawMaterialHistory;
use App\Models\SysAdmin\Company;

class CogsRawMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('delete_status', 0)->first();
        $companyId = $company ? $company->company_id : null;

        $materials = [
            [
                'raw_material_code' => 'RAW-AYAM01',
                'name' => 'Daging Ayam Utuh',
                'unit' => 'kg',
                'amount' => 60.0,
                'min_amount' => 10.0,
                'price_per_unit' => 38000,
                'loss_percent' => 20.0, // Yield 80% -> Effective Rp 47,500/kg
            ],
            [
                'raw_material_code' => 'RAW-SAPI01',
                'name' => 'Daging Sapi Sirloin',
                'unit' => 'kg',
                'amount' => 40.0,
                'min_amount' => 5.0,
                'price_per_unit' => 120000,
                'loss_percent' => 10.0, // Yield 90% -> Effective Rp 133,333/kg
            ],
            [
                'raw_material_code' => 'RAW-SEAFOOD01',
                'name' => 'Udang & Cumi Segar',
                'unit' => 'kg',
                'amount' => 25.0,
                'min_amount' => 5.0,
                'price_per_unit' => 85000,
                'loss_percent' => 15.0, // Yield 85% -> Effective Rp 100,000/kg
            ],
            [
                'raw_material_code' => 'RAW-BERAS01',
                'name' => 'Beras Premium',
                'unit' => 'kg',
                'amount' => 150.0,
                'min_amount' => 20.0,
                'price_per_unit' => 15000,
                'loss_percent' => 0.0,
            ],
            [
                'raw_material_code' => 'RAW-MINYAK01',
                'name' => 'Minyak Goreng',
                'unit' => 'liter',
                'amount' => 80.0,
                'min_amount' => 10.0,
                'price_per_unit' => 18000,
                'loss_percent' => 0.0,
            ],
            [
                'raw_material_code' => 'RAW-CABE01',
                'name' => 'Cabai Rawit Merah',
                'unit' => 'kg',
                'amount' => 20.0,
                'min_amount' => 3.0,
                'price_per_unit' => 45000,
                'loss_percent' => 10.0,
            ],
            [
                'raw_material_code' => 'RAW-BAWANG01',
                'name' => 'Bawang Putih & Merah',
                'unit' => 'kg',
                'amount' => 25.0,
                'min_amount' => 5.0,
                'price_per_unit' => 35000,
                'loss_percent' => 5.0,
            ],
            [
                'raw_material_code' => 'RAW-TELUR01',
                'name' => 'Telur Ayam Segar',
                'unit' => 'kg',
                'amount' => 35.0,
                'min_amount' => 5.0,
                'price_per_unit' => 28000,
                'loss_percent' => 2.0,
            ],
            [
                'raw_material_code' => 'RAW-TEPUNG01',
                'name' => 'Tepung Terigu Serbaguna',
                'unit' => 'kg',
                'amount' => 40.0,
                'min_amount' => 5.0,
                'price_per_unit' => 12000,
                'loss_percent' => 0.0,
            ],
            [
                'raw_material_code' => 'RAW-KOPI01',
                'name' => 'Biji Kopi Arabika',
                'unit' => 'kg',
                'amount' => 15.0,
                'min_amount' => 2.0,
                'price_per_unit' => 120000,
                'loss_percent' => 2.0,
            ],
            [
                'raw_material_code' => 'RAW-TEH01',
                'name' => 'Teh Tubruk Premium',
                'unit' => 'kg',
                'amount' => 15.0,
                'min_amount' => 2.0,
                'price_per_unit' => 40000,
                'loss_percent' => 0.0,
            ],
            [
                'raw_material_code' => 'RAW-SUSU01',
                'name' => 'Susu UHT / Fresh Milk',
                'unit' => 'liter',
                'amount' => 50.0,
                'min_amount' => 10.0,
                'price_per_unit' => 20000,
                'loss_percent' => 0.0,
            ],
            [
                'raw_material_code' => 'RAW-SYRUP01',
                'name' => 'Syrup & Gula Cair',
                'unit' => 'liter',
                'amount' => 30.0,
                'min_amount' => 5.0,
                'price_per_unit' => 30000,
                'loss_percent' => 0.0,
            ],
            [
                'raw_material_code' => 'RAW-BUAH01',
                'name' => 'Buah Alpukat & Mangga',
                'unit' => 'kg',
                'amount' => 20.0,
                'min_amount' => 3.0,
                'price_per_unit' => 25000,
                'loss_percent' => 20.0,
            ],
            [
                'raw_material_code' => 'RAW-GLN01',
                'name' => 'Gelas Plastik 16oz',
                'unit' => 'pcs',
                'amount' => 1000.0,
                'min_amount' => 100.0,
                'price_per_unit' => 250,
                'loss_percent' => 0.0,
            ],
            [
                'raw_material_code' => 'RAW-PLST01',
                'name' => 'Plastik Kemasan Takeaway',
                'unit' => 'pcs',
                'amount' => 1000.0,
                'min_amount' => 100.0,
                'price_per_unit' => 500,
                'loss_percent' => 0.0,
            ],
            [
                'raw_material_code' => 'RAW-BOX01',
                'name' => 'Dus Box Packaging Eco',
                'unit' => 'pcs',
                'amount' => 500.0,
                'min_amount' => 50.0,
                'price_per_unit' => 1200,
                'loss_percent' => 0.0,
            ],
        ];

        foreach ($materials as $m) {
            $raw = CogsRawMaterial::where('raw_material_code', $m['raw_material_code'])->first();
            if (!$raw) {
                $raw = new CogsRawMaterial();
            }
            $raw->company_id = $companyId;
            $raw->raw_material_code = $m['raw_material_code'];
            $raw->name = $m['name'];
            $raw->slug = \Illuminate\Support\Str::slug($m['name']);
            $raw->unit = $m['unit'];
            $raw->amount = $m['amount'];
            $raw->min_amount = $m['min_amount'];
            $raw->price_per_unit = $m['price_per_unit'];
            $raw->loss_percent = $m['loss_percent'];
            $raw->created_by = 'seeder';
            $raw->calculatePrices();
            $raw->save();

            // History Log Seeder
            $history = CogsRawMaterialHistory::where('cogs_raw_material_id', $raw->cogs_raw_material_id)
                ->where('action_type', 'create')
                ->first();

            if (!$history) {
                CogsRawMaterialHistory::create([
                    'cogs_raw_material_id' => $raw->cogs_raw_material_id,
                    'company_id' => $companyId,
                    'name' => $raw->name,
                    'unit' => $raw->unit,
                    'amount' => $raw->amount,
                    'price_per_unit' => $raw->price_per_unit,
                    'loss_percent' => $raw->loss_percent,
                    'yield_percent' => $raw->yield_percent,
                    'effective_price' => $raw->effective_price,
                    'action_type' => 'create',
                    'changed_by' => 'Seeder',
                    'effective_date' => now(),
                    'history_remark' => 'Initial seeder data bahan mentah COGS',
                    'created_by' => 'seeder',
                ]);
            }
        }
    }
}
