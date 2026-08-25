<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Keuangan\CogsWasteLogRequest;
use App\Models\Admin\Keuangan\CogsRawMaterial;
use App\Models\Admin\Keuangan\CogsRawMaterialHistory;
use App\Models\Admin\Keuangan\CogsWasteHistory;
use App\Models\Admin\Keuangan\CogsWasteLog;
use App\Models\Admin\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CogsWasteLogController extends Controller
{
    public function index(Request $request)
    {
        return $this->data($request);
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = CogsWasteLog::where('delete_status', 0)->with('rawMaterial');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhereHas('rawMaterial', function ($rawQ) use ($search) {
                      $rawQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $wasteLogs = $query->latest('loss_date')->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.keuangan.cogs-waste._data', compact('wasteLogs'))->render(),
                'pagination' => $wasteLogs->links('vendor.pagination.modern')->toHtml(),
                'total' => $wasteLogs->total(),
                'from' => $wasteLogs->firstItem(),
                'to' => $wasteLogs->lastItem(),
            ]);
        }

        return view('admin.keuangan.cogs-waste.index', compact('wasteLogs'));
    }

    public function create()
    {
        $rawMaterials = CogsRawMaterial::where('delete_status', 0)->get();
        return view('admin.keuangan.cogs-waste.create', compact('rawMaterials'));
    }

    public function store(CogsWasteLogRequest $request)
    {
        $companyId = session('active_outlet_id') ?? session('outlet_id') ?? Outlet::where('delete_status', 0)->value('outlet_id');

        $rawMaterial = CogsRawMaterial::find($request->cogs_raw_material_id);
        if (!$rawMaterial) {
            return redirect()->back()->with('error', 'Bahan mentah tidak ditemukan.');
        }

        $qtyLost = (float) $request->qty_lost;
        $wasteCost = $qtyLost * (float) $rawMaterial->effective_price;

        DB::transaction(function () use ($request, $companyId, $rawMaterial, $qtyLost, $wasteCost) {
            // Create Waste Log
            $wasteLog = CogsWasteLog::create([
                'outlet_id' => $companyId,
                'cogs_raw_material_id' => $rawMaterial->cogs_raw_material_id,
                'qty_lost' => $qtyLost,
                'waste_cost' => $wasteCost,
                'reason' => $request->reason,
                'loss_date' => $request->loss_date,
                'notes' => $request->notes,
                'created_by' => 'admin',
            ]);

            // Decrement amount di raw_materials
            $stockBefore = (float) $rawMaterial->amount;
            $stockAfter = max(0, $stockBefore - $qtyLost);
            $rawMaterial->update(['amount' => $stockAfter]);

            // Audit Trail Waste History
            CogsWasteHistory::create([
                'cogs_waste_log_id' => $wasteLog->cogs_waste_log_id,
                'outlet_id' => $companyId,
                'cogs_raw_material_id' => $rawMaterial->cogs_raw_material_id,
                'qty_lost' => $qtyLost,
                'waste_cost' => $wasteCost,
                'reason' => $request->reason,
                'loss_date' => $request->loss_date,
                'action_type' => 'create',
                'changed_by' => 'Admin',
                'history_remark' => 'Mencatat bahan terbuang (waste log)',
                'created_by' => 'admin',
            ]);

            // Log di raw material history
            CogsRawMaterialHistory::create([
                'cogs_raw_material_id' => $rawMaterial->cogs_raw_material_id,
                'outlet_id' => $companyId,
                'name' => $rawMaterial->name,
                'unit' => $rawMaterial->unit,
                'amount' => $stockAfter,
                'price_per_unit' => $rawMaterial->price_per_unit,
                'loss_percent' => $rawMaterial->loss_percent,
                'yield_percent' => $rawMaterial->yield_percent,
                'effective_price' => $rawMaterial->effective_price,
                'action_type' => 'waste',
                'changed_by' => 'Admin',
                'effective_date' => $request->loss_date,
                'history_remark' => "Pengurangan bahan terbuang: {$qtyLost} {$rawMaterial->unit} ({$request->reason})",
                'created_by' => 'admin',
            ]);
        });

        return redirect()->route('admin.keuangan.cogs-waste.index')
            ->with('success', 'Pencatatan bahan terbuang berhasil disimpan.');
    }

    public function destroy($id)
    {
        $cogsWasteLog = $id instanceof CogsWasteLog ? $id : CogsWasteLog::find($id);
        if (!$cogsWasteLog || $cogsWasteLog->delete_status) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        $cogsWasteLog->update(['delete_status' => 1, 'updated_by' => 'admin']);

        // Audit Trail Waste History
        CogsWasteHistory::create([
            'cogs_waste_log_id' => $cogsWasteLog->cogs_waste_log_id,
            'outlet_id' => $cogsWasteLog->outlet_id,
            'cogs_raw_material_id' => $cogsWasteLog->cogs_raw_material_id,
            'qty_lost' => $cogsWasteLog->qty_lost,
            'waste_cost' => $cogsWasteLog->waste_cost,
            'reason' => $cogsWasteLog->reason,
            'loss_date' => $cogsWasteLog->loss_date,
            'action_type' => 'delete',
            'changed_by' => 'Admin',
            'history_remark' => 'Menghapus catatan bahan terbuang',
            'updated_by' => 'admin',
        ]);

        return response()->json(['success' => true, 'message' => 'Catatan waste berhasil dihapus.']);
    }
}
