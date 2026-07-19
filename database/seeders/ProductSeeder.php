<?php

namespace Database\Seeders;

use App\Models\Admin\Category;
use App\Models\Admin\Product;
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

        foreach ($products as $data) {
            $cat = $categories[$data['category']] ?? null;
            if (!$cat) continue;

            Product::create([
                'company_id' => $company->company_id,
                'category_id' => $cat->category_id,
                'product_code' => $data['code'],
                'product_name' => $data['name'],
                'product_slug' => str()->slug($data['name']),
                'product_price' => $data['price'],
                'product_status' => 1,
            ]);
        }

        $this->command->info('✅ ' . Product::count() . ' produk berhasil di-seed.');
    }
}
