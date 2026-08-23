<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Transaction;
use App\Models\Admin\PurchaseOrder;
use App\Models\Admin\Keuangan\CogsWasteLog;

use Illuminate\Http\Request;
use Carbon\Carbon;

class CashFlowReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $totalCashIn = Transaction::where('transaction_status', 'success')
            ->whereBetween('transaction_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('transaction_grand_total');

        $totalPoOut = PurchaseOrder::where('po_status', 'completed')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('po_total_amount');

        $totalWasteOut = CogsWasteLog::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('waste_cost');

        $netCashFlow = $totalCashIn - ($totalPoOut + $totalWasteOut);

        return view('admin.keuangan.reports.cashflow', compact(
            'startDate',
            'endDate',
            'totalCashIn',
            'totalPoOut',
            'totalWasteOut',
            'netCashFlow'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $totalCashIn = Transaction::where('transaction_status', 'success')
            ->whereBetween('transaction_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('transaction_grand_total');

        $totalPoOut = PurchaseOrder::where('po_status', 'completed')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('po_total_amount');

        $totalWasteOut = CogsWasteLog::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('waste_cost');

        $filename = "Laporan_Arus_Kas_{$startDate}_sd_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($startDate, $endDate, $totalCashIn, $totalPoOut, $totalWasteOut) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kategori Arus Kas', 'Periode', 'Jumlah Nominal (Rp)']);
            fputcsv($file, ['Pemasukan Omzet POS Kasir (Inflow)', "$startDate s.d $endDate", $totalCashIn]);
            fputcsv($file, ['Pengeluaran PO Belanja Bahan Mentah (Outflow)', "$startDate s.d $endDate", -$totalPoOut]);
            fputcsv($file, ['Kerugian Bahan Rusak / Waste (Loss)', "$startDate s.d $endDate", -$totalWasteOut]);
            fputcsv($file, ['NET CASH FLOW ARUS KAS', "$startDate s.d $endDate", $totalCashIn - ($totalPoOut + $totalWasteOut)]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
