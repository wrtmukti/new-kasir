<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StockRequest;
use App\Models\Admin\Keuangan\CogsRawMaterial;
use App\Models\Admin\Keuangan\CogsRawMaterialHistory;
use App\Models\Admin\Stock;
use App\Models\Admin\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    protected function getActiveOutletId(): ?string
    {
        return session('active_outlet_id') ?? session('outlet_id') ?? Outlet::where('delete_status', 0)->value('outlet_id');
    }

    public function index()
    {
        $activeOutletId = $this->getActiveOutletId();
        $query = Stock::where('delete_status', 0)->with('outlet');
        if ($activeOutletId) {
            $query->where('outlet_id', $activeOutletId);
        }
        $stocks = $query->latest()->paginate(10);
        return view('admin.stock.index', compact('stocks'));
    }

    public function data(Request $request)
    {
        $activeOutletId = $this->getActiveOutletId();
        $perPage = $request->input('per_page', 10);
        $query = Stock::where('delete_status', 0)->with('outlet');
        if ($activeOutletId) {
            $query->where('outlet_id', $activeOutletId);
        }
        $stocks = $query->latest()->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.stock._data', compact('stocks'))->render(),
                'pagination' => $stocks->links('vendor.pagination.modern')->toHtml(),
                'total' => $stocks->total(),
                'from' => $stocks->firstItem(),
                'to' => $stocks->lastItem(),
            ]);
        }

        return view('admin.stock.index', compact('stocks'));
    }

    public function jsonList(Request $request)
    {
        $search = $request->input('search');
        $stocks = Stock::where('delete_status', 0)
            ->where('stock_status', 1)
            ->when($search, function ($q) use ($search) {
                $q->where('stock_name', 'like', "%{$search}%")
                  ->orWhere('stock_code', 'like', "%{$search}%");
            })
            ->orderBy('stock_name')
            ->get(['stock_id', 'stock_name', 'stock_code', 'stock_unit', 'stock_price']);

        return response()->json($stocks);
    }

    public function create()
    {
        $outlets = Outlet::where('delete_status', 0)->where('outlet_status', 1)->get();
        $cogsRawMaterials = CogsRawMaterial::where('delete_status', 0)->orderBy('name')->get();
        return view('admin.stock.create', compact('outlets', 'cogsRawMaterials'));
    }

    public function store(StockRequest $request)
    {
        $validated = $request->validated();

        $validated['stock_slug'] = str()->slug($validated['stock_name']);
        $validated['stock_status'] = $validated['stock_status'] ?? 1;
        $validated['stock_amount'] = $validated['stock_amount'] ?? 0;

        $stockData = collect($validated)->only([
            'outlet_id', 'stock_code', 'stock_name', 'stock_slug', 'stock_description',
            'stock_type', 'stock_unit', 'stock_amount', 'stock_price', 'stock_status'
        ])->toArray();

        $stock = Stock::create($stockData);

        // Optional Deduction of Raw Material (COGS)
        $this->handleRawDeduction($request, $stock);

        // Log ke stock_histories
        DB::table('stock_histories')->insert([
            'stock_id' => $stock->stock_id,
            'outlet_id' => $stock->outlet_id,
            'stock_code' => $stock->stock_code,
            'stock_name' => $stock->stock_name,
            'stock_slug' => $stock->stock_slug,
            'stock_description' => $stock->stock_description,
            'stock_type' => $stock->stock_type,
            'stock_unit' => $stock->stock_unit,
            'stock_counted' => $stock->stock_counted,
            'stock_amount' => $stock->stock_amount,
            'stock_price' => $stock->stock_price,
            'stock_status' => $stock->stock_status,
            'stock_image' => $stock->stock_image,
            'effective_date' => now(),
            'action_type' => 'create',
            'changed_by' => $validated['created_by'] ?? 'admin',
            'created_by' => $validated['created_by'] ?? 'admin',
            'delete_status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stok berhasil ditambahkan.');
    }

    public function show(Stock $stock)
    {
        $stock->load('outlet');
        return view('admin.stock.show', compact('stock'));
    }

    public function edit(Stock $stock)
    {
        $outlets = Outlet::where('delete_status', 0)->where('outlet_status', 1)->get();
        $cogsRawMaterials = CogsRawMaterial::where('delete_status', 0)->orderBy('name')->get();
        return view('admin.stock.edit', compact('stock', 'outlets', 'cogsRawMaterials'));
    }

    public function update(StockRequest $request, Stock $stock)
    {
        $validated = $request->validated();

        $validated['stock_slug'] = str()->slug($validated['stock_name']);

        $stockData = collect($validated)->only([
            'outlet_id', 'stock_code', 'stock_name', 'stock_slug', 'stock_description',
            'stock_type', 'stock_unit', 'stock_amount', 'stock_price', 'stock_status'
        ])->toArray();

        $stock->update($stockData);

        // Optional Deduction of Raw Material (COGS)
        $this->handleRawDeduction($request, $stock);

        // Log ke stock_histories
        DB::table('stock_histories')->insert([
            'stock_id' => $stock->stock_id,
            'outlet_id' => $stock->outlet_id,
            'stock_code' => $stock->stock_code,
            'stock_name' => $stock->stock_name,
            'stock_slug' => $stock->stock_slug,
            'stock_description' => $stock->stock_description,
            'stock_type' => $stock->stock_type,
            'stock_unit' => $stock->stock_unit,
            'stock_counted' => $stock->stock_counted,
            'stock_amount' => $stock->stock_amount,
            'stock_price' => $stock->stock_price,
            'stock_status' => $stock->stock_status,
            'stock_image' => $stock->stock_image,
            'effective_date' => now(),
            'action_type' => 'update',
            'changed_by' => $validated['updated_by'] ?? 'admin',
            'created_by' => $validated['updated_by'] ?? 'admin',
            'delete_status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stok berhasil diperbarui.');
    }

    public function destroy(Stock $stock)
    {
        DB::table('stock_histories')->insert([
            'stock_id' => $stock->stock_id,
            'outlet_id' => $stock->outlet_id,
            'stock_code' => $stock->stock_code,
            'stock_name' => $stock->stock_name,
            'stock_slug' => $stock->stock_slug,
            'stock_description' => $stock->stock_description,
            'stock_type' => $stock->stock_type,
            'stock_unit' => $stock->stock_unit,
            'stock_counted' => $stock->stock_counted,
            'stock_amount' => $stock->stock_amount,
            'stock_price' => $stock->stock_price,
            'stock_status' => $stock->stock_status,
            'stock_image' => $stock->stock_image,
            'effective_date' => now(),
            'action_type' => 'delete',
            'changed_by' => 'admin',
            'created_by' => 'admin',
            'delete_status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $stock->update(['delete_status' => 1]);

        if (request()->ajax()) {
            return response()->json(['success' => 'Stok berhasil dihapus.']);
        }

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stok berhasil dihapus.');
    }

    private function handleRawDeduction(Request $request, Stock $stock)
    {
        if ($request->input('deduct_raw_material') == 1 && $request->filled('cogs_raw_material_id')) {
            $cogsRawMaterialId = $request->input('cogs_raw_material_id');
            $rawQtyPerUnit = (float) $request->input('raw_qty_per_unit', 0);
            $stockAmount = (float) $stock->stock_amount;
            $totalRawUsed = $rawQtyPerUnit * $stockAmount;

            if ($totalRawUsed > 0) {
                $rawMat = CogsRawMaterial::find($cogsRawMaterialId);
                if ($rawMat) {
                    $rawMat->amount = max(0, (float)$rawMat->amount - $totalRawUsed);
                    $rawMat->save();

                    CogsRawMaterialHistory::create([
                        'cogs_raw_material_id' => $rawMat->cogs_raw_material_id,
                        'outlet_id' => $rawMat->outlet_id,
                        'name' => $rawMat->name,
                        'unit' => $rawMat->unit,
                        'amount' => $rawMat->amount,
                        'price_per_unit' => $rawMat->price_per_unit,
                        'loss_percent' => $rawMat->loss_percent,
                        'yield_percent' => $rawMat->yield_percent,
                        'effective_price' => $rawMat->effective_price,
                        'action_type' => 'production',
                        'changed_by' => 'Admin',
                        'effective_date' => now(),
                        'history_remark' => "Pembuatan Stok {$stock->stock_name}: Dipakai {$totalRawUsed} {$rawMat->unit} {$rawMat->name} (Takaran {$rawQtyPerUnit} {$rawMat->unit}/unit x {$stockAmount} {$stock->stock_unit})",
                        'created_by' => 'admin',
                    ]);
                }
            }
        }
    }
}
