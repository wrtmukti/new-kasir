<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Transaction;
use App\Models\Admin\PurchaseOrder;
use App\Models\Admin\CashDrawerLog;
use App\Models\Admin\Keuangan\CogsWasteLog;
use App\Models\Admin\Keuangan\HppFinancialReport;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CashFlowReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // 1. Inflow: Penjualan POS Kasir
        $totalSalesCash = (float) Transaction::where('transaction_status', 'success')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where(function ($q) {
                $q->whereHas('payment', function ($p) {
                    $p->where('payment_metode', 'LIKE', '%cash%')
                      ->orWhere('payment_metode', 'LIKE', '%tunai%');
                })->orWhereDoesntHave('payment');
            })->sum('transaction_grand_total');

        $totalSalesNonCash = (float) Transaction::where('transaction_status', 'success')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->whereHas('payment', function ($p) {
                $p->where('payment_metode', 'NOT LIKE', '%cash%')
                  ->where('payment_metode', 'NOT LIKE', '%tunai%');
            })->sum('transaction_grand_total');

        $totalSalesInflow = $totalSalesCash + $totalSalesNonCash;

        // 2. Inflow: Cash In Laci Kasir (Owner Topup / Modal Tambahan)
        $totalDrawerCashIn = (float) CashDrawerLog::where('type', 'in')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('amount');

        $totalCashIn = $totalSalesInflow + $totalDrawerCashIn;

        // 3. Outflow: Pembayaran PO Bahan Mentah yang SUDAH LUNAS (Disbursement)
        $totalPoPaid = (float) PurchaseOrder::where('payment_status', 'paid')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('payment_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->whereNull('payment_date')
                         ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                  });
            })->sum('po_total_amount');

        // Hutang PO Tempo yang BELUM LUNAS (Unpaid Commitments)
        $totalPoUnpaid = (float) PurchaseOrder::where('payment_status', 'unpaid')
            ->whereBetween('po_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('po_total_amount');

        // 4. Outflow: Pengeluaran Kas Laci (Petty Cash / Beli Es/Gas)
        $totalDrawerCashOut = (float) CashDrawerLog::where('type', 'out')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('amount');

        // 5. Kerugian Non-Kas (Waste Log)
        $totalWasteOut = (float) CogsWasteLog::whereBetween('loss_date', [$startDate, $endDate])
            ->sum('waste_cost');

        // 6. Biaya Operasional / Gaji / Overhead dari HPP Report
        $hppReport = HppFinancialReport::where('year', Carbon::parse($startDate)->year)
            ->where('month', Carbon::parse($startDate)->month)
            ->first();

        $totalLaborCost = (float) ($hppReport?->total_labor_cost ?? 0);
        $totalOverheadCost = (float) ($hppReport?->total_overhead_cost ?? 0);
        $totalOperatingOutflow = $totalPoPaid + $totalDrawerCashOut + $totalLaborCost + $totalOverheadCost;

        // Net Cash Flow Aktual (Arus Kas Bersih yang benar-benar cair & keluar)
        $netCashFlow = $totalCashIn - $totalOperatingOutflow;

        return view('admin.keuangan.reports.cashflow', compact(
            'startDate',
            'endDate',
            'totalSalesCash',
            'totalSalesNonCash',
            'totalSalesInflow',
            'totalDrawerCashIn',
            'totalCashIn',
            'totalPoPaid',
            'totalPoUnpaid',
            'totalDrawerCashOut',
            'totalLaborCost',
            'totalOverheadCost',
            'totalWasteOut',
            'totalOperatingOutflow',
            'netCashFlow'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $totalCashIn = Transaction::where('transaction_status', 'success')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('transaction_grand_total');

        $totalPoPaid = PurchaseOrder::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('po_total_amount');

        $totalDrawerCashOut = CashDrawerLog::where('type', 'out')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('amount');

        $filename = "Laporan_Arus_Kas_{$startDate}_sd_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($startDate, $endDate, $totalCashIn, $totalPoPaid, $totalDrawerCashOut) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kategori Arus Kas', 'Periode', 'Nominal (Rp)']);
            fputcsv($file, ['Pemasukan Penjualan POS Kasir (Inflow)', "$startDate s.d $endDate", $totalCashIn]);
            fputcsv($file, ['Pengeluaran PO Bahan Mentah Lunas (Outflow)', "$startDate s.d $endDate", -$totalPoPaid]);
            fputcsv($file, ['Pengeluaran Kas Operasional / Petty Cash (Outflow)', "$startDate s.d $endDate", -$totalDrawerCashOut]);
            fputcsv($file, ['NET CASH FLOW BERSIH', "$startDate s.d $endDate", $totalCashIn - ($totalPoPaid + $totalDrawerCashOut)]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
