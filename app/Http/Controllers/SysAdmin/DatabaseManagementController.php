<?php

namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\Controller;
use App\Models\SysAdmin\DatabaseConnection;
use App\Models\SysAdmin\Client;
use App\Models\SysAdmin\AuditLog;
use App\Services\Client\ClientDatabaseManager;
use Illuminate\Http\Request;

class DatabaseManagementController extends Controller
{
    /**
     * Tampilkan daftar seluruh Database Client Tenant
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 50, 100])) {
            $perPage = 10;
        }

        $query = DatabaseConnection::with('client')
            ->where('delete_status', 0);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('client_id', 'LIKE', "%{$search}%")
                  ->where('database_name', 'LIKE', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('client_name', 'LIKE', "%{$search}%")
                        ->orWhere('business_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($status && in_array($status, ['connected', 'disconnected', 'warning', 'error'])) {
            $query->where('connection_status', $status);
        }

        $databases = $query->latest('id')->paginate($perPage);

        // Ringkasan Statistik Database
        $totalDbs = DatabaseConnection::where('delete_status', 0)->count();
        $connectedDbs = DatabaseConnection::where('delete_status', 0)->where('connection_status', 'connected')->count();
        $avgLatency = round(DatabaseConnection::where('delete_status', 0)->avg('latency_ms') ?? 0, 2);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('sys_admin.databases.partials._table_rows', compact('databases'))->render(),
                'pagination' => (string) $databases->links('vendor.pagination.modern'),
                'total' => $databases->total(),
            ]);
        }

        return view('sys_admin.databases.index', [
            'activeMenu' => 'databases',
            'databases' => $databases,
            'stats' => [
                'total' => $totalDbs,
                'connected' => $connectedDbs,
                'avg_latency' => $avgLatency,
            ],
        ]);
    }

    /**
     * AJAX Test Connection ke Target Database Client
     */
    public function testConnection($clientId)
    {
        $client = Client::where('client_id', $clientId)->first();
        if (!$client) {
            return response()->json(['success' => false, 'message' => 'Client tidak ditemukan.'], 404);
        }

        $result = ClientDatabaseManager::testConnection($client->database_name);

        // Update database connection status record
        $dbConn = DatabaseConnection::updateOrCreate(
            ['client_id' => $clientId],
            [
                'database_name' => $client->database_name,
                'server_host' => $client->db_host,
                'server_port' => $client->db_port,
                'connection_status' => $result['success'] ? 'connected' : 'disconnected',
                'latency_ms' => $result['latency_ms'] ?? 0,
                'tables_count' => $result['tables_count'] ?? 0,
                'last_health_check_at' => now(),
                'status_message' => $result['message'],
            ]
        );

        AuditLog::record(
            action: 'test_database_connection',
            clientId: $clientId,
            targetType: 'DatabaseConnection',
            targetId: (string) $dbConn->id,
            result: $result['success'] ? 'success' : 'failure',
            metadata: ['latency_ms' => $result['latency_ms'] ?? null]
        );

        return response()->json([
            'success' => $result['success'],
            'latency_ms' => $result['latency_ms'] ?? 0,
            'tables_count' => $result['tables_count'] ?? 0,
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    /**
     * AJAX Jalankan Migrasi Skema ke Target Database Client
     */
    public function runMigration($clientId)
    {
        $client = Client::where('client_id', $clientId)->first();
        if (!$client) {
            return response()->json(['success' => false, 'message' => 'Client tidak ditemukan.'], 404);
        }

        $result = ClientDatabaseManager::runClientMigrations($client->database_name);

        if ($result['success']) {
            DatabaseConnection::updateOrCreate(
                ['client_id' => $clientId],
                [
                    'connection_status' => 'connected',
                    'last_health_check_at' => now(),
                ]
            );

            AuditLog::record(
                action: 'run_tenant_migration',
                clientId: $clientId,
                targetType: 'Database',
                targetId: $client->database_name,
                result: 'success'
            );

            return response()->json([
                'success' => true,
                'message' => "Migrasi berhasil dijalankan pada database {$client->database_name}.",
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "Migrasi gagal: " . $result['message'],
        ], 500);
    }
}
