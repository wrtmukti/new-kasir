<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StockRequest;
use App\Models\Admin\Stock;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;

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

        Stock::create($validated);

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

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stok berhasil diperbarui.');
    }

    public function destroy(Stock $stock)
    {
        $stock->update(['delete_status' => 1]);

        if (request()->ajax()) {
            return response()->json(['success' => 'Stok berhasil dihapus.']);
        }

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stok berhasil dihapus.');
    }
}
