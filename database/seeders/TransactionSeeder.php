<?php

namespace Database\Seeders;

use App\Models\Admin\Order;
use App\Models\Admin\Transaction;
use App\Models\Admin\TransactionItem;
use App\Models\SysAdmin\Company;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company) return;

        $completedOrders = Order::where('delete_status', 0)
            ->where('order_status', 'completed')
            ->with('products')
            ->get();

        if ($completedOrders->isEmpty()) {
            $this->command->info('⚠️ Tidak ada order completed, lewati TransactionSeeder.');
            return;
        }

        foreach ($completedOrders as $order) {
            // Hitung subtotal
            $subtotal = 0;
            foreach ($order->products as $product) {
                $qty = (int) $product->pivot->quantity;
                $price = (float) $product->product_price;
                $subtotal += $price * $qty;
            }

            // Simpan transaction
            $transaction = Transaction::create([
                'company_id' => $order->company_id,
                'transaction_code' => 'TRX-' . $order->order_id . '-' . $order->created_at->format('Ymd'),
                'transaction_date' => $order->created_at,
                'transaction_subtotal' => $subtotal,
                'transaction_tax' => 0,
                'transaction_service_charge' => 0,
                'transaction_grand_total' => $subtotal,
                'transaction_status' => 'success',
                'transaction_table_id' => $order->order_table_id,
                'transaction_customer_id' => $order->order_customer_id,
                'transaction_remark' => 'Dari pesanan #' . $order->order_id,
                'created_by' => 'seeder',
            ]);

            // Simpan transaction_items
            foreach ($order->products as $product) {
                $qty = (int) $product->pivot->quantity;
                $price = (float) $product->product_price;

                TransactionItem::create([
                    'company_id' => $order->company_id,
                    'transaction_id' => $transaction->transaction_id,
                    'product_id' => $product->product_id,
                    'product_name' => $product->product_name,
                    'price' => $price,
                    'qty' => $qty,
                    'subtotal' => $price * $qty,
                    'note' => $product->pivot->note,
                    'created_by' => 'seeder',
                ]);
            }
        }

        $this->command->info('✅ ' . Transaction::count() . ' transaksi + item berhasil di-seed dari order completed.');
    }
}
