<?php

namespace App\Services;

use App\Models\Admin\Outlet;
use App\Models\Admin\Transaction;
use App\Models\Admin\TransactionItem;
use App\Models\Admin\OrderBundle;
use App\Models\Admin\DailyClosing;
use App\Models\Admin\CashDrawerLog;
use App\Models\Admin\PurchaseOrder;
use App\Models\Admin\Keuangan\CogsRecipe;
use App\Models\Admin\Keuangan\CogsWasteLog;
use App\Models\Admin\Keuangan\HppFinancialReport;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ConsolidatedFinancialService
{
    /**
     * Ambil daftar semua outlet aktif untuk filter dropdown
     */
    public function getActiveOutlets(): Collection
    {
        return Outlet::where('delete_status', 0)->orderBy('outlet_name', 'asc')->get();
    }

    /**
     * Normalisasi filter ID outlet agar string kosong disaring
     */
    protected function normalizeOutletIds(array $selectedOutletIds = []): array
    {
        $filtered = array_filter($selectedOutletIds, fn($id) => !empty($id));
        if (!empty($filtered)) {
            return array_values($filtered);
        }
        return Outlet::where('delete_status', 0)->pluck('outlet_id')->toArray();
    }

    /**
     * Hitung KPI Finansial Konsolidasi Gabungan
     */
    public function getConsolidatedKPIs(string $startDate, string $endDate, array $selectedOutletIds = []): array
    {
        $outletIds = $this->normalizeOutletIds($selectedOutletIds);

        // 1. Total Revenue Omzet Kasir
        $transactionsQuery = Transaction::whereIn('outlet_id', $outletIds)
            ->where('transaction_status', 'success')
            ->where('delete_status', 0)
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        $totalRevenue = (float) (clone $transactionsQuery)->sum('transaction_grand_total');
        $totalOrdersCount = (int) (clone $transactionsQuery)->count();

        // Cash vs Non-Cash Sales
        $totalSalesCash = (float) (clone $transactionsQuery)->where(function ($q) {
            $q->whereHas('payment', function ($p) {
                $p->where('payment_metode', 'LIKE', '%cash%')
                  ->orWhere('payment_metode', 'LIKE', '%tunai%');
            })->orWhereDoesntHave('payment');
        })->sum('transaction_grand_total');

        $totalSalesNonCash = (float) (clone $transactionsQuery)->whereHas('payment', function ($p) {
            $p->where('payment_metode', 'NOT LIKE', '%cash%')
              ->where('payment_metode', 'NOT LIKE', '%tunai%');
        })->sum('transaction_grand_total');

        // 2. Total COGS Resep Murni (Theoretical COGS)
        $transactionIds = (clone $transactionsQuery)->pluck('transaction_id');
        $transactionItems = TransactionItem::whereIn('transaction_id', $transactionIds)
            ->where('delete_status', 0)
            ->get();

        $allRecipes = CogsRecipe::with('items.rawMaterial')->where('delete_status', 0)->get();
        $singleRecipe = $allRecipes->count() === 1 ? $allRecipes->first() : null;

        $totalCogs = 0;
        foreach ($transactionItems as $ti) {
            $prodId = $ti->product_id;
            $prodName = $ti->product_name;

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
            $totalCogs += $unitCogs * (int) $ti->qty;
        }

        // 3. Gross Profit & Gross Margin %
        $grossProfit = $totalRevenue - $totalCogs;
        $grossMarginPercent = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        // 4. Waste Cost
        $totalWasteCost = (float) CogsWasteLog::whereIn('outlet_id', $outletIds)
            ->where('delete_status', 0)
            ->whereBetween('loss_date', [$startDate, $endDate])
            ->sum('waste_cost');

        // 5. Operating Expenses (Labor & Overhead)
        $startCarbon = Carbon::parse($startDate);
        $hppReports = HppFinancialReport::whereIn('outlet_id', $outletIds)
            ->where('year', $startCarbon->year)
            ->where('month', $startCarbon->month)
            ->get();

        $totalLaborCost = (float) $hppReports->sum('total_labor_cost');
        $totalOverheadCost = (float) $hppReports->sum('total_overhead_cost');
        $totalOperatingExpense = $totalLaborCost + $totalOverheadCost;

        // 6. Net Profit & Net Margin %
        $netProfit = $grossProfit - $totalWasteCost - $totalOperatingExpense;
        $netMarginPercent = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        // 7. Cash Drawer Top-ups (Cash In Laci)
        $totalDrawerCashIn = (float) CashDrawerLog::whereIn('outlet_id', $outletIds)
            ->where('type', 'in')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('amount');

        // 8. Total Cash Inflow
        $totalCashInflow = $totalSalesCash + $totalSalesNonCash + $totalDrawerCashIn;

        // 9. PO Supplier Lunas (Cash Outflow)
        $totalPoPaid = (float) PurchaseOrder::whereIn('outlet_id', $outletIds)
            ->where('payment_status', 'paid')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('payment_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->whereNull('payment_date')
                         ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                  });
            })->sum('po_total_amount');

        // 10. Petty Cash Kasir Keluar
        $totalDrawerCashOut = (float) CashDrawerLog::whereIn('outlet_id', $outletIds)
            ->where('type', 'out')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('amount');

        // 11. Total Operating Outflow
        $totalOperatingOutflow = $totalPoPaid + $totalDrawerCashOut + $totalLaborCost + $totalOverheadCost;

        // 12. Net Cash Flow (Surplus / Defisit)
        $netCashFlow = $totalCashInflow - $totalOperatingOutflow;

        // 13. Total Safe Deposit (Uang Disetor Kasir ke Brankas)
        $totalSafeDeposit = (float) DailyClosing::whereIn('outlet_id', $outletIds)
            ->where('status', 'closed')
            ->whereBetween('business_date', [$startDate, $endDate])
            ->sum('cash_deposit_to_safe');

        // 14. Total Hutang PO Supplier Tempo (Belum Lunas)
        $totalPoUnpaid = (float) PurchaseOrder::whereIn('outlet_id', $outletIds)
            ->where('payment_status', 'unpaid')
            ->where('po_status', '!=', 'cancelled')
            ->sum('po_total_amount');

        return [
            'total_revenue' => $totalRevenue,
            'total_orders_count' => $totalOrdersCount,
            'total_sales_cash' => $totalSalesCash,
            'total_sales_non_cash' => $totalSalesNonCash,
            'total_cogs' => $totalCogs,
            'gross_profit' => $grossProfit,
            'gross_margin_percent' => $grossMarginPercent,
            'total_waste_cost' => $totalWasteCost,
            'total_labor_cost' => $totalLaborCost,
            'total_overhead_cost' => $totalOverheadCost,
            'total_operating_expense' => $totalOperatingExpense,
            'net_profit' => $netProfit,
            'net_margin_percent' => $netMarginPercent,
            'total_drawer_cash_in' => $totalDrawerCashIn,
            'total_cash_inflow' => $totalCashInflow,
            'total_po_paid' => $totalPoPaid,
            'total_drawer_cash_out' => $totalDrawerCashOut,
            'total_operating_outflow' => $totalOperatingOutflow,
            'net_cash_flow' => $netCashFlow,
            'total_safe_deposit' => $totalSafeDeposit,
            'total_po_unpaid' => $totalPoUnpaid,
            'outlet_count_analyzed' => count($outletIds),
        ];
    }

    /**
     * Ambil Leaderboard & Ranking Performa Outlet
     */
    public function getOutletLeaderboard(string $startDate, string $endDate, array $selectedOutletIds = []): array
    {
        $outletIds = $this->normalizeOutletIds($selectedOutletIds);
        $outlets = Outlet::where('delete_status', 0)
            ->whereIn('outlet_id', $outletIds)
            ->get();

        $allRecipes = CogsRecipe::with('items.rawMaterial')->where('delete_status', 0)->get();
        $singleRecipe = $allRecipes->count() === 1 ? $allRecipes->first() : null;

        $leaderboard = [];

        foreach ($outlets as $outlet) {
            $outletId = $outlet->outlet_id;

            // Omzet & Transaksi
            $transactions = Transaction::where('outlet_id', $outletId)
                ->where('transaction_status', 'success')
                ->where('delete_status', 0)
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->get();

            $revenue = (float) $transactions->sum('transaction_grand_total');
            $ordersCount = $transactions->count();

            // COGS
            $trxIds = $transactions->pluck('transaction_id');
            $trxItems = TransactionItem::whereIn('transaction_id', $trxIds)->where('delete_status', 0)->get();

            $cogs = 0;
            foreach ($trxItems as $ti) {
                $prodId = $ti->product_id;
                $prodName = $ti->product_name;

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
                $cogs += $unitCogs * (int) $ti->qty;
            }

            $grossProfit = $revenue - $cogs;
            $grossMarginPercent = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;

            // Waste
            $wasteLoss = (float) CogsWasteLog::where('outlet_id', $outletId)
                ->where('delete_status', 0)
                ->whereBetween('loss_date', [$startDate, $endDate])
                ->sum('waste_cost');

            // Labor & Overhead
            $startCarbon = Carbon::parse($startDate);
            $hppReport = HppFinancialReport::where('outlet_id', $outletId)
                ->where('year', $startCarbon->year)
                ->where('month', $startCarbon->month)
                ->first();

            $laborCost = (float) ($hppReport?->total_labor_cost ?? 0);
            $overheadCost = (float) ($hppReport?->total_overhead_cost ?? 0);
            $netProfit = $grossProfit - $wasteLoss - ($laborCost + $overheadCost);
            $netMarginPercent = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;

            // Setoran Brankas
            $safeDeposit = (float) DailyClosing::where('outlet_id', $outletId)
                ->where('status', 'closed')
                ->whereBetween('business_date', [$startDate, $endDate])
                ->sum('cash_deposit_to_safe');

            // Status Shift Terkini
            $activeShift = DailyClosing::where('outlet_id', $outletId)
                ->where('status', 'open')
                ->first();

            $leaderboard[] = [
                'outlet_id' => $outlet->outlet_id,
                'outlet_name' => $outlet->outlet_name,
                'outlet_code' => $outlet->outlet_code,
                'outlet_branch' => $outlet->outlet_branch ?? 'Pusat',
                'revenue' => $revenue,
                'orders_count' => $ordersCount,
                'cogs' => $cogs,
                'gross_profit' => $grossProfit,
                'gross_margin_percent' => $grossMarginPercent,
                'waste_loss' => $wasteLoss,
                'net_profit' => $netProfit,
                'net_margin_percent' => $netMarginPercent,
                'safe_deposit' => $safeDeposit,
                'has_active_shift' => $activeShift ? true : false,
                'active_shift_name' => $activeShift ? $activeShift->shift_name : null,
            ];
        }

        // Urutkan dari Omzet Tertinggi (Revenue DESC)
        usort($leaderboard, function ($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });

        return $leaderboard;
    }

    /**
     * Ambil Data Multi-Branch Trend Chart (Time Series)
     */
    public function getMultiBranchTrendChart(string $startDate, string $endDate, array $selectedOutletIds = []): array
    {
        $outletIds = $this->normalizeOutletIds($selectedOutletIds);
        $outlets = Outlet::where('delete_status', 0)
            ->whereIn('outlet_id', $outletIds)
            ->get();

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $dateLabels = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $dateLabels[] = $current->format('Y-m-d');
            $current->addDay();
        }

        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6', '#06b6d4'];
        $datasets = [];

        foreach ($outlets as $index => $outlet) {
            $outletId = $outlet->outlet_id;
            $color = $colors[$index % count($colors)];

            $dailyRevenueMap = Transaction::where('outlet_id', $outletId)
                ->where('transaction_status', 'success')
                ->where('delete_status', 0)
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->selectRaw('DATE(transaction_date) as date_val, SUM(transaction_grand_total) as daily_rev')
                ->groupBy('date_val')
                ->pluck('daily_rev', 'date_val')
                ->toArray();

            $dataPoints = [];
            foreach ($dateLabels as $dt) {
                $dataPoints[] = (float) ($dailyRevenueMap[$dt] ?? 0);
            }

            $datasets[] = [
                'label' => $outlet->outlet_name,
                'data' => $dataPoints,
                'borderColor' => $color,
                'backgroundColor' => $color . '20',
                'borderWidth' => 2.5,
                'tension' => 0.35,
                'fill' => true,
            ];
        }

        // Formatted readable labels for UI
        $readableLabels = array_map(function ($dt) {
            return Carbon::parse($dt)->translatedFormat('d M');
        }, $dateLabels);

        return [
            'labels' => $readableLabels,
            'raw_dates' => $dateLabels,
            'datasets' => $datasets,
        ];
    }

    /**
     * Benchmark HPP Resep Antar Cabang & Deteksi Anomali Pemborosan Porsi
     */
    public function getCrossBranchHppBenchmark(string $startDate, string $endDate, array $selectedOutletIds = []): array
    {
        $outletIds = $this->normalizeOutletIds($selectedOutletIds);
        $outlets = Outlet::where('delete_status', 0)
            ->whereIn('outlet_id', $outletIds)
            ->get();

        $allRecipes = CogsRecipe::with('items.rawMaterial')->where('delete_status', 0)->get();

        $benchmarkItems = [];

        foreach ($allRecipes as $recipe) {
            $prodId = $recipe->product_id;
            $recipeName = $recipe->recipe_name;
            $unitCogs = (float) $recipe->estimated_cogs;

            $outletStats = [];
            $allCogsMargins = [];

            foreach ($outlets as $outlet) {
                $outletId = $outlet->outlet_id;

                $trxItems = TransactionItem::whereHas('transaction', function ($q) use ($outletId, $startDate, $endDate) {
                    $q->where('outlet_id', $outletId)
                      ->where('transaction_status', 'success')
                      ->where('delete_status', 0)
                      ->whereBetween('transaction_date', [$startDate, $endDate]);
                })
                ->when($prodId, fn($q) => $q->where('product_id', $prodId))
                ->when(!$prodId, fn($q) => $q->where('product_name', 'LIKE', '%' . $recipeName . '%'))
                ->where('delete_status', 0)
                ->get();

                $qtySold = (int) $trxItems->sum('qty');
                $revSold = (float) $trxItems->sum('subtotal');
                $totalCogsSold = $qtySold * $unitCogs;
                $avgPrice = $qtySold > 0 ? $revSold / $qtySold : (float) $recipe->target_selling_price;
                $cogsPercent = $avgPrice > 0 ? ($unitCogs / $avgPrice) * 100 : 0;

                $outletStats[$outletId] = [
                    'outlet_name' => $outlet->outlet_name,
                    'qty_sold' => $qtySold,
                    'total_revenue' => $revSold,
                    'unit_cogs' => $unitCogs,
                    'cogs_percent' => $cogsPercent,
                ];

                if ($qtySold > 0) {
                    $allCogsMargins[] = $cogsPercent;
                }
            }

            // Hitung Deviasi Anomali (Apakah ada selisih > 5% antar cabang)
            $isAnomaly = false;
            $anomalyMessage = null;
            if (count($allCogsMargins) > 1) {
                $maxMargin = max($allCogsMargins);
                $minMargin = min($allCogsMargins);
                if (($maxMargin - $minMargin) > 5) {
                    $isAnomaly = true;
                    $anomalyMessage = "Deviasi Margin " . number_format($maxMargin - $minMargin, 1) . "% antar cabang!";
                }
            }

            $benchmarkItems[] = [
                'recipe_id' => $recipe->cogs_recipe_id,
                'recipe_name' => $recipeName,
                'standard_cogs' => $unitCogs,
                'target_price' => (float) $recipe->target_selling_price,
                'is_anomaly' => $isAnomaly,
                'anomaly_message' => $anomalyMessage,
                'outlet_breakdown' => $outletStats,
            ];
        }

        return $benchmarkItems;
    }

    /**
     * Audit Kasir, Selisih Kas & Bahan Terbuang (Waste)
     */
    public function getAuditCenterData(string $startDate, string $endDate, array $selectedOutletIds = []): array
    {
        $outletIds = $this->normalizeOutletIds($selectedOutletIds);

        // 1. Audit Selisih Kasir saat Tutup Shift
        $cashierClosings = DailyClosing::with('outlet')
            ->whereIn('outlet_id', $outletIds)
            ->where('status', 'closed')
            ->whereBetween('business_date', [$startDate, $endDate])
            ->orderBy('cash_difference', 'asc') // Menampilkan selisih minus (shortage) paling atas
            ->get();

        // 2. Audit Kerugian Dapur (Waste Logs)
        $wasteLogs = CogsWasteLog::with('outlet', 'rawMaterial')
            ->whereIn('outlet_id', $outletIds)
            ->where('delete_status', 0)
            ->whereBetween('loss_date', [$startDate, $endDate])
            ->orderBy('waste_cost', 'desc')
            ->get();

        return [
            'cashier_closings' => $cashierClosings,
            'waste_logs' => $wasteLogs,
            'total_shortage' => (float) $cashierClosings->where('cash_difference', '<', 0)->sum('cash_difference'),
            'total_overage' => (float) $cashierClosings->where('cash_difference', '>', 0)->sum('cash_difference'),
            'total_waste_loss' => (float) $wasteLogs->sum('waste_cost'),
        ];
    }

    /**
     * Rekap Setoran Brankas Kas & Kalender Hutang PO Supplier
     */
    public function getCashAndDebtCenterData(string $startDate, string $endDate, array $selectedOutletIds = []): array
    {
        $outletIds = $this->normalizeOutletIds($selectedOutletIds);

        // 1. Live Safe Deposits per Outlet
        $safeDeposits = DailyClosing::with('outlet')
            ->whereIn('outlet_id', $outletIds)
            ->where('status', 'closed')
            ->whereBetween('business_date', [$startDate, $endDate])
            ->orderBy('business_date', 'desc')
            ->get();

        // 2. Kalender Hutang PO Supplier Tempo (Semua Cabang)
        $unpaidPurchaseOrders = PurchaseOrder::with('outlet', 'supplier')
            ->whereIn('outlet_id', $outletIds)
            ->where('payment_status', 'unpaid')
            ->where('po_status', '!=', 'cancelled')
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($po) {
                $dueDate = $po->due_date ? Carbon::parse($po->due_date) : null;
                $today = Carbon::today();

                $urgency = 'normal';
                $daysRemaining = null;

                if ($dueDate) {
                    $diff = $today->diffInDays($dueDate, false);
                    $daysRemaining = $diff;
                    if ($diff < 0) {
                        $urgency = 'overdue';
                    } elseif ($diff <= 1) {
                        $urgency = 'critical';
                    } elseif ($diff <= 3) {
                        $urgency = 'warning';
                    } else {
                        $urgency = 'safe';
                    }
                }

                $po->urgency_level = $urgency;
                $po->days_remaining = $daysRemaining;
                return $po;
            });

        return [
            'safe_deposits' => $safeDeposits,
            'total_safe_deposit' => (float) $safeDeposits->sum('cash_deposit_to_safe'),
            'unpaid_purchase_orders' => $unpaidPurchaseOrders,
            'total_unpaid_po' => (float) $unpaidPurchaseOrders->sum('po_total_amount'),
        ];
    }
}
