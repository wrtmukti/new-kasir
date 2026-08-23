<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Product;
use App\Models\Admin\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $perPageInput = $request->input('per_page', 10);
        $perPage = ($perPageInput === 'all') ? 999999 : (int) $perPageInput;
        $search = $request->input('search');

        $query = TransactionItem::select(
            'product_id',
            'product_name',
            DB::raw('SUM(qty) as total_qty'),
            DB::raw('SUM(subtotal) as total_sales')
        )
        ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if (!empty($search)) {
            $query->where('product_name', 'LIKE', "%{$search}%");
        }

        $items = $query->groupBy('product_id', 'product_name')
            ->orderByDesc('total_qty')
            ->paginate($perPage);

        return view('admin.keuangan.reports.products', compact('startDate', 'endDate', 'perPageInput', 'search', 'items'));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $search = $request->input('search');

        $query = TransactionItem::select(
            'product_id',
            'product_name',
            DB::raw('SUM(qty) as total_qty'),
            DB::raw('SUM(subtotal) as total_sales')
        )
        ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if (!empty($search)) {
            $query->where('product_name', 'LIKE', "%{$search}%");
        }

        $items = $query->groupBy('product_id', 'product_name')
            ->orderByDesc('total_qty')
            ->get();

        $filename = "Laporan_Performa_Menu_{$startDate}_sd_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID Produk', 'Nama Produk / Menu', 'Qty Terjual', 'Total Omzet Sales']);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->product_id,
                    $item->product_name,
                    $item->total_qty,
                    $item->total_sales,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
