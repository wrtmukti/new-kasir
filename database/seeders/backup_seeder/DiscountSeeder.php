<?php

namespace Database\Seeders;

use App\Models\Admin\Discount;
use App\Models\Admin\Product;
use App\Models\Admin\Outlet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        $outlet = Outlet::where('delete_status', 0)->first();
        $outletId = $outlet ? $outlet->outlet_id : null;

        if (!$outletId) return;

        $discounts = [
            [
                'key' => 'weekend',
                'outlet_id' => $outletId,
                'discount_name' => 'Diskon Akhir Pekan',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'discount_max_amount' => 50000,
                'discount_description' => 'Diskon 10% untuk transaksi di akhir pekan',
                'discount_status' => 1,
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(30),
            ],
            [
                'key' => 'lebaran',
                'outlet_id' => $outletId,
                'discount_name' => 'Diskon Spesial Lebaran',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'discount_max_amount' => 100000,
                'discount_description' => 'Diskon 15% spesial lebaran',
                'discount_status' => 1,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(60),
            ],
            [
                'key' => 'member',
                'outlet_id' => $outletId,
                'discount_name' => 'Diskon Member Baru',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'discount_max_amount' => 10000,
                'discount_description' => 'Diskon 10% untuk member baru',
                'discount_status' => 1,
                'start_date' => null,
                'end_date' => null,
            ],
            [
                'key' => 'minorder',
                'outlet_id' => $outletId,
                'discount_name' => 'Diskon Minimum Order',
                'discount_type' => 'percentage',
                'discount_value' => 12,
                'discount_max_amount' => 15000,
                'discount_description' => 'Diskon 12% untuk minimum order Rp100.000',
                'discount_status' => 1,
                'start_date' => null,
                'end_date' => null,
            ],
            [
                'key' => 'nonaktif',
                'outlet_id' => $outletId,
                'discount_name' => 'Diskon Nonaktif (Test)',
                'discount_type' => 'percentage',
                'discount_value' => 5,
                'discount_max_amount' => 10000,
                'discount_description' => 'Diskon ini sudah tidak aktif',
                'discount_status' => 0,
                'start_date' => now()->subDays(60),
                'end_date' => now()->subDays(1),
            ],
        ];

        $created = [];
        foreach ($discounts as $data) {
            $key = $data['key'];
            unset($data['key']);
            $created[$key] = Discount::create($data);
        }

        // === Hubungin beberapa produk ke diskon ===

        $products = Product::where('delete_status', 0)->get()->keyBy('product_code');

        $links = [
            // Diskon Akhir Pekan (10%) → Nasi Goreng, Mie Ayam, Kwetiau
            ['discount' => $created['weekend'], 'codes' => ['PRD-001', 'PRD-002', 'PRD-006']],
            // Diskon Spesial Lebaran (15%) → Ayam Geprek, Sate Ayam, Nasi Padang
            ['discount' => $created['lebaran'], 'codes' => ['PRD-003', 'PRD-004', 'PRD-005']],
            // Diskon Member Baru (Rp20.000) → Bakso Urat, Mie Goreng Jawa
            ['discount' => $created['member'], 'codes' => ['PRD-007', 'PRD-009']],
            // Diskon Minimum Order (Rp15.000) → Nasi Uduk
            ['discount' => $created['minorder'], 'codes' => ['PRD-008']],
        ];

        foreach ($links as $link) {
            $discount = $link['discount'];
            foreach ($link['codes'] as $code) {
                $product = $products->get($code);
                if (!$product) continue;

                // Simpan ke pivot discount_product (bukan kolom produk)
                DB::table('discount_product')->insert([
                    'outlet_id' => $outletId,
                    'product_id' => $product->product_id,
                    'discount_id' => $discount->discount_id,
                    'start_date' => now(),
                    'created_by' => 'seeder',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Log ke product_histories
                DB::table('product_histories')->insert([
                    'product_id' => $product->product_id,
                    'outlet_id' => $outletId,
                    'history_code' => $product->product_code,
                    'history_name' => $product->product_name,
                    'history_slug' => $product->product_slug,
                    'history_description' => $product->product_description,
                    'history_grand_total' => $product->product_price,
                    'history_price' => $product->product_price,
                    'history_discount' => $discount->discount_value,
                    'history_status' => $product->product_status,
                    'history_image' => $product->product_image,
                    'effective_date' => now(),
                    'action_type' => 'create',
                    'changed_by' => 'seeder',
                    'created_by' => 'seeder',
                    'delete_status' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $totalLinked = collect($links)->sum(fn($l) => count($l['codes']));
        $this->command->info('✅ ' . count($created) . ' diskon + ' . $totalLinked . ' produk di-link.');
    }
}
