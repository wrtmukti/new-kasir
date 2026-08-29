<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Admin\DailyClosing;
use App\Models\Admin\Shift;
use App\Models\Admin\ShiftSetting;
use App\Models\Admin\Order;
use App\Models\Admin\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftOperationalController extends Controller
{
    /**
     * Tampilan Utama Halaman Dedicated Buka / Tutup Shift (Clock-In & Clock-Out)
     */
    public function index()
    {
        $companyId = session('outlet_id') ?? 'COMP-001';

        // Ambil Shift Settings
        $setting = ShiftSetting::where('outlet_id', $companyId)->first()
            ?? ShiftSetting::first()
            ?? new ShiftSetting([
                'daily_cutoff_time' => '03:00:00',
                'shift_mode' => 'auto_master',
                'auto_lock_unclosed' => 1,
            ]);

        // Ambil Daftar Master Shift yang Aktif
        $masterShifts = Shift::where('outlet_id', $companyId)
            ->where('is_active', 1)
            ->orderBy('shift_number', 'asc')
            ->get();

        // Cek Sesi Shift yang Sedang AKTIF (Status = open)
        $activeShift = DailyClosing::where('outlet_id', $companyId)
            ->where('status', 'open')
            ->latest()
            ->first();

        $liveStats = null;
        $drawerLogs = collect();
        if ($activeShift) {
            // Hitung Penjualan Realtime dari Order/Transaction yang terikat daily_closing_id ini
            $cashSales = (float) Transaction::where('daily_closing_id', $activeShift->id)
                ->where('transaction_status', 'success')
                ->where(function ($q) {
                    $q->whereHas('payment', function ($p) {
                        $p->where('payment_metode', 'LIKE', '%cash%')
                          ->orWhere('payment_metode', 'LIKE', '%tunai%');
                    })->orWhereDoesntHave('payment');
                })->sum('transaction_grand_total');

            $nonCashSales = (float) Transaction::where('daily_closing_id', $activeShift->id)
                ->where('transaction_status', 'success')
                ->whereHas('payment', function ($p) {
                    $p->where('payment_metode', 'NOT LIKE', '%cash%')
                      ->where('payment_metode', 'NOT LIKE', '%tunai%');
                })->sum('transaction_grand_total');

            if ($cashSales == 0 && $activeShift->system_cash_sales > 0) {
                $cashSales = (float) $activeShift->system_cash_sales;
            }
            if ($nonCashSales == 0 && $activeShift->system_non_cash_sales > 0) {
                $nonCashSales = (float) $activeShift->system_non_cash_sales;
            }

            // Hitung Uang Masuk / Keluar Laci Realtime
            $drawerCashIn = (float) \App\Models\Admin\CashDrawerLog::where('daily_closing_id', $activeShift->id)->where('type', 'in')->sum('amount');
            $drawerCashOut = (float) \App\Models\Admin\CashDrawerLog::where('daily_closing_id', $activeShift->id)->where('type', 'out')->sum('amount');

            $orderCount = Order::where('daily_closing_id', $activeShift->id)->count();

            $expectedCash = $activeShift->starting_cash + $cashSales + $drawerCashIn - $drawerCashOut;

            $liveStats = [
                'cash_sales' => $cashSales,
                'non_cash_sales' => $nonCashSales,
                'drawer_cash_in' => $drawerCashIn,
                'drawer_cash_out' => $drawerCashOut,
                'total_sales' => $cashSales + $nonCashSales,
                'order_count' => $orderCount,
                'starting_cash' => (float) $activeShift->starting_cash,
                'expected_cash' => $expectedCash,
                'runtime_duration' => Carbon::parse($activeShift->opened_at)->diffForHumans(null, true),
            ];

            $drawerLogs = \App\Models\Admin\CashDrawerLog::where('daily_closing_id', $activeShift->id)
                ->latest()
                ->get();
        }

        // Histori 5 Shift Closing Terakhir
        $recentClosings = DailyClosing::where('outlet_id', $companyId)
            ->where('status', 'closed')
            ->orderBy('closed_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.keuangan.shift-operational.index', compact(
            'setting',
            'masterShifts',
            'activeShift',
            'liveStats',
            'drawerLogs',
            'recentClosings'
        ));
    }

    /**
     * Proses Clock-In / Buka Shift Kasir
     */
    public function openShift(Request $request)
    {
        $companyId = session('outlet_id') ?? 'COMP-001';

        // Cek apakah sudah ada shift yang sedang OPEN
        $existingActive = DailyClosing::where('outlet_id', $companyId)
            ->where('status', 'open')
            ->first();

        if ($existingActive) {
            $msg = 'Gagal Clock-In: Masih ada sesi shift yang berstatus AKTIF (' . $existingActive->shift_name . '). Harap Clock-Out terlebih dahulu.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
            'shift_name' => 'required|string|max:50',
        ], [
            'starting_cash.required' => 'Modal awal kasir wajib diisi.',
            'starting_cash.numeric' => 'Modal awal harus berupa angka.',
            'shift_name.required' => 'Nama shift wajib diisi/dipilih.',
        ]);

        // Hitung Tanggal Bisnis berdasarkan Cut-Off Time Resto
        $setting = ShiftSetting::where('outlet_id', $companyId)->first();
        $cutoffTime = $setting ? $setting->daily_cutoff_time : '03:00:00';
        $businessDate = $this->calculateBusinessDate($cutoffTime);

        $shiftNumber = $request->input('shift_number', 1);

        $dailyClosing = DailyClosing::create([
            'outlet_id' => $companyId,
            'cashier_id' => auth()->id() ?? 1,
            'shift_number' => $shiftNumber,
            'shift_name' => $request->shift_name,
            'business_date' => $businessDate,
            'opened_at' => now(),
            'starting_cash' => $request->starting_cash,
            'system_cash_sales' => 0,
            'system_non_cash_sales' => 0,
            'cash_in_amount' => 0,
            'cash_out_amount' => 0,
            'system_expected_cash' => $request->starting_cash,
            'actual_cash_counted' => 0,
            'cash_difference' => 0,
            'notes' => 'Clock-In Kasir dimulakan pada ' . now()->format('d/m/Y H:i:s'),
            'status' => 'open',
        ]);

        $msg = 'Berhasil Clock-In! Shift (' . $dailyClosing->shift_name . ') telah dibuka dengan modal awal Rp ' . number_format($dailyClosing->starting_cash, 0, ',', '.') . '.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'data' => $dailyClosing,
            ]);
        }

        return redirect()->route('admin.keuangan.shift-operational.index')->with('success', $msg);
    }

    /**
     * Catat Kas Masuk Laci (Cash In / Owner Top Up)
     */
    public function cashIn(Request $request)
    {
        $companyId = session('outlet_id') ?? 'COMP-001';

        $activeShift = DailyClosing::where('outlet_id', $companyId)
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$activeShift) {
            $errMsg = 'Tidak ada sesi shift yang sedang aktif.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => $errMsg], 422);
            }
            return redirect()->route('admin.keuangan.shift-operational.index')->with('error', $errMsg);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'reason' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        $amount = (float) $request->amount;
        $category = $request->input('category') ?: 'Top-up Owner';

        \App\Models\Admin\CashDrawerLog::create([
            'outlet_id' => $companyId,
            'daily_closing_id' => $activeShift->id,
            'cashier_id' => auth()->id() ?? 1,
            'type' => 'in',
            'category' => $category,
            'amount' => $amount,
            'reason' => $request->reason,
            'created_by' => auth()->user()->name ?? 'Kasir',
        ]);

        $activeShift->increment('cash_in_amount', $amount);
        $activeShift->update([
            'system_expected_cash' => $activeShift->starting_cash + $activeShift->system_cash_sales + $activeShift->cash_in_amount - $activeShift->cash_out_amount,
        ]);

        $msg = 'Kas masuk sebesar Rp ' . number_format($amount, 0, ',', '.') . ' berhasil dicatat ke laci.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'new_cash_in' => $activeShift->fresh()->cash_in_amount,
                'expected_cash' => $activeShift->fresh()->system_expected_cash,
            ]);
        }

        return redirect()->route('admin.keuangan.shift-operational.index')->with('success', $msg);
    }

    /**
     * Catat Kas Keluar Laci (Cash Out / Petty Cash / Beli Es/Gas)
     */
    public function cashOut(Request $request)
    {
        $companyId = session('outlet_id') ?? 'COMP-001';

        $activeShift = DailyClosing::where('outlet_id', $companyId)
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$activeShift) {
            $errMsg = 'Tidak ada sesi shift yang sedang aktif.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => $errMsg], 422);
            }
            return redirect()->route('admin.keuangan.shift-operational.index')->with('error', $errMsg);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'reason' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        $amount = (float) $request->amount;
        $category = $request->input('category') ?: 'Petty Cash';

        \App\Models\Admin\CashDrawerLog::create([
            'outlet_id' => $companyId,
            'daily_closing_id' => $activeShift->id,
            'cashier_id' => auth()->id() ?? 1,
            'type' => 'out',
            'category' => $category,
            'amount' => $amount,
            'reason' => $request->reason,
            'created_by' => auth()->user()->name ?? 'Kasir',
        ]);

        $activeShift->increment('cash_out_amount', $amount);
        $activeShift->update([
            'system_expected_cash' => $activeShift->starting_cash + $activeShift->system_cash_sales + $activeShift->cash_in_amount - $activeShift->cash_out_amount,
        ]);

        $msg = 'Kas keluar sebesar Rp ' . number_format($amount, 0, ',', '.') . ' berhasil dicatat.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'new_cash_out' => $activeShift->fresh()->cash_out_amount,
                'expected_cash' => $activeShift->fresh()->system_expected_cash,
            ]);
        }

        return redirect()->route('admin.keuangan.shift-operational.index')->with('success', $msg);
    }

    /**
     * Proses Clock-Out / Tutup Shift Kasir & Cetak Z-Report
     */
    public function closeShift(Request $request)
    {
        $companyId = session('outlet_id') ?? 'COMP-001';

        $activeShift = DailyClosing::where('outlet_id', $companyId)
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$activeShift) {
            $msg = 'Gagal Clock-Out: Tidak ditemukan sesi shift yang sedang AKTIF.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $request->validate([
            'actual_cash_counted' => 'required|numeric|min:0',
            'retained_cash_float' => 'nullable|numeric|min:0',
            'cashier_note' => 'nullable|string|max:500',
        ], [
            'actual_cash_counted.required' => 'Hitungan fisik uang tunai kasir wajib diisi.',
            'actual_cash_counted.numeric' => 'Hitungan fisik kasir harus berupa angka.',
        ]);

        // Hitung Akhir Penjualan Tunai & Non-Tunai Realtime
        $cashSales = (float) Transaction::where('daily_closing_id', $activeShift->id)
            ->where('transaction_status', 'success')
            ->where(function ($q) {
                $q->whereHas('payment', function ($p) {
                    $p->where('payment_metode', 'LIKE', '%cash%')
                      ->orWhere('payment_metode', 'LIKE', '%tunai%');
                })->orWhereDoesntHave('payment');
            })->sum('transaction_grand_total');

        $nonCashSales = (float) Transaction::where('daily_closing_id', $activeShift->id)
            ->where('transaction_status', 'success')
            ->whereHas('payment', function ($p) {
                $p->where('payment_metode', 'NOT LIKE', '%cash%')
                  ->where('payment_metode', 'NOT LIKE', '%tunai%');
            })->sum('transaction_grand_total');

        if ($cashSales == 0 && $activeShift->system_cash_sales > 0) {
            $cashSales = (float) $activeShift->system_cash_sales;
        }
        if ($nonCashSales == 0 && $activeShift->system_non_cash_sales > 0) {
            $nonCashSales = (float) $activeShift->system_non_cash_sales;
        }

        $expectedCash = $activeShift->starting_cash + $cashSales + $activeShift->cash_in_amount - $activeShift->cash_out_amount;

        $actualCash = (float) $request->actual_cash_counted;
        $difference = $actualCash - $expectedCash;

        $retainedFloat = (float) ($request->retained_cash_float ?? 0);
        $depositToSafe = max(0, $actualCash - $retainedFloat);

        $notes = $request->input('notes', '');
        if ($difference != 0) {
            $diffType = $difference > 0 ? 'Kelebihan Kas (Over)' : 'Kekurangan Kas (Short)';
            $notes .= ' [Audit Selisih: ' . $diffType . ' Rp ' . number_format(abs($difference), 0, ',', '.') . ']';
        }

        $activeShift->update([
            'closed_at' => now(),
            'system_cash_sales' => $cashSales,
            'system_non_cash_sales' => $nonCashSales,
            'system_expected_cash' => $expectedCash,
            'actual_cash_counted' => $actualCash,
            'retained_cash_float' => $retainedFloat,
            'cash_deposit_to_safe' => $depositToSafe,
            'cash_difference' => $difference,
            'notes' => trim($notes),
            'cashier_note' => $request->cashier_note,
            'status' => 'closed',
        ]);

        $msg = 'Berhasil Clock-Out! Shift (' . $activeShift->shift_name . ') telah ditutup. Setor brankas: Rp ' . number_format($depositToSafe, 0, ',', '.') . ' (Sisa laci: Rp ' . number_format($retainedFloat, 0, ',', '.') . '). Selisih: Rp ' . number_format($difference, 0, ',', '.') . '.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'data' => $activeShift,
                'z_report_url' => route('admin.keuangan.shift-operational.z-report', $activeShift->id),
            ]);
        }

        return redirect()->route('admin.keuangan.shift-operational.index')->with('success', $msg);
    }

    /**
     * Struk Rekapitulasi Z-Report Shift (Format Printer Thermal / Modal Preview)
     */
    public function zReport(DailyClosing $dailyClosing)
    {
        $orders = Order::where('daily_closing_id', $dailyClosing->id)->get();
        $transactions = Transaction::where('daily_closing_id', $dailyClosing->id)->get();

        return view('admin.keuangan.shift-operational.z-report', compact('dailyClosing', 'orders', 'transactions'));
    }

    /**
     * Helper Penentuan Tanggal Bisnis Berdasarkan Jam Cut-Off
     */
    private function calculateBusinessDate($cutoffTimeStr = '03:00:00')
    {
        $now = Carbon::now();
        $cutoffParts = explode(':', $cutoffTimeStr);
        $cutoffHour = (int) ($cutoffParts[0] ?? 3);
        $cutoffMinute = (int) ($cutoffParts[1] ?? 0);

        $cutoffToday = Carbon::today()->setTime($cutoffHour, $cutoffMinute, 0);

        // Jika transaksi terjadi di antara jam 00:00 s.d jam cut-off (misal 01:30 AM),
        // Maka transaksi masih terhitung pada Tanggal Bisnis KEMARIN.
        if ($now->lt($cutoffToday)) {
            return Carbon::yesterday()->format('Y-m-d');
        }

        return Carbon::today()->format('Y-m-d');
    }
}
