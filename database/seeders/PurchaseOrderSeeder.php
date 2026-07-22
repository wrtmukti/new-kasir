<?php

namespace Database\Seeders;

use App\Models\Admin\PurchaseOrder;
use App\Models\Admin\PurchaseOrderItem;
use App\Models\Admin\PurchaseReceiving;
use App\Models\Admin\PurchaseReceivingItem;
use App\Models\Admin\StockLog;
use App\Models\Admin\Supplier;
use App\Models\Admin\Stock;
use App\Models\SysAdmin\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseOrderSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company) return;

        $suppliers = Supplier::all()->keyBy('supplier_code');
        $stocks = Stock::all()->keyBy('stock_code');

        // Helper: buat PO items & return array
        $makeItems = function (array $stockCodes, array $overrides = []) use ($stocks) {
            $items = [];
            $total = 0;
            foreach ($stockCodes as $sc) {
                $stock = $stocks[$sc] ?? null;
                if (!$stock) continue;
                $qty = $overrides[$sc]['qty'] ?? 10;
                $price = $overrides[$sc]['price'] ?? ($stock->stock_price ?? 0);
                $subtotal = $qty * $price;
                $total += $subtotal;
                $items[] = ['stock' => $stock, 'qty' => $qty, 'price' => $price, 'subtotal' => $subtotal];
            }
            return [$items, $total];
        };

        // Helper: buat receiving + stock_logs
        $createReceiving = function ($po, $items, $rcvSuffix, $date) use ($company) {
            $rcvCode = 'RCV-' . $rcvSuffix;
            $receiving = PurchaseReceiving::create([
                'company_id' => $company->company_id,
                'receiving_code' => $rcvCode,
                'receiving_date' => $date,
                'po_id' => $po->po_id,
                'po_code' => $po->po_code,
                'receiving_status' => 'completed',
                'receiving_notes' => 'Penerimaan ' . $po->po_code,
                'received_by' => 'Seeder',
            ]);

            $poItems = PurchaseOrderItem::where('po_id', $po->po_id)->get()->keyBy('stock_id');

            foreach ($items as $item) {
                $poItem = $poItems[$item['stock']->stock_id] ?? null;
                if (!$poItem) continue;

                $price = $item['price'];
                $qty = $item['qty'];
                $subtotal = $qty * $price;

                PurchaseReceivingItem::create([
                    'receiving_id' => $receiving->receiving_id,
                    'po_item_id' => $poItem->po_item_id,
                    'stock_id' => $item['stock']->stock_id,
                    'received_qty' => $qty,
                    'received_price' => $price,
                    'subtotal' => $subtotal,
                ]);

                $poItem->increment('received_qty', $qty);
                $stockBefore = $item['stock']->stock_amount;
                $item['stock']->increment('stock_amount', $qty);

                StockLog::create([
                    'company_id' => $company->company_id,
                    'stock_id' => $item['stock']->stock_id,
                    'type' => 'in',
                    'qty' => $qty,
                    'price' => $price,
                    'total' => $subtotal,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockBefore + $qty,
                    'reference_type' => 'purchase_receiving',
                    'reference_code' => $rcvCode,
                    'notes' => 'Penerimaan ' . $po->po_code . ' - ' . $item['stock']->stock_name,
                ]);
            }

            return $receiving;
        };

        // Helper: simpan PO + items
        $savePo = function ($poCode, $supplierCode, $status, $items, $date, $notes = null, $cancelReason = null, $overrides = []) use ($company, $suppliers) {
            $supplier = $suppliers[$supplierCode] ?? null;
            if (!$supplier) return null;

            $po = PurchaseOrder::create([
                'company_id' => $company->company_id,
                'po_code' => $poCode,
                'po_date' => $date,
                'supplier_id' => $supplier->supplier_id,
                'po_status' => $status,
                'po_total_amount' => $overrides['total'] ?? 0,
                'po_notes' => $cancelReason
                    ? $notes . "\n[DIBATALKAN: " . $date->format('d M Y H:i') . '] ' . $cancelReason
                    : ($notes ?? ''),
            ]);

            foreach ($items as $item) {
                PurchaseOrderItem::create([
                    'po_id' => $po->po_id,
                    'stock_id' => $item['stock']->stock_id,
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'received_qty' => $overrides['received'][$item['stock']->stock_id] ?? 0,
                ]);
            }

            return $po;
        };

        $baseDate = now()->subDays(14);

        // ===================================================================
        // 1. PO-001 — DRAFT (SUP-001: Beras 50kg, Cabai 10kg, Bawang 15kg)
        // ===================================================================
        [$items1, $total1] = $makeItems(['BRS-001', 'CBE-001', 'BWG-001'], [
            'BRS-001' => ['qty' => 50],
            'CBE-001' => ['qty' => 10],
            'BWG-001' => ['qty' => 15],
        ]);
        $savePo('PO-001', 'SUP-001', 'draft', $items1, $baseDate, 'Draft PO bulanan');

        // ===================================================================
        // 2. PO-002 — COMPLETED (SUP-001: Beras 30kg, Cabai 5kg)
        //    2 receiving items → full receive
        // ===================================================================
        [$items2, $total2] = $makeItems(['BRS-001', 'CBE-001'], [
            'BRS-001' => ['qty' => 30],
            'CBE-001' => ['qty' => 5],
        ]);
        $po2 = $savePo('PO-002', 'SUP-001', 'completed', $items2, $baseDate->copy()->addDay(1), 'PO rutin mingguan');
        $createReceiving($po2, $items2, '20260722-001', $baseDate->copy()->addDay(1)->addHours(4));

        // ===================================================================
        // 3. PO-003 — ORDERED (SUP-002: Minyak 20 liter)
        //    confirmed, belum receive
        // ===================================================================
        [$items3, $total3] = $makeItems(['MNY-001'], ['MNY-001' => ['qty' => 20]]);
        $savePo('PO-003', 'SUP-002', 'ordered', $items3, $baseDate->copy()->addDays(2), 'PO minyak goreng');

        // ===================================================================
        // 4. PO-004 — CANCELLED (SUP-002: Minyak 10 liter)
        //    dibatalkan sebelum receive
        // ===================================================================
        [$items4, $total4] = $makeItems(['MNY-001'], ['MNY-001' => ['qty' => 10]]);
        $savePo('PO-004', 'SUP-002', 'cancelled', $items4, $baseDate->copy()->addDays(3),
            'PO minyak tambahan', 'Stok minyak masih mencukupi, PO tidak jadi dilanjutkan');

        // ===================================================================
        // 5. PO-005 — PARTIAL (SUP-003: Ayam 25kg)
        //    received 15/25
        // ===================================================================
        [$items5, $total5] = $makeItems(['AYM-001'], ['AYM-001' => ['qty' => 25]]);
        $po5 = $savePo('PO-005', 'SUP-003', 'partial', $items5, $baseDate->copy()->addDays(4),
            'PO ayam fillet', null, [
                'received' => [$stocks['AYM-001']->stock_id => 15],
            ]);
        $createReceiving($po5, [['stock' => $stocks['AYM-001'], 'qty' => 15, 'price' => 38000, 'subtotal' => 570000]],
            '20260722-002', $baseDate->copy()->addDays(4)->addHours(3));

        // ===================================================================
        // 6. PO-006 — COMPLETED (SUP-003: Ayam 20kg)
        //    full receive + 1 return (5kg rusak)
        // ===================================================================
        [$items6, $total6] = $makeItems(['AYM-001'], ['AYM-001' => ['qty' => 20]]);
        $po6 = $savePo('PO-006', 'SUP-003', 'completed', $items6, $baseDate->copy()->addDays(5),
            'PO ayam mingguan');
        $createReceiving($po6, $items6, '20260722-003', $baseDate->copy()->addDays(5)->addHours(3));

        // Return 5kg ayam dari PO-006 (rusak saat pengiriman)
        $stockAyam = $stocks['AYM-001'];
        $stockBefore = $stockAyam->stock_amount;
        $stockAyam->decrement('stock_amount', 5);
        StockLog::create([
            'company_id' => $company->company_id,
            'stock_id' => $stockAyam->stock_id,
            'type' => 'return',
            'qty' => 5,
            'price' => 38000,
            'total' => 190000,
            'stock_before' => $stockBefore,
            'stock_after' => $stockBefore - 5,
            'reference_type' => 'purchase_return',
            'reference_code' => 'PO-006',
            'notes' => 'Return dari PO-006: Ayam rusak saat pengiriman (sebagian tidak layak pakai)',
        ]);

        // ===================================================================
        // 7. PO-007 — DRAFT (SUP-004: Cabai 8kg, Bawang 10kg)
        // ===================================================================
        [$items7, $total7] = $makeItems(['CBE-001', 'BWG-001'], [
            'CBE-001' => ['qty' => 8],
            'BWG-001' => ['qty' => 10],
        ]);
        $savePo('PO-007', 'SUP-004', 'draft', $items7, $baseDate->copy()->addDays(7), 'PO bumbu dapur');

        // ===================================================================
        // 8. PO-008 — CANCELLED (SUP-004: Cabai 5kg)
        //    dibatalkan + alasan
        // ===================================================================
        [$items8, $total8] = $makeItems(['CBE-001'], ['CBE-001' => ['qty' => 5]]);
        $savePo('PO-008', 'SUP-004', 'cancelled', $items8, $baseDate->copy()->addDays(8),
            'PO cabai tambahan', 'Harga cabai turun, PO ditunda');

        // ===================================================================
        // 9. PO-009 — PARTIAL (SUP-005: Plastik 300pcs, Gelas 500pcs)
        //    received: Plastik 100, Gelas 200
        // ===================================================================
        [$items9, $total9] = $makeItems(['PLST-001', 'GLN-001'], [
            'PLST-001' => ['qty' => 300, 'price' => 500],
            'GLN-001' => ['qty' => 500, 'price' => 250],
        ]);
        $po9 = $savePo('PO-009', 'SUP-005', 'partial', $items9, $baseDate->copy()->addDays(10),
            'PO kemasan', null, [
                'received' => [
                    $stocks['PLST-001']->stock_id => 100,
                    $stocks['GLN-001']->stock_id => 200,
                ],
            ]);
        $createReceiving($po9, [
            ['stock' => $stocks['PLST-001'], 'qty' => 100, 'price' => 500, 'subtotal' => 50000],
            ['stock' => $stocks['GLN-001'], 'qty' => 200, 'price' => 250, 'subtotal' => 50000],
        ], '20260722-004', $baseDate->copy()->addDays(10)->addHours(5));

        // ===================================================================
        // 10. PO-010 — ORDERED (SUP-005: Plastik 100pcs, Gelas 300pcs)
        //     confirmed, belum receive
        // ===================================================================
        [$items10, $total10] = $makeItems(['PLST-001', 'GLN-001'], [
            'PLST-001' => ['qty' => 100, 'price' => 500],
            'GLN-001' => ['qty' => 300, 'price' => 250],
        ]);
        $savePo('PO-010', 'SUP-005', 'ordered', $items10, $baseDate->copy()->addDays(12),
            'PO kemasan bulan depan');

        $this->command->info('✅ 10 PO berhasil di-seed: 2 draft, 2 ordered, 2 partial, 2 completed, 2 cancelled + 1 return.');
    }
}
