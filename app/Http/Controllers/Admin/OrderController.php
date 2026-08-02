<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Admin\OrderBundle;
use App\Models\Admin\OrderVoucher;
use App\Models\Admin\Product;
use App\Models\Admin\Category;
use App\Models\Admin\Table;
use App\Models\Admin\Customer;
use App\Models\Admin\Transaction;
use App\Models\Admin\TransactionItem;
use App\Models\Admin\Voucher;
use App\Models\Admin\Bundle;
use App\Models\SysAdmin\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // ——— Ordering page (pilih produk + cart) ———
    public function index()
    {
        $products = Product::where('delete_status', 0)
            ->with('company', 'category', 'stocks')
            ->latest()
            ->paginate(10);
        return view('admin.order.index', compact('products'));
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $categoryId = $request->input('category_id');

        $query = Product::where('delete_status', 0)
            ->with('company', 'category', 'stocks');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->latest()->paginate($perPage);

        if ($request->ajax()) {
            $viewMode = $request->input('view', 'list');

            return response()->json([
                'html' => $viewMode === 'card'
                    ? view('admin.order._card', compact('products'))->render()
                    : view('admin.order._data', compact('products'))->render(),
                'pagination' => $products->links('vendor.pagination.modern')->toHtml(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]);
        }

        return view('admin.order.index', compact('products'));
    }

    // ——— Bundle data (AJAX) ———
    public function bundleData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $view = $request->input('view', 'card');

        $bundles = Bundle::where('delete_status', 0)
            ->where('bundle_status', 1)
            ->with('items.product')
            ->latest()
            ->paginate($perPage);

        if ($request->ajax()) {
            $partial = $view === 'list' ? 'admin.order._bundle_data' : 'admin.order._bundle_card';
            return response()->json([
                'html' => view($partial, compact('bundles'))->render(),
                'pagination' => $bundles->links('vendor.pagination.modern')->toHtml(),
                'total' => $bundles->total(),
                'from' => $bundles->firstItem(),
                'to' => $bundles->lastItem(),
            ]);
        }

        return response()->json(['html' => '', 'pagination' => '']);
    }

    // ——— List pesanan (table) ———
    public function list()
    {
        $orders = Order::where('delete_status', 0)
            ->with('company')
            ->with('transaction.items')
            ->with('bundles')
            ->latest()
            ->paginate(10);
        return view('admin.order.list', compact('orders'));
    }

    public function listData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Order::where('delete_status', 0)->with('company');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', '%' . $search . '%')
                  ->orWhere('order_type', 'like', '%' . $search . '%')
                  ->orWhere('order_status', 'like', '%' . $search . '%');
            });
        }

        $orders = $query->latest()->with('transaction.items', 'bundles')->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.order._list_data', compact('orders'))->render(),
                'pagination' => $orders->links('vendor.pagination.modern')->toHtml(),
                'total' => $orders->total(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
            ]);
        }

        return view('admin.order.list', compact('orders'));
    }

    // ——— Complete pesanan ———
    public function complete(Order $order)
    {
        if ($order->delete_status || $order->order_status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak dapat diselesaikan.'], 400);
        }

        DB::transaction(function () use ($order) {
            $order->load('products', 'bundles');

            $companyId = $order->company_id;

            // Hitung per item (include diskon produk)
            $items = [];
            $totalSubtotal = 0;
            $totalDiscount = 0;

            foreach ($order->products as $product) {
                $qty = (int) $product->pivot->quantity;
                $price = (float) $product->product_price;
                // Baca diskon dari pivot aktif
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
                // Cap biar gak minus
                $discountAmount = min($discountAmount, $price);

                $subtotal = ($price - $discountAmount) * $qty;
                $totalSubtotal += $subtotal;
                $totalDiscount += $discountAmount * $qty;

                $items[] = [
                    'product' => $product,
                    'qty' => $qty,
                    'price' => $price,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'discount_amount' => $discountAmount,
                    'subtotal' => $subtotal,
                ];
            }

            // Bundle masuk ke transaction_subtotal (bundle_price x qty, tanpa diskon)
            foreach ($order->bundles as $ob) {
                $totalSubtotal += (float) $ob->bundle_price * (int) $ob->quantity;
            }

            // Simpan transaction — grand_total pake dari order (udah include voucher)
            $transaction = Transaction::create([
                'company_id' => $companyId,
                'transaction_code' => 'TRX-' . $order->order_id . '-' . now()->format('Ymd'),
                'transaction_date' => now(),
                'transaction_subtotal' => $totalSubtotal,
                'transaction_tax' => 0,
                'transaction_service_charge' => 0,
                'transaction_grand_total' => (float) $order->order_grand_total,
                'transaction_status' => 'success',
                'transaction_table_id' => $order->order_table_id,
                'transaction_customer_id' => $order->order_customer_id,
                'transaction_remark' => 'Dari pesanan #' . $order->order_id,
                'created_by' => 'admin',
            ]);

            // Simpan transaction_items (include diskon)
            foreach ($items as $item) {
                $product = $item['product'];
                TransactionItem::create([
                    'company_id' => $companyId,
                    'transaction_id' => $transaction->transaction_id,
                    'product_id' => $product->product_id,
                    'product_name' => $product->product_name,
                    'price' => $item['price'],
                    'discount_type' => $item['discount_type'],
                    'discount_value' => $item['discount_value'],
                    'discount_amount' => $item['discount_amount'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                    'note' => $product->pivot->note,
                    'created_by' => 'admin',
                ]);
            }

            // Link bundle ke transaction (isi transaction_id — 1 tabel utuh)
            $order->bundles()->update([
                'transaction_id' => $transaction->transaction_id,
                'updated_by' => 'admin',
            ]);

            // Update status order + link ke transaction
            $order->update([
                'order_status' => 'completed',
                'order_transaction_id' => $transaction->transaction_id,
            ]);

            // Free table
            if ($order->order_table_id) {
                Table::where('table_id', $order->order_table_id)
                    ->where('delete_status', 0)
                    ->update(['table_status' => 'tersedia']);
            }
        });

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pesanan selesai.']);
        }

        return redirect()->route('admin.order.show', $order)
            ->with('success', 'Pesanan selesai.');
    }

    // ——— Terima pesanan (pending → in_progress, decrement stock, meja terisi) ———
    public function accept(Order $order)
    {
        if ($order->delete_status || $order->order_status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak dapat diterima.'], 400);
        }

        DB::transaction(function () use ($order) {
            $order->load('products', 'bundles');

            // Auto-decrement stok produk
            foreach ($order->products as $product) {
                $orderQty = (int) $product->pivot->quantity;
                $product->load('stocks');
                foreach ($product->stocks as $stock) {
                    $bomQty = (int) $stock->pivot->quantity;
                    $deductQty = $bomQty * $orderQty;
                    if ($deductQty <= 0) continue;
                    $stockAfter = max(0, (int) $stock->stock_amount - $deductQty);
                    $stock->update(['stock_amount' => $stockAfter]);
                }
            }

            // Auto-decrement stok isi bundle
            foreach ($order->bundles as $ob) {
                $bd = $ob->bundle;
                if (!$bd) continue;
                foreach ($bd->items as $bi) {
                    $product = Product::with('stocks')->find($bi->product_id);
                    if (!$product) continue;
                    $orderQty = (int) $bi->quantity * (int) $ob->quantity;
                    foreach ($product->stocks as $stock) {
                        $bomQty = (int) $stock->pivot->quantity;
                        $deductQty = $bomQty * $orderQty;
                        if ($deductQty <= 0) continue;
                        $stockAfter = max(0, (int) $stock->stock_amount - $deductQty);
                        $stock->update(['stock_amount' => $stockAfter]);
                    }
                }
            }

            // Update status order + meja jadi terisi
            $order->update(['order_status' => 'in_progress']);

            if ($order->order_table_id) {
                Table::where('table_id', $order->order_table_id)
                    ->where('delete_status', 0)
                    ->update(['table_status' => 'terisi']);
            }
        });

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pesanan diterima.']);
        }

        return redirect()->route('admin.order.show', $order)
            ->with('success', 'Pesanan diterima.');
    }

    // ——— Cetak struk ———
    public function receipt(Order $order)
    {
        if ($order->delete_status || $order->order_status !== 'completed') {
            abort(404);
        }

        // Load transaction_items untuk snapshot harga & diskon
        $order->load('vouchers', 'bundles.bundle.items.product');
        $transaction = Transaction::with('items')
            ->where('transaction_id', $order->order_transaction_id)
            ->where('delete_status', 0)
            ->first();

        $table = null;
        if ($order->order_table_id) {
            $table = Table::where('table_id', $order->order_table_id)->first();
        }

        $company = Company::where('delete_status', 0)->first();

        return view('admin.order.receipt', compact('order', 'transaction', 'table', 'company'));
    }

    // ——— Detail pesanan ———
    public function show(Order $order)
    {
        if ($order->delete_status) {
            return redirect()->route('admin.order.list')
                ->with('error', 'Pesanan tidak ditemukan.');
        }

        $order->load('company', 'products', 'vouchers', 'bundles.bundle.items.product');

        // Kalo completed, load transaction_items jg biar dapet snapshot harga & diskon
        $transaction = null;
        $transactionItems = null;
        if ($order->order_status === 'completed' && $order->order_transaction_id) {
            $transaction = Transaction::with('items')
                ->where('transaction_id', $order->order_transaction_id)
                ->where('delete_status', 0)
                ->first();
            if ($transaction) {
                $transactionItems = $transaction->items;
            }
        }

        $table = null;
        if ($order->order_table_id) {
            $table = Table::where('table_id', $order->order_table_id)->first();
        }

        $customer = null;
        if ($order->order_customer_id) {
            $customer = Customer::where('customer_id', $order->order_customer_id)->first();
        }

        return view('admin.order.show', compact('order', 'table', 'customer', 'transaction', 'transactionItems'));
    }

    public function storeCart(Request $request)
    {
        $cart = $request->input('cart', []);
        if (empty($cart)) {
            return response()->json(['ok' => false, 'message' => 'Cart kosong']);
        }

        // simpan ke session
        session(['order_cart' => $cart]);

        return response()->json(['ok' => true]);
    }

    // ——— Cek voucher via AJAX ———
    public function checkVoucher(Request $request): JsonResponse
    {
        $request->validate([
            'voucher_code' => 'required|string|max:50',
            'grand_total' => 'required|numeric|min:0',
        ]);

        $voucher = Voucher::active()->byCode($request->voucher_code)->first();

        if (!$voucher) {
            return response()->json(['ok' => false, 'message' => 'Kode voucher tidak valid atau sudah kedaluwarsa.']);
        }

        $grandTotal = (float) $request->grand_total;
        $minPurchase = (float) ($voucher->voucher_min_purchase ?? 0);

        if ($grandTotal < $minPurchase) {
            return response()->json(['ok' => false, 'message' => 'Minimal belanja Rp ' . number_format($minPurchase, 0) . ' untuk menggunakan voucher ini.']);
        }

        // Hitung voucher_amount
        $voucherAmount = 0;
        if ($voucher->voucher_type === 'percentage') {
            $voucherAmount = round($grandTotal * ((float) $voucher->voucher_value / 100), 2);
            $maxDisc = (float) ($voucher->voucher_max_discount ?? 0);
            if ($maxDisc > 0) {
                $voucherAmount = min($voucherAmount, $maxDisc);
            }
        } elseif ($voucher->voucher_type === 'nominal') {
            $voucherAmount = min((float) $voucher->voucher_value, $grandTotal);
        }

        return response()->json([
            'ok' => true,
            'voucher_name' => $voucher->voucher_name,
            'voucher_code' => $voucher->voucher_code,
            'voucher_type' => $voucher->voucher_type,
            'voucher_value' => (float) $voucher->voucher_value,
            'voucher_max_discount' => (float) ($voucher->voucher_max_discount ?? 0),
            'voucher_amount' => $voucherAmount,
        ]);
    }

    public function create()
    {
        $cart = session('order_cart', []);
        if (empty($cart)) {
            return redirect()->route('admin.order.index')
                ->with('error', 'Keranjang kosong.');
        }

        $tables = Table::where('delete_status', 0)
            ->where('table_status', 'tersedia')
            ->orderBy('table_number')
            ->get();
        $customers = Customer::where('delete_status', 0)
            ->orderBy('customer_name')
            ->get(['customer_id', 'customer_name', 'customer_phone']);
        $vouchers = Voucher::active()->get();

        return view('admin.order.create', compact('cart', 'tables', 'customers', 'vouchers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_type' => 'required|string|in:dine_in,take_away,delivery',
            'order_table_id' => 'nullable|string',
            'order_customer_id' => 'nullable|string',
            'order_remark' => 'nullable|string',
            'voucher_code' => 'nullable|string|max:50',
            'items' => 'nullable|array',
            'items.*.product_id' => 'required|string',
            'items.*.product_name' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.note' => 'nullable|string|max:500',
            'bundles' => 'nullable|array',
            'bundles.*.bundle_id' => 'required|string',
            'bundles.*.bundle_name' => 'required|string',
            'bundles.*.bundle_price' => 'required|numeric|min:0',
            'bundles.*.qty' => 'required|integer|min:1',
            'bundles.*.items' => 'required|array|min:1',
            'bundles.*.items.*.product_id' => 'required|string',
            'bundles.*.items.*.quantity' => 'required|integer|min:1',
        ]);

        if (empty($validated['items'] ?? []) && empty($validated['bundles'] ?? [])) {
            return back()->withErrors(['items' => 'Minimal satu item produk atau bundle.'])->withInput();
        }

        $companyId = Company::where('delete_status', 0)->value('company_id');

        // Hitung grand total include diskon produk
        $grandTotal = 0;
        $itemDetails = [];
        foreach ($validated['items'] ?? [] as $item) {
            $price = (float) $item['price'];
            $product = Product::find($item['product_id']);
            $activeDisc = $product ? $product->activeDiscount()->first() : null;
            $discountType = $activeDisc?->discount_type;
            $discountValue = $activeDisc ? (float) ($activeDisc->discount_value ?? 0) : 0;

            $discountAmount = 0;
            if ($discountType === 'percentage' && $discountValue > 0) {
                $discountAmount = round($price * ($discountValue / 100), 2);
            } elseif ($discountType === 'nominal' && $discountValue > 0) {
                $discountAmount = min($discountValue, $price);
            }
            $discountAmount = min($discountAmount, $price);

            $subtotal = ($price - $discountAmount) * (int) $item['qty'];
            $grandTotal += $subtotal;

            $itemDetails[] = [
                'product_id' => $item['product_id'],
                'qty' => (int) $item['qty'],
                'note' => $item['note'] ?? null,
                'price' => $price,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_amount' => $discountAmount,
                'subtotal' => $subtotal,
            ];
        }

        // Hitung bundle (harga paket tetap, tanpa diskon)
        $bundleDetails = [];
        foreach ($validated['bundles'] ?? [] as $b) {
            $bSubtotal = (float) $b['bundle_price'] * (int) $b['qty'];
            $grandTotal += $bSubtotal;
            $bundleDetails[] = [
                'bundle_id' => $b['bundle_id'],
                'bundle_name' => $b['bundle_name'],
                'bundle_price' => (float) $b['bundle_price'],
                'qty' => (int) $b['qty'],
                'items' => array_map(fn($i) => [
                    'product_id' => $i['product_id'],
                    'quantity' => (int) $i['quantity'],
                ], $b['items']),
            ];
        }

        // Proses voucher
        $voucherAmount = 0;
        $voucherData = null;
        $voucherCode = $validated['voucher_code'] ?? null;

        if ($voucherCode) {
            $voucher = Voucher::active()->byCode($voucherCode)->first();

            if (!$voucher) {
                return back()->withErrors(['voucher_code' => 'Kode voucher tidak valid atau sudah kedaluwarsa.'])->withInput();
            }

            // Cek min purchase
            $minPurchase = (float) ($voucher->voucher_min_purchase ?? 0);
            if ($grandTotal < $minPurchase) {
                return back()->withErrors(['voucher_code' => 'Minimal belanja Rp ' . number_format($minPurchase, 0) . ' untuk menggunakan voucher ini.'])->withInput();
            }

            // Hitung voucher_amount
            if ($voucher->voucher_type === 'percentage') {
                $voucherAmount = round($grandTotal * ((float) $voucher->voucher_value / 100), 2);
                $maxDisc = (float) ($voucher->voucher_max_discount ?? 0);
                if ($maxDisc > 0) {
                    $voucherAmount = min($voucherAmount, $maxDisc);
                }
            } elseif ($voucher->voucher_type === 'nominal') {
                $voucherAmount = min((float) $voucher->voucher_value, $grandTotal);
            }

            $voucherData = [
                'company_id' => $companyId,
                'voucher_code' => $voucher->voucher_code,
                'voucher_type' => $voucher->voucher_type,
                'voucher_value' => (float) $voucher->voucher_value,
                'voucher_max_discount' => (float) ($voucher->voucher_max_discount ?? 0),
                'voucher_amount' => $voucherAmount,
                'created_by' => 'admin',
            ];
        }

        $finalGrandTotal = $grandTotal - $voucherAmount;

        DB::transaction(function () use ($validated, $companyId, $finalGrandTotal, $itemDetails, $bundleDetails, $voucherData, $request) {
            $order = Order::create([
                'company_id' => $companyId,
                'order_type' => $validated['order_type'],
                'order_status' => 'in_progress',
                'order_grand_total' => $finalGrandTotal,
                'order_remark' => $validated['order_remark'] ?? null,
                'order_table_id' => $validated['order_table_id'] ?? null,
                'order_customer_id' => $validated['order_customer_id'] ?? null,
                'created_by' => 'admin',
            ]);

            // Sync products ke pivot order_product
            $syncData = [];
            foreach ($itemDetails as $item) {
                $syncData[$item['product_id']] = [
                    'company_id' => $companyId,
                    'quantity' => $item['qty'],
                    'note' => $item['note'] ?? null,
                    'delete_status' => 0,
                    'created_by' => 'admin',
                ];
            }
            $order->products()->sync($syncData);

            // Simpan bundle: 1 baris per bundle di order_bundle (identitas utuh)
            foreach ($bundleDetails as $bd) {
                OrderBundle::create([
                    'company_id' => $companyId,
                    'order_id' => $order->order_id,
                    'bundle_id' => $bd['bundle_id'],
                    'bundle_name' => $bd['bundle_name'],
                    'bundle_price' => $bd['bundle_price'],
                    'quantity' => $bd['qty'],
                    'subtotal' => (float) $bd['bundle_price'] * $bd['qty'],
                    'created_by' => 'admin',
                ]);
            }

            // Simpan voucher kalo ada
            if ($voucherData) {
                $voucherData['order_id'] = $order->order_id;
                OrderVoucher::create($voucherData);
            }

            // Auto-decrement stok
            foreach ($itemDetails as $item) {
                $product = Product::with('stocks')->find($item['product_id']);
                if (!$product) continue;

                $orderQty = $item['qty'];

                foreach ($product->stocks as $stock) {
                    $bomQty = (int) $stock->pivot->quantity;
                    $deductQty = $bomQty * $orderQty;

                    if ($deductQty <= 0) continue;

                    $stockAfter = max(0, (int) $stock->stock_amount - $deductQty);
                    $stock->update(['stock_amount' => $stockAfter]);
                }
            }

            // Auto-decrement stok isi bundle (BOM per produk isinya)
            foreach ($bundleDetails as $bd) {
                foreach ($bd['items'] as $bi) {
                    $product = Product::with('stocks')->find($bi['product_id']);
                    if (!$product) continue;

                    $orderQty = $bi['quantity'] * $bd['qty'];

                    foreach ($product->stocks as $stock) {
                        $bomQty = (int) $stock->pivot->quantity;
                        $deductQty = $bomQty * $orderQty;

                        if ($deductQty <= 0) continue;

                        $stockAfter = max(0, (int) $stock->stock_amount - $deductQty);
                        $stock->update(['stock_amount' => $stockAfter]);
                    }
                }
            }

            // Update status meja jadi terisi jika dine_in
            if ($validated['order_type'] === 'dine_in' && !empty($validated['order_table_id'])) {
                Table::where('table_id', $validated['order_table_id'])
                    ->where('delete_status', 0)
                    ->update(['table_status' => 'terisi']);
            }
        });

        session()->forget('order_cart');

        return redirect()->route('admin.order.index')
            ->with('success', 'Pesanan berhasil dibuat.');
    }
}
