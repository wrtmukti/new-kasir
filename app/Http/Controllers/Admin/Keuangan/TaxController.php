<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Tax;
use App\Models\Admin\ServiceCharge;
use App\Http\Requests\Admin\TaxRequest;
use App\Http\Requests\Admin\ServiceChargeRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TaxController extends Controller
{
    /**
     * Tampilan utama Setting Pajak & Service Charge
     */
    public function index()
    {
        $tax = Tax::where('is_active', 1)->first() ?? Tax::first();
        $service = ServiceCharge::where('is_active', 1)->first() ?? ServiceCharge::first();

        return view('admin.keuangan.setting-tax.index', compact('tax', 'service'));
    }

    /**
     * Update atau Create Master Pajak (PB1)
     */
    public function updateTax(TaxRequest $request)
    {
        $companyId = session('outlet_id') ?? 'COMP-001';

        $tax = Tax::updateOrCreate(
            ['outlet_id' => $companyId],
            [
                'tax_name' => $request->tax_name,
                'rate_percent' => $request->rate_percent,
                'type' => $request->type,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pengaturan Pajak Restoran (PB1) berhasil diperbarui.',
                'data' => $tax,
            ]);
        }

        return redirect()->route('admin.keuangan.setting-tax.index')->with('success', 'Pengaturan Pajak Restoran (PB1) berhasil diperbarui.');
    }

    /**
     * Update atau Create Master Service Charge
     */
    public function updateServiceCharge(ServiceChargeRequest $request)
    {
        $companyId = session('outlet_id') ?? 'COMP-001';

        $service = ServiceCharge::updateOrCreate(
            ['outlet_id' => $companyId],
            [
                'service_name' => $request->service_name,
                'rate_percent' => $request->rate_percent,
                'is_taxable' => $request->has('is_taxable') ? 1 : 0,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pengaturan Service Charge berhasil diperbarui.',
                'data' => $service,
            ]);
        }

        return redirect()->route('admin.keuangan.setting-tax.index')->with('success', 'Pengaturan Service Charge berhasil diperbarui.');
    }
}

