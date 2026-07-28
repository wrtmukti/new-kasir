<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VoucherRequest;
use App\Models\Admin\Voucher;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $voucher = Voucher::create($validated);

        // Log ke voucher_histories
        DB::table('voucher_histories')->insert([
            'voucher_id' => $voucher->voucher_id,
            'company_id' => $voucher->company_id,
            'voucher_code' => $voucher->voucher_code,
            'voucher_name' => $voucher->voucher_name,
            'voucher_description' => $voucher->voucher_description,
            'voucher_type' => $voucher->voucher_type,
            'voucher_value' => $voucher->voucher_value,
            'voucher_max_discount' => $voucher->voucher_max_discount,
            'voucher_min_purchase' => $voucher->voucher_min_purchase,
            'voucher_applicable_to' => $voucher->voucher_applicable_to,
            'voucher_usage_limit' => $voucher->voucher_usage_limit,
            'voucher_usage_per_customer' => $voucher->voucher_usage_per_customer,
            'voucher_start_date' => $voucher->voucher_start_date,
            'voucher_end_date' => $voucher->voucher_end_date,
            'voucher_status' => $voucher->voucher_status,
            'action' => 'create',
            'user_id' => $request->input('created_by'),
            'created_by' => $request->input('created_by'),
            'delete_status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.voucher.index')
            ->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function show(Voucher $voucher)
    {
        return redirect()->route('admin.voucher.edit', $voucher);
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

        // Log ke voucher_histories (data sesudah update)
        DB::table('voucher_histories')->insert([
            'voucher_id' => $voucher->voucher_id,
            'company_id' => $voucher->company_id,
            'voucher_code' => $voucher->voucher_code,
            'voucher_name' => $voucher->voucher_name,
            'voucher_description' => $voucher->voucher_description,
            'voucher_type' => $voucher->voucher_type,
            'voucher_value' => $voucher->voucher_value,
            'voucher_max_discount' => $voucher->voucher_max_discount,
            'voucher_min_purchase' => $voucher->voucher_min_purchase,
            'voucher_applicable_to' => $voucher->voucher_applicable_to,
            'voucher_usage_limit' => $voucher->voucher_usage_limit,
            'voucher_usage_per_customer' => $voucher->voucher_usage_per_customer,
            'voucher_start_date' => $voucher->voucher_start_date,
            'voucher_end_date' => $voucher->voucher_end_date,
            'voucher_status' => $voucher->voucher_status,
            'action' => 'update',
            'user_id' => $request->input('updated_by'),
            'created_by' => $request->input('updated_by'),
            'delete_status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.voucher.index')
            ->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy(Voucher $voucher)
    {
        // Log ke voucher_histories dulu sebelum soft delete
        DB::table('voucher_histories')->insert([
            'voucher_id' => $voucher->voucher_id,
            'company_id' => $voucher->company_id,
            'voucher_code' => $voucher->voucher_code,
            'voucher_name' => $voucher->voucher_name,
            'voucher_description' => $voucher->voucher_description,
            'voucher_type' => $voucher->voucher_type,
            'voucher_value' => $voucher->voucher_value,
            'voucher_max_discount' => $voucher->voucher_max_discount,
            'voucher_min_purchase' => $voucher->voucher_min_purchase,
            'voucher_applicable_to' => $voucher->voucher_applicable_to,
            'voucher_usage_limit' => $voucher->voucher_usage_limit,
            'voucher_usage_per_customer' => $voucher->voucher_usage_per_customer,
            'voucher_start_date' => $voucher->voucher_start_date,
            'voucher_end_date' => $voucher->voucher_end_date,
            'voucher_status' => $voucher->voucher_status,
            'action' => 'delete',
            'user_id' => 'admin',
            'created_by' => 'admin',
            'delete_status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $voucher->update(['delete_status' => 1]);

        if (request()->ajax()) {
            return response()->json(['success' => 'Voucher berhasil dihapus.']);
        }

        return redirect()->route('admin.voucher.index')
            ->with('success', 'Voucher berhasil dihapus.');
    }
}
