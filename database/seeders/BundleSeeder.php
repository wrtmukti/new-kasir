<?php

namespace Database\Seeders;

use App\Models\Admin\Bundle;
use App\Models\Admin\Product;
use App\Models\SysAdmin\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BundleSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company) return;

        // Ambil produk by code
        $products = Product::where('delete_status', 0)->get()->keyBy('product_code');

        $bundles = [
            [
                'code' => 'BND-001',
                'name' => 'Paket Nasi Goreng Komplit',
                'desc' => 'Nasi goreng spesial + es teh + topping keju',
                'price' => 42000,
                'items' => [
                    ['code' => 'PRD-001', 'qty' => 1], // Nasi Goreng Spesial
                    ['code' => 'PRD-013', 'qty' => 1], // Es Teh Manis
                    ['code' => 'PRD-031', 'qty' => 1], // Keju Parut
                ],
            ],
            [
                'code' => 'BND-002',
                'name' => 'Paket Ayam Geprek',
                'desc' => 'Ayam geprek + es teh + tahu crispy',
                'price' => 35000,
                'items' => [
                    ['code' => 'PRD-003', 'qty' => 1], // Ayam Geprek
                    ['code' => 'PRD-013', 'qty' => 1], // Es Teh Manis
                    ['code' => 'PRD-025', 'qty' => 1], // Tahu Crispy
                ],
            ],
            [
                'code' => 'BND-003',
                'name' => 'Paket Bakso Urat',
                'desc' => 'Bakso urat + es jeruk + pisang goreng',
                'price' => 38000,
                'items' => [
                    ['code' => 'PRD-007', 'qty' => 1], // Bakso Urat
                    ['code' => 'PRD-014', 'qty' => 1], // Es Jeruk
                    ['code' => 'PRD-024', 'qty' => 1], // Pisang Goreng
                ],
            ],
            [
                'code' => 'BND-004',
                'name' => 'Paket Nasi Goreng Spesial',
                'desc' => 'Nasi goreng + kopi susu',
                'price' => 45000,
                'items' => [
                    ['code' => 'PRD-001', 'qty' => 1], // Nasi Goreng Spesial
                    ['code' => 'PRD-016', 'qty' => 1], // Kopi Susu
                ],
            ],
            [
                'code' => 'BND-005',
                'name' => 'Paket Nasi Padang',
                'desc' => 'Nasi padang + es teh manis + kentang goreng',
                'price' => 48000,
                'items' => [
                    ['code' => 'PRD-005', 'qty' => 1], // Nasi Padang
                    ['code' => 'PRD-013', 'qty' => 1], // Es Teh Manis
                    ['code' => 'PRD-023', 'qty' => 1], // Kentang Goreng
                ],
            ],
            [
                'code' => 'BND-006',
                'name' => 'Paket Snack',
                'desc' => 'Kentang goreng + pisang goreng + tahu crispy + es teh',
                'price' => 28000,
                'items' => [
                    ['code' => 'PRD-023', 'qty' => 1], // Kentang Goreng
                    ['code' => 'PRD-024', 'qty' => 1], // Pisang Goreng
                    ['code' => 'PRD-025', 'qty' => 1], // Tahu Crispy
                    ['code' => 'PRD-013', 'qty' => 1], // Es Teh Manis
                ],
            ],
            [
                'code' => 'BND-007',
                'name' => 'Paket Minuman',
                'desc' => 'Matcha latte + milkshake coklat + whipped cream',
                'price' => 40000,
                'items' => [
                    ['code' => 'PRD-017', 'qty' => 1], // Matcha Latte
                    ['code' => 'PRD-020', 'qty' => 1], // Milkshake Coklat
                    ['code' => 'PRD-036', 'qty' => 1], // Whipped Cream
                ],
            ],
            [
                'code' => 'BND-008',
                'name' => 'Paket Sate Ayam',
                'desc' => 'Sate ayam + nasi uduk + es jeruk',
                'price' => 50000,
                'items' => [
                    ['code' => 'PRD-004', 'qty' => 1], // Sate Ayam
                    ['code' => 'PRD-008', 'qty' => 1], // Nasi Uduk
                    ['code' => 'PRD-014', 'qty' => 1], // Es Jeruk
                ],
            ],
        ];

        $count = 0;

        foreach ($bundles as $data) {
            // Hitung total normal untuk cek harga
            $totalNormal = 0;
            foreach ($data['items'] as $item) {
                $product = $products[$item['code']] ?? null;
                if ($product) {
                    $totalNormal += $product->product_price * $item['qty'];
                }
            }

            // Harga paket harus lebih murah dari total normal
            $bundlePrice = min($data['price'], $totalNormal - ($totalNormal * 0.1));

            $bundle = Bundle::create([
                'company_id' => $company->company_id,
                'bundle_code' => $data['code'],
                'bundle_name' => $data['name'],
                'bundle_slug' => str()->slug($data['name']),
                'bundle_description' => $data['desc'],
                'bundle_price' => $bundlePrice,
                'bundle_status' => 1,
            ]);

            $syncData = [];
            foreach ($data['items'] as $item) {
                $product = $products[$item['code']] ?? null;
                if ($product) {
                    $syncData[$product->product_id] = [
                        'quantity' => $item['qty'],
                        'price_snapshot' => $product->product_price,
                        'delete_status' => 0,
                    ];
                }
            }

            if (!empty($syncData)) {
                $bundle->products()->sync($syncData);
            }

            $count++;
        }

        $this->command->info('✅ ' . $count . ' bundle berhasil di-seed.');
    }
}
