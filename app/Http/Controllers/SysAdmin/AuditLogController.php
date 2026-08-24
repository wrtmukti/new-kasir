<?php

namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\Controller;
use App\Models\SysAdmin\AuditLog;
use App\Models\SysAdmin\Client;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Tampilkan Riwayat Central Audit Log Platform
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $action = $request->input('action');
        $clientId = $request->input('client_id');
        $result = $request->input('result');
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 50, 100])) {
            $perPage = 10;
        }

        $query = AuditLog::with('client');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('actor_name', 'LIKE', "%{$search}%")
                  ->orWhere('actor_role', 'LIKE', "%{$search}%")
                  ->orWhere('ip_address', 'LIKE', "%{$search}%")
                  ->orWhere('target_type', 'LIKE', "%{$search}%")
                  ->orWhere('target_id', 'LIKE', "%{$search}%")
                  ->orWhere('client_id', 'LIKE', "%{$search}%");
            });
        }

        if ($action) {
            $query->where('action', $action);
        }

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($result && in_array($result, ['success', 'failure'])) {
            $query->where('result', $result);
        }

        $auditLogs = $query->latest('created_at')->paginate($perPage);

        // Distinct actions for filter dropdown
        $availableActions = AuditLog::select('action')->distinct()->pluck('action');
        $clients = Client::where('delete_status', 0)->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('sys_admin.audit_logs.partials._table_rows', compact('auditLogs'))->render(),
                'pagination' => (string) $auditLogs->links('vendor.pagination.modern'),
                'total' => $auditLogs->total(),
            ]);
        }

        return view('sys_admin.audit_logs.index', [
            'activeMenu' => 'audit_logs',
            'auditLogs' => $auditLogs,
            'availableActions' => $availableActions,
            'clients' => $clients,
        ]);
    }
}
