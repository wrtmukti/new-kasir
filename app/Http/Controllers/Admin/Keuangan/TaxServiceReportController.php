<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Admin\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaxServiceReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $perPageInput = $request->input('per_page', 10);
        $perPage = ($perPageInput === 'all') ? 999999 : (int) $perPageInput;
        $search = $request->input('search');

        $query = Order::where('delete_status', 0)
            ->where('order_status', 'completed')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'LIKE', "%{$search}%")
                  ->orWhere('tax_type', 'LIKE', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate($perPage);

        $totalTaxableBase = Order::where('delete_status', 0)
            ->where('order_status', 'completed')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('order_grand_total') - Order::where('delete_status', 0)->where('order_status', 'completed')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->sum('tax_amount');

        $totalTax = Order::where('delete_status', 0)
            ->where('order_status', 'completed')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('tax_amount');

        $totalService = Order::where('delete_status', 0)
            ->where('order_status', 'completed')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('service_charge_amount');

        return view('admin.kasir.keuangan.reports.tax-service', compact(
            'startDate',
            'endDate',
            'perPageInput',
            'search',
            'orders',
            'totalTaxableBase',
            'totalTax',
            'totalService'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $search = $request->input('search');

        $query = Order::where('delete_status', 0)
            ->where('order_status', 'completed')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'LIKE', "%{$search}%")
                  ->orWhere('tax_type', 'LIKE', "%{$search}%");
            });
        }

        $orders = $query->latest()->get();

        $filename = "Laporan_Pajak_PB1_Service_{$startDate}_sd_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No Order', 'Tanggal', 'Tipe Pajak', 'Tarif Pajak (%)', 'Service Charge (5%)', 'Setoran Pajak PB1 (10%)', 'Grand Total Struk']);

            foreach ($orders as $ord) {
                fputcsv($file, [
                    '#' . $ord->order_id,
                    $ord->created_at,
                    $ord->tax_type,
                    $ord->tax_percent . '%',
                    $ord->service_charge_amount,
                    $ord->tax_amount,
                    $ord->order_grand_total,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
