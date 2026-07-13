<?php

namespace Database\Seeders;

use App\Models\Admin\Stock;
use App\Models\SysAdmin\Company;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $companyIds = Company::pluck('company_id')->toArray();
        $cid = $companyIds[0] ?? null;

        $stocks = [
            [
                'company_id' => $cid,
                'stock_code' => 'BRS-001',
                'stock_name' => 'Beras',
                'stock_slug' => 'beras',
                'stock_description' => 'Beras putih kualitas premium',
                'stock_type' => 'bahan pokok',
                'stock_unit' => 'kg',
                'stock_amount' => 50,
                'stock_price' => 15000,
                'stock_status' => 1,
            ],
            [
                'company_id' => $cid,
                'stock_code' => 'MNY-001',
                'stock_name' => 'Minyak Goreng',
                'stock_slug' => 'minyak-goreng',
                'stock_description' => 'Minyak goreng kemasan 1 liter',
                'stock_type' => 'bahan pokok',
                'stock_unit' => 'liter',
                'stock_amount' => 30,
                'stock_price' => 18000,
                'stock_status' => 1,
            ],
            [
                'company_id' => $cid,
                'stock_code' => 'CBE-001',
                'stock_name' => 'Cabai Merah',
                'stock_slug' => 'cabai-merah',
                'stock_description' => 'Cabai merah segar',
                'stock_type' => 'bumbu',
                'stock_unit' => 'kg',
                'stock_amount' => 10,
                'stock_price' => 45000,
                'stock_status' => 1,
            ],
            [
                'company_id' => $cid,
                'stock_code' => 'BWG-001',
                'stock_name' => 'Bawang Putih',
                'stock_slug' => 'bawang-putih',
                'stock_description' => 'Bawang putih kualitas ekspor',
                'stock_type' => 'bumbu',
                'stock_unit' => 'kg',
                'stock_amount' => 15,
                'stock_price' => 35000,
                'stock_status' => 1,
            ],
            [
                'company_id' => $cid,
                'stock_code' => 'AYM-001',
                'stock_name' => 'Ayam Fillet',
                'stock_slug' => 'ayam-fillet',
                'stock_description' => 'Ayam fillet paha & dada',
                'stock_type' => 'protein',
                'stock_unit' => 'kg',
                'stock_amount' => 25,
                'stock_price' => 38000,
                'stock_status' => 1,
            ],
            [
                'company_id' => $cid,
                'stock_code' => 'PLST-001',
                'stock_name' => 'Plastik Kemasan',
                'stock_slug' => 'plastik-kemasan',
                'stock_type' => 'kemasan',
                'stock_unit' => 'pcs',
                'stock_amount' => 200,
                'stock_price' => 500,
                'stock_status' => 1,
            ],
            [
                'company_id' => $cid,
                'stock_code' => 'GLN-001',
                'stock_name' => 'Gelas Plastik',
                'stock_slug' => 'gelas-plastik',
                'stock_type' => 'kemasan',
                'stock_unit' => 'pcs',
                'stock_amount' => 500,
                'stock_price' => 250,
                'stock_status' => 1,
            ],
        ];

        foreach ($stocks as $data) {
            Stock::create($data);
        }
    }
}
