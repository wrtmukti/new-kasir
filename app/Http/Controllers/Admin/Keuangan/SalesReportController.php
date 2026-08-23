<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Admin\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $perPageInput = $request->input('per_page', 10);
        $perPage = ($perPageInput === 'all') ? 999999 : (int) $perPageInput;
        $search = $request->input('search');

        $query = Transaction::with(['order', 'dailyClosing'])
            ->where('transaction_status', 'success')
            ->whereBetween('transaction_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'LIKE', "%{$search}%")
                  ->orWhere('transaction_remark', 'LIKE', "%{$search}%");
            });
        }

        $transactions = $query->latest('transaction_date')->paginate($perPage);

        $totalSubtotal = Transaction::where('transaction_status', 'success')
            ->whereBetween('transaction_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('transaction_subtotal');

        $totalTax = Transaction::where('transaction_status', 'success')
            ->whereBetween('transaction_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('transaction_tax');

        $totalService = Transaction::where('transaction_status', 'success')
            ->whereBetween('transaction_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('transaction_service_charge');

        $totalGrand = Transaction::where('transaction_status', 'success')
            ->whereBetween('transaction_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('transaction_grand_total');

        return view('admin.keuangan.reports.sales', compact(
            'startDate',
            'endDate',
            'perPageInput',
            'search',
            'transactions',
            'totalSubtotal',
            'totalTax',
            'totalService',
            'totalGrand'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $search = $request->input('search');

        $query = Transaction::with(['order', 'dailyClosing'])
            ->where('transaction_status', 'success')
            ->whereBetween('transaction_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'LIKE', "%{$search}%")
                  ->orWhere('transaction_remark', 'LIKE', "%{$search}%");
            });
        }

        $transactions = $query->latest('transaction_date')->get();

        $filename = "Laporan_Penjualan_{$startDate}_sd_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No TRX', 'Tanggal', 'Shift', 'Kasir', 'Subtotal', 'Service Charge (5%)', 'Pajak PB1 (10%)', 'Grand Total', 'Status']);

            foreach ($transactions as $trx) {
                fputcsv($file, [
                    $trx->transaction_code,
                    $trx->transaction_date,
                    optional($trx->dailyClosing)->shift_name ?? 'Default',
                    'Kasir',
                    $trx->transaction_subtotal,
                    $trx->transaction_service_charge,
                    $trx->transaction_tax,
                    $trx->transaction_grand_total,
                    $trx->transaction_status,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
