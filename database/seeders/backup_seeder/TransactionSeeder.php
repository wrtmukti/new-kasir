<?php

namespace Database\Seeders;

use App\Models\Admin\Order;
use App\Models\Admin\Transaction;
use App\Models\Admin\TransactionItem;
use App\Models\Admin\Payment;
use App\Models\Admin\Outlet;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $outlet = Outlet::first();
        if (!$outlet) return;

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

            $grandTotal = (float) ($order->order_grand_total ?? $totalSubtotal);
            $isCash = ($order->order_id % 2 === 0);
            $paymentMetode = $isCash ? 'cash' : 'debit';
            $paymentAmount = $isCash ? ceil($grandTotal / 10000) * 10000 : $grandTotal;
            $paymentRef = $isCash ? 'CASH-ORD' . $order->order_id : 'EDC-BCA-' . rand(100000, 999999);
            $change = max(0, $paymentAmount - $grandTotal);
            $paymentRemark = $isCash
                ? 'Tunai: Rp ' . number_format($paymentAmount, 0, ',', '.') . ' (Kembalian: Rp ' . number_format($change, 0, ',', '.') . ')'
                : 'Debit Card EDC BCA';

            // Simpan transaction
            $transaction = Transaction::create([
                'outlet_id' => $order->outlet_id,
                'daily_closing_id' => $order->daily_closing_id,
                'transaction_code' => 'TRX-' . $order->order_id . '-' . $order->created_at->format('YmdHi'),
                'transaction_date' => $order->created_at,
                'transaction_subtotal' => $totalSubtotal,
                'transaction_tax' => (float) ($order->tax_amount ?? 0),
                'transaction_service_charge' => (float) ($order->service_charge_amount ?? 0),
                'transaction_grand_total' => $grandTotal,
                'transaction_status' => 'success',
                'transaction_table_id' => $order->order_table_id,
                'transaction_customer_id' => $order->order_customer_id,
                'transaction_remark' => 'Dari pesanan #' . $order->order_id,
                'created_by' => 'seeder',
                'created_at' => $order->created_at,
                'updated_at' => $order->created_at,
            ]);

            // Simpan payment
            $payment = Payment::create([
                'outlet_id' => $order->outlet_id,
                'transaction_id' => $transaction->transaction_id,
                'payment_metode' => $paymentMetode,
                'payment_amount' => $paymentAmount,
                'payment_reference' => $paymentRef,
                'payment_status' => 'completed',
                'payment_grand_total' => $grandTotal,
                'payment_remark' => $paymentRemark,
                'payment_date' => $order->created_at->format('Y-m-d H:i:s'),
                'payment_table_id' => (string) ($order->order_table_id ?? ''),
                'payment_customer_id' => (string) ($order->order_customer_id ?? ''),
                'created_by' => 'seeder',
                'updated_by' => 'seeder',
                'delete_status' => 0,
                'created_at' => $order->created_at,
                'updated_at' => $order->created_at,
            ]);

            $transaction->update(['payment_id' => $payment->payment_id]);

            // Update statistik DailyClosing jika terikat
            if ($order->daily_closing_id) {
                $closing = \App\Models\Admin\DailyClosing::find($order->daily_closing_id);
                if ($closing) {
                    if ($isCash) {
                        $closing->system_cash_sales += $grandTotal;
                    } else {
                        $closing->system_non_cash_sales += $grandTotal;
                    }
                    $closing->system_expected_cash = $closing->starting_cash + $closing->system_cash_sales;
                    $closing->actual_cash_counted = $closing->system_expected_cash;
                    $closing->save();
                }
            }



            // Simpan transaction_items (include diskon)
            foreach ($items as $item) {
                $product = $item['product'];
                TransactionItem::create([
                    'outlet_id' => $order->outlet_id,
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
                    'created_at' => $order->created_at,
                    'updated_at' => $order->created_at,
                ]);
            }

            // Link order ke transaction
            $order->update(['order_transaction_id' => $transaction->transaction_id]);
        }

        $this->command->info('✅ ' . Transaction::count() . ' transaksi + item berhasil di-seed dari order completed.');
    }
}
