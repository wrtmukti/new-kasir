<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Admin\Category;
use App\Models\Admin\Product;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('delete_status', 0)
            ->with('company', 'category')
            ->latest()
            ->paginate(10);
        return view('admin.product.index', compact('products'));
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $categoryId = $request->input('category_id');

        $query = Product::where('delete_status', 0)
            ->with('company', 'category');

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
        return view('admin.product.create', compact('companies', 'categories'));
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

        Product::create($validated);

        return redirect()->route('admin.product.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load('company', 'category');
        return view('admin.product.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        $categories = Category::where('delete_status', 0)->where('category_status', 1)->get();
        return view('admin.product.edit', compact('product', 'companies', 'categories'));
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

        $product->update($validated);

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
