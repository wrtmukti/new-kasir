<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DiscountRequest;
use App\Models\Admin\Discount;
use App\Models\Admin\Product;
use App\Models\Admin\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::where('delete_status', 0)
            ->with('outlet')
            ->latest()
            ->paginate(10);
        return view('admin.discount.index', compact('discounts'));
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $discounts = Discount::where('delete_status', 0)
            ->with('outlet')
            ->latest()
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.discount._data', compact('discounts'))->render(),
                'pagination' => $discounts->links('vendor.pagination.modern')->toHtml(),
                'total' => $discounts->total(),
                'from' => $discounts->firstItem(),
                'to' => $discounts->lastItem(),
            ]);
        }

        return view('admin.discount.index', compact('discounts'));
    }

    public function create()
    {
        $outlets = Outlet::where('delete_status', 0)->where('outlet_status', 1)->get();
        return view('admin.discount.create', compact('outlets'));
    }

    public function store(DiscountRequest $request)
    {
        $validated = $request->validated();
        $validated['discount_status'] = $validated['discount_status'] ?? 1;

        $discount = Discount::create($validated);

        // Log ke discount_histories
        DB::table('discount_histories')->insert([
            'discount_id' => $discount->discount_id,
            'outlet_id' => $discount->outlet_id,
            'discount_name' => $discount->discount_name,
            'discount_type' => $discount->discount_type,
            'discount_value' => $discount->discount_value,
            'discount_max_amount' => $discount->discount_max_amount,
            'start_date' => $discount->start_date ?? now(),
            'end_date' => $discount->end_date,
            'reason' => 'create',
            'changed_by' => $validated['created_by'] ?? 'admin',
            'created_by' => $validated['created_by']  ?? 'admin',
            'delete_status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.discount.index')
            ->with('success', 'Diskon berhasil ditambahkan.');
    }

    public function show(Discount $discount)
    {
        $discount->load('outlet', 'activeProducts');
        $products = Product::where('delete_status', 0)
            ->where('product_status', 1)
            ->with('category')
            ->orderBy('product_name')
            ->get();

        return view('admin.discount.show', compact('discount', 'products'));
    }

    public function edit(Discount $discount)
    {
        $outlets = Outlet::where('delete_status', 0)->where('outlet_status', 1)->get();
        return view('admin.discount.edit', compact('discount', 'outlets'));
    }

    public function update(DiscountRequest $request, Discount $discount)
    {
        $validated = $request->validated();
        $discount->update($validated);

        // Log ke discount_histories
        DB::table('discount_histories')->insert([
            'discount_id' => $discount->discount_id,
            'outlet_id' => $discount->outlet_id,
            'discount_name' => $discount->discount_name,
            'discount_type' => $discount->discount_type,
            'discount_value' => $discount->discount_value,
            'discount_max_amount' => $discount->discount_max_amount,
            'start_date' => $discount->start_date ?? now(),
            'end_date' => $discount->end_date,
            'reason' => 'update',
            'changed_by' => $validated['updated_by'] ?? 'admin',
            'created_by' => $validated['updated_by']  ?? 'admin',
            'delete_status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.discount.index')
            ->with('success', 'Diskon berhasil diperbarui.');
    }

    public function destroy(Discount $discount)
    {
        // Log ke discount_histories dulu sebelum soft delete
        DB::table('discount_histories')->insert([
            'discount_id' => $discount->discount_id,
            'outlet_id' => $discount->outlet_id,
            'discount_name' => $discount->discount_name,
            'discount_type' => $discount->discount_type,
            'discount_value' => $discount->discount_value,
            'discount_max_amount' => $discount->discount_max_amount,
            'start_date' => $discount->start_date ?? now(),
            'end_date' => $discount->end_date,
            'reason' => 'delete',
            'changed_by' => 'admin',
            'created_by' => 'admin',
            'delete_status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $discount->update(['delete_status' => 1]);

        if (request()->ajax()) {
            return response()->json(['success' => 'Diskon berhasil dihapus.']);
        }

        return redirect()->route('admin.discount.index')
            ->with('success', 'Diskon berhasil dihapus.');
    }

    /**
     * Attach discount ke product + log ke product_histories
     * Nulis ke pivot discount_product, bukan ke kolom products
     */
    public function attachProduct(Request $request, Discount $discount)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
        ]);

        $product = Product::findOrFail($request->product_id);

        DB::transaction(function () use ($product, $discount, $request) {
            $companyId = $product->outlet_id;
            $userId = $request->input('created_by', 'admin');

            // 1. Matikan pivot aktif kalo ada
            DB::table('discount_product')
                ->where('product_id', $product->product_id)
                ->whereNull('end_date')
                ->where('delete_status', 0)
                ->update([
                    'end_date' => now(),
                    'updated_by' => $userId,
                    'updated_at' => now(),
                ]);

            // 2. Insert pivot baru
            DB::table('discount_product')->insert([
                'outlet_id' => $companyId,
                'product_id' => $product->product_id,
                'discount_id' => $discount->discount_id,
                'start_date' => now(),
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Log ke product_histories — rekam perubahan
            DB::table('product_histories')->insert([
                'product_id' => $product->product_id,
                'outlet_id' => $companyId,
                'history_code' => $product->product_code,
                'history_name' => $product->product_name,
                'history_slug' => $product->product_slug,
                'history_description' => $product->product_description,
                'history_price' => $product->product_price,
                'history_discount' => $discount->discount_value,
                'history_status' => $product->product_status,
                'history_image' => $product->product_image,
                'effective_date' => now(),
                'action_type' => 'attach_discount',
                'changed_by' => $userId,
                'created_by' => $userId,
                'delete_status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return response()->json(['success' => 'Diskon berhasil dihubungkan ke produk.']);
    }

    /**
     * Detach diskon dari product + log ke product_histories
     * Soft-delete pivot discount_product (end_date + delete_status)
     */
    public function detachProduct(Request $request, Discount $discount)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
        ]);

        $product = Product::findOrFail($request->product_id);

        DB::transaction(function () use ($product, $discount, $request) {
            $userId = $request->input('created_by', 'admin');

            // Matikan pivot aktif
            DB::table('discount_product')
                ->where('product_id', $product->product_id)
                ->where('discount_id', $discount->discount_id)
                ->whereNull('end_date')
                ->where('delete_status', 0)
                ->update([
                    'end_date' => now(),
                    'delete_status' => 1,
                    'updated_by' => $userId,
                    'updated_at' => now(),
                ]);

            // Log ke product_histories
            DB::table('product_histories')->insert([
                'product_id' => $product->product_id,
                'outlet_id' => $product->outlet_id,
                'history_code' => $product->product_code,
                'history_name' => $product->product_name,
                'history_slug' => $product->product_slug,
                'history_description' => $product->product_description,
                'history_price' => $product->product_price,
                'history_discount' => $discount->discount_value,
                'history_status' => $product->product_status,
                'history_image' => $product->product_image,
                'effective_date' => now(),
                'action_type' => 'detach_discount',
                'changed_by' => $userId,
                'created_by' => $userId,
                'delete_status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return response()->json(['success' => 'Diskon berhasil dilepas dari produk.']);
    }
}
