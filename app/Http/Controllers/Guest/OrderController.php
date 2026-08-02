<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Admin\Category;
use App\Models\Admin\Customer;
use App\Models\Admin\Order;
use App\Models\Admin\OrderVoucher;
use App\Models\Admin\Product;
use App\Models\Admin\Table;
use App\Models\Admin\Voucher;
use App\Models\SysAdmin\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Halaman menu (QR scan meja).
     * Menampilkan produk aktif + kategori + diskon aktif + info meja.
     */
    public function index($table_id)
    {
        $table = Table::where('table_id', $table_id)
            ->where('delete_status', 0)
            ->first();

        // Meja gak ditemukan / nonaktif → lempar balik
        if (!$table || $table->table_status === 'nonaktif') {
            abort(404);
        }

        $company = Company::where('delete_status', 0)->first();

        $products = Product::where('delete_status', 0)
            ->where('product_status', 1)
            ->with('category', 'activeDiscount')
            ->get();

        $categories = Category::where('delete_status', 0)
            ->where('category_status', 1)
            ->whereHas('products', function ($q) {
                $q->where('delete_status', 0)->where('product_status', 1);
            })
            ->orderBy('category_name')
            ->get();

        return view('guest.index', compact('table', 'company', 'products', 'categories'));
    }

    /**
     * Checkout (POST) — validasi cart dari halaman menu, simpan ke session,
     * lalu redirect ke halaman review (GET) biar bisa di-refresh.
     */
    public function checkout(Request $request)
    {
        $cart = json_decode($request->cart_data, true);
        $totalPrice = (float) $request->total_price;

        $table = Table::where('table_id', $request->table_id)
            ->where('delete_status', 0)
            ->first();

        if (!$table) {
            return redirect()->route('guest.index', $request->table_id)
                ->with('error', 'Meja tidak ditemukan.');
        }

        if (empty($cart) || !is_array($cart)) {
            return redirect()->route('guest.index', $request->table_id)
                ->with('error', 'Keranjang masih kosong.');
        }

        // Simpan cart + total ke session (PRG)
        session([
            'guest_cart' => $cart,
            'guest_total' => $totalPrice,
            'guest_table' => $table->table_id,
        ]);

        return redirect()->route('guest.review', $table->table_id);
    }

    /**
     * Halaman review (GET) — baca cart dari session, render + hitung ulang.
     */
    public function review($table_id)
    {
        $table = Table::where('table_id', $table_id)
            ->where('delete_status', 0)
            ->first();

        $cart = session('guest_cart', []);
        $totalPrice = (float) session('guest_total', 0);

        if (!$table) {
            return redirect()->route('guest.index', $table_id)
                ->with('error', 'Meja tidak ditemukan.');
        }

        if (empty($cart) || !is_array($cart)) {
            return redirect()->route('guest.index', $table_id)
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

            // Hitung diskon produk dari pivot aktif (sama kayak admin)
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
            return redirect()->route('guest.index', $table_id)
                ->with('error', 'Produk di keranjang tidak valid.');
        }

        // Build JSON item buat JS submit (id, qty, note)
        $itemsJson = array_map(function ($i) {
            return [
                'product_id' => $i['product']->product_id,
                'qty' => $i['qty'],
                'note' => $i['note'] ?? '',
            ];
        }, $items);

        $company = Company::where('delete_status', 0)->first();

        return view('guest.review', compact('table', 'company', 'items', 'itemsJson', 'grandTotal', 'totalPrice'));
    }

    /**
     * Simpan pesanan guest → order_status = 'pending'.
     * Belum decrement stock — nunggu kasir "terima".
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.note' => 'nullable|string|max:500',
            'order_remark' => 'nullable|string|max:500',
            'voucher_code' => 'nullable|string|max:50',
            'total_price' => 'required|numeric|min:0',
        ]);

        $table = Table::where('table_id', $validated['table_id'])
            ->where('delete_status', 0)
            ->first();

        if (!$table) {
            return back()->withInput()->with('error', 'Meja tidak ditemukan.');
        }

        $companyId = Company::where('delete_status', 0)->value('company_id');

        // ——— Customer per meja: ambil yang lagi ada order aktif, atau bikin baru ———
        $customer = Customer::where('company_id', $companyId)
            ->where('customer_name', 'Meja ' . $table->table_number)
            ->latest()
            ->first();

        if (!$customer) {
            $customer = Customer::create([
                'company_id' => $companyId,
                'customer_name' => 'Meja ' . $table->table_number,
                'customer_notes' => 'Dibuat otomatis dari guest ordering meja ' . $table->table_number,
                'created_by' => 'guest',
            ]);
        }

        // ——— Hitung grand total (include diskon produk) ———
        $grandTotal = 0;
        $itemDetails = [];
        foreach ($validated['items'] as $item) {
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

        // ——— Proses voucher ———
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
                'company_id' => $companyId,
                'voucher_code' => $voucher->voucher_code,
                'voucher_type' => $voucher->voucher_type,
                'voucher_value' => (float) $voucher->voucher_value,
                'voucher_max_discount' => (float) ($voucher->voucher_max_discount ?? 0),
                'voucher_amount' => $voucherAmount,
                'created_by' => 'guest',
            ];
        }

        $finalGrandTotal = $grandTotal - $voucherAmount;

        DB::transaction(function () use ($validated, $companyId, $customer, $table, $finalGrandTotal, $itemDetails, $voucherData) {
            $order = Order::create([
                'company_id' => $companyId,
                'order_type' => 'dine_in',
                'order_status' => 'pending',
                'order_grand_total' => $finalGrandTotal,
                'order_remark' => $validated['order_remark'] ?? null,
                'order_table_id' => $table->table_id,
                'order_customer_id' => $customer->customer_id,
                'created_by' => 'guest',
            ]);

            // Attach produk ke order_product
            $syncData = [];
            foreach ($itemDetails as $item) {
                $syncData[$item['product_id']] = [
                    'company_id' => $companyId,
                    'quantity' => $item['qty'],
                    'note' => $item['note'] ?? null,
                    'delete_status' => 0,
                    'created_by' => 'guest',
                ];
            }
            $order->products()->sync($syncData);

            // Simpan voucher kalo ada
            if ($voucherData) {
                $voucherData['order_id'] = $order->order_id;
                OrderVoucher::create($voucherData);
            }
        });

        // Hapus cart dari session (udah jadi order)
        session()->forget(['guest_cart', 'guest_total', 'guest_table']);

        return redirect()->route('guest.status', $table->table_id)
            ->with('success', 'Pesanan berhasil dikirim!');
    }

    /**
     * Tracking status pesanan per meja.
     */
    public function status($table_id)
    {
        $table = Table::where('table_id', $table_id)
            ->where('delete_status', 0)
            ->first();

        if (!$table) {
            abort(404);
        }

        $orders = Order::where('delete_status', 0)
            ->where('order_table_id', $table->table_id)
            ->with('products', 'vouchers')
            ->orderBy('created_at', 'asc')
            ->get();

        $company = Company::where('delete_status', 0)->first();

        return view('guest.status', compact('table', 'company', 'orders'));
    }

    /**
     * Cek voucher via AJAX (versi guest).
     */
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
}
