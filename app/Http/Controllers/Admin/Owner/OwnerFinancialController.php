<?php

namespace App\Http\Controllers\Admin\Owner;

use App\Http\Controllers\Controller;
use App\Services\ConsolidatedFinancialService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OwnerFinancialController extends Controller
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
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $rawOutletIds = $request->input('outlet_ids', []);
        $selectedOutletIds = is_array($rawOutletIds) ? array_filter($rawOutletIds) : ($rawOutletIds ? [$rawOutletIds] : []);

        $kpis = $this->financialService->getConsolidatedKPIs($startDate, $endDate, $selectedOutletIds);
        $leaderboard = $this->financialService->getOutletLeaderboard($startDate, $endDate, $selectedOutletIds);

        return view('admin.owner.financial', compact(
            'activeOutlets',
            'startDate',
            'endDate',
            'selectedOutletIds',
            'kpis',
            'leaderboard'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $rawOutletIds = $request->input('outlet_ids', []);
        $selectedOutletIds = is_array($rawOutletIds) ? array_filter($rawOutletIds) : ($rawOutletIds ? [$rawOutletIds] : []);

        $kpis = $this->financialService->getConsolidatedKPIs($startDate, $endDate, $selectedOutletIds);
        $leaderboard = $this->financialService->getOutletLeaderboard($startDate, $endDate, $selectedOutletIds);

        $filename = 'Laporan_Finansial_Konsolidasi_' . Carbon::parse($startDate)->format('dMY') . '_' . Carbon::parse($endDate)->format('dMY') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($kpis, $leaderboard, $startDate, $endDate) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            // Header Laporan
            fputcsv($handle, ['LAPORAN KEUANGAN KONSOLIDASI & ARUS KAS HOLDING (MULTI-CABANG)']);
            fputcsv($handle, ['Periode', Carbon::parse($startDate)->format('d M Y') . ' s/d ' . Carbon::parse($endDate)->format('d M Y')]);
            fputcsv($handle, ['Tanggal Export', Carbon::now()->translatedFormat('d F Y H:i')]);
            fputcsv($handle, []);

            // Bagian 1: Ringkasan Eksekutif Konsolidasi
            fputcsv($handle, ['=== RINGKASAN EKSEKUTIF KONSOLIDASI ===']);
            fputcsv($handle, ['Metrik Finansial', 'Nilai (IDR)', 'Keterangan']);
            fputcsv($handle, ['Total Omzet Penjualan (Revenue)', number_format($kpis['total_revenue'], 0, ',', '.'), 'Total transaksi sukses']);
            fputcsv($handle, ['Total Modal Resep (Theoretical COGS)', number_format($kpis['total_cogs'], 0, ',', '.'), 'Modal bahan baku menu terjual']);
            fputcsv($handle, ['Total Laba Kotor (Gross Profit)', number_format($kpis['gross_profit'], 0, ',', '.'), 'Gross Margin: ' . number_format($kpis['gross_margin_percent'], 1) . '%']);
            fputcsv($handle, ['Kerugian Bahan Busuk (Waste Cost)', number_format($kpis['total_waste_cost'], 0, ',', '.'), 'Bahan terbuang/rusak']);
            fputcsv($handle, ['Total Biaya Gaji (Labor Cost)', number_format($kpis['total_labor_cost'], 0, ',', '.'), 'Beban gaji staf']);
            fputcsv($handle, ['Total Overhead / Listrik / Air', number_format($kpis['total_overhead_cost'], 0, ',', '.'), 'Biaya operasional']);
            fputcsv($handle, ['LABA BERSIH KONSOLIDASI (NET PROFIT)', number_format($kpis['net_profit'], 0, ',', '.'), 'Net Margin: ' . number_format($kpis['net_margin_percent'], 1) . '%']);
            fputcsv($handle, []);

            // Bagian 2: Arus Kas Riil (Cash Flow)
            fputcsv($handle, ['=== ARUS KAS NYATA KONSOLIDASI (CASH FLOW) ===']);
            fputcsv($handle, ['Total Cash Inflow (Pemasukan Kas)', number_format($kpis['total_cash_inflow'], 0, ',', '.'), 'Tunai + QRIS + Topup Laci']);
            fputcsv($handle, ['Total Operating Outflow (Pengeluaran Kas)', number_format($kpis['total_operating_outflow'], 0, ',', '.'), 'PO Lunas + Petty Cash + OPEX']);
            fputcsv($handle, ['NET CASH FLOW (SURPLUS/DEFISIT KAS)', number_format($kpis['net_cash_flow'], 0, ',', '.'), $kpis['net_cash_flow'] >= 0 ? 'SURPLUS' : 'DEFISIT']);
            fputcsv($handle, ['Total Setoran Kas Brankas Malam Ini', number_format($kpis['total_safe_deposit'], 0, ',', '.'), 'Kasir siap setor']);
            fputcsv($handle, ['Komitmen Hutang PO Tempo Belum Lunas', number_format($kpis['total_po_unpaid'], 0, ',', '.'), 'Jatuh tempo supplier']);
            fputcsv($handle, []);

            // Bagian 3: Rincian Kinerja Per Cabang
            fputcsv($handle, ['=== RINCIAN PERFORMA PER CABANG OUTLET ===']);
            fputcsv($handle, ['No', 'Nama Cabang', 'Kode', 'Omzet (IDR)', 'Pesanan', 'Modal HPP (IDR)', 'Gross Margin %', 'Waste (IDR)', 'Laba Bersih (IDR)', 'Net Margin %', 'Setoran Brankas (IDR)']);

            $no = 1;
            foreach ($leaderboard as $row) {
                fputcsv($handle, [
                    $no++,
                    $row['outlet_name'],
                    $row['outlet_code'],
                    number_format($row['revenue'], 0, ',', '.'),
                    $row['orders_count'],
                    number_format($row['cogs'], 0, ',', '.'),
                    number_format($row['gross_margin_percent'], 1) . '%',
                    number_format($row['waste_loss'], 0, ',', '.'),
                    number_format($row['net_profit'], 0, ',', '.'),
                    number_format($row['net_margin_percent'], 1) . '%',
                    number_format($row['safe_deposit'], 0, ',', '.'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
