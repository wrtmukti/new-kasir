<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Keuangan\CogsRawMaterial;
use App\Models\Admin\Keuangan\CogsWasteLog;
use App\Models\Admin\PurchaseOrder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InventoryReportController extends Controller
{
    public function index(Request $request)
    {
        $perPageInput = $request->input('per_page', 10);
        $perPage = ($perPageInput === 'all') ? 999999 : (int) $perPageInput;
        $search = $request->input('search');

        $query = CogsRawMaterial::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('raw_material_code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        $materials = $query->latest()->paginate($perPage);

        $totalAssetValue = CogsRawMaterial::all()->sum(function ($m) {
            return (float) $m->amount * (float) $m->effective_price;
        });

        $totalWaste = CogsWasteLog::sum('waste_cost');
        $totalPo = PurchaseOrder::where('po_status', 'completed')->sum('po_total_amount');

        return view('admin.keuangan.reports.inventory', compact(
            'perPageInput',
            'search',
            'materials',
            'totalAssetValue',
            'totalWaste',
            'totalPo'
        ));
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $query = CogsRawMaterial::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('raw_material_code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        $materials = $query->get();
        $filename = "Laporan_Stok_Bahan_Gudang_" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($materials) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kode Bahan', 'Nama Bahan Mentah', 'Stok Saat Ini', 'Satuan', 'Harga Efektif per Unit', 'Nilai Aset Gudang (Rp)']);

            foreach ($materials as $m) {
                fputcsv($file, [
                    $m->raw_material_code,
                    $m->name,
                    $m->amount,
                    $m->unit,
                    $m->effective_price,
                    (float) $m->amount * (float) $m->effective_price,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
