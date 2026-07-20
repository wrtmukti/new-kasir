<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Admin\Category;
use App\Models\Admin\Product;
use App\Models\Admin\Stock;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('delete_status', 0)
            ->with('company', 'category', 'stocks')
            ->latest()
            ->paginate(10);
        return view('admin.product.index', compact('products'));
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
                    ? view('admin.product._card', compact('products'))->render()
                    : view('admin.product._data', compact('products'))->render(),
                'pagination' => $products->links('vendor.pagination.modern')->toHtml(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]);
        }

        return view('admin.product.index', compact('products'));
    }

    public function create()
    {
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        $categories = Category::where('delete_status', 0)->where('category_status', 1)->get();
        $stocks = Stock::where('delete_status', 0)->where('stock_status', 1)
            ->orderBy('stock_name')
            ->get(['stock_id', 'stock_name', 'stock_code', 'stock_unit', 'stock_price', 'stock_amount']);
        return view('admin.product.create', compact('companies', 'categories', 'stocks'));
    }

    public function store(ProductRequest $request)
    {
        $validated = $request->validated();

        $validated['product_slug'] = str()->slug($validated['product_name']);
        $validated['product_status'] = $validated['product_status'] ?? 1;

        if ($request->hasFile('product_image')) {
            $validated['product_image'] = $request->file('product_image')
                ->store('products', 'public');
        }

        DB::transaction(function () use ($validated, $request) {
            $product = Product::create($validated);

            // Sync ingredients (stock pivot)
            $stockIds = $request->input('stock_ids', []);
            $quantities = $request->input('quantities', []);

            // stock_ids comes as JSON string from hidden input — decode if needed
            if (is_string($stockIds)) {
                $stockIds = json_decode($stockIds, true) ?? [];
            }

            $syncData = [];

            if (is_array($stockIds)) {
                foreach ($stockIds as $index => $stockId) {
                    $qty = $quantities[$index] ?? 0;
                    if ((int)$qty > 0) {
                        $syncData[$stockId] = [
                            'quantity' => (int)$qty,
                            'delete_status' => 0,
                            'created_by' => $validated['created_by'] ?? null,
                        ];
                    }
                }
            }

            if (!empty($syncData)) {
                $product->stocks()->sync($syncData);
            }
        });

        return redirect()->route('admin.product.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load('company', 'category', 'stocks');
        return view('admin.product.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        $categories = Category::where('delete_status', 0)->where('category_status', 1)->get();
        $stocks = Stock::where('delete_status', 0)->where('stock_status', 1)
            ->orderBy('stock_name')
            ->get(['stock_id', 'stock_name', 'stock_code', 'stock_unit', 'stock_price', 'stock_amount']);
        $product->load('stocks');
        return view('admin.product.edit', compact('product', 'companies', 'categories', 'stocks'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        $validated['product_slug'] = str()->slug($validated['product_name']);

        if ($request->hasFile('product_image')) {
            if ($product->product_image) {
                Storage::disk('public')->delete($product->product_image);
            }
            $validated['product_image'] = $request->file('product_image')
                ->store('products', 'public');
        } else {
            unset($validated['product_image']);
        }

        DB::transaction(function () use ($validated, $request, $product) {
            $product->update($validated);

            // Sync ingredients (stock pivot)
            $stockIds = $request->input('stock_ids', []);
            $quantities = $request->input('quantities', []);

            // stock_ids comes as JSON string from hidden input — decode if needed
            if (is_string($stockIds)) {
                $stockIds = json_decode($stockIds, true) ?? [];
            }

            $syncData = [];

            if (is_array($stockIds)) {
                foreach ($stockIds as $index => $stockId) {
                    $qty = (int)($quantities[$index] ?? 0);
                    if ($qty > 0) {
                        $syncData[$stockId] = [
                            'quantity' => $qty,
                            'delete_status' => 0,
                            'updated_by' => $validated['updated_by'] ?? null,
                        ];
                    }
                }
            }

            $product->stocks()->sync($syncData);
        });

        return redirect()->route('admin.product.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->product_image) {
            Storage::disk('public')->delete($product->product_image);
        }

        $product->update(['delete_status' => 1]);

        if (request()->ajax()) {
            return response()->json(['success' => 'Produk berhasil dihapus.']);
        }

        return redirect()->route('admin.product.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
