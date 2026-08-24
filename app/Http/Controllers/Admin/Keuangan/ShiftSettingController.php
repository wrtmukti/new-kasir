<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Admin\ShiftSetting;
use App\Models\Admin\Shift;
use Illuminate\Http\Request;

class ShiftSettingController extends Controller
{
    /**
     * Tampilan utama Master Setting Shift & Jam Cut-Off Restoran
     */
    public function index()
    {
        $companyId = session('outlet_id') ?? 'COMP-001';

        $setting = ShiftSetting::where('outlet_id', $companyId)->first() 
            ?? ShiftSetting::first() 
            ?? new ShiftSetting([
                'daily_cutoff_time' => '03:00:00',
                'shift_mode' => 'auto_master',
                'auto_lock_unclosed' => 1,
            ]);

        $shifts = Shift::where('outlet_id', $companyId)
            ->orderBy('shift_number', 'asc')
            ->get();

        return view('admin.keuangan.setting-shift.index', compact('setting', 'shifts'));
    }

    /**
     * Update Pengaturan Jam Cut-Off & Mode Shift
     */
    public function updateCutoff(Request $request)
    {
        $request->validate([
            'daily_cutoff_time' => 'required|date_format:H:i',
            'shift_mode' => 'required|in:auto_master,manual,single_daily',
        ], [
            'daily_cutoff_time.required' => 'Jam cut-off operasional wajib diisi.',
            'daily_cutoff_time.date_format' => 'Format jam cut-off tidak valid (HH:MM).',
            'shift_mode.required' => 'Mode pengoperasian shift wajib dipilih.',
            'shift_mode.in' => 'Mode shift tidak valid.',
        ]);

        $companyId = session('outlet_id') ?? 'COMP-001';

        $cutoffTime = $request->daily_cutoff_time . ':00';

        $setting = ShiftSetting::updateOrCreate(
            ['outlet_id' => $companyId],
            [
                'daily_cutoff_time' => $cutoffTime,
                'shift_mode' => $request->shift_mode,
                'auto_lock_unclosed' => $request->has('auto_lock_unclosed') ? 1 : 0,
            ]
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pengaturan jam cut-off operasional & mode shift berhasil diperbarui.',
                'data' => $setting,
            ]);
        }

        return redirect()->route('admin.keuangan.setting-shift.index')->with('success', 'Pengaturan jam cut-off operasional & mode shift berhasil diperbarui.');
    }

    /**
     * Tambah Master Shift Baru
     */
    public function storeShift(Request $request)
    {
        $request->validate([
            'shift_name' => 'required|string|max:50',
            'start_time' => 'required',
            'end_time' => 'required',
            'default_starting_cash' => 'required|numeric|min:0',
        ], [
            'shift_name.required' => 'Nama shift wajib diisi.',
            'shift_name.max' => 'Nama shift maksimal 50 karakter.',
            'start_time.required' => 'Jam mulai shift wajib diisi.',
            'end_time.required' => 'Jam selesai shift wajib diisi.',
            'default_starting_cash.required' => 'Default modal awal kasir wajib diisi.',
            'default_starting_cash.numeric' => 'Default modal awal harus berupa angka.',
        ]);

        $companyId = session('outlet_id') ?? 'COMP-001';

        $nextShiftNumber = Shift::where('outlet_id', $companyId)->max('shift_number') + 1;

        $startTime = strlen($request->start_time) == 5 ? $request->start_time . ':00' : $request->start_time;
        $endTime = strlen($request->end_time) == 5 ? $request->end_time . ':00' : $request->end_time;

        $shift = Shift::create([
            'outlet_id' => $companyId,
            'shift_number' => $nextShiftNumber,
            'shift_name' => $request->shift_name,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'default_starting_cash' => $request->default_starting_cash,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Master Shift baru berhasil ditambahkan.',
                'data' => $shift,
            ]);
        }

        return redirect()->route('admin.keuangan.setting-shift.index')->with('success', 'Master Shift baru berhasil ditambahkan.');
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

        return redirect()->route('admin.keuangan.setting-shift.index')->with('success', 'Master Shift berhasil diperbarui.');
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

        return redirect()->route('admin.keuangan.setting-shift.index')->with('success', 'Master Shift berhasil dihapus.');
    }
}
