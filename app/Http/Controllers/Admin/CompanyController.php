<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::where('delete_status', 0)->latest()->get();
        return view('sys_admin.company.index', compact('companies'));
    }

    public function create()
    {
        return view('sys_admin.company.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_code' => 'nullable|string|max:50',
            'company_branch' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_address' => 'nullable|string',
            'company_status' => 'nullable|integer|in:0,1',
        ]);

        $validated['company_slug'] = str()->slug($validated['company_name']);
        $validated['company_status'] = $validated['company_status'] ?? 1;

        Company::create($validated);

        return redirect()->route('admin.company.index')
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

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_code' => 'nullable|string|max:50',
            'company_branch' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_address' => 'nullable|string',
            'company_status' => 'nullable|integer|in:0,1',
        ]);

        $validated['company_slug'] = str()->slug($validated['company_name']);

        $company->update($validated);

        return redirect()->route('admin.company.index')
            ->with('success', 'Perusahaan berhasil diperbarui.');
    }

    public function destroy(Company $company)
    {
        $company->update(['delete_status' => 1]);

        return redirect()->route('admin.company.index')
            ->with('success', 'Perusahaan berhasil dihapus.');
    }
}
