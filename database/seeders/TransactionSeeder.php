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
            $order->load('products');

            $items = [];
            $totalSubtotal = 0;

            foreach ($order->products as $product) {
                $qty = (int) $product->pivot->quantity;
                $price = (float) $product->product_price;
                $activeDisc = $product->activeDiscount()->first();
                $discountType = $activeDisc?->discount_type;
                $discountValue = $activeDisc ? (float) ($activeDisc->discount_value ?? 0) : 0;

                // Hitung discount_amount
                $discountAmount = 0;
                if ($discountType === 'percentage' && $discountValue > 0) {
                    $discountAmount = round($price * ($discountValue / 100), 2);
                } elseif ($discountType === 'nominal' && $discountValue > 0) {
                    $discountAmount = min($discountValue, $price);
                }
                $discountAmount = min($discountAmount, $price);

                $subtotal = ($price - $discountAmount) * $qty;
                $totalSubtotal += $subtotal;

                $items[] = [
                    'product' => $product,
                    'qty' => $qty,
                    'price' => $price,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'discount_amount' => $discountAmount,
                    'subtotal' => $subtotal,
                    'note' => $product->pivot->note,
                ];
            }

            // Simpan transaction
            $transaction = Transaction::create([
                'company_id' => $order->company_id,
                'transaction_code' => 'TRX-' . $order->order_id . '-' . $order->created_at->format('Ymd'),
                'transaction_date' => $order->created_at,
                'transaction_subtotal' => $totalSubtotal,
                'transaction_tax' => 0,
                'transaction_service_charge' => 0,
                'transaction_grand_total' => $totalSubtotal,
                'transaction_status' => 'success',
                'transaction_table_id' => $order->order_table_id,
                'transaction_customer_id' => $order->order_customer_id,
                'transaction_remark' => 'Dari pesanan #' . $order->order_id,
                'created_by' => 'seeder',
            ]);

            // Simpan transaction_items (include diskon)
            foreach ($items as $item) {
                $product = $item['product'];
                TransactionItem::create([
                    'company_id' => $order->company_id,
                    'transaction_id' => $transaction->transaction_id,
                    'product_id' => $product->product_id,
                    'product_name' => $product->product_name,
                    'price' => $item['price'],
                    'discount_type' => $item['discount_type'],
                    'discount_value' => $item['discount_value'],
                    'discount_amount' => $item['discount_amount'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                    'note' => $item['note'],
                    'created_by' => 'seeder',
                ]);
            }

            // Link order ke transaction
            $order->update(['order_transaction_id' => $transaction->transaction_id]);
        }

        $this->command->info('✅ ' . Transaction::count() . ' transaksi + item berhasil di-seed dari order completed.');
    }
}
