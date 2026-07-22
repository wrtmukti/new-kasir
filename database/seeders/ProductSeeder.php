<?php

namespace Database\Seeders;

use App\Models\Admin\Category;
use App\Models\Admin\Product;
use App\Models\Admin\Stock;
use App\Models\SysAdmin\Company;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company) return;

        // Pastikan kategori ada
        $categories = [
            'Makanan' => Category::firstOrCreate(
                ['category_name' => 'Makanan'],
                ['category_slug' => 'makanan', 'category_type' => 'produk', 'category_status' => 1]
            ),
            'Minuman' => Category::firstOrCreate(
                ['category_name' => 'Minuman'],
                ['category_slug' => 'minuman', 'category_type' => 'produk', 'category_status' => 1]
            ),
            'Snack' => Category::firstOrCreate(
                ['category_name' => 'Snack'],
                ['category_slug' => 'snack', 'category_type' => 'produk', 'category_status' => 1]
            ),
            'Topping' => Category::firstOrCreate(
                ['category_name' => 'Topping'],
                ['category_slug' => 'topping', 'category_type' => 'produk', 'category_status' => 1]
            ),
        ];

        // Ambil stock & index by code
        $stocksByCode = Stock::pluck('stock_id', 'stock_code');

        $products = [
            // Makanan
            ['name' => 'Nasi Goreng Spesial', 'code' => 'PRD-001', 'category' => 'Makanan', 'price' => 35000],
            ['name' => 'Mie Ayam Bakso', 'code' => 'PRD-002', 'category' => 'Makanan', 'price' => 28000],
            ['name' => 'Ayam Geprek', 'code' => 'PRD-003', 'category' => 'Makanan', 'price' => 25000],
            ['name' => 'Sate Ayam', 'code' => 'PRD-004', 'category' => 'Makanan', 'price' => 30000],
            ['name' => 'Nasi Padang', 'code' => 'PRD-005', 'category' => 'Makanan', 'price' => 38000],
            ['name' => 'Kwetiau Goreng', 'code' => 'PRD-006', 'category' => 'Makanan', 'price' => 32000],
            ['name' => 'Bakso Urat', 'code' => 'PRD-007', 'category' => 'Makanan', 'price' => 27000],
            ['name' => 'Nasi Uduk', 'code' => 'PRD-008', 'category' => 'Makanan', 'price' => 22000],
            ['name' => 'Mie Goreng Jawa', 'code' => 'PRD-009', 'category' => 'Makanan', 'price' => 26000],
            ['name' => 'Soto Ayam', 'code' => 'PRD-010', 'category' => 'Makanan', 'price' => 29000],
            ['name' => 'Gado-Gado', 'code' => 'PRD-011', 'category' => 'Makanan', 'price' => 24000],
            ['name' => 'Nasi Liwet', 'code' => 'PRD-012', 'category' => 'Makanan', 'price' => 33000],

            // Minuman
            ['name' => 'Es Teh Manis', 'code' => 'PRD-013', 'category' => 'Minuman', 'price' => 5000],
            ['name' => 'Es Jeruk', 'code' => 'PRD-014', 'category' => 'Minuman', 'price' => 7000],
            ['name' => 'Kopi Hitam', 'code' => 'PRD-015', 'category' => 'Minuman', 'price' => 15000],
            ['name' => 'Kopi Susu', 'code' => 'PRD-016', 'category' => 'Minuman', 'price' => 18000],
            ['name' => 'Matcha Latte', 'code' => 'PRD-017', 'category' => 'Minuman', 'price' => 22000],
            ['name' => 'Jus Alpukat', 'code' => 'PRD-018', 'category' => 'Minuman', 'price' => 16000],
            ['name' => 'Jus Mangga', 'code' => 'PRD-019', 'category' => 'Minuman', 'price' => 15000],
            ['name' => 'Milkshake Coklat', 'code' => 'PRD-020', 'category' => 'Minuman', 'price' => 25000],
            ['name' => 'Teh Tarik', 'code' => 'PRD-021', 'category' => 'Minuman', 'price' => 12000],
            ['name' => 'Air Mineral', 'code' => 'PRD-022', 'category' => 'Minuman', 'price' => 3000],

            // Snack
            ['name' => 'Kentang Goreng', 'code' => 'PRD-023', 'category' => 'Snack', 'price' => 15000],
            ['name' => 'Pisang Goreng', 'code' => 'PRD-024', 'category' => 'Snack', 'price' => 10000],
            ['name' => 'Tahu Crispy', 'code' => 'PRD-025', 'category' => 'Snack', 'price' => 12000],
            ['name' => 'Tempe Mendoan', 'code' => 'PRD-026', 'category' => 'Snack', 'price' => 8000],
            ['name' => 'Cireng Isi', 'code' => 'PRD-027', 'category' => 'Snack', 'price' => 13000],
            ['name' => 'Siomay', 'code' => 'PRD-028', 'category' => 'Snack', 'price' => 15000],
            ['name' => 'Risol Mayo', 'code' => 'PRD-029', 'category' => 'Snack', 'price' => 11000],
            ['name' => 'Lumpia', 'code' => 'PRD-030', 'category' => 'Snack', 'price' => 12000],

            // Topping
            ['name' => 'Keju Parut', 'code' => 'PRD-031', 'category' => 'Topping', 'price' => 5000],
            ['name' => 'Meses Coklat', 'code' => 'PRD-032', 'category' => 'Topping', 'price' => 4000],
            ['name' => 'Kacang Almond', 'code' => 'PRD-033', 'category' => 'Topping', 'price' => 8000],
            ['name' => 'Bubble Pearl', 'code' => 'PRD-034', 'category' => 'Topping', 'price' => 6000],
            ['name' => 'Sirup Caramel', 'code' => 'PRD-035', 'category' => 'Topping', 'price' => 5000],
            ['name' => 'Whipped Cream', 'code' => 'PRD-036', 'category' => 'Topping', 'price' => 7000],
        ];

        // Mapping tiap produk → stock apa aja yg dipake + quantity (dalam satuan unit stock)
        $productStocks = [
            'PRD-001' => ['BRS-001' => 200, 'MNY-001' => 50, 'CBE-001' => 20, 'BWG-001' => 10], // Nasi Goreng
            'PRD-002' => ['AYM-001' => 100, 'BWG-001' => 10, 'CBE-001' => 10],                 // Mie Ayam Bakso
            'PRD-003' => ['AYM-001' => 150, 'MNY-001' => 100, 'CBE-001' => 15, 'BWG-001' => 10, 'BRS-001' => 150], // Ayam Geprek
            'PRD-004' => ['AYM-001' => 100, 'BRS-001' => 100],                                  // Sate Ayam
            'PRD-005' => ['BRS-001' => 200, 'AYM-001' => 100, 'CBE-001' => 15, 'BWG-001' => 10], // Nasi Padang
            'PRD-006' => ['MNY-001' => 50, 'BWG-001' => 10, 'CBE-001' => 10],                   // Kwetiau Goreng
            'PRD-007' => ['AYM-001' => 100, 'BWG-001' => 5],                                     // Bakso Urat
            'PRD-008' => ['BRS-001' => 200, 'CBE-001' => 10, 'BWG-001' => 5],                   // Nasi Uduk
            'PRD-009' => ['MNY-001' => 50, 'BWG-001' => 10, 'CBE-001' => 10],                   // Mie Goreng Jawa
            'PRD-010' => ['AYM-001' => 100, 'BRS-001' => 100, 'BWG-001' => 10],                 // Soto Ayam
            'PRD-011' => ['BWG-001' => 10],                                                      // Gado-Gado
            'PRD-012' => ['BRS-001' => 250, 'AYM-001' => 100, 'CBE-001' => 10, 'BWG-001' => 10], // Nasi Liwet
            // Minuman — pake gelas plastik
            'PRD-013' => ['GLN-001' => 1],
            'PRD-014' => ['GLN-001' => 1],
            'PRD-015' => ['GLN-001' => 1],
            'PRD-016' => ['GLN-001' => 1],
            'PRD-017' => ['GLN-001' => 1],
            'PRD-018' => ['GLN-001' => 1],
            'PRD-019' => ['GLN-001' => 1],
            'PRD-020' => ['GLN-001' => 1],
            'PRD-021' => ['GLN-001' => 1],
            'PRD-022' => ['GLN-001' => 1],
            // Snack
            'PRD-023' => ['MNY-001' => 30, 'PLST-001' => 1],     // Kentang Goreng
            'PRD-024' => ['MNY-001' => 20, 'PLST-001' => 1],     // Pisang Goreng
            'PRD-025' => ['MNY-001' => 20, 'PLST-001' => 1],     // Tahu Crispy
            'PRD-026' => ['MNY-001' => 15, 'PLST-001' => 1],     // Tempe Mendoan
            'PRD-027' => ['MNY-001' => 20, 'PLST-001' => 1],     // Cireng Isi
            'PRD-028' => ['PLST-001' => 1],                       // Siomay
            'PRD-029' => ['MNY-001' => 10, 'PLST-001' => 1],     // Risol Mayo
            'PRD-030' => ['MNY-001' => 15, 'PLST-001' => 1],     // Lumpia
            // Topping — semua pake plastik
            'PRD-031' => ['PLST-001' => 1],
            'PRD-032' => ['PLST-001' => 1],
            'PRD-033' => ['PLST-001' => 1],
            'PRD-034' => ['PLST-001' => 1],
            'PRD-035' => ['PLST-001' => 1],
            'PRD-036' => ['PLST-001' => 1],
        ];

        foreach ($products as $data) {
            $cat = $categories[$data['category']] ?? null;
            if (!$cat) continue;

            $product = Product::create([
                'company_id' => $company->company_id,
                'category_id' => $cat->category_id,
                'product_code' => $data['code'],
                'product_name' => $data['name'],
                'product_slug' => str()->slug($data['name']),
                'product_price' => $data['price'],
                'product_status' => 1,
            ]);

            // Hubungin product ke stock (pivot product_stock)
            if (isset($productStocks[$data['code']])) {
                $syncData = [];
                foreach ($productStocks[$data['code']] as $stockCode => $qty) {
                    if (isset($stocksByCode[$stockCode])) {
                        $syncData[$stocksByCode[$stockCode]] = [
                            'quantity' => $qty,
                            'delete_status' => 0,
                        ];
                    }
                }
                if (!empty($syncData)) {
                    $product->stocks()->sync($syncData);
                }
            }
        }

        $this->command->info('✅ ' . Product::count() . ' produk + pivot stock berhasil di-seed.');
    }
}
