<?php

namespace Database\Seeders;

use App\Models\Admin\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            [
                'company_id' => '01',
                'voucher_code' => 'BARU10',
                'voucher_name' => 'Diskon Pelanggan Baru 10%',
                'voucher_description' => 'Voucher khusus pelanggan baru, potongan 10% dari total belanja.',
                'voucher_type' => 'percentage',
                'voucher_value' => 10,
                'voucher_max_discount' => 30000,
                'voucher_min_purchase' => 50000,
                'voucher_applicable_to' => 'all',
                'voucher_usage_limit' => 100,
                'voucher_usage_per_customer' => 1,
                'voucher_start_date' => now(),
                'voucher_end_date' => now()->addMonths(3),
                'voucher_status' => 1,
                'created_by' => 'seeder',
            ],
            [
                'company_id' => '01',
                'voucher_code' => 'ULTAH15',
                'voucher_name' => 'Diskon Ulang Tahun 15%',
                'voucher_description' => 'Rayakan ulang tahun dengan diskon 15% maksimal Rp50.000.',
                'voucher_type' => 'percentage',
                'voucher_value' => 15,
                'voucher_max_discount' => 50000,
                'voucher_min_purchase' => 75000,
                'voucher_applicable_to' => 'all',
                'voucher_usage_limit' => 50,
                'voucher_usage_per_customer' => 1,
                'voucher_start_date' => now(),
                'voucher_end_date' => now()->addYear(),
                'voucher_status' => 1,
                'created_by' => 'seeder',
            ],
            [
                'company_id' => '01',
                'voucher_code' => 'HEMAT20',
                'voucher_name' => 'Voucher Hemat 20rb',
                'voucher_description' => 'Potongan Rp20.000 untuk pembelian minimal Rp100.000.',
                'voucher_type' => 'nominal',
                'voucher_value' => 20000,
                'voucher_max_discount' => null,
                'voucher_min_purchase' => 100000,
                'voucher_applicable_to' => 'all',
                'voucher_usage_limit' => 200,
                'voucher_usage_per_customer' => 2,
                'voucher_start_date' => now(),
                'voucher_end_date' => now()->addMonths(2),
                'voucher_status' => 1,
                'created_by' => 'seeder',
            ],
            [
                'company_id' => '01',
                'voucher_code' => 'NGOPI50',
                'voucher_name' => 'Kopi Spesial 50%',
                'voucher_description' => 'Diskon 50% untuk semua menu kopi, maksimal potongan Rp15.000.',
                'voucher_type' => 'percentage',
                'voucher_value' => 50,
                'voucher_max_discount' => 15000,
                'voucher_min_purchase' => 0,
                'voucher_applicable_to' => 'all',
                'voucher_usage_limit' => 500,
                'voucher_usage_per_customer' => 1,
                'voucher_start_date' => now(),
                'voucher_end_date' => now()->addDays(30),
                'voucher_status' => 1,
                'created_by' => 'seeder',
            ],
            [
                'company_id' => '01',
                'voucher_code' => 'GRATIS01',
                'voucher_name' => 'Free Item Spesial',
                'voucher_description' => 'Voucher free item untuk pembelian menu spesial (contoh).',
                'voucher_type' => 'free_item',
                'voucher_value' => 1,
                'voucher_max_discount' => null,
                'voucher_min_purchase' => 50000,
                'voucher_applicable_to' => 'all',
                'voucher_usage_limit' => 30,
                'voucher_usage_per_customer' => 1,
                'voucher_start_date' => now(),
                'voucher_end_date' => now()->addMonth(),
                'voucher_status' => 1,
                'created_by' => 'seeder',
            ],
        ];

        foreach ($vouchers as $data) {
            Voucher::create($data);
        }

        $this->command->info('5 voucher berhasil dibuat.');
    }
}
