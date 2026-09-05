<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Admin\OrderBundle;
use App\Models\Admin\OrderVoucher;
use App\Models\Admin\Voucher;
use App\Models\Admin\Product;
use App\Models\Admin\Category;
use App\Models\Admin\Bundle;
use App\Models\Admin\Table;
use App\Models\Admin\Customer;
use App\Models\Admin\Transaction;
use App\Models\Admin\TransactionItem;
use App\Models\Admin\Payment;
use App\Models\Admin\Tax;
use App\Models\Admin\ServiceCharge;
use App\Models\Admin\DailyClosing;
use App\Models\Admin\Outlet;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected function getActiveOutletId(): ?string
    {
        return session('active_outlet_id') ?? session('outlet_id') ?? Outlet::where('delete_status', 0)->value('outlet_id');
    }

    // ——— Ordering page (pilih produk + cart) ———
    public function index()
    {
        $activeOutletId = $this->getActiveOutletId();
        $query = Product::where('delete_status', 0)->with('outlet', 'category', 'stocks');
        if ($activeOutletId) {
            $query->where(function ($q) use ($activeOutletId) {
                $q->where('outlet_id', $activeOutletId)->orWhereNull('outlet_id');
            });
        }
        $products = $query->latest()->paginate(10);
        return view('admin.kasir.order.index', compact('products'));
    }

    public function data(Request $request)
    {
        $activeOutletId = $this->getActiveOutletId();
        $perPage = $request->input('per_page', 10);
        $categoryId = $request->input('category_id');

        $query = Product::where('delete_status', 0)
            ->with('outlet', 'category', 'stocks');

        if ($activeOutletId) {
            $query->where(function ($q) use ($activeOutletId) {
                $q->where('outlet_id', $activeOutletId)->orWhereNull('outlet_id');
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->latest()->paginate($perPage);

        if ($request->ajax()) {
            $viewMode = $request->input('view', 'list');

            return response()->json([
                'html' => $viewMode === 'card'
                    ? view('admin.kasir.order._card', compact('products'))->render()
                    : view('admin.kasir.order._data', compact('products'))->render(),
                'pagination' => $products->links('vendor.pagination.modern')->toHtml(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]);
        }

        return view('admin.kasir.order.index', compact('products'));
    }

    // ——— Bundle data (AJAX) ———
    public function bundleData(Request $request)
    {
        $activeOutletId = $this->getActiveOutletId();
        $perPage = $request->input('per_page', 10);
        $view = $request->input('view', 'card');

        $query = Bundle::where('delete_status', 0)
            ->where('bundle_status', 1)
            ->with('items.product');

        if ($activeOutletId) {
            $query->where(function ($q) use ($activeOutletId) {
                $q->where('outlet_id', $activeOutletId)->orWhereNull('outlet_id');
            });
        }

        $bundles = $query->latest()->paginate($perPage);

        if ($request->ajax()) {
            $partial = $view === 'list' ? 'admin.kasir.order._bundle_data' : 'admin.kasir.order._bundle_card';
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
        $activeOutletId = $this->getActiveOutletId();
        $query = Order::where('delete_status', 0)
            ->with('outlet', 'transaction.items', 'bundles');

        if ($activeOutletId) {
            $query->where('outlet_id', $activeOutletId);
        }

        $orders = $query->orderBy('order_id', 'desc')->paginate(10);
        return view('admin.kasir.order.list', compact('orders'));
    }

    public function listData(Request $request)
    {
        $activeOutletId = $this->getActiveOutletId();
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Order::where('delete_status', 0)->with('outlet');

        if ($activeOutletId) {
            $query->where('outlet_id', $activeOutletId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', '%' . $search . '%')
                  ->orWhere('order_type', 'like', '%' . $search . '%')
                  ->orWhere('order_status', 'like', '%' . $search . '%');
            });
        }

        $orders = $query->orderBy('order_id', 'desc')->with('transaction.items', 'bundles')->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.kasir.order._list_data', compact('orders'))->render(),
                'pagination' => $orders->links('vendor.pagination.modern')->toHtml(),
                'total' => $orders->total(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
            ]);
        }

        return view('admin.kasir.order.list', compact('orders'));
    }

    // ——— Halaman Pembayaran Kasir ———
    public function payment(Order $order)
    {
        if ($order->delete_status) {
            return redirect()->route('admin.order.list')
                ->with('error', 'Pesanan tidak ditemukan.');
        }

        if ($order->order_status === 'completed') {
            return redirect()->route('admin.order.show', $order)
                ->with('info', 'Pesanan ini sudah selesai dan dibayar.');
        }

        if ($order->order_status === 'cancelled') {
            return redirect()->route('admin.order.show', $order)
                ->with('error', 'Pesanan ini sudah dibatalkan.');
        }

        $order->load(['outlet', 'products.activeDiscount', 'vouchers', 'bundles.bundle.items.product']);

        $table = null;
        if ($order->order_table_id) {
            $table = Table::where('table_id', $order->order_table_id)->first();
        }

        $customer = null;
        if ($order->order_customer_id) {
            $customer = Customer::where('customer_id', $order->order_customer_id)->first();
        }

        $outlet = Outlet::where('delete_status', 0)->first();

        // Hitung Subtotal Produk & Diskon
        $items = [];
        $totalSubtotal = 0;
        foreach ($order->products as $product) {
            $qty = (int) $product->pivot->quantity;
            $price = (float) $product->product_price;
            $activeDisc = $product->activeDiscount()->first();
            $discountType = $activeDisc?->discount_type;
            $discountValue = $activeDisc ? (float) ($activeDisc->discount_value ?? 0) : 0;

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

        // Subtotal Bundle
        $bundleSubtotal = 0;
        foreach ($order->bundles as $ob) {
            $bundleSubtotal += (float) $ob->bundle_price * (int) $ob->quantity;
        }
        $totalSubtotal += $bundleSubtotal;

        return view('admin.kasir.order.payment', compact('order', 'table', 'customer', 'outlet', 'items', 'totalSubtotal'));
    }

    // ——— Proses Simpan Pembayaran Kasir ———
    public function processPayment(Request $request, Order $order)
    {
        if ($order->delete_status) {
            return redirect()->route('admin.order.list')
                ->with('error', 'Pesanan tidak ditemukan.');
        }

        if ($order->order_status === 'completed') {
            return redirect()->route('admin.order.show', $order)
                ->with('info', 'Pesanan ini sudah selesai dan dibayar.');
        }

        $validated = $request->validate([
            'payment_metode' => 'required|in:cash,debit',
            'payment_amount' => 'required|numeric|min:0',
            'payment_reference' => 'nullable|string|max:100',
            'payment_remark' => 'nullable|string|max:255',
        ], [
            'payment_metode.required' => 'Metode pembayaran wajib dipilih.',
            'payment_metode.in' => 'Metode pembayaran harus cash atau debit.',
            'payment_amount.required' => 'Nominal pembayaran wajib diisi.',
            'payment_amount.numeric' => 'Nominal pembayaran harus berupa angka.',
            'payment_amount.min' => 'Nominal pembayaran minimal 0.',
        ]);

        $grandTotal = (float) $order->order_grand_total;
        $paymentMetode = strtolower($validated['payment_metode']);
        $paymentAmount = ($paymentMetode === 'debit') ? $grandTotal : (float) $validated['payment_amount'];

        if ($paymentMetode === 'cash' && $paymentAmount < $grandTotal) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Uang tunai yang diterima (Rp ' . number_format($paymentAmount, 0, ',', '.') . ') kurang dari total tagihan (Rp ' . number_format($grandTotal, 0, ',', '.') . ').',
                ], 422);
            }
            return back()->withInput()->with('error', 'Uang tunai yang diterima kurang dari total tagihan.');
        }

        $companyId = $order->outlet_id ?? Outlet::first()?->outlet_id;

        // Ambil sesi shift aktif jika ada
        $dailyClosingId = $order->daily_closing_id;
        if (!$dailyClosingId) {
            $activeShift = DailyClosing::where('outlet_id', $companyId)->where('status', 'open')->latest()->first();
            $dailyClosingId = $activeShift ? $activeShift->id : null;
        }

        $initialStatus = $order->order_status;
        $isPrePayment = ($initialStatus === 'pending');

        $order->load(['products.activeDiscount', 'bundles', 'vouchers']);

        DB::transaction(function () use ($order, $validated, $companyId, $dailyClosingId, $grandTotal, $paymentMetode, $paymentAmount, $isPrePayment) {
            $items = [];
            $totalSubtotal = 0;

            foreach ($order->products as $product) {
                $qty = (int) $product->pivot->quantity;
                $price = (float) $product->product_price;
                $activeDisc = $product->activeDiscount()->first();
                $discountType = $activeDisc?->discount_type;
                $discountValue = $activeDisc ? (float) ($activeDisc->discount_value ?? 0) : 0;

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

            foreach ($order->bundles as $ob) {
                $totalSubtotal += (float) $ob->bundle_price * (int) $ob->quantity;
            }

            // 1. Simpan Transaksi
            $transaction = Transaction::create([
                'outlet_id' => $companyId,
                'daily_closing_id' => $dailyClosingId,
                'transaction_code' => 'TRX-' . $order->order_id . '-' . now()->format('YmdHis'),
                'transaction_date' => now(),
                'transaction_subtotal' => $totalSubtotal,
                'transaction_tax' => (float) ($order->tax_amount ?? 0),
                'transaction_service_charge' => (float) ($order->service_charge_amount ?? 0),
                'transaction_grand_total' => $grandTotal,
                'transaction_status' => 'success',
                'transaction_table_id' => $order->order_table_id,
                'transaction_customer_id' => $order->order_customer_id,
                'transaction_remark' => 'Pembayaran ' . ($paymentMetode === 'cash' ? 'Tunai' : 'Debit Card') . ' pesanan #' . $order->order_id,
                'created_by' => 'admin',
            ]);

            // Hitung kembalian & set format remark
            $changeAmount = max(0, $paymentAmount - $grandTotal);
            $paymentRemark = $validated['payment_remark'] ?? null;
            if (!$paymentRemark) {
                if ($paymentMetode === 'cash') {
                    $paymentRemark = 'Tunai: Rp ' . number_format($paymentAmount, 0, ',', '.') . ' (Kembalian: Rp ' . number_format($changeAmount, 0, ',', '.') . ')';
                } else {
                    $paymentRemark = 'Debit Card: ' . ($validated['payment_reference'] ?? 'EDC');
                }
            }

            $paymentRef = $validated['payment_reference'] ?? null;
            if (!$paymentRef) {
                $paymentRef = strtoupper($paymentMetode) . '-ORD' . $order->order_id;
            }

            // 2. Simpan Pembayaran (Semua Kolom Terisi Lengkap)
            $payment = Payment::create([
                'outlet_id' => $companyId,
                'transaction_id' => $transaction->transaction_id,
                'payment_metode' => $paymentMetode,
                'payment_amount' => $paymentAmount,
                'payment_reference' => $paymentRef,
                'payment_status' => 'completed',
                'payment_grand_total' => $grandTotal,
                'payment_remark' => $paymentRemark,
                'payment_date' => now()->format('Y-m-d H:i:s'),
                'payment_table_id' => (string) ($order->order_table_id ?? ''),
                'payment_customer_id' => (string) ($order->order_customer_id ?? ''),
                'created_by' => 'admin',
                'updated_by' => 'admin',
                'delete_status' => 0,
            ]);

            // 3. Kaitkan payment_id ke transaction
            $transaction->update(['payment_id' => $payment->payment_id]);

            // 4. Simpan Transaction Items
            foreach ($items as $item) {
                $product = $item['product'];
                TransactionItem::create([
                    'outlet_id' => $companyId,
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
                    'created_by' => 'admin',
                ]);
            }

            // 5. Link bundle ke transaction
            $order->bundles()->update([
                'transaction_id' => $transaction->transaction_id,
                'updated_by' => 'admin',
            ]);

            // 6. Jika Pre-Payment (dari status pending): potong stok sekarang & set meja terisi
            if ($isPrePayment) {
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

                // Meja di-set terisi
                if ($order->order_table_id) {
                    Table::where('table_id', $order->order_table_id)
                        ->where('delete_status', 0)
                        ->update(['table_status' => 'terisi']);
                }

                // Update order status: in_progress & paid
                $order->update([
                    'order_status' => 'in_progress',
                    'payment_status' => 'paid',
                    'order_transaction_id' => $transaction->transaction_id,
                    'daily_closing_id' => $dailyClosingId,
                ]);
            } else {
                // Mode Post-Payment: pesanan sudah selesai dimasak & sekarang dibayar
                $order->update([
                    'order_status' => 'completed',
                    'payment_status' => 'paid',
                    'order_transaction_id' => $transaction->transaction_id,
                    'daily_closing_id' => $dailyClosingId,
                ]);

                // Free table
                if ($order->order_table_id) {
                    Table::where('table_id', $order->order_table_id)
                        ->where('delete_status', 0)
                        ->update(['table_status' => 'tersedia']);
                }
            }

            // 7. Update Statistik Kas Shift Aktif jika ada
            if ($dailyClosingId) {
                $closing = DailyClosing::find($dailyClosingId);
                if ($closing && $closing->status === 'open') {
                    if ($validated['payment_metode'] === 'cash') {
                        $closing->system_cash_sales += $grandTotal;
                    } else {
                        $closing->system_non_cash_sales += $grandTotal;
                    }
                    $closing->system_expected_cash = $closing->starting_cash + $closing->system_cash_sales;
                    $closing->save();
                }
            }
        });

        $message = $isPrePayment
            ? 'Pembayaran berhasil diterima. Pesanan dikirim ke dapur untuk dimasak.'
            : 'Pembayaran berhasil disimpan dan pesanan telah selesai.';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect_url' => route('admin.order.receipt', $order),
                'show_url' => route('admin.order.show', $order),
            ]);
        }

        return redirect()->route('admin.order.show', $order)
            ->with('success', $message);
    }

    // ——— Tandai Selesai Disajikan (Khusus Order Pre-Payment yang sudah Lunas) ———
    public function completeServing(Order $order)
    {
        if ($order->delete_status || $order->order_status !== 'in_progress' || $order->payment_status !== 'paid') {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Pesanan tidak dapat diselesaikan.'], 400);
            }
            return redirect()->route('admin.order.show', $order)->with('error', 'Pesanan tidak dapat diselesaikan.');
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'order_status' => 'completed',
            ]);

            if ($order->order_table_id) {
                Table::where('table_id', $order->order_table_id)
                    ->where('delete_status', 0)
                    ->update(['table_status' => 'tersedia']);
            }
        });

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesanan #' . $order->order_id . ' telah selesai disajikan dan meja telah dikosongkan.',
            ]);
        }

        return redirect()->route('admin.order.show', $order)
            ->with('success', 'Pesanan #' . $order->order_id . ' telah selesai disajikan dan meja telah dikosongkan.');
    }

    // ——— Complete pesanan (Fallback) ———
    public function complete(Order $order)
    {
        if ($order->delete_status || $order->order_status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak dapat diselesaikan.'], 400);
        }

        $request = new Request([
            'payment_metode' => 'cash',
            'payment_amount' => (float) $order->order_grand_total,
            'payment_reference' => 'CASH-ORD' . $order->order_id,
            'payment_remark' => 'Pembayaran Tunai Pas (Auto-Complete)',
        ]);

        return $this->processPayment($request, $order);
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
        if ($order->delete_status || ($order->order_status !== 'completed' && $order->payment_status !== 'paid')) {
            abort(404);
        }

        // Load transaction_items & payment untuk snapshot harga & diskon
        $order->load('vouchers', 'bundles.bundle.items.product');
        $transaction = Transaction::with('items', 'payment')
            ->where('transaction_id', $order->order_transaction_id)
            ->where('delete_status', 0)
            ->first();

        $table = null;
        if ($order->order_table_id) {
            $table = Table::where('table_id', $order->order_table_id)->first();
        }

        $outlet = Outlet::where('delete_status', 0)->first();

        return view('admin.kasir.order.receipt', compact('order', 'transaction', 'table', 'outlet'));
    }

    // ——— Detail pesanan ———
    public function show(Order $order)
    {
        if ($order->delete_status) {
            return redirect()->route('admin.order.list')
                ->with('error', 'Pesanan tidak ditemukan.');
        }

        $order->load('outlet', 'products', 'vouchers', 'bundles.bundle.items.product');

        // Kalo ada order_transaction_id (lunas), load transaction_items & payment jg biar dapet snapshot harga & diskon
        $transaction = null;
        $transactionItems = null;
        if ($order->order_transaction_id) {
            $transaction = Transaction::with('items', 'payment')
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

        $setting = \App\Models\Admin\SettingOutlet::where('delete_status', 0)->first();
        $paymentTiming = $setting?->payment_timing ?? 'post_payment';

        return view('admin.kasir.order.show', compact('order', 'table', 'customer', 'transaction', 'transactionItems', 'setting', 'paymentTiming'));
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

        return view('admin.kasir.order.create', compact('cart', 'tables', 'customers', 'vouchers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_type' => 'required|string|in:dine_in,take_away,delivery',
            'order_table_id' => 'nullable',
            'order_customer_id' => 'nullable',
            'order_remark' => 'nullable|string',
            'voucher_code' => 'nullable|string|max:50',
            'items' => 'nullable|array',
            'items.*.product_id' => 'required',
            'items.*.product_name' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.note' => 'nullable|string|max:500',
            'bundles' => 'nullable|array',
            'bundles.*.bundle_id' => 'required',
            'bundles.*.bundle_name' => 'required|string',
            'bundles.*.bundle_price' => 'required|numeric|min:0',
            'bundles.*.qty' => 'required|integer|min:1',
            'bundles.*.items' => 'required|array|min:1',
            'bundles.*.items.*.product_id' => 'required',
            'bundles.*.items.*.quantity' => 'required|integer|min:1',
        ]);


        if (empty($validated['items'] ?? []) && empty($validated['bundles'] ?? [])) {
            return back()->withErrors(['items' => 'Minimal satu item produk atau bundle.'])->withInput();
        }

        $companyId = Outlet::where('delete_status', 0)->value('outlet_id');

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
                'outlet_id' => $companyId,
                'voucher_code' => $voucher->voucher_code,
                'voucher_type' => $voucher->voucher_type,
                'voucher_value' => (float) $voucher->voucher_value,
                'voucher_max_discount' => (float) ($voucher->voucher_max_discount ?? 0),
                'voucher_amount' => $voucherAmount,
                'created_by' => 'admin',
            ];
        }

        $afterDiscountTotal = max(0, $grandTotal - $voucherAmount);

        // Ambil Master Service Charge Aktif
        $activeService = ServiceCharge::where('is_active', 1)->first();
        $scPercent = $activeService ? (float) $activeService->rate_percent : 0;
        $scAmount = round($afterDiscountTotal * ($scPercent / 100), 2);

        // Ambil Master Pajak PB1 Aktif
        $activeTax = Tax::where('is_active', 1)->first();
        $taxPercent = $activeTax ? (float) $activeTax->rate_percent : 0;
        $taxType = $activeTax ? $activeTax->type : 'exclusive';
        $isScTaxable = $activeService ? (bool) $activeService->is_taxable : false;
        $taxableBase = $afterDiscountTotal + ($isScTaxable ? $scAmount : 0);
        $taxAmount = round($taxableBase * ($taxPercent / 100), 2);

        $finalGrandTotal = $taxableBase + $taxAmount;

        // Ambil Sesi Shift Aktif
        $activeShift = DailyClosing::where('outlet_id', $companyId)->where('status', 'open')->latest()->first();
        $dailyClosingId = $activeShift ? $activeShift->id : null;

        try {
            DB::transaction(function () use ($validated, $companyId, $dailyClosingId, $finalGrandTotal, $taxPercent, $taxAmount, $taxType, $scPercent, $scAmount, $itemDetails, $bundleDetails, $voucherData, $request) {
                $order = Order::create([
                    'outlet_id' => $companyId,
                    'daily_closing_id' => $dailyClosingId,
                    'order_type' => $validated['order_type'],
                    'order_status' => 'in_progress',
                    'order_grand_total' => $finalGrandTotal,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $taxAmount,
                    'tax_type' => $taxType,
                    'service_charge_percent' => $scPercent,
                    'service_charge_amount' => $scAmount,
                    'order_remark' => $validated['order_remark'] ?? null,
                    'order_table_id' => $validated['order_table_id'] ?? null,
                    'order_customer_id' => $validated['order_customer_id'] ?? null,
                    'created_by' => 'admin',
                ]);


                // Sync products ke pivot order_product
                $syncData = [];
                foreach ($itemDetails as $item) {
                    $syncData[$item['product_id']] = [
                        'outlet_id' => $companyId,
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
                        'outlet_id' => $companyId,
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

            return redirect()->route('admin.order.list')
                ->with('success', 'Pesanan berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage())->withInput();
        }


    }
}
