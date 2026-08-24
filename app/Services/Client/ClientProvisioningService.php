<?php

namespace App\Services\Client;

use App\Models\SysAdmin\Client;
use App\Models\SysAdmin\Plan;
use App\Models\SysAdmin\Subscription;
use App\Models\SysAdmin\DatabaseConnection;
use App\Models\SysAdmin\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class ClientProvisioningService
{
    /**
     * Generate format ID Client unik berupa ULID (contoh: 01JM4X...).
     */
    public static function generateClientId(): string
    {
        return (string) Str::ulid();
    }

    /**
     * Generate nama database client yang valid dan terisolasi dengan format:
     * new_kasir_[client_code]_[env] (contoh: new_kasir_kopisenja_dev, new_kasir_geprekgambos_prod)
     */
    public static function generateDatabaseName(string $clientCode, string $env = 'dev'): string
    {
        $cleanCode = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $clientCode));
        $cleanCode = trim(substr($cleanCode, 0, 30), '_');
        $cleanEnv = in_array(strtolower($env), ['prod', 'production']) ? 'prod' : 'dev';

        return "new_kasir_{$cleanCode}_{$cleanEnv}";
    }

    /**
     * Jalankan workflow automated provisioning 11 langkah sesuai PRD.
     *
     * @param array $data
     * @return array
     */
    public static function provision(array $data): array
    {
        $clientId = $data['client_id'] ?? self::generateClientId();
        $clientName = trim($data['client_name']);
        $clientCode = trim($data['client_code'] ?? strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $clientName), 0, 8)));
        $environment = $data['environment'] ?? (config('app.env') === 'production' ? 'prod' : 'dev');
        $slug = $data['client_slug'] ?? Str::slug($clientName);
        if (Client::where('client_slug', $slug)->exists()) {
            $slug = $slug . '-' . strtolower($clientId);
        }
        $businessName = $data['business_name'] ?? $clientName;
        $ownerName = trim($data['owner_name']);
        $ownerEmail = trim($data['owner_email']);
        $ownerPhone = $data['owner_phone'] ?? null;
        $ownerPassword = $data['owner_password'] ?? 'password123';
        $address = $data['address'] ?? null;

        // Ambil plan atau default ke TRIAL
        $planId = $data['plan_id'] ?? null;
        $plan = null;
        if ($planId) {
            $plan = Plan::find($planId);
        }
        if (!$plan) {
            $plan = Plan::where('plan_code', 'TRIAL')->first() ?? Plan::first();
        }

        $dbHost = $data['db_host'] ?? env('DB_HOST', '127.0.0.1');
        $dbPort = $data['db_port'] ?? env('DB_PORT', 3306);
        $dbUsername = $data['db_username'] ?? env('DB_USERNAME', 'root');
        $dbPassword = $data['db_password'] ?? env('DB_PASSWORD', '');
        $databaseName = $data['database_name'] ?? self::generateDatabaseName($clientCode, $environment);

        $client = null;
        $createdDatabase = false;

        try {
            // STEP 1-3: Insert record Client di Central DB (Status: provisioning)
            $client = Client::create([
                'client_id' => $clientId,
                'client_slug' => $slug,
                'client_name' => $clientName,
                'client_code' => $clientCode,
                'business_name' => $businessName,
                'owner_name' => $ownerName,
                'owner_email' => $ownerEmail,
                'owner_phone' => $ownerPhone,
                'address' => $address,
                'database_name' => $databaseName,
                'db_host' => $dbHost,
                'db_port' => $dbPort,
                'db_username' => $dbUsername,
                'db_password' => $dbPassword,
                'status' => 'provisioning',
                'created_by' => auth('system_admin')->user()?->name ?? 'System Provisioner',
            ]);

            // STEP 4: Buat Database Client Baru
            $dbCreated = ClientDatabaseManager::createDatabase($databaseName);
            if (!$dbCreated) {
                throw new Exception("Gagal membuat database fisik MySQL: {$databaseName}");
            }
            $createdDatabase = true;

            // STEP 5: Jalankan Migrasi Skema POS ke Database Client
            $migrationResult = ClientDatabaseManager::runClientMigrations($databaseName);
            if (!$migrationResult['success']) {
                throw new Exception("Gagal menjalankan migrasi client: " . $migrationResult['message']);
            }

            // STEP 6-8: Seed Outlet Pertama, Owner User, dan Setting Outlet di Client DB
            ClientDatabaseManager::connectToClient($databaseName);

            // Seed Outlet Pertama
            $outletId = (string) Str::ulid();
            DB::connection('client')->table('outlets')->insert([
                'outlet_id' => $outletId,
                'outlet_name' => $businessName,
                'outlet_slug' => $slug,
                'outlet_code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $clientName), 0, 4)),
                'outlet_branch' => 'Pusat',
                'outlet_phone' => $ownerPhone,
                'outlet_email' => $ownerEmail,
                'outlet_address' => $address,
                'outlet_status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Seed Owner User di Client DB
            DB::connection('client')->table('users')->insert([
                'name' => $ownerName,
                'email' => $ownerEmail,
                'password' => Hash::make($ownerPassword),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Seed Default Setting Outlet di Client DB
            DB::connection('client')->table('setting_outlets')->insert([
                'outlet_id' => $outletId,
                'outlet_name' => $businessName,
                'payment_timing' => 'post_payment',
                'theme' => 'standard',
                'created_by' => 'System Provisioning',
                'delete_status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // STEP 9: Switch kembali ke Central DB dan buat Subscription Record
            ClientDatabaseManager::connectToCentral();

            $startDate = now()->toDateString();
            $trialDays = $plan ? $plan->trial_days : 14;
            $expiredDate = now()->addDays($trialDays > 0 ? $trialDays : 30)->toDateString();

            Subscription::create([
                'subscription_id' => 'SUB-' . $clientId . '-' . date('YmdHis'),
                'client_id' => $clientId,
                'plan_id' => $plan->id,
                'start_date' => $startDate,
                'expired_date' => $expiredDate,
                'status' => $trialDays > 0 ? 'trial' : 'active',
                'billing_reference' => 'INITIAL-PROV-' . strtoupper(Str::random(6)),
                'amount_paid' => 0,
                'auto_renew' => 1,
                'notes' => 'Langganan perdana saat provisioning client.',
                'created_by' => 'System Provisioning',
            ]);

            // STEP 10: Buat DatabaseConnection Metadata & Ping Health
            $testConn = ClientDatabaseManager::testConnection($databaseName);

            DatabaseConnection::updateOrCreate(
                ['client_id' => $clientId],
                [
                    'database_name' => $databaseName,
                    'server_host' => $dbHost,
                    'server_port' => $dbPort,
                    'connection_status' => $testConn['success'] ? 'connected' : 'warning',
                    'latency_ms' => $testConn['latency_ms'] ?? 0,
                    'tables_count' => $testConn['tables_count'] ?? 0,
                    'migration_version' => '2026_08_24_000001_create_setting_outlets_table',
                    'last_health_check_at' => now(),
                    'status_message' => $testConn['message'] ?? 'Connected successfully',
                ]
            );

            // STEP 11: Mark Client Active & Catat Audit Log
            $client->update([
                'status' => 'active',
                'provisioned_at' => now(),
                'last_active_at' => now(),
            ]);

            AuditLog::record(
                action: 'client_provisioned',
                clientId: $clientId,
                targetType: 'Client',
                targetId: (string) $client->id,
                result: 'success',
                metadata: [
                    'database_name' => $databaseName,
                    'plan' => $plan->plan_name,
                    'owner_email' => $ownerEmail,
                ]
            );

            return [
                'success' => true,
                'client' => $client->fresh(),
                'database_name' => $databaseName,
                'message' => "Client {$clientName} ({$clientId}) berhasil diprovisioning secara otomatis!",
            ];
        } catch (Exception $e) {
            Log::error("[ClientProvisioningService] Provisioning failed for {$clientName}: " . $e->getMessage());

            // Switch kembali ke Central DB
            ClientDatabaseManager::connectToCentral();

            // Rollback database jika sudah sempat dibuat
            if ($createdDatabase && $databaseName) {
                ClientDatabaseManager::dropDatabase($databaseName);
            }

            // Tandai status client gagal
            if ($client) {
                $client->update([
                    'status' => 'failed_provisioning',
                    'suspension_reason' => 'Provisioning Error: ' . $e->getMessage(),
                ]);
            }

            AuditLog::record(
                action: 'client_provisioning_failed',
                clientId: $clientId,
                targetType: 'Client',
                targetId: $client ? (string) $client->id : null,
                result: 'failure',
                metadata: [
                    'error' => $e->getMessage(),
                    'database_name' => $databaseName,
                ]
            );

            return [
                'success' => false,
                'message' => 'Gagal memprovisioning client: ' . $e->getMessage(),
            ];
        }
    }
}
