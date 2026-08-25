<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Admin\Bundle;
use App\Models\Admin\Category;
use App\Models\Admin\Customer;
use App\Models\Admin\Order;
use App\Models\Admin\OrderBundle;
use App\Models\Admin\OrderVoucher;
use App\Models\Admin\Product;
use App\Models\Admin\Table;
use App\Models\Admin\Voucher;
use App\Models\Admin\SettingOutlet;
use App\Models\Admin\Outlet;
use App\Models\SysAdmin\Client;
use App\Services\Client\ClientDatabaseManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class OrderController extends Controller
{
    /**
     * Resolve and configure Multi-Client & Multi-Outlet context from ULID parameters.
     */
    protected function setupGuestContext(string $clientId, string $outletId, string $tableId): array
    {
        // 1. Hubungkan ke Central DB untuk mencari data Client
        ClientDatabaseManager::connectToCentral();
        $client = Client::where('client_id', $clientId)
            ->orWhere('client_slug', $clientId)
            ->orWhere('client_code', strtoupper($clientId))
            ->where('status', 'active')
            ->where('delete_status', 0)
            ->first();

        if (!$client) {
            abort(404, 'Restoran / Client tidak ditemukan atau sedang dinonaktifkan.');
        }

        // 2. Hubungkan koneksi database ke client DB yang bersangkutan
        $connected = ClientDatabaseManager::connectToClient($client->database_name);
        if (!$connected) {
            abort(500, 'Gagal terhubung ke database restoran client.');
        }

        // 3. Simpan state sesi client context
        session([
            'guest_client_id' => $client->client_id,
            'guest_outlet_id' => $outletId,
            'guest_table_id' => $tableId,
            'client_database' => $client->database_name,
            'client_id' => $client->client_id,
            'client_name' => $client->client_name,
            'client_code' => $client->client_code,
            'business_name' => $client->business_name,
        ]);

        // 4. Set Default URL parameters untuk semua helper route('guest.*')
        URL::defaults([
            'client_id' => $client->client_id,
            'outlet_id' => $outletId,
            'table_id' => $tableId,
        ]);

        // 5. Ambil data Outlet di database client
        $outlet = Outlet::where('outlet_id', $outletId)
            ->where('delete_status', 0)
            ->first();

        if (!$outlet) {
            $outlet = Outlet::where('delete_status', 0)->first();
        }

        if (!$outlet) {
            abort(404, 'Cabang outlet restoran tidak ditemukan.');
        }

        // 6. Ambil data Meja di database client
        $table = Table::where('table_id', $tableId)
            ->where('delete_status', 0)
            ->first();

        if (!$table || $table->table_status === 'nonaktif') {
            abort(404, 'Meja tidak ditemukan atau sedang nonaktif.');
        }

        return [
            'client' => $client,
            'outlet' => $outlet,
            'table' => $table,
        ];
    }

    /**
     * Resolve nama view guest berdasarkan template yg di-set di SettingOutlet (DB) atau fallback .env.
     * Format: guest.{template}.{view}
     */
    protected function guestView(string $view): string
    {
        $setting = SettingOutlet::where('delete_status', 0)->first();
        $template = $setting?->theme ?? config('app.guest_template', 'spicy_bites');
        return "guest.{$template}.{$view}";
    }

    /**
     * Halaman menu (QR scan meja).
     * URL: /{client_id}/{outlet_id}/{table_id}
     */
    public function index($client_id, $outlet_id, $table_id)
    {
        $ctx = $this->setupGuestContext($client_id, $outlet_id, $table_id);
        $client = $ctx['client'];
        $outlet = $ctx['outlet'];
        $table = $ctx['table'];

        // Ambil produk aktif
        $products = Product::where('delete_status', 0)
            ->where('product_status', 1)
            ->where(function ($q) use ($outlet) {
                $q->where('outlet_id', $outlet->outlet_id)->orWhereNull('outlet_id');
            })
            ->with('category', 'activeDiscount')
            ->get();

        if ($products->isEmpty()) {
            $products = Product::where('delete_status', 0)
                ->where('product_status', 1)
                ->with('category', 'activeDiscount')
                ->get();
        }

        $categories = Category::where('delete_status', 0)
            ->where('category_status', 1)
            ->orderBy('category_name')
            ->get();

        // Bundle aktif (harga paket tetap)
        $bundles = Bundle::where('delete_status', 0)
            ->where('bundle_status', 1)
            ->with('items.product')
            ->latest()
            ->get();

        return view($this->guestView('index'), compact('client', 'table', 'outlet', 'products', 'categories', 'bundles'));
    }

    /**
     * Checkout (POST) — validasi cart dari halaman menu, simpan ke session,
     * lalu redirect ke halaman review (GET) biar bisa di-refresh.
     */
    public function checkout(Request $request, $client_id, $outlet_id, $table_id)
    {
        $ctx = $this->setupGuestContext($client_id, $outlet_id, $table_id);
        $table = $ctx['table'];

        $cart = json_decode($request->cart_data, true);
        $bundles = json_decode($request->bundle_data, true);
        $totalPrice = (float) $request->total_price;

        if ((empty($cart) || !is_array($cart)) && (empty($bundles) || !is_array($bundles))) {
            return redirect()->route('guest.index', [$client_id, $outlet_id, $table_id])
                ->with('error', 'Keranjang masih kosong.');
        }

        // Simpan cart + bundle + total ke session (PRG)
        session([
            'guest_cart' => $cart ?: [],
            'guest_bundles' => $bundles ?: [],
            'guest_total' => $totalPrice,
            'guest_table' => $table->table_id,
        ]);

        return redirect()->route('guest.review', [$client_id, $outlet_id, $table_id]);
    }

    /**
     * Halaman review (GET) — baca cart dari session, render + hitung ulang.
     */
    public function review($client_id, $outlet_id, $table_id)
    {
        $ctx = $this->setupGuestContext($client_id, $outlet_id, $table_id);
        $client = $ctx['client'];
        $outlet = $ctx['outlet'];
        $table = $ctx['table'];

        $cart = session('guest_cart', []);
        $totalPrice = (float) session('guest_total', 0);

        if (empty($cart) || !is_array($cart)) {
            return redirect()->route('guest.index', [$client_id, $outlet_id, $table_id])
                ->with('error', 'Keranjang masih kosong.');
        }

        // Ambil produk + diskon aktif buat re-hitung & validasi
        $productIds = collect($cart)->pluck('product_id')->toArray();
        $products = Product::where('delete_status', 0)
            ->whereIn('product_id', $productIds)
            ->with('activeDiscount')
            ->get()
            ->keyBy('product_id');

        $items = [];
        $grandTotal = 0;
        foreach ($cart as $c) {
            $product = $products->get($c['product_id']);
            if (!$product) continue;

            $qty = max(1, (int) ($c['qty'] ?? 1));
            $price = (float) $product->product_price;

            // Hitung diskon produk dari pivot aktif
            $activeDisc = $product->activeDiscount->first();
            $discountAmount = 0;
            if ($activeDisc) {
                $discType = $activeDisc->discount_type;
                $discValue = (float) ($activeDisc->discount_value ?? 0);
                if ($discType === 'percentage' && $discValue > 0) {
                    $discountAmount = round($price * ($discValue / 100), 2);
                } elseif ($discType === 'nominal' && $discValue > 0) {
                    $discountAmount = min($discValue, $price);
                }
                $discountAmount = min($discountAmount, $price);
            }

            $subtotal = ($price - $discountAmount) * $qty;
            $grandTotal += $subtotal;

            $items[] = [
                'product' => $product,
                'qty' => $qty,
                'note' => $c['note'] ?? null,
                'price' => $price,
                'discount_amount' => $discountAmount,
                'subtotal' => $subtotal,
            ];
        }

        if (empty($items)) {
            return redirect()->route('guest.index', [$client_id, $outlet_id, $table_id])
                ->with('error', 'Produk di keranjang tidak valid.');
        }

        // ——— Bundle dari session ———
        $bundleRows = [];
        $guestBundles = session('guest_bundles', []);
        foreach ($guestBundles as $b) {
            $qty = max(1, (int) ($b['qty'] ?? 1));
            $price = (float) ($b['bundle_price'] ?? 0);
            $bSubtotal = $price * $qty;
            $grandTotal += $bSubtotal;
            $bundleRows[] = [
                'bundle_id' => $b['bundle_id'] ?? null,
                'bundle_name' => $b['bundle_name'] ?? 'Paket',
                'bundle_price' => $price,
                'qty' => $qty,
                'subtotal' => $bSubtotal,
                'items' => $b['items'] ?? [],
            ];
        }

        if (empty($items) && empty($bundleRows)) {
            return redirect()->route('guest.index', [$client_id, $outlet_id, $table_id])
                ->with('error', 'Keranjang masih kosong.');
        }

        // Build JSON item buat JS submit (id, qty, note)
        $itemsJson = array_map(function ($i) {
            return [
                'product_id' => $i['product']->product_id,
                'qty' => $i['qty'],
                'note' => $i['note'] ?? '',
            ];
        }, $items);

        $setting = SettingOutlet::where('delete_status', 0)->first();
        $paymentTiming = $setting?->payment_timing ?? 'post_payment';

        return view($this->guestView('review'), compact('client', 'table', 'outlet', 'items', 'bundleRows', 'itemsJson', 'grandTotal', 'totalPrice', 'paymentTiming', 'setting'));
    }

    /**
     * Simpan pesanan guest → order_status = 'pending'.
     */
    public function submit(Request $request, $client_id, $outlet_id, $table_id)
    {
        $ctx = $this->setupGuestContext($client_id, $outlet_id, $table_id);
        $client = $ctx['client'];
        $outlet = $ctx['outlet'];
        $table = $ctx['table'];

        if (is_string($request->items)) {
            $request->merge(['items' => json_decode($request->items, true) ?: []]);
        }
        if (is_string($request->bundles)) {
            $request->merge(['bundles' => json_decode($request->bundles, true) ?: []]);
        }

        $validated = $request->validate([
            'table_id' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.product_id' => 'required',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.note' => 'nullable|string|max:500',
            'bundles' => 'nullable|array',
            'bundles.*.bundle_id' => 'required',
            'bundles.*.bundle_name' => 'nullable|string',
            'bundles.*.bundle_price' => 'nullable|numeric|min:0',
            'bundles.*.qty' => 'required|integer|min:1',
            'order_remark' => 'nullable|string|max:500',
            'voucher_code' => 'nullable|string|max:50',
            'total_price' => 'required|numeric|min:0',
        ]);

        if (empty($validated['items'] ?? []) && empty($validated['bundles'] ?? [])) {
            return back()->withInput()->with('error', 'Minimal satu item produk atau paket.');
        }

        $companyId = $outlet->outlet_id;

        // Customer per meja
        $customer = Customer::where('outlet_id', $companyId)
            ->where('customer_name', 'Meja ' . $table->table_number)
            ->latest()
            ->first();

        if (!$customer) {
            $customer = Customer::create([
                'outlet_id' => $companyId,
                'customer_name' => 'Meja ' . $table->table_number,
                'customer_notes' => 'Dibuat otomatis dari guest ordering meja ' . $table->table_number,
                'created_by' => 'guest',
            ]);
        }

        $grandTotal = 0;
        $itemDetails = [];
        foreach ($validated['items'] ?? [] as $item) {
            $product = Product::with('activeDiscount')->find($item['product_id']);
            if (!$product) {
                return back()->withInput()->with('error', 'Produk tidak ditemukan.');
            }

            $price = (float) $product->product_price;
            $qty = (int) $item['qty'];

            $activeDisc = $product->activeDiscount->first();
            $discountAmount = 0;
            if ($activeDisc) {
                $discType = $activeDisc->discount_type;
                $discValue = (float) ($activeDisc->discount_value ?? 0);
                if ($discType === 'percentage' && $discValue > 0) {
                    $discountAmount = round($price * ($discValue / 100), 2);
                } elseif ($discType === 'nominal' && $discValue > 0) {
                    $discountAmount = min($discValue, $price);
                }
                $discountAmount = min($discountAmount, $price);
            }

            $subtotal = ($price - $discountAmount) * $qty;
            $grandTotal += $subtotal;

            $itemDetails[] = [
                'product_id' => $product->product_id,
                'qty' => $qty,
                'note' => $item['note'] ?? null,
                'price' => $price,
                'discount_amount' => $discountAmount,
                'subtotal' => $subtotal,
            ];
        }

        $bundleDetails = [];
        foreach ($validated['bundles'] ?? [] as $b) {
            $bundleModel = Bundle::where('delete_status', 0)->find($b['bundle_id']);
            $bPrice = $bundleModel ? (float) $bundleModel->bundle_price : (float) ($b['bundle_price'] ?? 0);
            $bName = $bundleModel ? $bundleModel->bundle_name : ($b['bundle_name'] ?? 'Paket');
            $bQty = (int) ($b['qty'] ?? 1);
            $bSubtotal = $bPrice * $bQty;
            $grandTotal += $bSubtotal;
            $bundleDetails[] = [
                'bundle_id' => $b['bundle_id'],
                'bundle_name' => $bName,
                'bundle_price' => $bPrice,
                'qty' => $bQty,
                'subtotal' => $bSubtotal,
            ];
        }

        // Voucher
        $voucherAmount = 0;
        $voucherData = null;
        $voucherCode = $validated['voucher_code'] ?? null;

        if ($voucherCode) {
            $voucher = Voucher::active()->byCode($voucherCode)->first();
            if (!$voucher) {
                return back()->withInput()->with('error', 'Kode voucher tidak valid atau sudah kedaluwarsa.');
            }

            $minPurchase = (float) ($voucher->voucher_min_purchase ?? 0);
            if ($grandTotal < $minPurchase) {
                return back()->withInput()->with('error', 'Minimal belanja Rp ' . number_format($minPurchase, 0) . ' untuk menggunakan voucher ini.');
            }

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
                'created_by' => 'guest',
            ];
        }

        $finalGrandTotal = $grandTotal - $voucherAmount;

        DB::transaction(function () use ($validated, $companyId, $customer, $table, $finalGrandTotal, $itemDetails, $bundleDetails, $voucherData) {
            $order = Order::create([
                'outlet_id' => $companyId,
                'order_type' => 'dine_in',
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'order_grand_total' => $finalGrandTotal,
                'order_remark' => $validated['order_remark'] ?? null,
                'order_table_id' => $table->table_id,
                'order_customer_id' => $customer->customer_id,
                'created_by' => 'guest',
            ]);

            $syncData = [];
            foreach ($itemDetails as $item) {
                $syncData[$item['product_id']] = [
                    'outlet_id' => $companyId,
                    'quantity' => $item['qty'],
                    'note' => $item['note'] ?? null,
                    'delete_status' => 0,
                    'created_by' => 'guest',
                ];
            }
            $order->products()->sync($syncData);

            foreach ($bundleDetails as $bd) {
                OrderBundle::create([
                    'outlet_id' => $companyId,
                    'order_id' => $order->order_id,
                    'bundle_id' => $bd['bundle_id'],
                    'bundle_name' => $bd['bundle_name'],
                    'bundle_price' => $bd['bundle_price'],
                    'quantity' => $bd['qty'],
                    'subtotal' => (float) $bd['bundle_price'] * $bd['qty'],
                    'created_by' => 'guest',
                ]);
            }

            if ($voucherData) {
                $voucherData['order_id'] = $order->order_id;
                OrderVoucher::create($voucherData);
            }
        });

        // Hapus cart dari session
        session()->forget(['guest_cart', 'guest_bundles', 'guest_total', 'guest_table']);

        return redirect()->route('guest.status', [$client_id, $outlet_id, $table_id])
            ->with('success', 'Pesanan berhasil dikirim!');
    }

    /**
     * Tracking status pesanan per meja.
     */
    public function status($client_id, $outlet_id, $table_id)
    {
        $ctx = $this->setupGuestContext($client_id, $outlet_id, $table_id);
        $client = $ctx['client'];
        $outlet = $ctx['outlet'];
        $table = $ctx['table'];

        $orders = Order::where('delete_status', 0)
            ->where('order_table_id', $table->table_id)
            ->with('products', 'vouchers')
            ->orderBy('created_at', 'asc')
            ->get();

        $setting = SettingOutlet::where('delete_status', 0)->first();
        $paymentTiming = $setting?->payment_timing ?? 'post_payment';

        return view($this->guestView('status'), compact('client', 'table', 'outlet', 'orders', 'paymentTiming', 'setting'));
    }

    /**
     * Cek voucher via AJAX (versi guest).
     */
    public function checkVoucher(Request $request, $client_id, $outlet_id, $table_id): JsonResponse
    {
        $this->setupGuestContext($client_id, $outlet_id, $table_id);

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
            'voucher_amount' => $voucherAmount,
        ]);
    }

    /**
     * Fallback legacy route redirect.
     */
    public function legacyIndex($table_id)
    {
        $clientId = session('client_id') ?? session('tenant_client_id');
        $outletId = session('outlet_id') ?? session('guest_outlet_id');

        if ($clientId && $outletId) {
            return redirect()->route('guest.index', [$clientId, $outletId, $table_id]);
        }

        abort(404, 'Format URL QR lama. Silakan scan ulang QR Code terbaru pada meja.');
    }
}
