<?php

namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\Controller;
use App\Models\SysAdmin\Client;
use App\Models\SysAdmin\AuditLog;
use App\Services\Client\ClientDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class OutletOverviewController extends Controller
{
    /**
     * Tampilkan seluruh Outlet Cabang di semua Client
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $activeClients = Client::where('status', 'active')->where('delete_status', 0)->get();

        $allOutlets = collect();

        foreach ($activeClients as $client) {
            try {
                $connected = ClientDatabaseManager::connectToClient($client->database_name);
                if ($connected) {
                    $outlets = DB::connection(ClientDatabaseManager::CLIENT_CONNECTION)
                        ->table('outlets')
                        ->where('delete_status', 0)
                        ->get();

                    foreach ($outlets as $o) {
                        $allOutlets->push((object) [
                            'client_id' => $client->client_id,
                            'client_name' => $client->client_name,
                            'business_name' => $client->business_name,
                            'outlet_id' => $o->outlet_id,
                            'outlet_name' => $o->outlet_name,
                            'outlet_code' => $o->outlet_code,
                            'outlet_branch' => $o->outlet_branch ?? 'Pusat',
                            'outlet_phone' => $o->outlet_phone,
                            'outlet_email' => $o->outlet_email,
                            'outlet_address' => $o->outlet_address,
                            'outlet_status' => $o->outlet_status,
                            'created_at' => $o->created_at,
                        ]);
                    }
                }
            } catch (Exception $e) {
                // Ignore failure on single client
            }
        }

        ClientDatabaseManager::connectToCentral();

        if ($search) {
            $allOutlets = $allOutlets->filter(function ($item) use ($search) {
                return str_contains(strtolower($item->outlet_name), strtolower($search))
                    || str_contains(strtolower($item->client_name), strtolower($search))
                    || str_contains(strtolower($item->outlet_branch), strtolower($search));
            });
        }

        return view('sys_admin.outlets.index', [
            'activeMenu' => 'outlets',
            'outlets' => $allOutlets,
            'totalClients' => $activeClients->count(),
            'totalOutlets' => $allOutlets->count(),
        ]);
    }

    /**
     * Form Halaman Penuh Tambah Outlet Klien
     */
    public function create(Request $request)
    {
        $clients = Client::where('status', 'active')->where('delete_status', 0)->orderBy('client_name')->get();
        $selectedClientId = $request->query('client_id');
        $selectedClient = $selectedClientId ? $clients->firstWhere('client_id', $selectedClientId) : null;
        $fromClient = $request->query('from') === 'client' || $selectedClientId;
        $returnUrl = $fromClient && $selectedClientId 
            ? route('sys_admin.clients.show', $selectedClientId) 
            : route('sys_admin.outlets.index');

        return view('sys_admin.outlets.create', [
            'activeMenu' => 'outlets',
            'clients' => $clients,
            'selectedClientId' => $selectedClientId,
            'selectedClient' => $selectedClient,
            'fromClient' => $fromClient,
            'returnUrl' => $returnUrl,
        ]);
    }

    /**
     * Simpan Outlet Baru ke Database Fisik Klien
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|string|exists:clients,client_id',
            'outlet_name' => 'required|string|max:150',
            'outlet_code' => 'nullable|string|max:20',
            'outlet_branch' => 'nullable|string|max:100',
            'outlet_phone' => 'nullable|string|max:30',
            'outlet_email' => 'nullable|email|max:100',
            'outlet_address' => 'nullable|string|max:500',
            'outlet_status' => 'nullable|in:0,1',
        ]);

        $client = Client::where('client_id', $validated['client_id'])->firstOrFail();

        try {
            $connected = ClientDatabaseManager::connectToClient($client->database_name);
            if (!$connected) {
                return back()->withInput()->with('error', 'Gagal terhubung ke database fisik client ' . $client->client_name);
            }

            $outletId = (string) Str::ulid();
            $outletSlug = Str::slug($validated['outlet_name']) . '-' . Str::lower(Str::random(4));

            // 1. Simpan ke tabel outlets
            DB::connection(ClientDatabaseManager::CLIENT_CONNECTION)->table('outlets')->insert([
                'outlet_id' => $outletId,
                'outlet_name' => $validated['outlet_name'],
                'outlet_code' => $validated['outlet_code'] ?? strtoupper(Str::random(4)),
                'outlet_branch' => $validated['outlet_branch'] ?? 'Cabang',
                'outlet_slug' => $outletSlug,
                'outlet_phone' => $validated['outlet_phone'] ?? null,
                'outlet_email' => $validated['outlet_email'] ?? null,
                'outlet_address' => $validated['outlet_address'] ?? null,
                'outlet_status' => $validated['outlet_status'] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Inisialisasi setting_outlets
            DB::connection(ClientDatabaseManager::CLIENT_CONNECTION)->table('setting_outlets')->insert([
                'outlet_id' => $outletId,
                'outlet_name' => $validated['outlet_name'],
                'payment_timing' => 'post_payment',
                'theme' => 'spicy_bites',
                'created_by' => 'SysAdmin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Inisialisasi shift_settings
            DB::connection(ClientDatabaseManager::CLIENT_CONNECTION)->table('shift_settings')->insert([
                'outlet_id' => $outletId,
                'daily_cutoff_time' => '03:00:00',
                'shift_mode' => 'auto_master',
                'auto_lock_unclosed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Inisialisasi Pajak & Service Charge
            DB::connection(ClientDatabaseManager::CLIENT_CONNECTION)->table('taxes')->insert([
                'outlet_id' => $outletId,
                'tax_name' => 'PB1 Restoran (10%)',
                'rate_percent' => 10.00,
                'type' => 'exclusive',
                'is_active' => 1,
                'created_by' => 'SysAdmin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::connection(ClientDatabaseManager::CLIENT_CONNECTION)->table('service_charges')->insert([
                'outlet_id' => $outletId,
                'service_name' => 'Service Charge (5%)',
                'rate_percent' => 5.00,
                'is_taxable' => 1,
                'is_active' => 1,
                'created_by' => 'SysAdmin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan cabang: ' . $e->getMessage());
        } finally {
            ClientDatabaseManager::connectToCentral();
        }

        // Catat Audit Log
        AuditLog::record(
            action: 'outlet_created',
            clientId: $client->client_id,
            targetType: 'Outlet',
            targetId: $outletId,
            result: 'success',
            metadata: [
                'outlet_name' => $validated['outlet_name'],
                'outlet_code' => $validated['outlet_code'] ?? '',
                'client_name' => $client->client_name,
            ]
        );

        if ($request->input('redirect_to') === 'client') {
            return redirect()->route('sys_admin.clients.show', $client->client_id)
                ->with('success', "Cabang '{$validated['outlet_name']}' berhasil ditambahkan ke {$client->client_name}!");
        }

        return redirect()->route('sys_admin.outlets.index')
            ->with('success', "Cabang '{$validated['outlet_name']}' berhasil ditambahkan ke {$client->client_name}!");
    }
}
