<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Keuangan\CogsRawMaterialRequest;
use App\Models\Admin\Keuangan\RawStockMaterial;
use App\Models\Admin\Keuangan\RawStockMaterialHistory;
use App\Models\Admin\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RawStockMaterialController extends Controller
{
    public function index(Request $request)
    {
        return $this->data($request);
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = RawStockMaterial::where('delete_status', 0);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('unit', 'like', "%{$search}%")
                  ->orWhere('raw_material_code', 'like', "%{$search}%");
            });
        }

        $rawMaterials = $query->latest()->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.kasir.keuangan.cogs-raw-material._data', compact('rawMaterials'))->render(),
                'pagination' => $rawMaterials->links('vendor.pagination.modern')->toHtml(),
                'total' => $rawMaterials->total(),
                'from' => $rawMaterials->firstItem(),
                'to' => $rawMaterials->lastItem(),
            ]);
        }

        return view('admin.kasir.keuangan.cogs-raw-material.index', compact('rawMaterials'));
    }

    public function create()
    {
        return view('admin.kasir.keuangan.cogs-raw-material.create');
    }

    public function store(CogsRawMaterialRequest $request)
    {
        $companyId = session('active_outlet_id') ?? session('outlet_id') ?? Outlet::where('delete_status', 0)->value('outlet_id');

        $rawMaterial = new RawStockMaterial();
        $rawMaterial->outlet_id = $companyId;
        $rawMaterial->raw_material_code = 'RAW-' . strtoupper(Str::random(6));
        $rawMaterial->name = $request->name;
        $rawMaterial->slug = Str::slug($request->name);
        $rawMaterial->unit = $request->unit;
        $rawMaterial->amount = (float) $request->amount;
        $rawMaterial->min_amount = (float) ($request->min_amount ?? 0);
        $rawMaterial->price_per_unit = (float) $request->price_per_unit;
        $rawMaterial->loss_percent = (float) ($request->loss_percent ?? 0);
        $rawMaterial->notes = $request->notes;
        $rawMaterial->created_by = auth()->user()->name ?? 'admin';

        $rawMaterial->calculatePrices();
        $rawMaterial->save();

        // Audit Trail History
        RawStockMaterialHistory::create([
            'raw_stock_material_id' => $rawMaterial->raw_stock_material_id,
            'outlet_id' => $companyId,
            'name' => $rawMaterial->name,
            'unit' => $rawMaterial->unit,
            'amount' => $rawMaterial->amount,
            'price_per_unit' => $rawMaterial->price_per_unit,
            'loss_percent' => $rawMaterial->loss_percent,
            'yield_percent' => $rawMaterial->yield_percent,
            'effective_price' => $rawMaterial->effective_price,
            'action_type' => 'create',
            'changed_by' => auth()->user()->name ?? 'Admin',
            'effective_date' => now(),
            'history_remark' => 'Membuat bahan mentah baru',
            'created_by' => auth()->user()->name ?? 'admin',
        ]);

        return redirect()->route('admin.keuangan.cogs-raw-material.index')
            ->with('success', 'Bahan mentah berhasil ditambahkan.');
    }

    public function show($id)
    {
        $cogsRawMaterial = $id instanceof RawStockMaterial ? $id : RawStockMaterial::find($id);
        if (!$cogsRawMaterial || $cogsRawMaterial->delete_status) {
            return redirect()->route('admin.keuangan.cogs-raw-material.index')
                ->with('error', 'Bahan mentah tidak ditemukan.');
        }

        $cogsRawMaterial->load('histories');

        return view('admin.kasir.keuangan.cogs-raw-material.show', compact('cogsRawMaterial'));
    }

    public function edit($id)
    {
        $cogsRawMaterial = $id instanceof RawStockMaterial ? $id : RawStockMaterial::find($id);
        if (!$cogsRawMaterial || $cogsRawMaterial->delete_status) {
            return redirect()->route('admin.keuangan.cogs-raw-material.index')
                ->with('error', 'Bahan mentah tidak ditemukan.');
        }

        return view('admin.kasir.keuangan.cogs-raw-material.edit', compact('cogsRawMaterial'));
    }

    public function update(CogsRawMaterialRequest $request, $id)
    {
        $cogsRawMaterial = $id instanceof RawStockMaterial ? $id : RawStockMaterial::find($id);
        if (!$cogsRawMaterial || $cogsRawMaterial->delete_status) {
            return redirect()->route('admin.keuangan.cogs-raw-material.index')
                ->with('error', 'Bahan mentah tidak ditemukan.');
        }

        $cogsRawMaterial->name = $request->name;
        $cogsRawMaterial->slug = Str::slug($request->name);
        $cogsRawMaterial->unit = $request->unit;
        $cogsRawMaterial->amount = (float) $request->amount;
        $cogsRawMaterial->min_amount = (float) ($request->min_amount ?? 0);
        $cogsRawMaterial->price_per_unit = (float) $request->price_per_unit;
        $cogsRawMaterial->loss_percent = (float) ($request->loss_percent ?? 0);
        $cogsRawMaterial->notes = $request->notes;
        $cogsRawMaterial->updated_by = auth()->user()->name ?? 'admin';

        $cogsRawMaterial->calculatePrices();
        $cogsRawMaterial->save();

        // Audit Trail History
        RawStockMaterialHistory::create([
            'raw_stock_material_id' => $cogsRawMaterial->raw_stock_material_id,
            'outlet_id' => $cogsRawMaterial->outlet_id,
            'name' => $cogsRawMaterial->name,
            'unit' => $cogsRawMaterial->unit,
            'amount' => $cogsRawMaterial->amount,
            'price_per_unit' => $cogsRawMaterial->price_per_unit,
            'loss_percent' => $cogsRawMaterial->loss_percent,
            'yield_percent' => $cogsRawMaterial->yield_percent,
            'effective_price' => $cogsRawMaterial->effective_price,
            'action_type' => 'update',
            'changed_by' => auth()->user()->name ?? 'Admin',
            'effective_date' => now(),
            'history_remark' => 'Mengubah data bahan mentah',
            'updated_by' => auth()->user()->name ?? 'admin',
        ]);

        return redirect()->route('admin.keuangan.cogs-raw-material.index')
            ->with('success', 'Bahan mentah berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $cogsRawMaterial = $id instanceof RawStockMaterial ? $id : RawStockMaterial::find($id);
        if (!$cogsRawMaterial || $cogsRawMaterial->delete_status) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        $cogsRawMaterial->update(['delete_status' => 1, 'updated_by' => auth()->user()->name ?? 'admin']);

        // Audit Trail History
        RawStockMaterialHistory::create([
            'raw_stock_material_id' => $cogsRawMaterial->raw_stock_material_id,
            'outlet_id' => $cogsRawMaterial->outlet_id,
            'name' => $cogsRawMaterial->name,
            'unit' => $cogsRawMaterial->unit,
            'amount' => $cogsRawMaterial->amount,
            'price_per_unit' => $cogsRawMaterial->price_per_unit,
            'loss_percent' => $cogsRawMaterial->loss_percent,
            'yield_percent' => $cogsRawMaterial->yield_percent,
            'effective_price' => $cogsRawMaterial->effective_price,
            'action_type' => 'delete',
            'changed_by' => auth()->user()->name ?? 'Admin',
            'effective_date' => now(),
            'history_remark' => 'Menghapus bahan mentah',
            'updated_by' => auth()->user()->name ?? 'admin',
        ]);

        return response()->json(['success' => true, 'message' => 'Bahan mentah berhasil dihapus.']);
    }

    public function opname(Request $request, $id)
    {
        $request->validate([
            'physical_amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
        ]);

        $cogsRawMaterial = $id instanceof RawStockMaterial ? $id : RawStockMaterial::find($id);
        if (!$cogsRawMaterial || $cogsRawMaterial->delete_status) {
            return response()->json(['success' => false, 'message' => 'Bahan mentah tidak ditemukan.'], 404);
        }

        $stockBefore = (float) $cogsRawMaterial->amount;
        $physicalAmount = (float) $request->physical_amount;
        $diff = $physicalAmount - $stockBefore;
        $diffFormatted = ($diff >= 0 ? '+' : '') . number_format($diff, 2, ',', '.') . ' ' . $cogsRawMaterial->unit;

        $cogsRawMaterial->update([
            'amount' => $physicalAmount,
            'updated_by' => auth()->user()->name ?? 'admin',
        ]);

        // Audit Trail History Opname
        RawStockMaterialHistory::create([
            'raw_stock_material_id' => $cogsRawMaterial->raw_stock_material_id,
            'outlet_id' => $cogsRawMaterial->outlet_id,
            'name' => $cogsRawMaterial->name,
            'unit' => $cogsRawMaterial->unit,
            'amount' => $physicalAmount,
            'price_per_unit' => $cogsRawMaterial->price_per_unit,
            'loss_percent' => $cogsRawMaterial->loss_percent,
            'yield_percent' => $cogsRawMaterial->yield_percent,
            'effective_price' => $cogsRawMaterial->effective_price,
            'action_type' => 'adjustment',
            'changed_by' => auth()->user()->name ?? 'Admin',
            'effective_date' => now(),
            'history_remark' => "Stock Opname: Hasil fisik {$physicalAmount} {$cogsRawMaterial->unit} (Selisih: {$diffFormatted}, Alasan: {$request->reason})",
            'created_by' => auth()->user()->name ?? 'admin',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Penyesuaian stok opname {$cogsRawMaterial->name} berhasil disimpan. (Stok baru: {$physicalAmount} {$cogsRawMaterial->unit})",
        ]);
    }
}
