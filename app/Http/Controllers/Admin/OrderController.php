<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Admin\Product;
use App\Models\Admin\Category;
use App\Models\Admin\Table;
use App\Models\Admin\Customer;
use App\Models\Admin\Transaction;
use App\Models\Admin\TransactionItem;
use App\Models\SysAdmin\Company;
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

    // ——— List pesanan (table) ———
    public function list()
    {
        $orders = Order::where('delete_status', 0)
            ->with('company')
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

        $orders = $query->latest()->paginate($perPage);

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
            $order->load('products');

            $companyId = $order->company_id;

            // Hitung subtotal & grand total
            $subtotal = 0;
            foreach ($order->products as $product) {
                $qty = (int) $product->pivot->quantity;
                $price = (float) $product->product_price;
                $subtotal += $price * $qty;
            }

            // Simpan transaction
            $transaction = Transaction::create([
                'company_id' => $companyId,
                'transaction_code' => 'TRX-' . $order->order_id . '-' . now()->format('Ymd'),
                'transaction_date' => now(),
                'transaction_subtotal' => $subtotal,
                'transaction_tax' => 0,
                'transaction_service_charge' => 0,
                'transaction_grand_total' => $subtotal,
                'transaction_status' => 'success',
                'transaction_table_id' => $order->order_table_id,
                'transaction_customer_id' => $order->order_customer_id,
                'transaction_remark' => 'Dari pesanan #' . $order->order_id,
                'created_by' => 'admin',
            ]);

            // Simpan transaction_items
            foreach ($order->products as $product) {
                $qty = (int) $product->pivot->quantity;
                $price = (float) $product->product_price;

                TransactionItem::create([
                    'company_id' => $companyId,
                    'transaction_id' => $transaction->transaction_id,
                    'product_id' => $product->product_id,
                    'product_name' => $product->product_name,
                    'price' => $price,
                    'qty' => $qty,
                    'subtotal' => $price * $qty,
                    'note' => $product->pivot->note,
                    'created_by' => 'admin',
                ]);
            }

            // Update status order
            $order->update(['order_status' => 'completed']);

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

    // ——— Cetak struk ———
    public function receipt(Order $order)
    {
        if ($order->delete_status || $order->order_status !== 'completed') {
            abort(404);
        }

        $order->load('products');

        $table = null;
        if ($order->order_table_id) {
            $table = Table::where('table_id', $order->order_table_id)->first();
        }

        $company = Company::where('delete_status', 0)->first();

        return view('admin.order.receipt', compact('order', 'table', 'company'));
    }

    // ——— Detail pesanan ———
    public function show(Order $order)
    {
        if ($order->delete_status) {
            return redirect()->route('admin.order.list')
                ->with('error', 'Pesanan tidak ditemukan.');
        }

        $order->load('company', 'products');

        $table = null;
        if ($order->order_table_id) {
            $table = Table::where('table_id', $order->order_table_id)->first();
        }

        $customer = null;
        if ($order->order_customer_id) {
            $customer = Customer::where('customer_id', $order->order_customer_id)->first();
        }

        return view('admin.order.show', compact('order', 'table', 'customer'));
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

        return view('admin.order.create', compact('cart', 'tables', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_type' => 'required|string|in:dine_in,take_away,delivery',
            'order_table_id' => 'nullable|string',
            'order_customer_id' => 'nullable|string',
            'order_remark' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|string',
            'items.*.product_name' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.note' => 'nullable|string|max:500',
        ]);

        $companyId = Company::where('delete_status', 0)->value('company_id');

        $grandTotal = collect($validated['items'])->sum(fn($i) => $i['price'] * $i['qty']);

        DB::transaction(function () use ($validated, $companyId, $grandTotal, $request) {
            $order = Order::create([
                'company_id' => $companyId,
                'order_type' => $validated['order_type'],
                'order_status' => 'in_progress',
                'order_grand_total' => $grandTotal,
                'order_remark' => $validated['order_remark'] ?? null,
                'order_table_id' => $validated['order_table_id'] ?? null,
                'order_customer_id' => $validated['order_customer_id'] ?? null,
                'created_by' => 'admin',
            ]);

            // Sync products ke pivot order_product
            $syncData = [];
            foreach ($validated['items'] as $item) {
                $syncData[$item['product_id']] = [
                    'company_id' => $companyId,
                    'quantity' => $item['qty'],
                    'note' => $item['note'] ?? null,
                    'delete_status' => 0,
                    'created_by' => 'admin',
                ];
            }
            $order->products()->sync($syncData);

            // Auto-decrement stok
            foreach ($validated['items'] as $item) {
                $product = Product::with('stocks')->find($item['product_id']);
                if (!$product) continue;

                $orderQty = (int) $item['qty'];

                foreach ($product->stocks as $stock) {
                    $bomQty = (int) $stock->pivot->quantity;
                    $deductQty = $bomQty * $orderQty;

                    if ($deductQty <= 0) continue;

                    $stockAfter = max(0, (int) $stock->stock_amount - $deductQty);
                    $stock->update(['stock_amount' => $stockAfter]);
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
