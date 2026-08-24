<?php

namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\Controller;
use App\Models\SysAdmin\Subscription;
use App\Models\SysAdmin\Client;
use App\Models\SysAdmin\Plan;
use App\Models\SysAdmin\AuditLog;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Tampilkan daftar seluruh Subscription Langganan Tenant
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $planId = $request->input('plan_id');

        $query = Subscription::with(['client', 'plan'])
            ->where('delete_status', 0);

        if ($status) {
            $query->where('status', $status);
        }

        if ($planId) {
            $query->where('plan_id', $planId);
        }

        $subscriptions = $query->latest('id')->paginate(10);
        $plans = Plan::where('delete_status', 0)->where('is_active', 1)->get();
        $clients = Client::where('delete_status', 0)->get();

        return view('sys_admin.subscriptions.index', [
            'activeMenu' => 'subscriptions',
            'subscriptions' => $subscriptions,
            'plans' => $plans,
            'clients' => $clients,
        ]);
    }

    /**
     * Perpanjang / Update Subscription Klien
     */
    public function extend(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);

        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'expired_date' => 'required|date|after_or_equal:today',
            'status' => 'required|in:trial,active,expiring_soon,expired,suspended,cancelled',
            'amount_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        $subscription->update($validated);

        AuditLog::record(
            action: 'extend_subscription',
            clientId: $subscription->client_id,
            targetType: 'Subscription',
            targetId: (string) $subscription->id,
            result: 'success',
            metadata: ['new_expired_date' => $validated['expired_date']]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Langganan untuk client {$subscription->client_id} berhasil diperpanjang hingga {$subscription->expired_date->format('d M Y')}.",
            ]);
        }

        return back()->with('success', 'Langganan berhasil diperbarui.');
    }
}
