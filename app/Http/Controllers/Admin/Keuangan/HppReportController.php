<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Keuangan\CogsRecipe;
use App\Models\Admin\Keuangan\CogsWasteLog;
use App\Models\Admin\Keuangan\HppFinancialReport;
use App\Models\Admin\OrderBundle;
use App\Models\Admin\Product;
use App\Models\Admin\Transaction;
use App\Models\Admin\TransactionItem;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HppReportController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));

        $company = Company::where('delete_status', 0)->first();
        $companyId = $company ? $company->company_id : null;

        // Ambil data laporan tersimpan dari DB (jika ada)
        $existingReport = HppFinancialReport::where('company_id', $companyId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        // 1. Total Revenue Omzet Kasir (dari transaksi berstatus success)
        $totalRevenue = (float) Transaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->where('transaction_status', 'success')
            ->where('delete_status', 0)
            ->sum('transaction_grand_total');

        // 2. Total Waste Cost bulan ini
        $totalWasteCost = (float) CogsWasteLog::whereYear('loss_date', $year)
            ->whereMonth('loss_date', $month)
            ->where('delete_status', 0)
            ->sum('waste_cost');

        // 3. Estimasi COGS & Breakdown Per Menu Terjual
        $transactionIds = Transaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->where('transaction_status', 'success')
            ->where('delete_status', 0)
            ->pluck('transaction_id');

        $transactionItems = TransactionItem::whereIn('transaction_id', $transactionIds)
            ->where('delete_status', 0)
            ->get();

        $orderBundles = OrderBundle::with('bundle.products')->whereIn('transaction_id', $transactionIds)
            ->where('delete_status', 0)
            ->get();

        $allRecipes = CogsRecipe::with('items.rawMaterial')->where('delete_status', 0)->get();
        $singleRecipe = $allRecipes->count() === 1 ? $allRecipes->first() : null;

        $totalCogsEstimated = 0;
        $productBreakdownMap = [];

        // 3a. Process Single Product Items
        foreach ($transactionItems as $ti) {
            $prodId = $ti->product_id;
            $prodName = $ti->product_name;

            // Match recipe
            $recipe = $allRecipes->firstWhere('product_id', $prodId);
            if (!$recipe && $prodName) {
                $recipe = $allRecipes->first(function ($r) use ($prodName) {
                    return stripos($r->recipe_name, $prodName) !== false ||
                           stripos($prodName, $r->recipe_name) !== false;
                });
            }
            if (!$recipe && $singleRecipe) {
                $recipe = $singleRecipe;
            }

            $unitCogs = $recipe ? (float) $recipe->estimated_cogs : 0;
            $itemQty = (int) $ti->qty;
            $itemSubtotal = (float) $ti->subtotal;
            $itemCogsTotal = $unitCogs * $itemQty;

            $totalCogsEstimated += $itemCogsTotal;

            // Grouping per product name
            $key = $prodId ? 'id_' . $prodId : 'name_' . \Illuminate\Support\Str::slug($prodName);
            if (!isset($productBreakdownMap[$key])) {
                $productBreakdownMap[$key] = [
                    'product_id' => $prodId,
                    'product_name' => $prodName,
                    'recipe_name' => $recipe ? $recipe->recipe_name : null,
                    'qty_sold' => 0,
                    'unit_price' => (float) $ti->price,
                    'total_revenue' => 0,
                    'unit_cogs' => $unitCogs,
                    'total_cogs' => 0,
                    'gross_profit' => 0,
                    'margin_percent' => 0,
                    'has_recipe' => $recipe ? true : false,
                    'recipe_items' => $recipe ? $recipe->items->map(function ($item) {
                        return [
                            'material_name' => $item->rawMaterial ? $item->rawMaterial->name : '-',
                            'unit' => $item->rawMaterial ? $item->rawMaterial->unit : '-',
                            'effective_price' => $item->rawMaterial ? (float)$item->rawMaterial->effective_price : 0,
                            'ingredient_qty' => (float)$item->ingredient_qty,
                            'ingredient_cost' => (float)$item->ingredient_cost,
                        ];
                    })->toArray() : [],
                ];
            }

            $productBreakdownMap[$key]['qty_sold'] += $itemQty;
            $productBreakdownMap[$key]['total_revenue'] += $itemSubtotal;
            $productBreakdownMap[$key]['total_cogs'] += $itemCogsTotal;
            $productBreakdownMap[$key]['gross_profit'] = $productBreakdownMap[$key]['total_revenue'] - $productBreakdownMap[$key]['total_cogs'];
            $productBreakdownMap[$key]['margin_percent'] = $productBreakdownMap[$key]['total_revenue'] > 0
                ? ($productBreakdownMap[$key]['gross_profit'] / $productBreakdownMap[$key]['total_revenue']) * 100
                : 0;
        }

        // 3b. Process Order Bundles (Paket Hemat / Combo)
        foreach ($orderBundles as $ob) {
            $bundleName = $ob->bundle_name ?? 'Paket Bundle';
            $bundleQty = (int) ($ob->quantity ?? 1);
            $bundleSubtotal = (float) $ob->subtotal;
            $bundlePrice = (float) $ob->bundle_price;

            // Calculate accumulated COGS from component products in bundle
            $unitCogs = 0;
            $compositeRecipeItems = [];
            $recipeNames = [];

            if ($ob->bundle && $ob->bundle->products) {
                foreach ($ob->bundle->products as $p) {
                    $pRecipe = $allRecipes->firstWhere('product_id', $p->product_id);
                    if ($pRecipe) {
                        $unitCogs += (float) $pRecipe->estimated_cogs;
                        $recipeNames[] = $pRecipe->recipe_name;
                        foreach ($pRecipe->items as $item) {
                            $compositeRecipeItems[] = [
                                'material_name' => $item->rawMaterial ? $item->rawMaterial->name : '-',
                                'unit' => $item->rawMaterial ? $item->rawMaterial->unit : '-',
                                'effective_price' => $item->rawMaterial ? (float)$item->rawMaterial->effective_price : 0,
                                'ingredient_qty' => (float)$item->ingredient_qty,
                                'ingredient_cost' => (float)$item->ingredient_cost,
                            ];
                        }
                    }
                }
            }

            $bundleCogsTotal = $unitCogs * $bundleQty;
            $totalCogsEstimated += $bundleCogsTotal;

            $key = 'bundle_' . ($ob->bundle_id ?? \Illuminate\Support\Str::slug($bundleName));
            if (!isset($productBreakdownMap[$key])) {
                $productBreakdownMap[$key] = [
                    'product_id' => null,
                    'product_name' => '[Paket] ' . $bundleName,
                    'recipe_name' => !empty($recipeNames) ? implode(' + ', $recipeNames) : 'Komposisi Produk Bundle',
                    'qty_sold' => 0,
                    'unit_price' => $bundlePrice,
                    'total_revenue' => 0,
                    'unit_cogs' => $unitCogs,
                    'total_cogs' => 0,
                    'gross_profit' => 0,
                    'margin_percent' => 0,
                    'has_recipe' => $unitCogs > 0,
                    'recipe_items' => $compositeRecipeItems,
                ];
            }

            $productBreakdownMap[$key]['qty_sold'] += $bundleQty;
            $productBreakdownMap[$key]['total_revenue'] += $bundleSubtotal;
            $productBreakdownMap[$key]['total_cogs'] += $bundleCogsTotal;
            $productBreakdownMap[$key]['gross_profit'] = $productBreakdownMap[$key]['total_revenue'] - $productBreakdownMap[$key]['total_cogs'];
            $productBreakdownMap[$key]['margin_percent'] = $productBreakdownMap[$key]['total_revenue'] > 0
                ? ($productBreakdownMap[$key]['gross_profit'] / $productBreakdownMap[$key]['total_revenue']) * 100
                : 0;
        }

        $productBreakdown = collect(array_values($productBreakdownMap))->sortByDesc('qty_sold');

        // Ambil biaya Gaji & Overhead dari DB (atau 0 jika belum diinput)
        $totalLaborCost = $existingReport ? (float) $existingReport->total_labor_cost : 0.00;
        $totalOverheadCost = $existingReport ? (float) $existingReport->total_overhead_cost : 0.00;
        $notes = $existingReport ? $existingReport->notes : null;

        $grossProfit = $totalRevenue - $totalCogsEstimated;
        $netProfit = $grossProfit - $totalWasteCost - $totalLaborCost - $totalOverheadCost;

        $totalPaidTransactions = Transaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->where('transaction_status', 'success')
            ->where('delete_status', 0)
            ->count();

        $grossMarginPercent = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;
        $netMarginPercent = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        // Auto sync / update ke database hpp_financial_reports
        $report = HppFinancialReport::updateOrCreate(
            ['company_id' => $companyId, 'year' => $year, 'month' => $month],
            [
                'total_revenue' => $totalRevenue,
                'total_cogs_estimated' => $totalCogsEstimated,
                'total_waste_cost' => $totalWasteCost,
                'total_labor_cost' => $totalLaborCost,
                'total_overhead_cost' => $totalOverheadCost,
                'gross_profit' => $grossProfit,
                'net_profit_estimated' => $netProfit,
                'notes' => $notes,
                'updated_by' => 'admin',
            ]
        );

        // 4. Total Pembelian Bahan Mentah dari PO (Purchase Order) bulan ini
        $totalPoPurchases = (float) \App\Models\Admin\PurchaseOrder::whereYear('po_date', $year)
            ->whereMonth('po_date', $month)
            ->whereIn('po_status', ['ordered', 'partial', 'completed'])
            ->where('delete_status', 0)
            ->sum('po_total_amount');

        $totalPoReceivedCost = (float) \App\Models\Admin\PurchaseReceivingItem::whereHas('receiving', function ($q) use ($year, $month) {
            $q->whereYear('receiving_date', $year)->whereMonth('receiving_date', $month);
        })->where('delete_status', 0)->sum('subtotal');

        return view('admin.keuangan.hpp-report.index', compact(
            'year', 'month', 'totalRevenue', 'totalCogsEstimated',
            'totalWasteCost', 'totalLaborCost', 'totalOverheadCost',
            'grossProfit', 'netProfit', 'grossMarginPercent', 'netMarginPercent',
            'totalPaidTransactions', 'notes', 'report', 'productBreakdown',
            'totalPoPurchases', 'totalPoReceivedCost'
        ));
    }

    public function storeOperational(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer',
            'total_labor_cost' => 'required|numeric|min:0',
            'total_overhead_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $company = Company::where('delete_status', 0)->first();
        $companyId = $company ? $company->company_id : null;

        $year = (int) $request->year;
        $month = (int) $request->month;

        $report = HppFinancialReport::where('company_id', $companyId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        $totalRevenue = $report ? (float) $report->total_revenue : 0;
        $totalCogsEstimated = $report ? (float) $report->total_cogs_estimated : 0;
        $totalWasteCost = $report ? (float) $report->total_waste_cost : 0;

        $totalLaborCost = (float) $request->total_labor_cost;
        $totalOverheadCost = (float) $request->total_overhead_cost;

        $grossProfit = $totalRevenue - $totalCogsEstimated;
        $netProfit = $grossProfit - $totalWasteCost - $totalLaborCost - $totalOverheadCost;

        HppFinancialReport::updateOrCreate(
            ['company_id' => $companyId, 'year' => $year, 'month' => $month],
            [
                'total_revenue' => $totalRevenue,
                'total_cogs_estimated' => $totalCogsEstimated,
                'total_waste_cost' => $totalWasteCost,
                'total_labor_cost' => $totalLaborCost,
                'total_overhead_cost' => $totalOverheadCost,
                'gross_profit' => $grossProfit,
                'net_profit_estimated' => $netProfit,
                'notes' => $request->notes,
                'updated_by' => 'admin',
            ]
        );

        return redirect()->route('admin.keuangan.hpp-report.index', ['year' => $year, 'month' => $month])
            ->with('success', 'Biaya operasional (Gaji & Listrik/Overhead) berhasil disimpan.');
    }
}
