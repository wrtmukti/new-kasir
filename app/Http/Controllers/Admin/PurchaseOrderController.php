<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PurchaseOrderRequest;
use App\Http\Requests\Admin\PurchaseReceivingRequest;
use App\Models\Admin\PurchaseOrder;
use App\Models\Admin\PurchaseOrderItem;
use App\Models\Admin\PurchaseReceiving;
use App\Models\Admin\PurchaseReceivingItem;
use App\Models\Admin\Stock;
use App\Models\Admin\StockLog;
use App\Models\Admin\Supplier;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    // ===================== PURCHASE ORDER =====================

    public function index()
    {
        $orders = PurchaseOrder::where('delete_status', 0)
            ->with('supplier', 'company')
            ->latest()
            ->paginate(10);
        return view('admin.purchase-order.index', compact('orders'));
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $orders = PurchaseOrder::where('delete_status', 0)
            ->with('supplier', 'company')
            ->latest()
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.purchase-order._data', compact('orders'))->render(),
                'pagination' => view('vendor.pagination.modern', ['paginator' => $orders])->render(),
                'total' => $orders->total(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
            ]);
        }

        return view('admin.purchase-order.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::where('delete_status', 0)->where('supplier_status', 1)->get();
        $stocks = Stock::where('delete_status', 0)->where('stock_status', 1)->get();
        return view('admin.purchase-order.create', compact('suppliers', 'stocks'));
    }

    public function store(PurchaseOrderRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $companyId = session('company_id') ?? Company::first()?->company_id;

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
                    'stock_id' => $item['stock_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);
            }

            $order = PurchaseOrder::create([
                'company_id' => $companyId,
                'po_code' => $poCode,
                'po_date' => now(),
                'supplier_id' => $validated['supplier_id'],
                'po_status' => $validated['po_status'] ?? 'draft',
                'po_total_amount' => $totalAmount,
                'po_notes' => $validated['po_notes'] ?? null,
            ]);

            $order->items()->saveMany($items);
            DB::commit();

            return redirect()->route('admin.purchase-order.show', $order)
                ->with('success', 'Purchase Order berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal membuat PO: ' . $e->getMessage());
        }
    }

    public function show(PurchaseOrder $purchase_order)
    {
        $order = $purchase_order;
        $order->load(['supplier', 'items.stock', 'receivings.items']);
        return view('admin.purchase-order.show', compact('order'));
    }

    public function edit(PurchaseOrder $purchase_order)
    {
        $order = $purchase_order;
        if ($order->po_status !== 'draft') {
            return redirect()->route('admin.purchase-order.index')
                ->with('error', 'Hanya PO status draft yang bisa diedit.');
        }

        $order->load('items.stock');
        $suppliers = Supplier::where('delete_status', 0)->where('supplier_status', 1)->get();
        $stocks = Stock::where('delete_status', 0)->where('stock_status', 1)->get();
        return view('admin.purchase-order.edit', compact('order', 'suppliers', 'stocks'));
    }

    public function update(PurchaseOrderRequest $request, PurchaseOrder $purchase_order)
    {
        $order = $purchase_order;
        if ($order->po_status !== 'draft') {
            return redirect()->route('admin.purchase-order.index')
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
                    'stock_id' => $item['stock_id'],
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

            return redirect()->route('admin.purchase-order.show', $order)
                ->with('success', 'PO berhasil diperbarui.');
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

        return redirect()->route('admin.purchase-order.index')
            ->with('success', 'PO berhasil dihapus.');
    }

    // ===================== RECEIVING =====================

    public function receivingCreate(PurchaseOrder $purchase_order)
    {
        $order = $purchase_order;
        if (!in_array($order->po_status, ['ordered', 'partial'])) {
            return redirect()->route('admin.purchase-order.show', $order)
                ->with('error', 'PO harus berstatus ordered/partial untuk receiving.');
        }

        $order->load('items.stock');
        $lastReceiving = PurchaseReceiving::latest()->first();
        $nextNum = $lastReceiving ? ((int) substr($lastReceiving->receiving_code, -3)) + 1 : 1;
        $receivingCode = 'RCV-' . now()->format('Ymd') . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        return view('admin.purchase-receiving.create', compact('order', 'receivingCode'));
    }

    public function receivingStore(PurchaseReceivingRequest $request, PurchaseOrder $purchase_order)
    {
        $order = $purchase_order;
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $companyId = session('company_id') ?? Company::first()?->company_id;

            $lastReceiving = PurchaseReceiving::latest()->first();
            $nextNum = $lastReceiving ? ((int) substr($lastReceiving->receiving_code, -3)) + 1 : 1;
            $receivingCode = 'RCV-' . now()->format('Ymd') . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

            $receiving = PurchaseReceiving::create([
                'company_id' => $companyId,
                'receiving_code' => $receivingCode,
                'receiving_date' => $validated['receiving_date'],
                'po_id' => $order->po_id,
                'po_code' => $order->po_code,
                'receiving_status' => 'completed',
                'receiving_notes' => $validated['receiving_notes'] ?? null,
            ]);

            // Process each item
            $allCompleted = true;
            foreach ($validated['items'] as $item) {
                $subtotal = $item['received_qty'] * $item['received_price'];

                PurchaseReceivingItem::create([
                    'receiving_id' => $receiving->receiving_id,
                    'po_item_id' => $item['po_item_id'],
                    'stock_id' => $item['stock_id'],
                    'received_qty' => $item['received_qty'],
                    'received_price' => $item['received_price'],
                    'subtotal' => $subtotal,
                ]);

                // Update received_qty di PO item
                $poItem = PurchaseOrderItem::find($item['po_item_id']);
                if ($poItem) {
                    $poItem->increment('received_qty', $item['received_qty']);
                }

                // Tambah stok
                $stock = Stock::where('stock_id', $item['stock_id'])->first();
                $stockBefore = $stock ? $stock->stock_amount : 0;
                $stock?->increment('stock_amount', $item['received_qty']);
                $stockAfter = $stockBefore + $item['received_qty'];

                // Catat stock_log
                StockLog::create([
                    'company_id' => $companyId,
                    'stock_id' => $item['stock_id'],
                    'reference_type' => 'purchase_receiving',
                    'reference_code' => $receivingCode,
                    'type' => 'in',
                    'qty' => $item['received_qty'],
                    'price' => $item['received_price'],
                    'total' => $item['received_qty'] * $item['received_price'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'notes' => 'Penerimaan barang dari ' . $order->po_code,
                ]);
            }

            // Update PO status
            $totalReceived = $order->items()->sum('received_qty');
            $totalOrdered = $order->items()->sum('qty');
            $newStatus = $totalReceived >= $totalOrdered ? 'completed' : 'partial';
            $order->update(['po_status' => $newStatus]);

            DB::commit();

            return redirect()->route('admin.purchase-order.show', $order)
                ->with('success', "Penerimaan barang ($receivingCode) berhasil dicatat. Stok otomatis bertambah.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mencatat penerimaan: ' . $e->getMessage());
        }
    }
}
