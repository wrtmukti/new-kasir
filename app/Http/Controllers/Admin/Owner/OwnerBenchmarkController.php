<?php

namespace App\Http\Controllers\Admin\Owner;

use App\Http\Controllers\Controller;
use App\Services\ConsolidatedFinancialService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OwnerBenchmarkController extends Controller
{
    protected ConsolidatedFinancialService $financialService;

    public function __construct(ConsolidatedFinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index(Request $request)
    {
        $activeOutlets = $this->financialService->getActiveOutlets();

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $rawOutletIds = $request->input('outlet_ids', []);
        $selectedOutletIds = is_array($rawOutletIds) ? array_filter($rawOutletIds) : ($rawOutletIds ? [$rawOutletIds] : []);

        $leaderboard = $this->financialService->getOutletLeaderboard($startDate, $endDate, $selectedOutletIds);
        $benchmarkRecipes = $this->financialService->getCrossBranchHppBenchmark($startDate, $endDate, $selectedOutletIds);

        return view('admin.owner.benchmark', compact(
            'activeOutlets',
            'startDate',
            'endDate',
            'selectedOutletIds',
            'leaderboard',
            'benchmarkRecipes'
        ));
    }
}
