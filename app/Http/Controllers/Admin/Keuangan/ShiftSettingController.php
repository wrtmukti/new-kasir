<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Admin\ShiftSetting;
use App\Models\Admin\Shift;
use App\Models\Admin\DailyClosing;
use Illuminate\Http\Request;

class ShiftSettingController extends Controller
{
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
     * Tampilan utama Master Setting Shift & Jam Cut-Off Restoran
     */
    public function index()
    {
        $outletId = session('active_outlet_id') ?? session('outlet_id') ?? 'COMP-001';

        $shiftSettingCol = $this->resolveBranchColumn(new ShiftSetting());
        $shiftCol = $this->resolveBranchColumn(new Shift());
        $closingCol = $this->resolveBranchColumn(new DailyClosing());

        $setting = $shiftSettingCol ? ShiftSetting::where($shiftSettingCol, $outletId)->first() : null;
        $setting = $setting ?? ShiftSetting::first() 
            ?? new ShiftSetting([
                'daily_cutoff_time' => '03:00:00',
                'shift_mode' => 'auto_master',
                'auto_lock_unclosed' => 1,
            ]);

        $primaryShift = $shiftCol ? Shift::where($shiftCol, $outletId)->first() : null;
        $primaryShift = $primaryShift ?? Shift::first()
            ?? new Shift([
                'shift_name' => 'Jam Operasional Toko',
                'start_time' => '08:00:00',
                'end_time' => '22:00:00',
                'default_starting_cash' => 300000,
            ]);

        $activeShift = $closingCol ? DailyClosing::where($closingCol, $outletId)->where('status', 'open')->latest()->first() : null;

        $shifts = $shiftCol ? Shift::where($shiftCol, $outletId)->orderBy('shift_number', 'asc')->get() : Shift::orderBy('shift_number', 'asc')->get();

        return view('admin.kasir.keuangan.setting-shift.index', compact('setting', 'shifts', 'primaryShift', 'activeShift'));
    }

    /**
     * Update Pengaturan Jam Cut-Off & Mode Kasir (Manual vs Otomatis)
     */
    public function updateCutoff(Request $request)
    {
        $request->validate([
            'daily_cutoff_time' => 'required|date_format:H:i',
            'shift_mode' => 'required|in:auto_master,manual,single_daily',
        ], [
            'daily_cutoff_time.required' => 'Jam cut-off operasional wajib diisi.',
            'daily_cutoff_time.date_format' => 'Format jam cut-off tidak valid (HH:MM).',
            'shift_mode.required' => 'Mode pengoperasian kasir wajib dipilih.',
            'shift_mode.in' => 'Mode pengoperasian kasir tidak valid.',
        ]);

        $outletId = session('active_outlet_id') ?? session('outlet_id') ?? 'COMP-001';
        $cutoffTime = $request->daily_cutoff_time . ':00';

        $shiftSettingCol = $this->resolveBranchColumn(new ShiftSetting());
        $setting = $shiftSettingCol ? ShiftSetting::where($shiftSettingCol, $outletId)->first() : ShiftSetting::first();

        $attributes = [
            'daily_cutoff_time' => $cutoffTime,
            'shift_mode' => $request->shift_mode,
            'auto_lock_unclosed' => $request->has('auto_lock_unclosed') ? 1 : 0,
        ];
        if ($shiftSettingCol) {
            $attributes[$shiftSettingCol] = $outletId;
        }

        if ($setting) {
            $setting->update($attributes);
        } else {
            $setting = ShiftSetting::create($attributes);
        }

        // Simpan / update jam operasional & modal awal kasir
        $startingCash = (float) str_replace(['.', ','], ['', '.'], $request->input('default_starting_cash', 300000));
        $openTime = $request->input('open_time', '08:00');
        $closeTime = $request->input('close_time', '22:00');
        if (strlen($openTime) == 5) $openTime .= ':00';
        if (strlen($closeTime) == 5) $closeTime .= ':00';

        $shiftCol = $this->resolveBranchColumn(new Shift());
        $primaryShift = $shiftCol ? Shift::where($shiftCol, $outletId)->first() : Shift::first();

        $shiftData = [
            'shift_name' => 'Jam Operasional Toko',
            'start_time' => $openTime,
            'end_time' => $closeTime,
            'default_starting_cash' => $startingCash,
            'is_active' => 1,
        ];
        if ($shiftCol) {
            $shiftData[$shiftCol] = $outletId;
        }

        if ($primaryShift) {
            $primaryShift->update($shiftData);
        } else {
            $shiftData['shift_number'] = 1;
            Shift::create($shiftData);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pengaturan jam operasional & buka tutup kasir berhasil diperbarui.',
                'data' => $setting,
            ]);
        }

        return redirect()->back()->with('success', 'Pengaturan jam operasional & buka tutup kasir berhasil diperbarui.');
    }

    /**
     * Tambah Master Shift Baru
     */
    public function storeShift(Request $request)
    {
        $request->validate([
            'shift_name' => 'required|string|max:50',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'default_starting_cash' => 'required|numeric|min:0',
        ], [
            'shift_name.required' => 'Nama shift wajib diisi.',
            'shift_name.max' => 'Nama shift maksimal 50 karakter.',
            'start_time.required' => 'Jam mulai shift wajib diisi.',
            'end_time.required' => 'Jam selesai shift wajib diisi.',
            'default_starting_cash.required' => 'Default modal awal kasir wajib diisi.',
            'default_starting_cash.numeric' => 'Default modal awal harus berupa angka.',
        ]);

        $outletId = session('active_outlet_id') ?? session('outlet_id') ?? 'COMP-001';
        $shiftCol = $this->resolveBranchColumn(new Shift());

        $nextShiftNumber = ($shiftCol ? Shift::where($shiftCol, $outletId) : Shift::query())->max('shift_number') + 1;

        $startTime = strlen($request->start_time) == 5 ? $request->start_time . ':00' : $request->start_time;
        $endTime = strlen($request->end_time) == 5 ? $request->end_time . ':00' : $request->end_time;
        if ($startTime === '24:00:00') $startTime = '00:00:00';
        if ($endTime === '24:00:00') $endTime = '23:59:00';

        $shiftData = [
            'shift_number' => $nextShiftNumber,
            'shift_name' => $request->shift_name,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'default_starting_cash' => $request->default_starting_cash,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ];
        if ($shiftCol) {
            $shiftData[$shiftCol] = $outletId;
        }

        $shift = Shift::create($shiftData);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Master Shift baru berhasil ditambahkan.',
                'data' => $shift,
            ]);
        }

        return redirect()->back()->with('success', 'Master Shift baru berhasil ditambahkan.');
    }

    /**
     * Update Master Shift
     */
    public function updateShift(Request $request, Shift $shift)
    {
        $request->validate([
            'shift_name' => 'required|string|max:50',
            'start_time' => 'required',
            'end_time' => 'required',
            'default_starting_cash' => 'required|numeric|min:0',
        ], [
            'shift_name.required' => 'Nama shift wajib diisi.',
            'start_time.required' => 'Jam mulai shift wajib diisi.',
            'end_time.required' => 'Jam selesai shift wajib diisi.',
            'default_starting_cash.required' => 'Default modal awal kasir wajib diisi.',
        ]);

        $startTime = strlen($request->start_time) == 5 ? $request->start_time . ':00' : $request->start_time;
        $endTime = strlen($request->end_time) == 5 ? $request->end_time . ':00' : $request->end_time;
        if ($startTime === '24:00:00') $startTime = '00:00:00';
        if ($endTime === '24:00:00') $endTime = '23:59:00';

        $shift->update([
            'shift_name' => $request->shift_name,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'default_starting_cash' => $request->default_starting_cash,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Master Shift berhasil diperbarui.',
                'data' => $shift,
            ]);
        }

        return redirect()->back()->with('success', 'Master Shift berhasil diperbarui.');
    }

    /**
     * Hapus Master Shift
     */
    public function destroyShift(Request $request, Shift $shift)
    {
        $shift->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Master Shift berhasil dihapus.',
            ]);
        }

        return redirect()->back()->with('success', 'Master Shift berhasil dihapus.');
    }
}
