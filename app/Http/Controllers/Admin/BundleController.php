<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BundleRequest;
use App\Models\Admin\Bundle;
use App\Models\Admin\Category;
use App\Models\Admin\Product;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BundleController extends Controller
{
    public function index()
    {
        $bundles = Bundle::where('delete_status', 0)
            ->with('company', 'items.product')
            ->latest()
            ->paginate(10);
        return view('admin.bundle.index', compact('bundles'));
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $view = $request->input('view', 'list');

        $query = Bundle::where('delete_status', 0)
            ->with('company', 'items.product');

        $bundles = $query->latest()->paginate($perPage);

        if ($request->ajax()) {
            $partial = $view === 'card' ? 'admin.bundle._card' : 'admin.bundle._data';
            return response()->json([
                'html' => view($partial, compact('bundles'))->render(),
                'pagination' => $bundles->links('vendor.pagination.modern')->toHtml(),
                'total' => $bundles->total(),
                'from' => $bundles->firstItem(),
                'to' => $bundles->lastItem(),
            ]);
        }

        return view('admin.bundle.index', compact('bundles'));
    }

    public function productData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $categoryId = $request->input('category_id');
        $search = $request->input('search');
        $view = $request->input('view', 'card');

        $query = Product::where('delete_status', 0)->where('product_status', 1)
            ->with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', '%'.$search.'%')
                  ->orWhere('product_code', 'like', '%'.$search.'%');
            });
        }

        $products = $query->orderBy('product_name')->paginate($perPage);

        if ($request->ajax()) {
            $partial = 'admin.bundle._product_card';
            return response()->json([
                'html' => view($partial, compact('products'))->render(),
                'pagination' => $products->links('vendor.pagination.modern')->toHtml(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]);
        }

        return $products;
    }

    public function create()
    {
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        $categories = Category::where('delete_status', 0)->where('category_status', 1)->get();
        $products = Product::where('delete_status', 0)->where('product_status', 1)
            ->with('category')
            ->orderBy('product_name')
            ->get(['product_id', 'product_name', 'product_code', 'product_price', 'category_id']);
        return view('admin.bundle.create', compact('companies', 'categories', 'products'));
    }

    public function store(BundleRequest $request)
    {
        $validated = $request->validated();

        $validated['bundle_slug'] = str()->slug($validated['bundle_name']);
        $validated['bundle_status'] = $validated['bundle_status'] ?? 1;

        if ($request->hasFile('bundle_image')) {
            $validated['bundle_image'] = $request->file('bundle_image')
                ->store('bundles', 'public');
        }

        DB::transaction(function () use ($validated, $request) {
            $bundle = Bundle::create($validated);

            $productIds = $request->input('product_ids', []);
            $quantities = $request->input('quantities', []);

            if (is_string($productIds)) {
                $productIds = json_decode($productIds, true) ?? [];
            }

            $syncData = [];

            if (is_array($productIds)) {
                foreach ($productIds as $index => $productId) {
                    $qty = (int)($quantities[$index] ?? 1);
                    $product = Product::find($productId);
                    $priceSnapshot = $product ? $product->product_price : 0;

                    $syncData[$productId] = [
                        'quantity' => $qty,
                        'price_snapshot' => $priceSnapshot,
                        'delete_status' => 0,
                        'created_by' => $validated['created_by'] ?? null,
                    ];
                }
            }

            if (!empty($syncData)) {
                $bundle->products()->sync($syncData);
            }
        });

        return redirect()->route('admin.bundle.index')
            ->with('success', 'Paket berhasil ditambahkan.');
    }

    public function show(Bundle $bundle)
    {
        $bundle->load('company', 'items.product');
        return view('admin.bundle.show', compact('bundle'));
    }

    public function edit(Bundle $bundle)
    {
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        $categories = Category::where('delete_status', 0)->where('category_status', 1)->get();
        $products = Product::where('delete_status', 0)->where('product_status', 1)
            ->with('category')
            ->orderBy('product_name')
            ->get(['product_id', 'product_name', 'product_code', 'product_price', 'category_id']);
        $bundle->load('items.product');
        return view('admin.bundle.edit', compact('bundle', 'companies', 'categories', 'products'));
    }

    public function update(BundleRequest $request, Bundle $bundle)
    {
        $validated = $request->validated();

        $validated['bundle_slug'] = str()->slug($validated['bundle_name']);

        if ($request->hasFile('bundle_image')) {
            if ($bundle->bundle_image) {
                Storage::disk('public')->delete($bundle->bundle_image);
            }
            $validated['bundle_image'] = $request->file('bundle_image')
                ->store('bundles', 'public');
        } else {
            unset($validated['bundle_image']);
        }

        DB::transaction(function () use ($validated, $request, $bundle) {
            $bundle->update($validated);

            $productIds = $request->input('product_ids', []);
            $quantities = $request->input('quantities', []);

            if (is_string($productIds)) {
                $productIds = json_decode($productIds, true) ?? [];
            }

            $syncData = [];

            if (is_array($productIds)) {
                foreach ($productIds as $index => $productId) {
                    $qty = (int)($quantities[$index] ?? 1);
                    $product = Product::find($productId);
                    $priceSnapshot = $product ? $product->product_price : 0;

                    $syncData[$productId] = [
                        'quantity' => $qty,
                        'price_snapshot' => $priceSnapshot,
                        'delete_status' => 0,
                        'updated_by' => $validated['updated_by'] ?? null,
                    ];
                }
            }

            $bundle->products()->sync($syncData);
        });

        return redirect()->route('admin.bundle.index')
            ->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(Bundle $bundle)
    {
        if ($bundle->bundle_image) {
            Storage::disk('public')->delete($bundle->bundle_image);
        }

        $bundle->update(['delete_status' => 1]);

        if (request()->ajax()) {
            return response()->json(['success' => 'Paket berhasil dihapus.']);
        }

        return redirect()->route('admin.bundle.index')
            ->with('success', 'Paket berhasil dihapus.');
    }
}
