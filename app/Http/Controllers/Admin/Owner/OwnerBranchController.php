<?php

namespace App\Http\Controllers\Admin\Owner;

use App\Http\Controllers\Controller;
use App\Models\Admin\Outlet;
use App\Models\Admin\ShiftSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OwnerBranchController extends Controller
{
    public function index()
    {
        $outlets = Outlet::where('delete_status', 0)
            ->withCount(['products' => fn($q) => $q->where('delete_status', 0)])
            ->with(['shiftSetting'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.owner.branches.index', compact('outlets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'outlet_name' => 'required|string|max:150',
            'outlet_code' => 'required|string|max:20',
            'outlet_branch' => 'nullable|string|max:100',
            'outlet_phone' => 'nullable|string|max:30',
            'outlet_address' => 'nullable|string|max:255',
            'cutoff_time' => 'nullable|string|max:10',
        ]);

        $outletId = (string) Str::ulid();

        $outlet = Outlet::create([
            'outlet_id' => $outletId,
            'outlet_name' => $request->outlet_name,
            'outlet_code' => strtoupper($request->outlet_code),
            'outlet_branch' => $request->outlet_branch ?? 'Cabang',
            'outlet_slug' => Str::slug($request->outlet_name . '-' . $request->outlet_code),
            'outlet_phone' => $request->outlet_phone,
            'outlet_address' => $request->outlet_address,
            'outlet_status' => 1,
            'delete_status' => 0,
        ]);

        // Default cut-off shift setting
        ShiftSetting::create([
            'outlet_id' => $outletId,
            'daily_cutoff_time' => $request->cutoff_time ?? '03:00:00',
            'shift_mode' => 'auto_master',
            'auto_lock_unclosed' => 1,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Cabang outlet baru berhasil ditambahkan!',
                'data' => $outlet
            ]);
        }

        return redirect()->route('admin.owner.branches.index')->with('success', 'Cabang ' . $outlet->outlet_name . ' berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $outlet = Outlet::where('outlet_id', $id)->firstOrFail();

        $request->validate([
            'outlet_name' => 'required|string|max:150',
            'outlet_code' => 'required|string|max:20',
            'outlet_branch' => 'nullable|string|max:100',
            'outlet_phone' => 'nullable|string|max:30',
            'outlet_address' => 'nullable|string|max:255',
            'cutoff_time' => 'nullable|string|max:10',
            'outlet_status' => 'required|in:0,1',
        ]);

        $outlet->update([
            'outlet_name' => $request->outlet_name,
            'outlet_code' => strtoupper($request->outlet_code),
            'outlet_branch' => $request->outlet_branch,
            'outlet_phone' => $request->outlet_phone,
            'outlet_address' => $request->outlet_address,
            'outlet_status' => (int) $request->outlet_status,
        ]);

        if ($request->filled('cutoff_time')) {
            ShiftSetting::updateOrCreate(
                ['outlet_id' => $outlet->outlet_id],
                ['daily_cutoff_time' => $request->cutoff_time, 'shift_mode' => 'auto_master', 'auto_lock_unclosed' => 1]
            );
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data cabang berhasil diperbarui!',
                'data' => $outlet
            ]);
        }

        return redirect()->route('admin.owner.branches.index')->with('success', 'Data cabang ' . $outlet->outlet_name . ' berhasil diperbarui!');
    }

    public function destroy(Request $request, $id)
    {
        $outlet = Outlet::where('outlet_id', $id)->firstOrFail();
        $outlet->update(['delete_status' => 1]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Cabang berhasil dinonaktifkan!',
            ]);
        }

        return redirect()->route('admin.owner.branches.index')->with('success', 'Cabang ' . $outlet->outlet_name . ' berhasil dinonaktifkan!');
    }
}
