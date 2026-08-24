<?php

namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\Controller;
use App\Models\SysAdmin\Client;
use App\Models\SysAdmin\AuditLog;
use App\Models\SysAdmin\User;
use App\Services\Client\ClientDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class ImpersonationController extends Controller
{
    /**
     * Mulai sesi impersonation ("Login as Client")
     */
    public function start(Request $request, $clientId)
    {
        $client = Client::where('client_id', $clientId)->firstOrFail();

        if ($client->status !== 'active') {
            return back()->withErrors(['error' => "Tidak dapat login ke client dengan status {$client->status}."]);
        }

        $sysAdmin = Auth::guard('system_admin')->user();
        if (!$sysAdmin) {
            return redirect()->route('sys_admin.login');
        }

        try {
            // 1. Hubungkan ke database tenant
            $connected = ClientDatabaseManager::connectToClient($client->database_name);
            if (!$connected) {
                throw new Exception("Gagal terhubung ke database klien {$client->database_name}.");
            }

            // 2. Ambil user admin / owner di database tenant
            $userRecord = DB::connection('client')->table('users')
                ->where('email', $client->owner_email)
                ->first();

            if (!$userRecord) {
                $userRecord = DB::connection('client')->table('users')->first();
            }

            if (!$userRecord) {
                throw new Exception("User owner tidak ditemukan di database klien.");
            }

            // 3. Simpan state impersonation ke Session
            session([
                'is_impersonating' => true,
                'impersonator_id' => $sysAdmin->id,
                'impersonator_name' => $sysAdmin->name,
                'impersonator_role' => $sysAdmin->role,
                'impersonated_client_id' => $client->client_id,
                'impersonated_client_name' => $client->client_name,
                'impersonated_database_name' => $client->database_name,
                'impersonated_user_id' => $userRecord->id,
                'impersonated_user_email' => $userRecord->email,
                'client_database' => $client->database_name,
                'client_id' => $client->client_id,
                'client_name' => $client->client_name,
                'client_code' => $client->client_code,
                'business_name' => $client->business_name,
                'tenant_database' => $client->database_name,
                'tenant_client_id' => $client->client_id,
                'tenant_client_name' => $client->client_name,
                'tenant_business_name' => $client->business_name,
            ]);

            // 4. Login ke guard web sebagai user tenant
            $user = User::find($userRecord->id);
            if ($user) {
                Auth::guard('web')->login($user);
            }

            // 5. Catat Audit Log
            AuditLog::record(
                action: 'impersonation_start',
                clientId: $client->client_id,
                targetType: 'User',
                targetId: (string) $userRecord->id,
                result: 'success',
                metadata: [
                    'impersonator' => $sysAdmin->name,
                    'impersonator_role' => $sysAdmin->role,
                    'client_name' => $client->client_name,
                ]
            );

            return redirect()->route('admin.dashboard')
                ->with('success', "Mode Impersonation aktif untuk klien {$client->client_name}.");
        } catch (Exception $e) {
            ClientDatabaseManager::connectToCentral();
            return back()->withErrors(['error' => 'Gagal memulai impersonation: ' . $e->getMessage()]);
        }
    }

    /**
     * Hentikan sesi impersonation dan kembali ke System Admin
     */
    public function stop()
    {
        $clientId = session('impersonated_client_id');
        $impersonatorName = session('impersonator_name', 'System Administrator');
        $userId = session('impersonated_user_id');

        // Logout dari guard web tenant
        Auth::guard('web')->logout();

        // Hapus session keys impersonation
        session()->forget([
            'is_impersonating',
            'impersonator_id',
            'impersonator_name',
            'impersonator_role',
            'impersonated_client_id',
            'impersonated_client_name',
            'impersonated_database_name',
            'impersonated_user_id',
            'impersonated_user_email',
        ]);

        // Hubungkan kembali ke Central Database
        ClientDatabaseManager::connectToCentral();

        // Catat Audit Log
        if ($clientId) {
            AuditLog::record(
                action: 'impersonation_stop',
                clientId: $clientId,
                targetType: 'User',
                targetId: (string) $userId,
                result: 'success',
                metadata: ['impersonator' => $impersonatorName]
            );
        }

        if ($clientId) {
            return redirect()->route('sys_admin.clients.show', $clientId)
                ->with('success', 'Sesi impersonation dihentikan. Anda telah kembali ke System Admin.');
        }

        return redirect()->route('sys_admin.dashboard')
            ->with('success', 'Sesi impersonation dihentikan.');
    }
}
