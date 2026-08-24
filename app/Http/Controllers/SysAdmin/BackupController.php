<?php

namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\Controller;
use App\Models\SysAdmin\Client;
use App\Models\SysAdmin\AuditLog;
use App\Services\Client\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    /**
     * Tampilkan Riwayat Snapshot Backup Seluruh Database Tenant
     */
    public function index()
    {
        $backups = BackupService::getAllBackups();
        $clients = Client::where('status', 'active')->where('delete_status', 0)->get();

        $totalSizeMb = array_sum(array_column($backups, 'file_size_mb'));

        return view('sys_admin.backups.index', [
            'activeMenu' => 'backups',
            'backups' => $backups,
            'clients' => $clients,
            'totalSizeMb' => round($totalSizeMb, 2),
            'totalBackups' => count($backups),
        ]);
    }

    /**
     * Trigger Manual Snapshot Backup per Client
     */
    public function createSnapshot(Request $request, $clientId)
    {
        $result = BackupService::createSnapshot($clientId);

        if ($result['success']) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($result);
            }
            return back()->with('success', $result['message']);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result, 500);
        }
        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * Unduh File Snapshot SQL
     */
    public function download($clientId, $fileName)
    {
        $filePath = storage_path("app/backups/{$clientId}/{$fileName}");

        if (!File::exists($filePath)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        AuditLog::record(
            action: 'backup_downloaded',
            clientId: $clientId,
            targetType: 'DatabaseBackup',
            targetId: $fileName,
            result: 'success'
        );

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/sql',
        ]);
    }

    /**
     * Hapus File Snapshot Backup
     */
    public function destroy($clientId, $fileName)
    {
        $filePath = storage_path("app/backups/{$clientId}/{$fileName}");

        if (File::exists($filePath)) {
            File::delete($filePath);

            AuditLog::record(
                action: 'backup_deleted',
                clientId: $clientId,
                targetType: 'DatabaseBackup',
                targetId: $fileName,
                result: 'success'
            );

            return back()->with('success', "File backup {$fileName} berhasil dihapus.");
        }

        return back()->withErrors(['error' => 'File backup tidak ditemukan.']);
    }
}
