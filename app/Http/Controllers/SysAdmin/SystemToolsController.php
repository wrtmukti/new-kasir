<?php

namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\Controller;
use App\Models\SysAdmin\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Exception;

class SystemToolsController extends Controller
{
    /**
     * Tampilkan Halaman System Tools & Maintenance
     */
    public function index()
    {
        return view('sys_admin.tools.index', [
            'activeMenu' => 'tools',
        ]);
    }

    /**
     * Jalankan Operasi System Tool Pemeliharaan
     */
    public function runTool(Request $request)
    {
        $tool = $request->input('tool');
        $output = '';

        try {
            switch ($tool) {
                case 'clear_cache':
                    Artisan::call('cache:clear');
                    $output = "Application cache cleared successfully.";
                    break;

                case 'clear_config':
                    Artisan::call('config:clear');
                    $output = "Configuration cache cleared.";
                    break;

                case 'clear_route':
                    Artisan::call('route:clear');
                    $output = "Route cache cleared.";
                    break;

                case 'clear_view':
                    Artisan::call('view:clear');
                    $output = "Compiled Blade views cleared.";
                    break;

                case 'optimize':
                    Artisan::call('optimize:clear');
                    $output = "All optimizations & caches cleared.";
                    break;

                case 'queue_restart':
                    Artisan::call('queue:restart');
                    $output = "Queue workers restart signal broadcasted.";
                    break;

                default:
                    throw new Exception("Tool pemeliharaan tidak dikenal.");
            }

            AuditLog::record(
                action: 'run_system_tool',
                targetType: 'SystemMaintenance',
                targetId: $tool,
                result: 'success',
                metadata: ['output' => $output]
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Operasi '{$tool}' berhasil dijalankan: " . $output,
                ]);
            }

            return back()->with('success', "Operasi berhasil: " . $output);
        } catch (Exception $e) {
            AuditLog::record(
                action: 'run_system_tool_failed',
                targetType: 'SystemMaintenance',
                targetId: $tool,
                result: 'failure',
                metadata: ['error' => $e->getMessage()]
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Gagal menjalankan tool: " . $e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
