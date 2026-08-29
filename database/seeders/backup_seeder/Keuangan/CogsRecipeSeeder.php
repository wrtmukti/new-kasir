<?php

namespace Database\Seeders\Keuangan;

use Illuminate\Database\Seeder;
use App\Models\Admin\Keuangan\RawStockMaterial;
use App\Models\Admin\Keuangan\CogsRecipe;
use App\Models\Admin\Keuangan\CogsRecipeHistory;
use App\Models\Admin\Keuangan\CogsRecipeItem;
use App\Models\Admin\Product;
use App\Models\Admin\Outlet;

class CogsRecipeSeeder extends Seeder
{
    public function run(): void
    {
        $outlet = Outlet::where('delete_status', 0)->first();
        $outletId = $outlet ? $outlet->outlet_id : null;

        $materials = RawStockMaterial::where('delete_status', 0)->pluck('raw_stock_material_id', 'raw_material_code');

        $ayam = $materials['RAW-AYAM01'] ?? null;
        $sapi = $materials['RAW-SAPI01'] ?? null;
        $seafood = $materials['RAW-SEAFOOD01'] ?? null;
        $beras = $materials['RAW-BERAS01'] ?? null;
        $minyak = $materials['RAW-MINYAK01'] ?? null;
        $cabe = $materials['RAW-CABE01'] ?? null;
        $bawang = $materials['RAW-BAWANG01'] ?? null;
        $telur = $materials['RAW-TELUR01'] ?? null;
        $tepung = $materials['RAW-TEPUNG01'] ?? null;
        $kopi = $materials['RAW-KOPI01'] ?? null;
        $teh = $materials['RAW-TEH01'] ?? null;
        $susu = $materials['RAW-SUSU01'] ?? null;
        $syrup = $materials['RAW-SYRUP01'] ?? null;
        $buah = $materials['RAW-BUAH01'] ?? null;
        $gelas = $materials['RAW-GLN01'] ?? null;
        $plastik = $materials['RAW-PLST01'] ?? null;
        $box = $materials['RAW-BOX01'] ?? null;

        // Formula takaran per product code
        $recipeFormulas = [
            'PRD-001' => ['name' => 'Resep Nasi Goreng Spesial', 'items' => [[$beras, 0.20], [$minyak, 0.05], [$cabe, 0.02], [$bawang, 0.01], [$telur, 0.06], [$box, 1]]],
            'PRD-002' => ['name' => 'Resep Mie Ayam Bakso', 'items' => [[$ayam, 0.10], [$bawang, 0.01], [$cabe, 0.01], [$box, 1]]],
            'PRD-003' => ['name' => 'Resep Ayam Geprek', 'items' => [[$ayam, 0.18], [$minyak, 0.10], [$cabe, 0.02], [$beras, 0.15], [$tepung, 0.05], [$box, 1]]],
            'PRD-004' => ['name' => 'Resep Sate Ayam', 'items' => [[$ayam, 0.15], [$beras, 0.10], [$minyak, 0.02], [$box, 1]]],
            'PRD-005' => ['name' => 'Resep Nasi Padang', 'items' => [[$beras, 0.20], [$sapi, 0.12], [$cabe, 0.02], [$bawang, 0.01], [$box, 1]]],
            'PRD-006' => ['name' => 'Resep Kwetiau Goreng', 'items' => [[$seafood, 0.08], [$minyak, 0.05], [$bawang, 0.01], [$cabe, 0.01], [$box, 1]]],
            'PRD-007' => ['name' => 'Resep Bakso Urat', 'items' => [[$sapi, 0.12], [$bawang, 0.01], [$cabe, 0.01], [$box, 1]]],
            'PRD-008' => ['name' => 'Resep Nasi Uduk', 'items' => [[$beras, 0.20], [$ayam, 0.10], [$bawang, 0.01], [$telur, 0.03], [$box, 1]]],
            'PRD-009' => ['name' => 'Resep Mie Goreng Jawa', 'items' => [[$minyak, 0.05], [$bawang, 0.01], [$telur, 0.06], [$box, 1]]],
            'PRD-010' => ['name' => 'Resep Soto Ayam', 'items' => [[$ayam, 0.12], [$beras, 0.10], [$bawang, 0.01], [$box, 1]]],
            'PRD-011' => ['name' => 'Resep Gado-Gado', 'items' => [[$bawang, 0.01], [$cabe, 0.01], [$telur, 0.06], [$box, 1]]],
            'PRD-012' => ['name' => 'Resep Nasi Liwet', 'items' => [[$beras, 0.25], [$ayam, 0.10], [$bawang, 0.01], [$box, 1]]],
            // Minuman
            'PRD-013' => ['name' => 'Resep Es Teh Manis', 'items' => [[$teh, 0.01], [$syrup, 0.03], [$gelas, 1]]],
            'PRD-014' => ['name' => 'Resep Es Jeruk', 'items' => [[$syrup, 0.05], [$gelas, 1]]],
            'PRD-015' => ['name' => 'Resep Kopi Hitam', 'items' => [[$kopi, 0.018], [$gelas, 1]]],
            'PRD-016' => ['name' => 'Resep Kopi Susu', 'items' => [[$kopi, 0.018], [$susu, 0.15], [$syrup, 0.02], [$gelas, 1]]],
            'PRD-017' => ['name' => 'Resep Matcha Latte', 'items' => [[$susu, 0.20], [$syrup, 0.02], [$gelas, 1]]],
            'PRD-018' => ['name' => 'Resep Jus Alpukat', 'items' => [[$buah, 0.15], [$susu, 0.10], [$syrup, 0.03], [$gelas, 1]]],
            'PRD-019' => ['name' => 'Resep Jus Mangga', 'items' => [[$buah, 0.15], [$syrup, 0.03], [$gelas, 1]]],
            'PRD-020' => ['name' => 'Resep Milkshake Coklat', 'items' => [[$susu, 0.20], [$syrup, 0.04], [$gelas, 1]]],
            'PRD-021' => ['name' => 'Resep Teh Tarik', 'items' => [[$teh, 0.015], [$susu, 0.10], [$gelas, 1]]],
            'PRD-022' => ['name' => 'Resep Air Mineral', 'items' => [[$gelas, 1]]],
            // Snack
            'PRD-023' => ['name' => 'Resep Kentang Goreng', 'items' => [[$minyak, 0.03], [$plastik, 1]]],
            'PRD-024' => ['name' => 'Resep Pisang Goreng', 'items' => [[$tepung, 0.05], [$minyak, 0.02], [$plastik, 1]]],
            'PRD-025' => ['name' => 'Resep Tahu Crispy', 'items' => [[$tepung, 0.04], [$minyak, 0.03], [$plastik, 1]]],
            'PRD-026' => ['name' => 'Resep Tempe Mendoan', 'items' => [[$tepung, 0.05], [$minyak, 0.03], [$bawang, 0.005], [$plastik, 1]]],
            'PRD-027' => ['name' => 'Resep Cireng Isi', 'items' => [[$tepung, 0.08], [$minyak, 0.03], [$ayam, 0.03], [$plastik, 1]]],
            'PRD-028' => ['name' => 'Resep Siomay', 'items' => [[$seafood, 0.08], [$tepung, 0.03], [$bawang, 0.005], [$plastik, 1]]],
            'PRD-029' => ['name' => 'Resep Risol Mayo', 'items' => [[$tepung, 0.05], [$minyak, 0.02], [$telur, 0.03], [$plastik, 1]]],
            'PRD-030' => ['name' => 'Resep Lumpia', 'items' => [[$ayam, 0.05], [$tepung, 0.03], [$minyak, 0.02], [$plastik, 1]]],
        ];

        $products = Product::where('delete_status', 0)->get();

        foreach ($products as $prod) {
            $formula = $recipeFormulas[$prod->product_code] ?? [
                'name' => 'Resep Standar ' . $prod->product_name,
                'items' => [[$plastik ?? $gelas, 1]]
            ];

            // Cek apakah resep sudah ada
            $recipe = CogsRecipe::where('product_id', $prod->product_id)->first();
            if (!$recipe) {
                $recipe = CogsRecipe::create([
                    'outlet_id' => $outletId,
                    'product_id' => $prod->product_id,
                    'recipe_name' => $formula['name'],
                    'target_food_cost' => 30.00,
                    'notes' => 'Resep standar HPP terhubung ke ' . $prod->product_name,
                    'created_by' => 'seeder',
                ]);
            }

            // Clean & insert items
            CogsRecipeItem::where('cogs_recipe_id', $recipe->cogs_recipe_id)->delete();

            $snapshotItems = [];
            foreach ($formula['items'] as $itemArr) {
                $materialId = $itemArr[0] ?? null;
                $qty = (float) ($itemArr[1] ?? 0);
                if (!$materialId || $qty <= 0) continue;

                $rawMat = RawStockMaterial::find($materialId);
                if (!$rawMat) continue;

                $itemCost = $qty * (float) $rawMat->effective_price;

                CogsRecipeItem::create([
                    'cogs_recipe_id' => $recipe->cogs_recipe_id,
                    'raw_stock_material_id' => $rawMat->raw_stock_material_id,
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

            CogsRecipeHistory::create([
                'cogs_recipe_id' => $recipe->cogs_recipe_id,
                'outlet_id' => $outletId,
                'recipe_name' => $recipe->recipe_name,
                'target_food_cost' => $recipe->target_food_cost,
                'estimated_cogs' => $recipe->estimated_cogs,
                'suggested_price' => $recipe->suggested_price,
                'snapshot_items_json' => $snapshotItems,
                'action_type' => 'create',
                'changed_by' => 'Seeder',
                'effective_date' => now(),
                'history_remark' => 'Seeder resep terikat dengan produk ' . $prod->product_name,
                'created_by' => 'seeder',
            ]);
        }
    }
}
