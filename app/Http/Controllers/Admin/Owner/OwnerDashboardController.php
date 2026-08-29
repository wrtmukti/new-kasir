<?php

namespace App\Http\Controllers\Admin\Owner;

use App\Http\Controllers\Controller;
use App\Services\ConsolidatedFinancialService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OwnerDashboardController extends Controller
{
    protected ConsolidatedFinancialService $financialService;

    public function __construct(ConsolidatedFinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index(Request $request)
    {
        $activeOutlets = $this->financialService->getActiveOutlets();

        // Default Date Range: Bulan Ini
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Multi-outlet selector: string/array
        $rawOutletIds = $request->input('outlet_ids', []);
        $selectedOutletIds = is_array($rawOutletIds) ? array_filter($rawOutletIds) : ($rawOutletIds ? [$rawOutletIds] : []);

        // 1. Ambil KPI Konsolidasi
        $kpis = $this->financialService->getConsolidatedKPIs($startDate, $endDate, $selectedOutletIds);

        // 2. Ambil Leaderboard Cabang
        $leaderboard = $this->financialService->getOutletLeaderboard($startDate, $endDate, $selectedOutletIds);

        // 3. Ambil Chart Tren Omzet Multi-Cabang
        $trendChart = $this->financialService->getMultiBranchTrendChart($startDate, $endDate, $selectedOutletIds);

        return view('admin.owner.dashboard', compact(
            'activeOutlets',
            'startDate',
            'endDate',
            'selectedOutletIds',
            'kpis',
            'leaderboard',
            'trendChart'
        ));
    }
}
