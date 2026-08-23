<?php

namespace Database\Seeders;

use App\Models\Admin\Order;
use App\Models\Admin\Product;
use App\Models\Admin\DailyClosing;
use App\Models\SysAdmin\Company;

use App\Models\Admin\Table;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company) return;

        $products = Product::where('delete_status', 0)->get()->keyBy('product_code');
        $tables = Table::where('delete_status', 0)->get();

        if ($products->isEmpty()) return;

        $makananCodes = ['PRD-001', 'PRD-002', 'PRD-003', 'PRD-004', 'PRD-005', 'PRD-006', 'PRD-007', 'PRD-008', 'PRD-009', 'PRD-010', 'PRD-011', 'PRD-012'];
        $minumanCodes = ['PRD-013', 'PRD-014', 'PRD-015', 'PRD-016', 'PRD-017', 'PRD-018', 'PRD-019', 'PRD-020', 'PRD-021', 'PRD-022'];
        $snackCodes = ['PRD-023', 'PRD-024', 'PRD-025', 'PRD-026', 'PRD-027', 'PRD-028', 'PRD-029', 'PRD-030'];
        $toppingCodes = ['PRD-031', 'PRD-032', 'PRD-033', 'PRD-034'];

        $year = (int) date('Y');
        $month = (int) date('n');
        $currentDay = (int) date('j');
        $targetDays = max(26, min($currentDay, 28));

        $orderTypes = ['dine_in', 'dine_in', 'dine_in', 'take_away', 'delivery'];
        $notesPool = [null, null, null, 'Tidak pedas', 'Pedes banget', 'Es batu dipisah', 'Tanpa bawang goreng', 'Pake sambel extra', 'Sedikit gula'];

        $totalOrdersCreated = 0;

        // Loop dari tanggal 1 sampai targetDays (min 26 hari)
        for ($day = 1; $day <= $targetDays; $day++) {
            $date = Carbon::create($year, $month, $day);
            $isWeekend = $date->isWeekend();

            // Jumlah order per hari (Weekend lebih ramai)
            $dailyOrderCount = $isWeekend ? rand(12, 20) : rand(6, 12);

            for ($i = 0; $i < $dailyOrderCount; $i++) {
                // Tentukan jam acak antara jam 10:00 - 21:30
                $hour = rand(10, 21);
                $minute = rand(0, 59);
                $second = rand(0, 59);
                $orderTimestamp = Carbon::create($year, $month, $day, $hour, $minute, $second);

                $orderType = $orderTypes[array_rand($orderTypes)];
                $tableId = null;
                if ($orderType === 'dine_in' && $tables->isNotEmpty()) {
                    $tableId = $tables->random()->table_id;
                }

                // Pilih 1-3 Makanan, 1-2 Minuman, 0-2 Snack/Topping
                $selectedCodes = [];
                $makananQty = rand(1, 3);
                for ($m = 0; $m < $makananQty; $m++) {
                    $selectedCodes[] = $makananCodes[array_rand($makananCodes)];
                }
                $minumanQty = rand(1, 2);
                for ($m = 0; $m < $minumanQty; $m++) {
                    $selectedCodes[] = $minumanCodes[array_rand($minumanCodes)];
                }
                if (rand(0, 1) === 1) {
                    $selectedCodes[] = $snackCodes[array_rand($snackCodes)];
                }
                if (rand(0, 1) === 1) {
                    $selectedCodes[] = $toppingCodes[array_rand($toppingCodes)];
                }

                $itemSubtotalSum = 0;
                $syncData = [];

                foreach ($selectedCodes as $code) {
                    $product = $products->get($code);
                    if (!$product) continue;

                    $itemQty = rand(1, 2);
                    $price = (float) $product->product_price;
                    $subtotal = $price * $itemQty;
                    $itemSubtotalSum += $subtotal;

                    $note = $notesPool[array_rand($notesPool)];

                    if (isset($syncData[$product->product_id])) {
                        $syncData[$product->product_id]['quantity'] += $itemQty;
                    } else {
                        $syncData[$product->product_id] = [
                            'company_id' => $company->company_id,
                            'quantity' => $itemQty,
                            'note' => $note,
                            'delete_status' => 0,
                            'created_by' => 'seeder',
                        ];
                    }
                }

                if (empty($syncData)) continue;

                // Hitung Service Charge (5%) & Tax PB1 (10% Exclusive)
                $serviceChargePercent = 5.00;
                $serviceChargeAmount = round($itemSubtotalSum * ($serviceChargePercent / 100), 2);
                $taxableBase = $itemSubtotalSum + $serviceChargeAmount;
                $taxPercent = 10.00;
                $taxAmount = round($taxableBase * ($taxPercent / 100), 2);
                $grandTotal = $taxableBase + $taxAmount;

                // Hari ini beberapa pesanan terakhir bisa in_progress
                $status = 'completed';
                if ($day === $targetDays && $i >= ($dailyOrderCount - 2)) {
                    $status = 'in_progress';
                }

                // Cari daily_closing_id untuk shift tanggal & jam ini
                $shiftNumber = ((int) $orderTimestamp->format('H')) < 15 ? 1 : 2;
                $closing = DailyClosing::where('company_id', $company->company_id)
                    ->where('business_date', $date->format('Y-m-d'))
                    ->where('shift_number', $shiftNumber)
                    ->first();
                $dailyClosingId = $closing ? $closing->id : null;

                DB::transaction(function () use ($company, $dailyClosingId, $orderType, $status, $grandTotal, $taxPercent, $taxAmount, $serviceChargePercent, $serviceChargeAmount, $tableId, $orderTimestamp, $syncData) {

                    $order = Order::create([
                        'company_id' => $company->company_id,
                        'daily_closing_id' => $dailyClosingId,
                        'order_type' => $orderType,
                        'order_status' => $status,
                        'order_grand_total' => $grandTotal,
                        'tax_percent' => $taxPercent,
                        'tax_amount' => $taxAmount,
                        'tax_type' => 'exclusive',
                        'service_charge_percent' => $serviceChargePercent,
                        'service_charge_amount' => $serviceChargeAmount,
                        'order_table_id' => $tableId,
                        'created_by' => 'seeder',
                        'created_at' => $orderTimestamp,
                        'updated_at' => $orderTimestamp,
                    ]);


                    $order->products()->sync($syncData);

                    if ($orderType === 'dine_in' && $tableId && $status === 'in_progress') {
                        Table::where('table_id', $tableId)->update(['table_status' => 'terisi']);
                    }
                });


                $totalOrdersCreated++;
            }
        }

        $this->command->info("✅ {$totalOrdersCreated} pesanan (rentang {$targetDays} hari) + order_product berhasil di-seed.");
    }
}
