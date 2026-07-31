<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StockRequest;
use App\Models\Admin\Stock;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::where('delete_status', 0)
            ->with('company')
            ->latest()
            ->paginate(10);
        return view('admin.stock.index', compact('stocks'));
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $stocks = Stock::where('delete_status', 0)
            ->with('company')
            ->latest()
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.stock._data', compact('stocks'))->render(),
                'pagination' => $stocks->links('vendor.pagination.modern')->toHtml(),
                'total' => $stocks->total(),
                'from' => $stocks->firstItem(),
                'to' => $stocks->lastItem(),
            ]);
        }

        return view('admin.stock.index', compact('stocks'));
    }

    public function jsonList(Request $request)
    {
        $search = $request->input('search');
        $stocks = Stock::where('delete_status', 0)
            ->where('stock_status', 1)
            ->when($search, function ($q) use ($search) {
                $q->where('stock_name', 'like', "%{$search}%")
                  ->orWhere('stock_code', 'like', "%{$search}%");
            })
            ->orderBy('stock_name')
            ->get(['stock_id', 'stock_name', 'stock_code', 'stock_unit', 'stock_price']);

        return response()->json($stocks);
    }

    public function create()
    {
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        return view('admin.stock.create', compact('companies'));
    }

    public function store(StockRequest $request)
    {
        $validated = $request->validated();

        $validated['stock_slug'] = str()->slug($validated['stock_name']);
        $validated['stock_status'] = $validated['stock_status'] ?? 1;
        $validated['stock_amount'] = $validated['stock_amount'] ?? 0;

        $stock = Stock::create($validated);

        // Log ke stock_histories
        DB::table('stock_histories')->insert([
            'stock_id' => $stock->stock_id,
            'company_id' => $stock->company_id,
            'stock_code' => $stock->stock_code,
            'stock_name' => $stock->stock_name,
            'stock_slug' => $stock->stock_slug,
            'stock_description' => $stock->stock_description,
            'stock_type' => $stock->stock_type,
            'stock_unit' => $stock->stock_unit,
            'stock_counted' => $stock->stock_counted,
            'stock_amount' => $stock->stock_amount,
            'stock_price' => $stock->stock_price,
            'stock_status' => $stock->stock_status,
            'stock_image' => $stock->stock_image,
            'effective_date' => now(),
            'action_type' => 'create',
            'changed_by' => $validated['created_by'] ?? 'admin',
            'created_by' => $validated['created_by'],
            'delete_status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stok berhasil ditambahkan.');
    }

    public function show(Stock $stock)
    {
        $stock->load('company');
        return view('admin.stock.show', compact('stock'));
    }

    public function edit(Stock $stock)
    {
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        return view('admin.stock.edit', compact('stock', 'companies'));
    }

    public function update(StockRequest $request, Stock $stock)
    {
        $validated = $request->validated();

        $validated['stock_slug'] = str()->slug($validated['stock_name']);

        $stock->update($validated);

        // Log ke stock_histories
        DB::table('stock_histories')->insert([
            'stock_id' => $stock->stock_id,
            'company_id' => $stock->company_id,
            'stock_code' => $stock->stock_code,
            'stock_name' => $stock->stock_name,
            'stock_slug' => $stock->stock_slug,
            'stock_description' => $stock->stock_description,
            'stock_type' => $stock->stock_type,
            'stock_unit' => $stock->stock_unit,
            'stock_counted' => $stock->stock_counted,
            'stock_amount' => $stock->stock_amount,
            'stock_price' => $stock->stock_price,
            'stock_status' => $stock->stock_status,
            'stock_image' => $stock->stock_image,
            'effective_date' => now(),
            'action_type' => 'update',
            'changed_by' => $validated['updated_by'] ?? 'admin',
            'created_by' => $validated['updated_by'],
            'delete_status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stok berhasil diperbarui.');
    }

    public function destroy(Stock $stock)
    {
        // Log ke stock_histories dulu sebelum soft delete
        DB::table('stock_histories')->insert([
            'stock_id' => $stock->stock_id,
            'company_id' => $stock->company_id,
            'stock_code' => $stock->stock_code,
            'stock_name' => $stock->stock_name,
            'stock_slug' => $stock->stock_slug,
            'stock_description' => $stock->stock_description,
            'stock_type' => $stock->stock_type,
            'stock_unit' => $stock->stock_unit,
            'stock_counted' => $stock->stock_counted,
            'stock_amount' => $stock->stock_amount,
            'stock_price' => $stock->stock_price,
            'stock_status' => $stock->stock_status,
            'stock_image' => $stock->stock_image,
            'effective_date' => now(),
            'action_type' => 'delete',
            'changed_by' => 'admin',
            'created_by' => 'admin',
            'delete_status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $stock->update(['delete_status' => 1]);

        if (request()->ajax()) {
            return response()->json(['success' => 'Stok berhasil dihapus.']);
        }

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stok berhasil dihapus.');
    }
}
