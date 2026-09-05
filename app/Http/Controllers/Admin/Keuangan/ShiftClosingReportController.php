<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Admin\DailyClosing;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftClosingReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $perPageInput = $request->input('per_page', 10);
        $perPage = ($perPageInput === 'all') ? 999999 : (int) $perPageInput;
        $search = $request->input('search');

        $query = DailyClosing::whereBetween('business_date', [$startDate, $endDate]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('shift_name', 'LIKE', "%{$search}%")
                  ->orWhere('status', 'LIKE', "%{$search}%")
                  ->orWhere('id', 'LIKE', "%{$search}%");
            });
        }

        $closings = $query->latest('business_date')->paginate($perPage);

        $totalCash = DailyClosing::whereBetween('business_date', [$startDate, $endDate])
            ->sum('system_cash_sales');

        $totalNonCash = DailyClosing::whereBetween('business_date', [$startDate, $endDate])
            ->sum('system_non_cash_sales');

        $totalDifference = DailyClosing::whereBetween('business_date', [$startDate, $endDate])
            ->sum('cash_difference');

        return view('admin.kasir.keuangan.reports.shifts', compact(
            'startDate',
            'endDate',
            'perPageInput',
            'search',
            'closings',
            'totalCash',
            'totalNonCash',
            'totalDifference'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $search = $request->input('search');

        $query = DailyClosing::whereBetween('business_date', [$startDate, $endDate]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('shift_name', 'LIKE', "%{$search}%")
                  ->orWhere('status', 'LIKE', "%{$search}%")
                  ->orWhere('id', 'LIKE', "%{$search}%");
            });
        }

        $closings = $query->latest('business_date')->get();

        $filename = "Audit_Shift_Closing_Kasir_{$startDate}_sd_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($closings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID Shift', 'Tanggal Bisnis', 'Shift', 'Buka', 'Tutup', 'Modal Awal', 'Sales Cash', 'Sales Non-Cash', 'Expected Cash', 'Hitungan Fisik', 'Selisih', 'Status']);

            foreach ($closings as $c) {
                fputcsv($file, [
                    '#SHIFT-' . $c->id,
                    $c->business_date->format('Y-m-d'),
                    $c->shift_name,
                    $c->opened_at,
                    $c->closed_at,
                    $c->starting_cash,
                    $c->system_cash_sales,
                    $c->system_non_cash_sales,
                    $c->system_expected_cash,
                    $c->actual_cash_counted,
                    $c->cash_difference,
                    $c->status,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
