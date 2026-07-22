<?php

namespace Database\Seeders;

use App\Models\Admin\Supplier;
use App\Models\SysAdmin\Company;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $companyIds = Company::pluck('company_id')->toArray();
        $cid = $companyIds[0] ?? null;
        if (!$cid) return;

        $suppliers = [
            [
                'company_id' => $cid,
                'supplier_code' => 'SUP-001',
                'supplier_name' => 'PT Sumber Bahan Pangan',
                'supplier_contact' => 'Budi Santoso',
                'supplier_phone' => '021-5551234',
                'supplier_address' => 'Jl. Industri Raya No. 45, Jakarta Utara',
                'supplier_status' => 1,
            ],
            [
                'company_id' => $cid,
                'supplier_code' => 'SUP-002',
                'supplier_name' => 'CV Berkah Minyak Sejahtera',
                'supplier_contact' => 'Ahmad Rizki',
                'supplier_phone' => '021-5555678',
                'supplier_address' => 'Jl. Pelabuhan No. 12, Tanjung Priok',
                'supplier_status' => 1,
            ],
            [
                'company_id' => $cid,
                'supplier_code' => 'SUP-003',
                'supplier_name' => 'UD Ayam Segar',
                'supplier_contact' => 'Siti Nurhaliza',
                'supplier_phone' => '0251-888999',
                'supplier_address' => 'Jl. Peternakan No. 78, Bogor',
                'supplier_status' => 1,
            ],
            [
                'company_id' => $cid,
                'supplier_code' => 'SUP-004',
                'supplier_name' => 'Toko Bumbu Makmur',
                'supplier_contact' => 'Hendra Gunawan',
                'supplier_phone' => '022-777888',
                'supplier_address' => 'Jl. Pasar Induk No. 33, Bandung',
                'supplier_status' => 1,
            ],
            [
                'company_id' => $cid,
                'supplier_code' => 'SUP-005',
                'supplier_name' => 'CV Kemasan Plastik Prima',
                'supplier_contact' => 'Dewi Lestari',
                'supplier_phone' => '024-444555',
                'supplier_address' => 'Jl. Industri No. 90, Semarang',
                'supplier_status' => 1,
            ],
        ];

        foreach ($suppliers as $data) {
            Supplier::create($data);
        }

        $this->command->info('✅ ' . count($suppliers) . ' supplier berhasil di-seed.');
    }
}
