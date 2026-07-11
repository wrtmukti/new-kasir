<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Company;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::where('delete_status', 0)->with('company')->latest()->get();
        return view('admin.stock.index', compact('stocks'));
    }

    public function create()
    {
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        return view('admin.stock.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'nullable|string|max:255',
            'stock_code' => 'nullable|string|max:50',
            'stock_name' => 'required|string|max:255',
            'stock_description' => 'nullable|string',
            'stock_type' => 'nullable|string|max:50',
            'stock_unit' => 'nullable|string|max:20',
            'stock_amount' => 'nullable|integer|min:0',
            'stock_price' => 'nullable|numeric|min:0',
            'stock_status' => 'nullable|integer|in:0,1',
        ]);

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

    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'company_id' => 'nullable|string|max:255',
            'stock_code' => 'nullable|string|max:50',
            'stock_name' => 'required|string|max:255',
            'stock_description' => 'nullable|string',
            'stock_type' => 'nullable|string|max:50',
            'stock_unit' => 'nullable|string|max:20',
            'stock_amount' => 'nullable|integer|min:0',
            'stock_price' => 'nullable|numeric|min:0',
            'stock_status' => 'nullable|integer|in:0,1',
        ]);

        $validated['stock_slug'] = str()->slug($validated['stock_name']);

        $stock->update($validated);

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stok berhasil diperbarui.');
    }

    public function destroy(Stock $stock)
    {
        $stock->update(['delete_status' => 1]);

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stok berhasil dihapus.');
    }
}
