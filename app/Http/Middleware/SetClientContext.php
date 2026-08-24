<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Client\ClientDatabaseManager;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetClientContext
{
    /**
     * Handle an incoming request and switch DB to the active client.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah ada client_database di session (dari login atau impersonation)
        $clientDb = session('client_database') ?? session('tenant_database');

        if ($clientDb) {
            $connected = ClientDatabaseManager::connectToClient($clientDb);
            if (!$connected) {
                // Database client lama sudah tidak ada/di-reset -> Bersihkan sesi kadaluarsa
                $request->session()->forget([
                    'client_database',
                    'client_id',
                    'client_name',
                    'client_code',
                    'business_name',
                    'tenant_database',
                    'tenant_client_id',
                    'tenant_client_name',
                    'tenant_business_name',
                    'is_impersonating',
                    'impersonated_client_id',
                    'impersonated_client_name',
                ]);
                Auth::guard('web')->logout();
                ClientDatabaseManager::connectToCentral();
            } else {
                if (!session('client_name')) {
                    // Auto-enrich client profile info jika belum ada di sesi
                    $clientInfo = \App\Models\SysAdmin\Client::where('database_name', $clientDb)->first();
                    if ($clientInfo) {
                        session([
                            'client_id' => $clientInfo->client_id,
                            'client_name' => $clientInfo->client_name,
                            'client_code' => $clientInfo->client_code,
                            'business_name' => $clientInfo->business_name,
                        ]);
                    }
                }

                // Pastikan ada active_outlet_id di session
                if (!session('active_outlet_id') || !session('outlet_id')) {
                    $firstOutlet = \App\Models\Admin\Outlet::where('delete_status', 0)->first();
                    if ($firstOutlet) {
                        session([
                            'active_outlet_id' => $firstOutlet->outlet_id,
                            'outlet_id' => $firstOutlet->outlet_id,
                            'active_outlet_name' => $firstOutlet->outlet_name,
                        ]);
                    }
                }
            }
        } else {
            ClientDatabaseManager::connectToCentral();
        }

        return $next($request);
    }
}
