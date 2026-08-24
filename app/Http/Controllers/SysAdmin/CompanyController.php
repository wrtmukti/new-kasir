<?php

namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SysAdmin\CompanyRequest;
use App\Models\Admin\Outlet;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $outlets = Outlet::where('delete_status', 0)
            ->latest()
            ->paginate(10);
        return view('sys_admin.company.index', compact('outlets'));
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $outlets = Outlet::where('delete_status', 0)
            ->latest()
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('sys_admin.company._data', compact('outlets'))->render(),
                'pagination' => $outlets->links('vendor.pagination.modern')->toHtml(),
                'total' => $outlets->total(),
                'from' => $outlets->firstItem(),
                'to' => $outlets->lastItem(),
            ]);
        }

        return view('sys_admin.company.index', compact('outlets'));
    }

    public function create()
    {
        return view('sys_admin.company.create');
    }

    public function store(CompanyRequest $request)
    {
        $validated = $request->validated();

        $validated['outlet_slug'] = str()->slug($validated['outlet_name']);
        $validated['outlet_status'] = $validated['outlet_status'] ?? 1;

        Outlet::create($validated);

        return redirect()->route('sys_admin.company.index')
            ->with('success', 'Outlet berhasil ditambahkan.');
    }

    public function show(Outlet $company)
    {
        $outlet = $company;
        return view('sys_admin.company.show', compact('outlet'));
    }

    public function edit(Outlet $company)
    {
        $outlet = $company;
        return view('sys_admin.company.edit', compact('outlet'));
    }

    public function update(CompanyRequest $request, Outlet $company)
    {
        $validated = $request->validated();

        $validated['outlet_slug'] = str()->slug($validated['outlet_name']);

        $company->update($validated);

        return redirect()->route('sys_admin.company.index')
            ->with('success', 'Outlet berhasil diperbarui.');
    }

    public function destroy(Outlet $company)
    {
        $company->update(['delete_status' => 1]);

        if (request()->ajax()) {
            return response()->json(['success' => 'Outlet berhasil dihapus.']);
        }

        return redirect()->route('sys_admin.company.index')
            ->with('success', 'Outlet berhasil dihapus.');
    }
}
