<?php

namespace Database\Seeders;

use App\Models\Admin\PurchaseOrder;
use App\Models\Admin\PurchaseOrderItem;
use App\Models\Admin\PurchaseReceiving;
use App\Models\Admin\PurchaseReceivingItem;
use App\Models\Admin\Keuangan\CogsRawMaterial;
use App\Models\Admin\Keuangan\CogsRawMaterialHistory;
use App\Models\Admin\Supplier;
use App\Models\Admin\Outlet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseOrderSeeder extends Seeder
{
    public function run(): void
    {
        $outlet = Outlet::first();
        if (!$outlet) return;

        $suppliers = Supplier::all()->keyBy('supplier_code');
        $rawMaterials = CogsRawMaterial::all()->keyBy('raw_material_code');

        // Helper: buat PO items & return array
        $makeItems = function (array $rawCodes, array $overrides = []) use ($rawMaterials) {
            $items = [];
            $total = 0;
            foreach ($rawCodes as $rc) {
                $rawMat = $rawMaterials[$rc] ?? null;
                if (!$rawMat) continue;
                $qty = $overrides[$rc]['qty'] ?? 10;
                $price = $overrides[$rc]['price'] ?? ($rawMat->price_per_unit ?? 0);
                $subtotal = $qty * $price;
                $total += $subtotal;
                $items[] = ['raw' => $rawMat, 'qty' => $qty, 'price' => $price, 'subtotal' => $subtotal];
            }
            return [$items, $total];
        };

        // Helper: buat receiving + cogs_raw_material_histories
        $createReceiving = function ($po, $items, $rcvSuffix, $date) use ($outlet) {
            $rcvCode = 'RCV-' . $rcvSuffix;
            $receiving = PurchaseReceiving::create([
                'outlet_id' => $outlet->outlet_id,
                'receiving_code' => $rcvCode,
                'receiving_date' => $date,
                'po_id' => $po->po_id,
                'po_code' => $po->po_code,
                'receiving_status' => 'completed',
                'receiving_notes' => 'Penerimaan bahan mentah ' . $po->po_code,
                'received_by' => 'Seeder',
            ]);

            $poItems = PurchaseOrderItem::where('po_id', $po->po_id)->get()->keyBy('cogs_raw_material_id');

            foreach ($items as $item) {
                $poItem = $poItems[$item['raw']->cogs_raw_material_id] ?? null;
                if (!$poItem) continue;

                $price = $item['price'];
                $qty = $item['qty'];
                $subtotal = $qty * $price;

                PurchaseReceivingItem::create([
                    'receiving_id' => $receiving->receiving_id,
                    'po_item_id' => $poItem->po_item_id,
                    'cogs_raw_material_id' => $item['raw']->cogs_raw_material_id,
                    'received_qty' => $qty,
                    'received_price' => $price,
                    'subtotal' => $subtotal,
                ]);

                $poItem->increment('received_qty', $qty);
                $amountBefore = (float) $item['raw']->amount;
                $amountAfter = $amountBefore + $qty;
                $item['raw']->update(['amount' => $amountAfter]);

                CogsRawMaterialHistory::create([
                    'cogs_raw_material_id' => $item['raw']->cogs_raw_material_id,
                    'outlet_id' => $outlet->outlet_id,
                    'name' => $item['raw']->name,
                    'unit' => $item['raw']->unit,
                    'amount' => $amountAfter,
                    'price_per_unit' => $price,
                    'loss_percent' => $item['raw']->loss_percent,
                    'yield_percent' => $item['raw']->yield_percent,
                    'effective_price' => $item['raw']->effective_price,
                    'action_type' => 'purchase_receiving',
                    'changed_by' => 'Seeder',
                    'effective_date' => $date,
                    'history_remark' => 'Penerimaan PO Bahan Mentah ' . $po->po_code . ' (' . $rcvCode . ') - ' . $item['raw']->name,
                    'created_by' => 'seeder',
                ]);
            }

            return $receiving;
        };

        // Helper: simpan PO + items
        $savePo = function ($poCode, $supplierCode, $status, $items, $date, $notes = null, $cancelReason = null, $overrides = []) use ($outlet, $suppliers) {
            $supplier = $suppliers[$supplierCode] ?? null;
            if (!$supplier) return null;

            $po = PurchaseOrder::create([
                'outlet_id' => $outlet->outlet_id,
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
                    'cogs_raw_material_id' => $item['raw']->cogs_raw_material_id,
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'received_qty' => $overrides['received'][$item['raw']->cogs_raw_material_id] ?? 0,
                ]);
            }

            return $po;
        };

        $baseDate = now()->subDays(14);

        // 1. PO-001 — DRAFT (SUP-001: Beras, Cabai, Bawang)
        [$items1, $total1] = $makeItems(['RAW-BERAS01', 'RAW-CABE01', 'RAW-BAWANG01'], [
            'RAW-BERAS01' => ['qty' => 50],
            'RAW-CABE01' => ['qty' => 10],
            'RAW-BAWANG01' => ['qty' => 15],
        ]);
        $savePo('PO-001', 'SUP-001', 'draft', $items1, $baseDate, 'Draft PO bulanan bahan mentah');

        // 2. PO-002 — COMPLETED (SUP-001: Beras, Cabai)
        [$items2, $total2] = $makeItems(['RAW-BERAS01', 'RAW-CABE01'], [
            'RAW-BERAS01' => ['qty' => 30],
            'RAW-CABE01' => ['qty' => 5],
        ]);
        $po2 = $savePo('PO-002', 'SUP-001', 'completed', $items2, $baseDate->copy()->addDay(1), 'PO rutin mingguan beras & cabai');
        $createReceiving($po2, $items2, '20260722-001', $baseDate->copy()->addDay(1)->addHours(4));

        // 3. PO-003 — ORDERED (SUP-002: Minyak)
        [$items3, $total3] = $makeItems(['RAW-MINYAK01'], ['RAW-MINYAK01' => ['qty' => 20]]);
        $savePo('PO-003', 'SUP-002', 'ordered', $items3, $baseDate->copy()->addDays(2), 'PO minyak goreng');

        // 4. PO-004 — CANCELLED (SUP-002: Minyak)
        [$items4, $total4] = $makeItems(['RAW-MINYAK01'], ['RAW-MINYAK01' => ['qty' => 10]]);
        $savePo('PO-004', 'SUP-002', 'cancelled', $items4, $baseDate->copy()->addDays(3),
            'PO minyak tambahan', 'Stok minyak masih mencukupi, PO tidak jadi dilanjutkan');

        // 5. PO-005 — PARTIAL (SUP-003: Ayam)
        [$items5, $total5] = $makeItems(['RAW-AYAM01'], ['RAW-AYAM01' => ['qty' => 25]]);
        $po5 = $savePo('PO-005', 'SUP-003', 'partial', $items5, $baseDate->copy()->addDays(4),
            'PO daging ayam segar', null, [
                'received' => [$rawMaterials['RAW-AYAM01']->cogs_raw_material_id => 15],
            ]);
        $createReceiving($po5, [['raw' => $rawMaterials['RAW-AYAM01'], 'qty' => 15, 'price' => 38000, 'subtotal' => 570000]],
            '20260722-002', $baseDate->copy()->addDays(4)->addHours(3));

        // 6. PO-006 — COMPLETED (SUP-003: Ayam)
        [$items6, $total6] = $makeItems(['RAW-AYAM01'], ['RAW-AYAM01' => ['qty' => 20]]);
        $po6 = $savePo('PO-006', 'SUP-003', 'completed', $items6, $baseDate->copy()->addDays(5),
            'PO ayam mingguan');
        $createReceiving($po6, $items6, '20260722-003', $baseDate->copy()->addDays(5)->addHours(3));

        // Return 5kg ayam dari PO-006
        $rawAyam = $rawMaterials['RAW-AYAM01'];
        $amountBefore = (float) $rawAyam->amount;
        $rawAyam->update(['amount' => max(0, $amountBefore - 5)]);
        CogsRawMaterialHistory::create([
            'outlet_id' => $outlet->outlet_id,
            'cogs_raw_material_id' => $rawAyam->cogs_raw_material_id,
            'name' => $rawAyam->name,
            'unit' => $rawAyam->unit,
            'amount' => $amountBefore - 5,
            'price_per_unit' => 38000,
            'action_type' => 'purchase_return',
            'effective_date' => $baseDate->copy()->addDays(5)->addHours(6),
            'changed_by' => 'Seeder',
            'history_remark' => 'Return dari PO-006: Ayam rusak 5kg saat pengiriman',
            'created_by' => 'seeder',
        ]);

        // 7. PO-007 — DRAFT (SUP-004: Cabai, Bawang)
        [$items7, $total7] = $makeItems(['RAW-CABE01', 'RAW-BAWANG01'], [
            'RAW-CABE01' => ['qty' => 8],
            'RAW-BAWANG01' => ['qty' => 10],
        ]);
        $savePo('PO-007', 'SUP-004', 'draft', $items7, $baseDate->copy()->addDays(7), 'PO bumbu dapur');

        // 8. PO-008 — CANCELLED (SUP-004: Cabai)
        [$items8, $total8] = $makeItems(['RAW-CABE01'], ['RAW-CABE01' => ['qty' => 5]]);
        $savePo('PO-008', 'SUP-004', 'cancelled', $items8, $baseDate->copy()->addDays(8),
            'PO cabai tambahan', 'Harga cabai turun, PO ditunda');

        // 9. PO-009 — PARTIAL (SUP-005: Plastik & Gelas)
        [$items9, $total9] = $makeItems(['RAW-PLST01', 'RAW-GLN01'], [
            'RAW-PLST01' => ['qty' => 300, 'price' => 500],
            'RAW-GLN01' => ['qty' => 500, 'price' => 250],
        ]);
        $po9 = $savePo('PO-009', 'SUP-005', 'partial', $items9, $baseDate->copy()->addDays(10),
            'PO kemasan', null, [
                'received' => [
                    $rawMaterials['RAW-PLST01']->cogs_raw_material_id => 100,
                    $rawMaterials['RAW-GLN01']->cogs_raw_material_id => 200,
                ],
            ]);
        $createReceiving($po9, [
            ['raw' => $rawMaterials['RAW-PLST01'], 'qty' => 100, 'price' => 500, 'subtotal' => 50000],
            ['raw' => $rawMaterials['RAW-GLN01'], 'qty' => 200, 'price' => 250, 'subtotal' => 50000],
        ], '20260722-004', $baseDate->copy()->addDays(10)->addHours(5));

        // 10. PO-010 — ORDERED (SUP-005: Plastik & Gelas)
        [$items10, $total10] = $makeItems(['RAW-PLST01', 'RAW-GLN01'], [
            'RAW-PLST01' => ['qty' => 100, 'price' => 500],
            'RAW-GLN01' => ['qty' => 300, 'price' => 250],
        ]);
        $savePo('PO-010', 'SUP-005', 'ordered', $items10, $baseDate->copy()->addDays(12),
            'PO kemasan bulan depan');

        $this->command->info('✅ 10 PO Bahan Mentah berhasil di-seed: 2 draft, 2 ordered, 2 partial, 2 completed, 2 cancelled + 1 return.');
    }
}
