<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PurchaseOrderRequest;
use App\Http\Requests\Admin\PurchaseReceivingRequest;
use App\Models\Admin\PurchaseOrder;
use App\Models\Admin\PurchaseOrderItem;
use App\Models\Admin\PurchaseReceiving;
use App\Models\Admin\PurchaseReceivingItem;
use App\Models\Admin\Keuangan\CogsRawMaterial;
use App\Models\Admin\Keuangan\CogsRawMaterialHistory;
use App\Models\Admin\Supplier;
use App\Models\Admin\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    // ===================== PURCHASE ORDER =====================

    public function index()
    {
        $orders = PurchaseOrder::where('delete_status', 0)
            ->with('supplier', 'outlet')
            ->latest()
            ->paginate(10);
        return view('admin.kasir.keuangan.purchase-order.index', compact('orders'));
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $orders = PurchaseOrder::where('delete_status', 0)
            ->with('supplier', 'outlet')
            ->latest()
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.kasir.keuangan.purchase-order._data', compact('orders'))->render(),
                'pagination' => $orders->links('vendor.pagination.modern')->toHtml(),
                'total' => $orders->total(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
            ]);
        }

        return view('admin.kasir.keuangan.purchase-order.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::where('delete_status', 0)->where('supplier_status', 1)->get();
        $rawMaterials = CogsRawMaterial::where('delete_status', 0)->get();
        return view('admin.kasir.keuangan.purchase-order.create', compact('suppliers', 'rawMaterials'));
    }

    public function store(PurchaseOrderRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $companyId = session('outlet_id') ?? Outlet::first()?->outlet_id;

            // Generate PO code
            $lastPo = PurchaseOrder::latest()->first();
            $nextNum = $lastPo ? ((int) substr($lastPo->po_code, -3)) + 1 : 1;
            $poCode = 'PO-' . now()->format('Ymd') . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

            $totalAmount = 0;
            $items = [];
            foreach ($validated['items'] as $item) {
                $subtotal = $item['qty'] * $item['price'];
                $totalAmount += $subtotal;
                $items[] = new PurchaseOrderItem([
                    'cogs_raw_material_id' => $item['cogs_raw_material_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);
            }

            $order = PurchaseOrder::create([
                'outlet_id' => $companyId,
                'po_code' => $poCode,
                'po_date' => now(),
                'supplier_id' => $validated['supplier_id'],
                'po_status' => 'draft',
                'po_total_amount' => $totalAmount,
                'po_notes' => $validated['po_notes'] ?? null,
            ]);

            $order->items()->saveMany($items);
            DB::commit();

            return redirect()->route('admin.keuangan.purchase-order.show', [$order, 'confirm' => 1])
                ->with('success', 'Purchase Order Bahan Mentah berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal membuat PO: ' . $e->getMessage());
        }
    }

    public function show(PurchaseOrder $purchase_order)
    {
        $order = $purchase_order;
        $order->load(['supplier', 'items.cogsRawMaterial', 'receivings.items']);

        // Hitung returned_qty per cogs_raw_material_id dari CogsRawMaterialHistory
        $returnLogs = CogsRawMaterialHistory::where('action_type', 'purchase_return')
            ->where('history_remark', 'LIKE', "%{$order->po_code}%")
            ->get()
            ->groupBy('cogs_raw_material_id')
            ->map(function ($logs) {
                return $logs->sum('amount');
            });

        return view('admin.kasir.keuangan.purchase-order.show', compact('order', 'returnLogs'));
    }

    public function edit(PurchaseOrder $purchase_order)
    {
        $order = $purchase_order;
        if ($order->po_status !== 'draft') {
            return redirect()->route('admin.keuangan.purchase-order.index')
                ->with('error', 'Hanya PO status draft yang bisa diedit.');
        }

        $order->load('items.cogsRawMaterial');
        $suppliers = Supplier::where('delete_status', 0)->where('supplier_status', 1)->get();
        $rawMaterials = CogsRawMaterial::where('delete_status', 0)->get();
        return view('admin.kasir.keuangan.purchase-order.edit', compact('order', 'suppliers', 'rawMaterials'));
    }

    public function update(PurchaseOrderRequest $request, PurchaseOrder $purchase_order)
    {
        $order = $purchase_order;
        if ($order->po_status !== 'draft') {
            return redirect()->route('admin.keuangan.purchase-order.index')
                ->with('error', 'Hanya PO status draft yang bisa diedit.');
        }

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $newItems = [];
            foreach ($validated['items'] as $item) {
                $subtotal = $item['qty'] * $item['price'];
                $totalAmount += $subtotal;
                $newItems[] = new PurchaseOrderItem([
                    'cogs_raw_material_id' => $item['cogs_raw_material_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update([
                'supplier_id' => $validated['supplier_id'],
                'po_total_amount' => $totalAmount,
                'po_notes' => $validated['po_notes'] ?? null,
            ]);

            // Replace items
            $order->items()->delete();
            $order->items()->saveMany($newItems);
            DB::commit();

            return redirect()->route('admin.keuangan.purchase-order.show', $order)
                ->with('success', 'PO Bahan Mentah berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal update PO: ' . $e->getMessage());
        }
    }

    public function destroy(PurchaseOrder $purchase_order)
    {
        $purchase_order->update(['delete_status' => 1]);

        if (request()->ajax()) {
            return response()->json(['success' => 'PO berhasil dihapus.']);
        }

        return redirect()->route('admin.keuangan.purchase-order.index')
            ->with('success', 'PO berhasil dihapus.');
    }

    // ===================== CONFIRM =====================

    public function confirm(PurchaseOrder $purchase_order)
    {
        if ($purchase_order->po_status !== 'draft') {
            if (request()->ajax()) return response()->json(['error' => 'Hanya PO draft yang bisa dikonfirmasi.'], 422);
            return redirect()->back()->with('error', 'Hanya PO draft yang bisa dikonfirmasi.');
        }

        $purchase_order->update(['po_status' => 'ordered']);

        if (request()->ajax()) {
            return response()->json(['success' => 'PO berhasil dikonfirmasi.', 'status' => 'ordered']);
        }

        return redirect()->route('admin.keuangan.purchase-order.show', $purchase_order)
            ->with('success', 'PO berhasil dikonfirmasi. Status sekarang: Ordered.');
    }

    // ===================== PAYMENT (PLAN B CASH FLOW) =====================

    public function pay(Request $request, PurchaseOrder $purchase_order)
    {
        $request->validate([
            'payment_method' => 'required|string|in:cash,transfer_bank,qris,other',
            'payment_date' => 'nullable|date',
            'payment_notes' => 'nullable|string|max:500',
        ]);

        $purchase_order->update([
            'payment_status' => 'paid',
            'payment_date' => $request->payment_date ? Carbon::parse($request->payment_date) : now(),
            'payment_method' => $request->payment_method,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status pembayaran PO ' . $purchase_order->po_code . ' berhasil diubah menjadi LUNAS.',
                'payment_status' => 'paid',
            ]);
        }

        return redirect()->route('admin.keuangan.purchase-order.show', $purchase_order)
            ->with('success', 'Status pembayaran PO ' . $purchase_order->po_code . ' berhasil diubah menjadi LUNAS.');
    }

    // ===================== CANCEL =====================

    public function cancel(Request $request, PurchaseOrder $purchase_order)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        if (!in_array($purchase_order->po_status, ['draft', 'ordered'])) {
            if (request()->ajax()) return response()->json(['error' => 'PO tidak bisa dibatalkan.'], 422);
            return redirect()->back()->with('error', 'PO tidak bisa dibatalkan.');
        }

        if ($purchase_order->po_status === 'ordered') {
            $hasReceived = $purchase_order->items()->where('received_qty', '>', 0)->exists();
            if ($hasReceived) {
                if (request()->ajax()) return response()->json(['error' => 'PO sudah ada barang diterima. Gunakan Return.'], 422);
                return redirect()->back()->with('error', 'PO sudah ada barang diterima. Gunakan Return.');
            }
        }

        $notes = $purchase_order->po_notes;
        $cancelNote = '[DIBATALKAN: ' . now()->format('d M Y H:i') . '] ' . $request->cancellation_reason;
        $notes = $notes ? $notes . "\n" . $cancelNote : $cancelNote;

        $purchase_order->update([
            'po_status' => 'cancelled',
            'po_notes' => $notes,
        ]);

        if (request()->ajax()) {
            return response()->json(['success' => 'PO berhasil dibatalkan.']);
        }

        return redirect()->route('admin.keuangan.purchase-order.show', $purchase_order)
            ->with('success', 'PO berhasil dibatalkan.');
    }

    // ===================== RETURN =====================

    public function return(Request $request, PurchaseOrder $purchase_order)
    {
        $request->validate([
            'cogs_raw_material_id' => 'required|string',
            'qty' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:1000',
        ]);

        if (!in_array($purchase_order->po_status, ['partial', 'completed'])) {
            if (request()->ajax()) return response()->json(['error' => 'Hanya PO partial/completed yang bisa diretur.'], 422);
            return redirect()->back()->with('error', 'Hanya PO partial/completed yang bisa diretur.');
        }

        DB::beginTransaction();
        try {
            $poItem = $purchase_order->items()
                ->where('cogs_raw_material_id', $request->cogs_raw_material_id)
                ->firstOrFail();

            $rawMat = CogsRawMaterial::findOrFail($request->cogs_raw_material_id);
            $amountBefore = (float) $rawMat->amount;
            $returnQty = (float) $request->qty;
            $amountAfter = max(0, $amountBefore - $returnQty);

            $rawMat->update(['amount' => $amountAfter]);

            CogsRawMaterialHistory::create([
                'cogs_raw_material_id' => $rawMat->cogs_raw_material_id,
                'outlet_id' => $purchase_order->outlet_id,
                'name' => $rawMat->name,
                'unit' => $rawMat->unit,
                'amount' => $amountAfter,
                'price_per_unit' => $rawMat->price_per_unit,
                'loss_percent' => $rawMat->loss_percent,
                'yield_percent' => $rawMat->yield_percent,
                'effective_price' => $rawMat->effective_price,
                'action_type' => 'purchase_return',
                'changed_by' => auth()->user()->name ?? 'Admin',
                'effective_date' => now(),
                'history_remark' => "Return dari PO {$purchase_order->po_code}: {$returnQty} {$rawMat->unit} ({$request->reason})",
            ]);

            DB::commit();

            if (request()->ajax()) return response()->json(['success' => 'Return bahan mentah berhasil. Stok bahan berkurang.']);
            return redirect()->route('admin.keuangan.purchase-order.show', $purchase_order)
                ->with('success', 'Return bahan mentah berhasil. Stok bahan berkurang.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (request()->ajax()) return response()->json(['error' => 'Gagal return: ' . $e->getMessage()], 422);
            return redirect()->back()->with('error', 'Gagal return: ' . $e->getMessage());
        }
    }

    // ===================== RECEIVING =====================

    public function receivingCreate(PurchaseOrder $purchase_order)
    {
        $order = $purchase_order;
        if (!in_array($order->po_status, ['ordered', 'partial'])) {
            return redirect()->route('admin.keuangan.purchase-order.show', $order)
                ->with('error', 'PO harus berstatus ordered/partial untuk receiving.');
        }

        $order->load('items.cogsRawMaterial');
        $lastReceiving = PurchaseReceiving::latest()->first();
        $nextNum = $lastReceiving ? ((int) substr($lastReceiving->receiving_code, -3)) + 1 : 1;
        $receivingCode = 'RCV-' . now()->format('Ymd') . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        return view('admin.kasir.keuangan.purchase-receiving.create', compact('order', 'receivingCode'));
    }

    public function receivingStore(PurchaseReceivingRequest $request, PurchaseOrder $purchase_order)
    {
        $order = $purchase_order;
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $companyId = session('outlet_id') ?? Outlet::first()?->outlet_id;

            $lastReceiving = PurchaseReceiving::latest()->first();
            $nextNum = $lastReceiving ? ((int) substr($lastReceiving->receiving_code, -3)) + 1 : 1;
            $receivingCode = 'RCV-' . now()->format('Ymd') . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

            $receiving = PurchaseReceiving::create([
                'outlet_id' => $companyId,
                'receiving_code' => $receivingCode,
                'receiving_date' => $validated['receiving_date'],
                'po_id' => $order->po_id,
                'po_code' => $order->po_code,
                'receiving_status' => 'completed',
                'receiving_notes' => $validated['receiving_notes'] ?? null,
            ]);

            // Process each raw material item
            foreach ($validated['items'] as $item) {
                $subtotal = $item['received_qty'] * $item['received_price'];

                PurchaseReceivingItem::create([
                    'receiving_id' => $receiving->receiving_id,
                    'po_item_id' => $item['po_item_id'],
                    'cogs_raw_material_id' => $item['cogs_raw_material_id'],
                    'received_qty' => $item['received_qty'],
                    'received_price' => $item['received_price'],
                    'subtotal' => $subtotal,
                ]);

                // Update received_qty di PO item
                $poItem = PurchaseOrderItem::find($item['po_item_id']);
                if ($poItem) {
                    $poItem->increment('received_qty', $item['received_qty']);
                }

                // Tambah stok bahan mentah (Raw Stock)
                $rawMat = CogsRawMaterial::find($item['cogs_raw_material_id']);
                if ($rawMat) {
                    $amountBefore = (float) $rawMat->amount;
                    $receivedQty = (float) $item['received_qty'];
                    $amountAfter = $amountBefore + $receivedQty;

                    // Update harga unit jika ada perubahan harga beli
                    if ($item['received_price'] > 0) {
                        $rawMat->price_per_unit = $item['received_price'];
                        $rawMat->calculatePrices();
                    }

                    $rawMat->amount = $amountAfter;
                    $rawMat->save();

                    // Catat riwayat di CogsRawMaterialHistory
                    CogsRawMaterialHistory::create([
                        'cogs_raw_material_id' => $rawMat->cogs_raw_material_id,
                        'outlet_id' => $companyId,
                        'name' => $rawMat->name,
                        'unit' => $rawMat->unit,
                        'amount' => $amountAfter,
                        'price_per_unit' => $rawMat->price_per_unit,
                        'loss_percent' => $rawMat->loss_percent,
                        'yield_percent' => $rawMat->yield_percent,
                        'effective_price' => $rawMat->effective_price,
                        'action_type' => 'purchase_receiving',
                        'changed_by' => auth()->user()->name ?? 'Admin',
                        'effective_date' => now(),
                        'history_remark' => "Penerimaan PO {$order->po_code} ({$receivingCode}): Masuk {$receivedQty} {$rawMat->unit} @ Rp " . number_format($item['received_price']),
                    ]);
                }
            }

            // Update PO status
            $totalReceived = $order->items()->sum('received_qty');
            $totalOrdered = $order->items()->sum('qty');
            $newStatus = $totalReceived >= $totalOrdered ? 'completed' : 'partial';
            $order->update(['po_status' => $newStatus]);

            DB::commit();

            return redirect()->route('admin.keuangan.purchase-order.show', $order)
                ->with('success', "Penerimaan bahan mentah ($receivingCode) berhasil dicatat. Stok bahan mentah otomatis bertambah.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mencatat penerimaan: ' . $e->getMessage());
        }
    }
}
