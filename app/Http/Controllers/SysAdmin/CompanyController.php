<?php

namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SysAdmin\CompanyRequest;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::where('delete_status', 0)
            ->latest()
            ->paginate(10);
        return view('sys_admin.company.index', compact('companies'));
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $companies = Company::where('delete_status', 0)
            ->latest()
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('sys_admin.company._data', compact('companies'))->render(),
                'pagination' => $companies->links('vendor.pagination.modern')->toHtml(),
                'total' => $companies->total(),
                'from' => $companies->firstItem(),
                'to' => $companies->lastItem(),
            ]);
        }

        return view('sys_admin.company.index', compact('companies'));
    }

    public function create()
    {
        return view('sys_admin.company.create');
    }

    public function store(CompanyRequest $request)
    {
        $validated = $request->validated();

        $validated['company_slug'] = str()->slug($validated['company_name']);
        $validated['company_status'] = $validated['company_status'] ?? 1;

        Company::create($validated);

        return redirect()->route('sys_admin.company.index')
            ->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    public function show(Company $company)
    {
        return view('sys_admin.company.show', compact('company'));
    }

    public function edit(Company $company)
    {
        return view('sys_admin.company.edit', compact('company'));
    }

    public function update(CompanyRequest $request, Company $company)
    {
        $validated = $request->validated();

        $validated['company_slug'] = str()->slug($validated['company_name']);

        $company->update($validated);

        return redirect()->route('sys_admin.company.index')
            ->with('success', 'Perusahaan berhasil diperbarui.');
    }

    public function destroy(Company $company)
    {
        $company->update(['delete_status' => 1]);

        if (request()->ajax()) {
            return response()->json(['success' => 'Perusahaan berhasil dihapus.']);
        }

        return redirect()->route('sys_admin.company.index')
            ->with('success', 'Perusahaan berhasil dihapus.');
    }
}
