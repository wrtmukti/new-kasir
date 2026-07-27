<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DiscountRequest;
use App\Models\Admin\Discount;
use App\Models\Admin\Product;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::where('delete_status', 0)
            ->with('company')
            ->latest()
            ->paginate(10);
        return view('admin.discount.index', compact('discounts'));
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $discounts = Discount::where('delete_status', 0)
            ->with('company')
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
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        return view('admin.discount.create', compact('companies'));
    }

    public function store(DiscountRequest $request)
    {
        $validated = $request->validated();
        $validated['discount_status'] = $validated['discount_status'] ?? 1;

        Discount::create($validated);

        return redirect()->route('admin.discount.index')
            ->with('success', 'Diskon berhasil ditambahkan.');
    }

    public function show(Discount $discount)
    {
        $discount->load('company', 'products');
        $products = Product::where('delete_status', 0)
            ->where('product_status', 1)
            ->with('category')
            ->orderBy('product_name')
            ->get();

        return view('admin.discount.show', compact('discount', 'products'));
    }

    public function edit(Discount $discount)
    {
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        return view('admin.discount.edit', compact('discount', 'companies'));
    }

    public function update(DiscountRequest $request, Discount $discount)
    {
        $validated = $request->validated();
        $discount->update($validated);

        return redirect()->route('admin.discount.index')
            ->with('success', 'Diskon berhasil diperbarui.');
    }

    public function destroy(Discount $discount)
    {
        $discount->update(['delete_status' => 1]);

        if (request()->ajax()) {
            return response()->json(['success' => 'Diskon berhasil dihapus.']);
        }

        return redirect()->route('admin.discount.index')
            ->with('success', 'Diskon berhasil dihapus.');
    }

    /**
     * Attach discount ke product + log ke product_histories
     */
    public function attachProduct(Request $request, Discount $discount)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $oldDiscountId = $product->product_discount_id;

        DB::transaction(function () use ($product, $discount, $oldDiscountId, $request) {
            // Update product
            $product->update([
                'product_discount_id' => $discount->discount_id,
                'product_discount_type' => $discount->discount_type,
                'product_discount_value' => $discount->discount_value,
            ]);

            // Log ke product_histories
            DB::table('product_histories')->insert([
                'product_id' => $product->product_id,
                'company_id' => $product->company_id,
                'history_code' => $product->product_code,
                'history_name' => $product->product_name,
                'history_slug' => $product->product_slug,
                'history_description' => $product->product_description,
                'history_price' => $product->product_price,
                'history_discount' => $discount->discount_value,
                'history_status' => $product->product_status,
                'history_image' => $product->product_image,
                'effective_date' => now(),
                'action_type' => $oldDiscountId ? 'update' : 'create',
                'changed_by' => $request->input('created_by'),
                'created_by' => $request->input('created_by'),
                'delete_status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return response()->json(['success' => 'Diskon berhasil dihubungkan ke produk.']);
    }

    /**
     * Detach diskon dari product + log ke product_histories
     */
    public function detachProduct(Request $request, Discount $discount)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
        ]);

        $product = Product::findOrFail($request->product_id);

        DB::transaction(function () use ($product, $discount, $request) {
            // Simpan data lama sebelum diubah
            $oldDiscountValue = $product->product_discount_value;

            // Hapus diskon dari product
            $product->update([
                'product_discount_id' => null,
                'product_discount_type' => null,
                'product_discount_value' => null,
            ]);

            // Log ke product_histories — set history_discount = 0 (dihapus)
            DB::table('product_histories')->insert([
                'product_id' => $product->product_id,
                'company_id' => $product->company_id,
                'history_code' => $product->product_code,
                'history_name' => $product->product_name,
                'history_slug' => $product->product_slug,
                'history_description' => $product->product_description,
                'history_price' => $product->product_price,
                'history_discount' => $oldDiscountValue,
                'history_status' => $product->product_status,
                'history_image' => $product->product_image,
                'effective_date' => now(),
                'action_type' => 'delete',
                'changed_by' => $request->input('created_by'),
                'created_by' => $request->input('created_by'),
                'delete_status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return response()->json(['success' => 'Diskon berhasil dilepas dari produk.']);
    }
}
