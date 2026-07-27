<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VoucherRequest;
use App\Models\Admin\Voucher;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::where('delete_status', 0)
            ->with('company')
            ->latest()
            ->paginate(10);
        return view('admin.voucher.index', compact('vouchers'));
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $vouchers = Voucher::where('delete_status', 0)
            ->with('company')
            ->latest()
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.voucher._data', compact('vouchers'))->render(),
                'pagination' => $vouchers->links('vendor.pagination.modern')->toHtml(),
                'total' => $vouchers->total(),
                'from' => $vouchers->firstItem(),
                'to' => $vouchers->lastItem(),
            ]);
        }

        return view('admin.voucher.index', compact('vouchers'));
    }

    public function create()
    {
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        return view('admin.voucher.create', compact('companies'));
    }

    public function store(VoucherRequest $request)
    {
        $validated = $request->validated();
        $validated['voucher_status'] = $validated['voucher_status'] ?? 1;

        Voucher::create($validated);

        return redirect()->route('admin.voucher.index')
            ->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function show(Voucher $voucher)
    {
        $voucher->load('company');
        return view('admin.voucher.show', compact('voucher'));
    }

    public function edit(Voucher $voucher)
    {
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        return view('admin.voucher.edit', compact('voucher', 'companies'));
    }

    public function update(VoucherRequest $request, Voucher $voucher)
    {
        $validated = $request->validated();
        $voucher->update($validated);

        return redirect()->route('admin.voucher.index')
            ->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->update(['delete_status' => 1]);

        if (request()->ajax()) {
            return response()->json(['success' => 'Voucher berhasil dihapus.']);
        }

        return redirect()->route('admin.voucher.index')
            ->with('success', 'Voucher berhasil dihapus.');
    }
}
