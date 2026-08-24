<?php

namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SysAdmin\Client\StoreClientRequest;
use App\Models\SysAdmin\Client;
use App\Models\SysAdmin\Plan;
use App\Models\SysAdmin\Subscription;
use App\Models\SysAdmin\DatabaseConnection;
use App\Models\SysAdmin\AuditLog;
use App\Services\Client\ClientProvisioningService;
use App\Services\Client\ClientDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class ClientController extends Controller
{
    /**
     * Tampilkan daftar seluruh Client Tenant
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $planId = $request->input('plan_id');
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 50, 100])) {
            $perPage = 10;
        }

        $query = Client::with(['activeSubscription.plan', 'databaseConnection'])
            ->where('delete_status', 0);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('client_id', 'LIKE', "%{$search}%")
                  ->orWhere('client_code', 'LIKE', "%{$search}%")
                  ->orWhere('client_name', 'LIKE', "%{$search}%")
                  ->orWhere('business_name', 'LIKE', "%{$search}%")
                  ->orWhere('owner_name', 'LIKE', "%{$search}%")
                  ->orWhere('owner_email', 'LIKE', "%{$search}%");
            });
        }

        if ($status && in_array($status, ['active', 'provisioning', 'suspended', 'cancelled', 'failed_provisioning'])) {
            $query->where('status', $status);
        }

        if ($planId) {
            $query->whereHas('activeSubscription', function ($sq) use ($planId) {
                $sq->where('plan_id', $planId);
            });
        }

        $clients = $query->latest('id')->paginate($perPage);
        $plans = Plan::where('delete_status', 0)->where('is_active', 1)->orderBy('sort_order')->get();

        // Statistik Ringkasan
        $stats = [
            'total' => Client::where('delete_status', 0)->count(),
            'active' => Client::where('delete_status', 0)->where('status', 'active')->count(),
            'trial' => Client::where('delete_status', 0)->where('status', 'provisioning')->count(),
            'suspended' => Client::where('delete_status', 0)->whereIn('status', ['suspended', 'cancelled'])->count(),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('sys_admin.clients.partials._table_rows', compact('clients'))->render(),
                'pagination' => (string) $clients->links('vendor.pagination.modern'),
                'total' => $clients->total(),
            ]);
        }

        $suggestedClientId = ClientProvisioningService::generateClientId();

        return view('sys_admin.clients.index', [
            'activeMenu' => 'clients',
            'clients' => $clients,
            'plans' => $plans,
            'stats' => $stats,
            'suggestedClientId' => $suggestedClientId,
        ]);
    }

    /**
     * Form Modal Buat Client Baru
     */
    public function create()
    {
        $plans = Plan::where('delete_status', 0)->where('is_active', 1)->orderBy('sort_order')->get();
        $suggestedClientId = ClientProvisioningService::generateClientId();

        return view('sys_admin.clients.create', compact('plans', 'suggestedClientId'));
    }

    public function store(StoreClientRequest $request)
    {
        $validated = $request->validate($request->rules(), $request->messages());

        $result = ClientProvisioningService::provision($validated);

        if ($result['success']) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'redirect_url' => route('sys_admin.clients.show', $result['client']->client_id),
                ]);
            }

            return redirect()->route('sys_admin.clients.show', $result['client']->client_id)
                ->with('success', $result['message']);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return back()->withErrors(['error' => $result['message']])->withInput();
    }

    /**
     * Detail Client dengan 8 Tab Sesuai PRD Section 6
     */
    public function show($clientId)
    {
        $client = Client::where('client_id', $clientId)->firstOrFail();
        $plans = Plan::where('delete_status', 0)->where('is_active', 1)->get();
        $subscriptions = Subscription::where('client_id', $clientId)->with('plan')->latest('id')->get();
        $databaseConnection = DatabaseConnection::where('client_id', $clientId)->first();
        $auditLogs = AuditLog::where('client_id', $clientId)->latest('created_at')->take(20)->get();

        // Ambil data Outlets dan Users langsung dari Database Client terisolasi
        $clientOutlets = collect();
        $clientUsers = collect();
        $clientSetting = null;
        $clientDbError = null;

        try {
            $connected = ClientDatabaseManager::connectToClient($client->database_name);
            if ($connected) {
                $clientOutlets = DB::connection(ClientDatabaseManager::CLIENT_CONNECTION)
                    ->table('outlets')
                    ->where('delete_status', 0)
                    ->get();

                $clientUsers = DB::connection(ClientDatabaseManager::CLIENT_CONNECTION)
                    ->table('users')
                    ->get();

                $clientSetting = DB::connection(ClientDatabaseManager::CLIENT_CONNECTION)
                    ->table('setting_outlets')
                    ->first();
            } else {
                $clientDbError = 'Gagal terhubung ke database fisik client.';
            }
        } catch (\Exception $e) {
            $clientDbError = $e->getMessage();
        } finally {
            ClientDatabaseManager::connectToCentral();
        }

        return view('sys_admin.clients.show', [
            'activeMenu' => 'clients',
            'client' => $client,
            'plans' => $plans,
            'subscriptions' => $subscriptions,
            'databaseConnection' => $databaseConnection,
            'auditLogs' => $auditLogs,
            'clientOutlets' => $clientOutlets,
            'clientUsers' => $clientUsers,
            'clientSetting' => $clientSetting,
            'clientDbError' => $clientDbError,
        ]);
    }

    /**
     * Update Profil Client
     */
    public function update(Request $request, $clientId)
    {
        $client = Client::where('client_id', $clientId)->firstOrFail();

        $validated = $request->validate([
            'client_name' => 'required|string|max:150',
            'client_code' => 'nullable|string|max:50|regex:/^[A-Za-z0-9_]+$/',
            'business_name' => 'nullable|string|max:150',
            'owner_name' => 'required|string|max:100',
            'owner_email' => 'required|email|max:150',
            'owner_phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
        ], [
            'client_name.required' => 'Nama Klien wajib diisi.',
            'client_code.regex' => 'Kode Klien hanya boleh huruf, angka, atau underscore.',
            'owner_name.required' => 'Nama Pemilik wajib diisi.',
            'owner_email.required' => 'Email Pemilik wajib diisi.',
        ]);

        $client->update($validated);

        AuditLog::record(
            action: 'update_client_profile',
            clientId: $clientId,
            targetType: 'Client',
            targetId: (string) $client->id,
            result: 'success'
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profil informasi klien berhasil diperbarui.',
            ]);
        }

        return back()->with('success', 'Profil informasi klien berhasil diperbarui.');
    }

    /**
     * Suspend Client (Kunci Akses Masuk POS)
     */
    public function suspend(Request $request, $clientId)
    {
        $client = Client::where('client_id', $clientId)->firstOrFail();
        $reason = $request->input('reason', 'Ditangguhkan oleh System Administrator');

        $client->update([
            'status' => 'suspended',
            'suspension_reason' => $reason,
        ]);

        AuditLog::record(
            action: 'suspend_client',
            clientId: $clientId,
            targetType: 'Client',
            targetId: (string) $client->id,
            result: 'success',
            metadata: ['reason' => $reason]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Client {$client->client_name} ({$clientId}) berhasil ditangguhkan (Suspended).",
            ]);
        }

        return back()->with('success', "Client {$client->client_name} ({$clientId}) berhasil ditangguhkan.");
    }

    /**
     * Reactivate Client
     */
    public function reactivate(Request $request, $clientId)
    {
        $client = Client::where('client_id', $clientId)->firstOrFail();

        $client->update([
            'status' => 'active',
            'suspension_reason' => null,
        ]);

        AuditLog::record(
            action: 'reactivate_client',
            clientId: $clientId,
            targetType: 'Client',
            targetId: (string) $client->id,
            result: 'success'
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Client {$client->client_name} ({$clientId}) berhasil diaktifkan kembali.",
            ]);
        }

        return back()->with('success', "Client {$client->client_name} ({$clientId}) berhasil diaktifkan kembali.");
    }
}
