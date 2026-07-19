<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupplierRequest;
use App\Models\Admin\Supplier;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::where('delete_status', 0)
            ->with('company')
            ->latest()
            ->paginate(10);
        return view('admin.supplier.index', compact('suppliers'));
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $suppliers = Supplier::where('delete_status', 0)
            ->with('company')
            ->latest()
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.supplier._data', compact('suppliers'))->render(),
                'pagination' => $suppliers->links('vendor.pagination.modern')->toHtml(),
                'total' => $suppliers->total(),
                'from' => $suppliers->firstItem(),
                'to' => $suppliers->lastItem(),
            ]);
        }

        return view('admin.supplier.index', compact('suppliers'));
    }

    public function create()
    {
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        return view('admin.supplier.create', compact('companies'));
    }

    public function store(SupplierRequest $request)
    {
        $validated = $request->validated();
        $validated['supplier_status'] = $validated['supplier_status'] ?? 1;

        Supplier::create($validated);

        return redirect()->route('admin.supplier.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('company');
        return view('admin.supplier.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        return view('admin.supplier.edit', compact('supplier', 'companies'));
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $validated = $request->validated();
        $supplier->update($validated);

        return redirect()->route('admin.supplier.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->update(['delete_status' => 1]);

        if (request()->ajax()) {
            return response()->json(['success' => 'Supplier berhasil dihapus.']);
        }

        return redirect()->route('admin.supplier.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }
}
