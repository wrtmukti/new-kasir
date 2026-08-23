<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Admin\Transaction;
use App\Models\Admin\DailyClosing;
use App\Models\Admin\Keuangan\CogsRawMaterial;
use App\Models\Admin\Keuangan\CogsWasteLog;
use App\Models\Admin\PurchaseOrder;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportDashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Query orders in range
        $orders = Order::where('delete_status', 0)
            ->where('order_status', 'completed')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();

        $grossSales = $orders->sum('order_grand_total');
        $totalTax = $orders->sum('tax_amount');
        $totalService = $orders->sum('service_charge_amount');
        $netSales = $grossSales - $totalTax - $totalService;

        // Query Waste Log loss in range
        $wasteLoss = CogsWasteLog::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('waste_cost');

        // Query PO Belanja
        $poExpense = PurchaseOrder::where('po_status', 'completed')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('po_total_amount');

        // Total Shift Closing Count
        $totalShifts = DailyClosing::whereBetween('business_date', [$startDate, $endDate])->count();
        $totalDifference = DailyClosing::whereBetween('business_date', [$startDate, $endDate])->sum('cash_difference');

        return view('admin.keuangan.reports.dashboard', compact(
            'startDate',
            'endDate',
            'grossSales',
            'netSales',
            'totalTax',
            'totalService',
            'wasteLoss',
            'poExpense',
            'totalShifts',
            'totalDifference'
        ));
    }
}
