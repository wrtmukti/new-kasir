<?php

namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\Controller;
use App\Models\SysAdmin\Client;
use App\Models\SysAdmin\Plan;
use App\Models\SysAdmin\Subscription;
use App\Models\SysAdmin\DatabaseConnection;
use App\Models\SysAdmin\AuditLog;
use App\Models\SysAdmin\SystemUser;
use App\Services\Client\ClientDatabaseManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class DashboardController extends Controller
{
    /**
     * Tampilkan Executive Dashboard System Admin dengan Multi-Tenant Analytics
     */
    public function index(Request $request)
    {
        $range = $request->input('range', 'all_time');

        // Filter Date Scope
        $startDate = null;
        if ($range === 'today') {
            $startDate = now()->startOfDay();
        } elseif ($range === '7days') {
            $startDate = now()->subDays(7)->startOfDay();
        } elseif ($range === '30days') {
            $startDate = now()->subDays(30)->startOfDay();
        } elseif ($range === 'this_month') {
            $startDate = now()->startOfMonth();
        }

        // 1. KPI Tenant & Clients
        $clientQuery = Client::where('delete_status', 0);
        if ($startDate) {
            $clientQuery->where('created_at', '>=', $startDate);
        }
        $totalClients = $clientQuery->count();
        $activeClients = Client::where('delete_status', 0)->where('status', 'active')->count();
        $trialClients = Client::where('delete_status', 0)->where('status', 'provisioning')->count();
        $suspendedClients = Client::where('delete_status', 0)->whereIn('status', ['suspended', 'cancelled'])->count();

        // 2. Financial SaaS Metrics (MRR & ARR)
        $activeSubs = Subscription::where('delete_status', 0)
            ->where('status', 'active')
            ->with('plan')
            ->get();
        $mrr = $activeSubs->sum(function ($s) {
            return $s->plan ? (float) $s->plan->price_monthly : 0;
        });
        $arr = $mrr * 12;

        // 3. Subscriptions & Expiring Soon
        $totalSubscriptions = Subscription::where('delete_status', 0)->count();
        $expiringSoonSubs = Subscription::where('delete_status', 0)
            ->whereIn('status', ['active', 'trial'])
            ->whereBetween('expired_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->with(['client', 'plan'])
            ->get();
        $expiringSoonCount = $expiringSoonSubs->count();

        // 4. Infrastructure & Database Health
        $totalDatabases = DatabaseConnection::where('delete_status', 0)->count();
        $healthyDatabases = DatabaseConnection::where('delete_status', 0)->where('connection_status', 'connected')->count();
        $avgLatency = round(DatabaseConnection::where('delete_status', 0)->avg('latency_ms') ?? 0, 2);

        // Ping Central DB Health
        $centralHealth = 'Healthy';
        $centralLatencyMs = 0;
        try {
            $t0 = microtime(true);
            DB::connection('central')->select('SELECT 1');
            $centralLatencyMs = round((microtime(true) - $t0) * 1000, 2);
        } catch (Exception $e) {
            $centralHealth = 'Critical';
        }

        // 5. Chart 1: Acquisition Growth Trend (Last 6 Months)
        $growthLabels = [];
        $growthData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthName = $month->translatedFormat('M Y');
            $count = Client::where('delete_status', 0)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $growthLabels[] = $monthName;
            $growthData[] = $count;
        }

        // 6. Chart 2: Plan Distribution
        $plans = Plan::where('delete_status', 0)->where('is_active', 1)->orderBy('sort_order')->get();
        $planLabels = [];
        $planCounts = [];
        $planColors = ['#64748b', '#3b82f6', '#8b5cf6', '#10b981', '#f59e0b'];

        foreach ($plans as $p) {
            $planLabels[] = $p->plan_name;
            $cnt = Subscription::where('delete_status', 0)
                ->where('plan_id', $p->id)
                ->whereIn('status', ['active', 'trial'])
                ->count();
            $planCounts[] = $cnt;
        }

        // 7. Recent Clients & Recent Audit Logs
        $recentClients = Client::where('delete_status', 0)
            ->with(['activeSubscription.plan'])
            ->latest('id')
            ->take(6)
            ->get();

        $recentAuditLogs = AuditLog::latest('created_at')
            ->take(8)
            ->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'kpi' => [
                    'total_clients' => $totalClients,
                    'active_clients' => $activeClients,
                    'trial_clients' => $trialClients,
                    'suspended_clients' => $suspendedClients,
                    'mrr' => number_format($mrr, 0, ',', '.'),
                    'arr' => number_format($arr, 0, ',', '.'),
                    'healthy_dbs' => $healthyDatabases,
                    'total_dbs' => $totalDatabases,
                    'avg_latency' => $avgLatency,
                    'expiring_soon_count' => $expiringSoonCount,
                ],
                'chart_growth' => [
                    'labels' => $growthLabels,
                    'data' => $growthData,
                ],
                'chart_plans' => [
                    'labels' => $planLabels,
                    'data' => $planCounts,
                ],
            ]);
        }

        return view('sys_admin.dashboard.index', [
            'activeMenu' => 'dashboard',
            'range' => $range,
            'kpi' => [
                'total_clients' => $totalClients,
                'active_clients' => $activeClients,
                'trial_clients' => $trialClients,
                'suspended_clients' => $suspendedClients,
                'mrr' => $mrr,
                'arr' => $arr,
                'total_subscriptions' => $totalSubscriptions,
                'expiring_soon_count' => $expiringSoonCount,
                'expiring_soon_subs' => $expiringSoonSubs,
                'total_databases' => $totalDatabases,
                'healthy_databases' => $healthyDatabases,
                'avg_latency' => $avgLatency,
                'central_health' => $centralHealth,
                'central_latency' => $centralLatencyMs,
            ],
            'chartGrowth' => [
                'labels' => $growthLabels,
                'data' => $growthData,
            ],
            'chartPlans' => [
                'labels' => $planLabels,
                'data' => $planCounts,
                'colors' => $planColors,
            ],
            'recentClients' => $recentClients,
            'recentAuditLogs' => $recentAuditLogs,
            'plans' => $plans,
        ]);
    }
}
