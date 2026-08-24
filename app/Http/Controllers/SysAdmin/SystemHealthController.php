<?php

namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\Controller;
use App\Models\SysAdmin\DatabaseConnection;
use App\Models\SysAdmin\Client;
use App\Services\Client\ClientDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class SystemHealthController extends Controller
{
    /**
     * Tampilkan Halaman Monitoring Kesehatan Sistem & Infrastruktur
     */
    public function index()
    {
        // 1. Central Database Health
        $centralHealth = 'Connected';
        $centralLatency = 0;
        try {
            $t0 = microtime(true);
            DB::connection('central')->select('SELECT 1');
            $centralLatency = round((microtime(true) - $t0) * 1000, 2);
        } catch (Exception $e) {
            $centralHealth = 'Disconnected';
        }

        // 2. Server Environment Metrics
        $phpVersion = PHP_VERSION;
        $memoryUsage = round(memory_get_usage(true) / 1048576, 2);
        $memoryPeak = round(memory_get_peak_usage(true) / 1048576, 2);
        $memoryLimit = ini_get('memory_limit');

        // Disk Storage Information
        $diskTotal = @disk_total_space(base_path());
        $diskFree = @disk_free_space(base_path());
        $diskUsed = $diskTotal - $diskFree;
        $diskUsedPercent = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 1) : 0;

        $diskInfo = [
            'total_gb' => round($diskTotal / 1073741824, 2),
            'free_gb' => round($diskFree / 1073741824, 2),
            'used_gb' => round($diskUsed / 1073741824, 2),
            'used_percent' => $diskUsedPercent,
        ];

        // 3. Database Connections List
        $dbConnections = DatabaseConnection::with('client')->where('delete_status', 0)->get();
        $healthyCount = $dbConnections->where('connection_status', 'connected')->count();
        $totalCount = $dbConnections->count();
        $avgLatency = round($dbConnections->avg('latency_ms') ?? 0, 2);

        return view('sys_admin.health.index', [
            'activeMenu' => 'health',
            'central' => [
                'status' => $centralHealth,
                'latency_ms' => $centralLatency,
                'database' => config('database.connections.central.database'),
                'host' => config('database.connections.central.host'),
            ],
            'server' => [
                'php_version' => $phpVersion,
                'memory_usage_mb' => $memoryUsage,
                'memory_peak_mb' => $memoryPeak,
                'memory_limit' => $memoryLimit,
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'PHP CLI / Built-in',
                'os' => PHP_OS_FAMILY,
            ],
            'disk' => $diskInfo,
            'databases' => $dbConnections,
            'stats' => [
                'total' => $totalCount,
                'healthy' => $healthyCount,
                'avg_latency' => $avgLatency,
            ],
        ]);
    }

    /**
     * AJAX Batch Ping Semua Database Client
     */
    public function pingAll(Request $request)
    {
        $clients = Client::where('status', 'active')->where('delete_status', 0)->get();
        $results = [];

        foreach ($clients as $client) {
            $test = ClientDatabaseManager::testConnection($client->database_name);

            DatabaseConnection::updateOrCreate(
                ['client_id' => $client->client_id],
                [
                    'database_name' => $client->database_name,
                    'server_host' => $client->db_host,
                    'server_port' => $client->db_port,
                    'connection_status' => $test['success'] ? 'connected' : 'disconnected',
                    'latency_ms' => $test['latency_ms'] ?? 0,
                    'tables_count' => $test['tables_count'] ?? 0,
                    'last_health_check_at' => now(),
                    'status_message' => $test['message'],
                ]
            );

            $results[] = [
                'client_id' => $client->client_id,
                'database' => $client->database_name,
                'status' => $test['status'],
                'latency_ms' => $test['latency_ms'] ?? 0,
            ];
        }

        return response()->json([
            'success' => true,
            'total_checked' => count($results),
            'results' => $results,
            'message' => 'Batch health ping selesai dijalankan untuk seluruh tenant!',
        ]);
    }
}
