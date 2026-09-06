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
     * Helper resolusi ID outlet aktif sesi kasir/admin
     */
    private function resolveOutletId(): string
    {
        $id = session('active_outlet_id') ?? session('outlet_id');
        if ($id) return (string) $id;

        if (\Illuminate\Support\Facades\Schema::hasTable('outlets')) {
            $id = \App\Models\Admin\Outlet::where('delete_status', 0)->value('outlet_id');
            if ($id) return (string) $id;
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('companies')) {
            $id = \Illuminate\Support\Facades\DB::table('companies')->value('company_id')
                ?? \Illuminate\Support\Facades\DB::table('companies')->value('id');
            if ($id) return (string) $id;
        }

        return 'COMP-001';
    }

    /**
     * Resolusi nama kolom relasi cabang fisik (outlet_id vs company_id) pada model
     */
    private function resolveBranchColumn(\Illuminate\Database\Eloquent\Model $model): ?string
    {
        $table = $model->getTable();
        $conn = $model->getConnectionName() ?: config('database.default');
        if (\Illuminate\Support\Facades\Schema::connection($conn)->hasColumn($table, 'outlet_id')) {
            return 'outlet_id';
        }
        if (\Illuminate\Support\Facades\Schema::connection($conn)->hasColumn($table, 'company_id')) {
            return 'company_id';
        }
        return null;
    }

    /**
     * Tampilan Utama Halaman Dedicated Buka / Tutup Shift (Clock-In & Clock-Out)
     */
    public function index()
    {
        $companyId = $this->resolveOutletId();
        $shiftSettingCol = $this->resolveBranchColumn(new ShiftSetting());
        $shiftCol = $this->resolveBranchColumn(new Shift());
        $closingCol = $this->resolveBranchColumn(new DailyClosing());

        // Ambil Shift Settings
        $setting = ($shiftSettingCol ? ShiftSetting::where($shiftSettingCol, $companyId)->first() : null)
            ?? ShiftSetting::first()
            ?? new ShiftSetting([
                'daily_cutoff_time' => '03:00:00',
                'shift_mode' => 'auto_master',
                'auto_lock_unclosed' => 1,
            ]);

        // Ambil Daftar Master Shift yang Aktif
        $masterShifts = $shiftCol ? Shift::where($shiftCol, $companyId)
            ->where('is_active', 1)
            ->orderBy('shift_number', 'asc')
            ->get() : Shift::where('is_active', 1)->orderBy('shift_number', 'asc')->get();

        // Cek Sesi Shift yang Sedang AKTIF (Status = open)
        $activeShift = $closingCol ? DailyClosing::where($closingCol, $companyId)
            ->where('status', 'open')
            ->latest()
            ->first() : DailyClosing::where('status', 'open')->latest()->first();

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
            if (\Illuminate\Support\Facades\Schema::hasTable('cash_drawer_logs')) {
                $drawerCashIn = (float) \App\Models\Admin\CashDrawerLog::where('daily_closing_id', $activeShift->id)->where('type', 'in')->sum('amount');
                $drawerCashOut = (float) \App\Models\Admin\CashDrawerLog::where('daily_closing_id', $activeShift->id)->where('type', 'out')->sum('amount');
                $drawerLogs = \App\Models\Admin\CashDrawerLog::where('daily_closing_id', $activeShift->id)
                    ->latest()
                    ->get();
            } else {
                $drawerCashIn = (float) ($activeShift->cash_in_amount ?? 0);
                $drawerCashOut = (float) ($activeShift->cash_out_amount ?? 0);
                $drawerLogs = collect();
            }

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
        }

        // Histori 5 Shift Closing Terakhir
        $recentClosings = $closingCol ? DailyClosing::where($closingCol, $companyId)
            ->where('status', 'closed')
            ->orderBy('closed_at', 'desc')
            ->take(5)
            ->get() : DailyClosing::where('status', 'closed')->orderBy('closed_at', 'desc')->take(5)->get();

        return view('admin.kasir.shift.index', compact(
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
        $companyId = $this->resolveOutletId();
        $closingCol = $this->resolveBranchColumn(new DailyClosing());
        $shiftSettingCol = $this->resolveBranchColumn(new ShiftSetting());

        // Cek apakah sudah ada shift yang sedang OPEN
        $existingActive = $closingCol ? DailyClosing::where($closingCol, $companyId)
            ->where('status', 'open')
            ->first() : DailyClosing::where('status', 'open')->first();

        if ($existingActive) {
            $msg = 'Gagal Buka Kasir: Masih ada sesi kasir yang berstatus AKTIF (' . $existingActive->shift_name . '). Harap Tutup Kasir terlebih dahulu.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
            'shift_name' => 'nullable|string|max:50',
        ], [
            'starting_cash.required' => 'Modal awal kasir wajib diisi.',
            'starting_cash.numeric' => 'Modal awal harus berupa angka.',
        ]);

        // Hitung Tanggal Bisnis berdasarkan Cut-Off Time Resto
        $setting = ($shiftSettingCol ? ShiftSetting::where($shiftSettingCol, $companyId)->first() : null) ?? ShiftSetting::first();
        $cutoffTime = $setting ? $setting->daily_cutoff_time : '03:00:00';
        $businessDate = $this->calculateBusinessDate($cutoffTime);

        $shiftNumber = $request->input('shift_number', 1);
        $cashierName = auth()->user()?->name ?? 'Kasir Utama';
        $shiftName = $request->filled('shift_name') ? $request->shift_name : ('Kasir ' . $cashierName);

        $closingData = [
            'cashier_id' => auth()->id() ?? 1,
            'shift_number' => $shiftNumber,
            'shift_name' => $shiftName,
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
            'notes' => 'Buka Kasir dimulai pada ' . now()->format('d/m/Y H:i:s'),
            'status' => 'open',
        ];
        if ($closingCol) {
            $closingData[$closingCol] = $companyId;
        }
        $dailyClosing = DailyClosing::create($closingData);

        $msg = 'Buka Kasir Berhasil! Kasir telah dibuka dengan modal awal Rp ' . number_format($dailyClosing->starting_cash, 0, ',', '.') . '.';

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
        $companyId = $this->resolveOutletId();
        $closingCol = $this->resolveBranchColumn(new DailyClosing());

        $activeShift = $closingCol ? DailyClosing::where($closingCol, $companyId)
            ->where('status', 'open')
            ->latest()
            ->first() : DailyClosing::where('status', 'open')->latest()->first();

        if (!$activeShift) {
            $errMsg = 'Kasir belum dibuka.';
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

        if (\Illuminate\Support\Facades\Schema::hasTable('cash_drawer_logs')) {
            $drawerLogCol = $this->resolveBranchColumn(new \App\Models\Admin\CashDrawerLog());
            $logData = [
                'daily_closing_id' => $activeShift->id,
                'cashier_id' => auth()->id() ?? 1,
                'type' => 'in',
                'category' => $category,
                'amount' => $amount,
                'reason' => $request->reason,
                'created_by' => auth()->user()->name ?? 'Kasir',
            ];
            if ($drawerLogCol) {
                $logData[$drawerLogCol] = $companyId;
            }
            \App\Models\Admin\CashDrawerLog::create($logData);
        }

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
        $companyId = $this->resolveOutletId();
        $closingCol = $this->resolveBranchColumn(new DailyClosing());

        $activeShift = $closingCol ? DailyClosing::where($closingCol, $companyId)
            ->where('status', 'open')
            ->latest()
            ->first() : DailyClosing::where('status', 'open')->latest()->first();

        if (!$activeShift) {
            $errMsg = 'Kasir belum dibuka.';
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

        if (\Illuminate\Support\Facades\Schema::hasTable('cash_drawer_logs')) {
            $drawerLogCol = $this->resolveBranchColumn(new \App\Models\Admin\CashDrawerLog());
            $outLogData = [
                'daily_closing_id' => $activeShift->id,
                'cashier_id' => auth()->id() ?? 1,
                'type' => 'out',
                'category' => $category,
                'amount' => $amount,
                'reason' => $request->reason,
                'created_by' => auth()->user()->name ?? 'Kasir',
            ];
            if ($drawerLogCol) {
                $outLogData[$drawerLogCol] = $companyId;
            }
            \App\Models\Admin\CashDrawerLog::create($outLogData);
        }

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
        $companyId = $this->resolveOutletId();
        $closingCol = $this->resolveBranchColumn(new DailyClosing());

        $activeShift = $closingCol ? DailyClosing::where($closingCol, $companyId)
            ->where('status', 'open')
            ->latest()
            ->first() : DailyClosing::where('status', 'open')->latest()->first();

        if (!$activeShift) {
            $msg = 'Gagal Tutup Kasir: Kasir belum dibuka.';
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

        $updateData = [
            'closed_at' => now(),
            'system_cash_sales' => $cashSales,
            'system_non_cash_sales' => $nonCashSales,
            'system_expected_cash' => $expectedCash,
            'actual_cash_counted' => $actualCash,
            'cash_difference' => $difference,
            'status' => 'closed',
        ];

        // Audit catatan terpadu
        $auditNotes = [];
        if ($notes) {
            $auditNotes[] = $notes;
        }
        if ($retainedFloat > 0) {
            $auditNotes[] = '[Sisa Laci: Rp ' . number_format($retainedFloat, 0, ',', '.') . ']';
        }
        if ($depositToSafe > 0) {
            $auditNotes[] = '[Setor Brankas: Rp ' . number_format($depositToSafe, 0, ',', '.') . ']';
        }
        if ($request->filled('cashier_note')) {
            $auditNotes[] = '[Catatan Kasir: ' . $request->cashier_note . ']';
        }
        $updateData['notes'] = trim(implode(' ', $auditNotes));

        $conn = (new DailyClosing())->getConnectionName() ?: config('database.default');
        if (\Illuminate\Support\Facades\Schema::connection($conn)->hasColumn('daily_closings', 'retained_cash_float')) {
            $updateData['retained_cash_float'] = $retainedFloat;
        }
        if (\Illuminate\Support\Facades\Schema::connection($conn)->hasColumn('daily_closings', 'cash_deposit_to_safe')) {
            $updateData['cash_deposit_to_safe'] = $depositToSafe;
        }
        if (\Illuminate\Support\Facades\Schema::connection($conn)->hasColumn('daily_closings', 'cashier_note')) {
            $updateData['cashier_note'] = $request->cashier_note;
        }

        $activeShift->update($updateData);

        $msg = 'Tutup Kasir Berhasil! Kasir telah ditutup. Setor brankas: Rp ' . number_format($depositToSafe, 0, ',', '.') . ' (Sisa modal laci: Rp ' . number_format($retainedFloat, 0, ',', '.') . '). Selisih kas: Rp ' . number_format($difference, 0, ',', '.') . '.';

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

        return view('admin.kasir.shift.z-report', compact('dailyClosing', 'orders', 'transactions'));
    }

    /**
     * Struk Rekapitulasi X-Report Shift (Interim Mid-Shift Thermal 80mm Print)
     */
    public function xReport(DailyClosing $dailyClosing)
    {
        $orders = Order::where('daily_closing_id', $dailyClosing->id)->get();
        $transactions = Transaction::where('daily_closing_id', $dailyClosing->id)->get();

        $cashSales = (float) Transaction::where('daily_closing_id', $dailyClosing->id)
            ->where('transaction_status', 'success')
            ->where(function ($q) {
                $q->whereHas('payment', function ($p) {
                    $p->where('payment_metode', 'LIKE', '%cash%')
                      ->orWhere('payment_metode', 'LIKE', '%tunai%');
                })->orWhereDoesntHave('payment');
            })->sum('transaction_grand_total');

        $nonCashSales = (float) Transaction::where('daily_closing_id', $dailyClosing->id)
            ->where('transaction_status', 'success')
            ->whereHas('payment', function ($p) {
                $p->where('payment_metode', 'NOT LIKE', '%cash%')
                  ->where('payment_metode', 'NOT LIKE', '%tunai%');
            })->sum('transaction_grand_total');

        if ($cashSales == 0 && $dailyClosing->system_cash_sales > 0) {
            $cashSales = (float) $dailyClosing->system_cash_sales;
        }
        if ($nonCashSales == 0 && $dailyClosing->system_non_cash_sales > 0) {
            $nonCashSales = (float) $dailyClosing->system_non_cash_sales;
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('cash_drawer_logs')) {
            $drawerCashIn = (float) \App\Models\Admin\CashDrawerLog::where('daily_closing_id', $dailyClosing->id)->where('type', 'in')->sum('amount');
            $drawerCashOut = (float) \App\Models\Admin\CashDrawerLog::where('daily_closing_id', $dailyClosing->id)->where('type', 'out')->sum('amount');
            $drawerLogs = \App\Models\Admin\CashDrawerLog::where('daily_closing_id', $dailyClosing->id)->latest()->get();
        } else {
            $drawerCashIn = (float) ($dailyClosing->cash_in_amount ?? 0);
            $drawerCashOut = (float) ($dailyClosing->cash_out_amount ?? 0);
            $drawerLogs = collect();
        }
        $expectedCash = $dailyClosing->starting_cash + $cashSales + $drawerCashIn - $drawerCashOut;

        return view('admin.kasir.shift.x-report', compact(
            'dailyClosing',
            'orders',
            'transactions',
            'cashSales',
            'nonCashSales',
            'drawerCashIn',
            'drawerCashOut',
            'expectedCash',
            'drawerLogs'
        ));
    }

    /**
     * API JSON Live Status Drawer untuk Topbar HUD & Offcanvas Slide-over
     */
    public function getLiveDrawerStatus(?Request $request = null)
    {
        $request = $request ?? request();
        $companyId = $this->resolveOutletId();
        $closingCol = $this->resolveBranchColumn(new DailyClosing());

        $activeShift = $closingCol ? DailyClosing::where($closingCol, $companyId)
            ->where('status', 'open')
            ->latest()
            ->first() : DailyClosing::where('status', 'open')->latest()->first();

        if (!$activeShift) {
            return response()->json([
                'status' => 'inactive',
                'has_active_shift' => false,
                'message' => 'Kasir belum dibuka.',
                'data' => null,
            ]);
        }

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

        if (\Illuminate\Support\Facades\Schema::hasTable('cash_drawer_logs')) {
            $drawerCashIn = (float) \App\Models\Admin\CashDrawerLog::where('daily_closing_id', $activeShift->id)->where('type', 'in')->sum('amount');
            $drawerCashOut = (float) \App\Models\Admin\CashDrawerLog::where('daily_closing_id', $activeShift->id)->where('type', 'out')->sum('amount');
            $recentLogs = \App\Models\Admin\CashDrawerLog::where('daily_closing_id', $activeShift->id)
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'type' => $log->type,
                        'category' => $log->category,
                        'amount' => (float) $log->amount,
                        'amount_formatted' => 'Rp ' . number_format($log->amount, 0, ',', '.'),
                        'reason' => $log->reason,
                        'created_by' => $log->created_by,
                        'time_formatted' => $log->created_at ? $log->created_at->format('H:i') : '-',
                    ];
                });
        } else {
            $drawerCashIn = (float) ($activeShift->cash_in_amount ?? 0);
            $drawerCashOut = (float) ($activeShift->cash_out_amount ?? 0);
            $recentLogs = collect();
        }

        $orderCount = Order::where('daily_closing_id', $activeShift->id)->count();

        $expectedCash = (float) ($activeShift->starting_cash + $cashSales + $drawerCashIn - $drawerCashOut);

        $cashierUser = \App\Models\SysAdmin\User::find($activeShift->cashier_id);
        $cashierName = ($cashierUser && isset($cashierUser->name)) ? $cashierUser->name : 'Kasir POS';

        return response()->json([
            'status' => 'active',
            'has_active_shift' => true,
            'data' => [
                'id' => $activeShift->id,
                'shift_name' => $activeShift->shift_name,
                'shift_number' => $activeShift->shift_number,
                'cashier_id' => $activeShift->cashier_id,
                'cashier_name' => $cashierName,
                'business_date' => $activeShift->business_date,
                'business_date_formatted' => Carbon::parse($activeShift->business_date)->format('d M Y'),
                'opened_at' => $activeShift->opened_at,
                'opened_at_formatted' => Carbon::parse($activeShift->opened_at)->format('H:i'),
                'duration' => Carbon::parse($activeShift->opened_at)->diffForHumans(null, true),
                'starting_cash' => (float) $activeShift->starting_cash,
                'starting_cash_formatted' => 'Rp ' . number_format($activeShift->starting_cash, 0, ',', '.'),
                'cash_sales' => $cashSales,
                'cash_sales_formatted' => 'Rp ' . number_format($cashSales, 0, ',', '.'),
                'non_cash_sales' => $nonCashSales,
                'non_cash_sales_formatted' => 'Rp ' . number_format($nonCashSales, 0, ',', '.'),
                'total_sales' => $cashSales + $nonCashSales,
                'total_sales_formatted' => 'Rp ' . number_format($cashSales + $nonCashSales, 0, ',', '.'),
                'order_count' => $orderCount,
                'drawer_cash_in' => $drawerCashIn,
                'drawer_cash_in_formatted' => 'Rp ' . number_format($drawerCashIn, 0, ',', '.'),
                'drawer_cash_out' => $drawerCashOut,
                'drawer_cash_out_formatted' => 'Rp ' . number_format($drawerCashOut, 0, ',', '.'),
                'expected_cash' => $expectedCash,
                'expected_cash_formatted' => 'Rp ' . number_format($expectedCash, 0, ',', '.'),
                'recent_logs' => $recentLogs,
                'x_report_url' => route('admin.keuangan.shift-operational.x-report', $activeShift->id),
            ],
        ]);
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
