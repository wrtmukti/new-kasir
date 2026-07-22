<?php

namespace Database\Seeders;

use App\Models\Admin\Order;
use App\Models\Admin\Product;
use App\Models\SysAdmin\Company;
use App\Models\Admin\Table;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company) return;

        $products = Product::where('delete_status', 0)->get()->keyBy('product_code');
        $tables = Table::where('delete_status', 0)->get();

        $orders = [
            [
                'type' => 'dine_in',
                'table_idx' => 0,
                'status' => 'completed',
                'items' => [
                    ['code' => 'PRD-001', 'qty' => 2, 'note' => null],     // Nasi Goreng
                    ['code' => 'PRD-013', 'qty' => 2, 'note' => 'Es batu'], // Es Teh
                ],
            ],
            [
                'type' => 'dine_in',
                'table_idx' => 1,
                'status' => 'completed',
                'items' => [
                    ['code' => 'PRD-003', 'qty' => 1, 'note' => 'Level 3'], // Ayam Geprek
                    ['code' => 'PRD-015', 'qty' => 1, 'note' => null],     // Kopi Hitam
                ],
            ],
            [
                'type' => 'take_away',
                'table_idx' => null,
                'status' => 'completed',
                'items' => [
                    ['code' => 'PRD-007', 'qty' => 3, 'note' => 'Pake sambel'], // Bakso
                    ['code' => 'PRD-023', 'qty' => 2, 'note' => null],         // Kentang
                ],
            ],
            [
                'type' => 'dine_in',
                'table_idx' => 2,
                'status' => 'in_progress',
                'items' => [
                    ['code' => 'PRD-005', 'qty' => 1, 'note' => null],     // Nasi Padang
                    ['code' => 'PRD-016', 'qty' => 1, 'note' => 'Dingin'], // Kopi Susu
                    ['code' => 'PRD-031', 'qty' => 1, 'note' => null],     // Keju Parut
                ],
            ],
            [
                'type' => 'delivery',
                'table_idx' => null,
                'status' => 'in_progress',
                'items' => [
                    ['code' => 'PRD-009', 'qty' => 2, 'note' => null],     // Mie Goreng
                    ['code' => 'PRD-014', 'qty' => 2, 'note' => null],     // Es Jeruk
                    ['code' => 'PRD-024', 'qty' => 1, 'note' => null],     // Pisang Goreng
                ],
            ],
            [
                'type' => 'dine_in',
                'table_idx' => 3,
                'status' => 'in_progress',
                'items' => [
                    ['code' => 'PRD-002', 'qty' => 1, 'note' => null],     // Mie Ayam
                    ['code' => 'PRD-017', 'qty' => 1, 'note' => null],     // Matcha Latte
                ],
            ],
        ];

        foreach ($orders as $data) {
            $tableId = null;
            if ($data['table_idx'] !== null && isset($tables[$data['table_idx']])) {
                $tableId = $tables[$data['table_idx']]->table_id;
            }

            $grandTotal = 0;
            $syncData = [];

            foreach ($data['items'] as $item) {
                $product = $products->get($item['code']);
                if (!$product) continue;

                $price = (float) $product->product_price;
                $subtotal = $price * $item['qty'];
                $grandTotal += $subtotal;

                $syncData[$product->product_id] = [
                    'company_id' => $company->company_id,
                    'quantity' => $item['qty'],
                    'note' => $item['note'],
                    'delete_status' => 0,
                    'created_by' => 'seeder',
                ];
            }

            if (empty($syncData)) continue;

            DB::transaction(function () use ($company, $data, $tableId, $grandTotal, $syncData) {
                $order = Order::create([
                    'company_id' => $company->company_id,
                    'order_type' => $data['type'],
                    'order_status' => $data['status'],
                    'order_grand_total' => $grandTotal,
                    'order_table_id' => $tableId,
                    'created_by' => 'seeder',
                ]);

                $order->products()->sync($syncData);

                // Update status meja jadi terisi kalo dine_in & in_progress
                if ($data['type'] === 'dine_in' && $tableId && $data['status'] === 'in_progress') {
                    Table::where('table_id', $tableId)->update(['table_status' => 'terisi']);
                }
            });
        }

        $this->command->info('✅ ' . Order::count() . ' pesanan + order_product berhasil di-seed.');
    }
}
