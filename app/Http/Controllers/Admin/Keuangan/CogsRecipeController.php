<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Keuangan\CogsRecipeRequest;
use App\Models\Admin\Keuangan\CogsRawMaterial;
use App\Models\Admin\Keuangan\CogsRecipe;
use App\Models\Admin\Keuangan\CogsRecipeHistory;
use App\Models\Admin\Keuangan\CogsRecipeItem;
use App\Models\Admin\Product;
use App\Models\Admin\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CogsRecipeController extends Controller
{
    public function index(Request $request)
    {
        return $this->data($request);
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = CogsRecipe::where('delete_status', 0)->with('product', 'items.rawMaterial');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('recipe_name', 'like', "%{$search}%")
                  ->orWhere('recipe_category', 'like', "%{$search}%");
            });
        }

        $recipes = $query->latest()->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.keuangan.cogs-recipe._data', compact('recipes'))->render(),
                'pagination' => $recipes->links('vendor.pagination.modern')->toHtml(),
                'total' => $recipes->total(),
                'from' => $recipes->firstItem(),
                'to' => $recipes->lastItem(),
            ]);
        }

        return view('admin.keuangan.cogs-recipe.index', compact('recipes'));
    }

    public function create()
    {
        $products = Product::where('delete_status', 0)->get();
        $rawMaterials = CogsRawMaterial::where('delete_status', 0)->get();

        return view('admin.keuangan.cogs-recipe.create', compact('products', 'rawMaterials'));
    }

    public function store(CogsRecipeRequest $request)
    {
        $outlet = Outlet::where('delete_status', 0)->first();
        $companyId = $company ? $outlet->outlet_id : null;

        DB::transaction(function () use ($request, $companyId, &$recipe) {
            $recipe = CogsRecipe::create([
                'outlet_id' => $companyId,
                'product_id' => $request->product_id,
                'recipe_name' => $request->recipe_name,
                'target_food_cost' => (float) $request->target_food_cost,
                'notes' => $request->notes,
                'created_by' => 'admin',
            ]);

            $snapshotItems = [];
            foreach ($request->items as $item) {
                $rawMat = CogsRawMaterial::find($item['cogs_raw_material_id']);
                if (!$rawMat) continue;

                $qty = (float) $item['ingredient_qty'];
                $itemCost = $qty * (float) $rawMat->effective_price;

                CogsRecipeItem::create([
                    'cogs_recipe_id' => $recipe->cogs_recipe_id,
                    'cogs_raw_material_id' => $rawMat->cogs_raw_material_id,
                    'ingredient_qty' => $qty,
                    'ingredient_cost' => $itemCost,
                ]);

                $snapshotItems[] = [
                    'material_name' => $rawMat->name,
                    'qty' => $qty,
                    'unit' => $rawMat->unit,
                    'effective_price' => $rawMat->effective_price,
                    'item_cost' => $itemCost,
                ];
            }

            $recipe->recalculateCost();

            // Audit Trail History
            CogsRecipeHistory::create([
                'cogs_recipe_id' => $recipe->cogs_recipe_id,
                'outlet_id' => $companyId,
                'recipe_name' => $recipe->recipe_name,
                'target_food_cost' => $recipe->target_food_cost,
                'estimated_cogs' => $recipe->estimated_cogs,
                'suggested_price' => $recipe->suggested_price,
                'snapshot_items_json' => $snapshotItems,
                'action_type' => 'create',
                'changed_by' => 'Admin',
                'effective_date' => now(),
                'history_remark' => 'Membuat resep standar HPP baru',
                'created_by' => 'admin',
            ]);
        });

        return redirect()->route('admin.keuangan.cogs-recipe.index')
            ->with('success', 'Resep standar HPP berhasil dibuat.');
    }

    public function show($id)
    {
        $cogsRecipe = $id instanceof CogsRecipe ? $id : CogsRecipe::find($id);
        if (!$cogsRecipe || $cogsRecipe->delete_status) {
            return redirect()->route('admin.keuangan.cogs-recipe.index')
                ->with('error', 'Resep tidak ditemukan.');
        }

        $cogsRecipe->load('product', 'items.rawMaterial', 'histories');

        return view('admin.keuangan.cogs-recipe.show', compact('cogsRecipe'));
    }

    public function edit($id)
    {
        $cogsRecipe = $id instanceof CogsRecipe ? $id : CogsRecipe::find($id);
        if (!$cogsRecipe || $cogsRecipe->delete_status) {
            return redirect()->route('admin.keuangan.cogs-recipe.index')
                ->with('error', 'Resep tidak ditemukan.');
        }

        $products = Product::where('delete_status', 0)->get();
        $rawMaterials = CogsRawMaterial::where('delete_status', 0)->get();
        $cogsRecipe->load('items.rawMaterial');

        return view('admin.keuangan.cogs-recipe.edit', compact('cogsRecipe', 'products', 'rawMaterials'));
    }

    public function update(CogsRecipeRequest $request, $id)
    {
        $cogsRecipe = $id instanceof CogsRecipe ? $id : CogsRecipe::find($id);
        if (!$cogsRecipe || $cogsRecipe->delete_status) {
            return redirect()->route('admin.keuangan.cogs-recipe.index')
                ->with('error', 'Resep tidak ditemukan.');
        }

        DB::transaction(function () use ($request, $cogsRecipe) {
            $cogsRecipe->update([
                'product_id' => $request->product_id,
                'recipe_name' => $request->recipe_name,
                'target_food_cost' => (float) $request->target_food_cost,
                'notes' => $request->notes,
                'updated_by' => 'admin',
            ]);

            // Clear old items & rebuild
            CogsRecipeItem::where('cogs_recipe_id', $cogsRecipe->cogs_recipe_id)->delete();

            $snapshotItems = [];
            foreach ($request->items as $item) {
                $rawMat = CogsRawMaterial::find($item['cogs_raw_material_id']);
                if (!$rawMat) continue;

                $qty = (float) $item['ingredient_qty'];
                $itemCost = $qty * (float) $rawMat->effective_price;

                CogsRecipeItem::create([
                    'cogs_recipe_id' => $cogsRecipe->cogs_recipe_id,
                    'cogs_raw_material_id' => $rawMat->cogs_raw_material_id,
                    'ingredient_qty' => $qty,
                    'ingredient_cost' => $itemCost,
                ]);

                $snapshotItems[] = [
                    'material_name' => $rawMat->name,
                    'qty' => $qty,
                    'unit' => $rawMat->unit,
                    'effective_price' => $rawMat->effective_price,
                    'item_cost' => $itemCost,
                ];
            }

            $cogsRecipe->recalculateCost();

            // Audit Trail History
            CogsRecipeHistory::create([
                'cogs_recipe_id' => $cogsRecipe->cogs_recipe_id,
                'outlet_id' => $cogsRecipe->outlet_id,
                'recipe_name' => $cogsRecipe->recipe_name,
                'target_food_cost' => $cogsRecipe->target_food_cost,
                'estimated_cogs' => $cogsRecipe->estimated_cogs,
                'suggested_price' => $cogsRecipe->suggested_price,
                'snapshot_items_json' => $snapshotItems,
                'action_type' => 'update',
                'changed_by' => 'Admin',
                'effective_date' => now(),
                'history_remark' => 'Mengubah takaran/resep standar HPP',
                'updated_by' => 'admin',
            ]);
        });

        return redirect()->route('admin.keuangan.cogs-recipe.index')
            ->with('success', 'Resep standar HPP berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $cogsRecipe = $id instanceof CogsRecipe ? $id : CogsRecipe::find($id);
        if (!$cogsRecipe || $cogsRecipe->delete_status) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        $cogsRecipe->update(['delete_status' => 1, 'updated_by' => 'admin']);

        // Audit Trail History
        CogsRecipeHistory::create([
            'cogs_recipe_id' => $cogsRecipe->cogs_recipe_id,
            'outlet_id' => $cogsRecipe->outlet_id,
            'recipe_name' => $cogsRecipe->recipe_name,
            'target_food_cost' => $cogsRecipe->target_food_cost,
            'estimated_cogs' => $cogsRecipe->estimated_cogs,
            'suggested_price' => $cogsRecipe->suggested_price,
            'snapshot_items_json' => [],
            'action_type' => 'delete',
            'changed_by' => 'Admin',
            'effective_date' => now(),
            'history_remark' => 'Menghapus resep standar HPP',
            'updated_by' => 'admin',
        ]);

        return response()->json(['success' => true, 'message' => 'Resep berhasil dihapus.']);
    }
}
