<?php

namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\Controller;
use App\Models\SysAdmin\Plan;
use App\Models\SysAdmin\AuditLog;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Tampilkan daftar Master Plans SaaS
     */
    public function index()
    {
        $plans = Plan::where('delete_status', 0)->withCount('subscriptions')->orderBy('sort_order')->get();

        return view('sys_admin.plans.index', [
            'activeMenu' => 'plans',
            'plans' => $plans,
        ]);
    }

    /**
     * Simpan / Update Plan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_code' => 'required|string|max:50',
            'plan_name' => 'required|string|max:100',
            'badge_label' => 'nullable|string|max:50',
            'max_outlets' => 'required|integer|min:1',
            'max_users' => 'required|integer|min:1',
            'max_storage_mb' => 'required|integer|min:50',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'trial_days' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $planId = $request->input('plan_id');

        if ($planId) {
            $plan = Plan::findOrFail($planId);
            $plan->update($validated);
            $msg = "Paket SaaS '{$plan->plan_name}' berhasil diperbarui.";
            $action = 'update_plan';
        } else {
            $plan = Plan::create($validated);
            $msg = "Paket SaaS '{$plan->plan_name}' berhasil ditambahkan.";
            $action = 'create_plan';
        }

        AuditLog::record(
            action: $action,
            targetType: 'Plan',
            targetId: (string) $plan->id,
            result: 'success'
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return back()->with('success', $msg);
    }
}
