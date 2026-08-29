<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SysAdmin\SystemUser;
use App\Models\SysAdmin\Plan;
use App\Models\SysAdmin\SystemSetting;
use App\Models\SysAdmin\Client;
use App\Models\SysAdmin\Subscription;
use App\Models\SysAdmin\DatabaseConnection;
use App\Models\SysAdmin\AuditLog;
use App\Services\Client\ClientDatabaseManager;
use App\Services\Client\ClientProvisioningService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CentralDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Central Platform Database & Provision Initial Demo Clients.
     */
    public function run(): void
    {
        ClientDatabaseManager::connectToCentral();

        // 1. Seed Super Admin User
        SystemUser::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator Platform',
                'email' => 'admin@system.local',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'phone' => '081234567890',
                'is_active' => 1,
                'created_by' => 'system_init',
            ]
        );

        // 2. Seed Default SaaS Plans
        $plans = [
            [
                'plan_code' => 'TRIAL',
                'plan_name' => 'Free Trial (14 Hari)',
                'badge_label' => 'Uji Coba Gratis',
                'description' => 'Akses uji coba gratis seluruh fitur POS & QR Ordering selama 14 hari.',
                'max_outlets' => 1,
                'max_users' => 3,
                'max_storage_mb' => 250,
                'features_json' => [
                    'pos_cashier' => true,
                    'guest_qr' => true,
                    'multi_theme' => true,
                    'inventory_basic' => true,
                    'reports_sales' => true,
                    'cogs_recipe' => false,
                    'shift_audit' => false,
                ],
                'price_monthly' => 0,
                'price_yearly' => 0,
                'trial_days' => 14,
                'is_active' => 1,
                'sort_order' => 1,
            ],
            [
                'plan_code' => 'STARTER',
                'plan_name' => 'Starter Cafe & Resto',
                'badge_label' => 'Single Outlet',
                'description' => 'Paket ideal untuk outlet tunggal, coffee shop, atau resto yang butuh POS kasir dan QR ordering.',
                'max_outlets' => 1,
                'max_users' => 5,
                'max_storage_mb' => 1000,
                'features_json' => [
                    'pos_cashier' => true,
                    'guest_qr' => true,
                    'multi_theme' => true,
                    'inventory_basic' => true,
                    'reports_sales' => true,
                    'shift_audit' => true,
                    'cogs_recipe' => false,
                ],
                'price_monthly' => 150000,
                'price_yearly' => 1500000,
                'trial_days' => 0,
                'is_active' => 1,
                'sort_order' => 2,
            ],
            [
                'plan_code' => 'PRO',
                'plan_name' => 'Pro Multi-Outlet & COGS',
                'badge_label' => 'Paling Populer',
                'description' => 'Dukungan hingga 5 cabang outlet, modul COGS resep makanan, waste log, dan audit shift lengkap.',
                'max_outlets' => 5,
                'max_users' => 20,
                'max_storage_mb' => 3000,
                'features_json' => [
                    'pos_cashier' => true,
                    'guest_qr' => true,
                    'multi_theme' => true,
                    'inventory_basic' => true,
                    'reports_sales' => true,
                    'shift_audit' => true,
                    'cogs_recipe' => true,
                    'waste_log' => true,
                    'purchase_order' => true,
                ],
                'price_monthly' => 450000,
                'price_yearly' => 4500000,
                'trial_days' => 0,
                'is_active' => 1,
                'sort_order' => 3,
            ],
            [
                'plan_code' => 'ENTERPRISE',
                'plan_name' => 'Enterprise Unlimited Franchise',
                'badge_label' => 'Skala Besar',
                'description' => 'Tanpa batasan cabang outlet dan pengguna, dedicated server storage, dan SLA support prioritas.',
                'max_outlets' => 999,
                'max_users' => 999,
                'max_storage_mb' => 20000,
                'features_json' => [
                    'pos_cashier' => true,
                    'guest_qr' => true,
                    'multi_theme' => true,
                    'inventory_basic' => true,
                    'reports_sales' => true,
                    'shift_audit' => true,
                    'cogs_recipe' => true,
                    'waste_log' => true,
                    'purchase_order' => true,
                    'api_access' => true,
                    'custom_domain' => true,
                ],
                'price_monthly' => 1200000,
                'price_yearly' => 12000000,
                'trial_days' => 0,
                'is_active' => 1,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $p) {
            Plan::updateOrCreate(
                ['plan_code' => $p['plan_code']],
                $p
            );
        }

        // 3. Seed Central System Settings
        $settings = [
            ['platform_name', 'Nexora Multi-Client POS Platform', 'general', 'Nama Platform Platform'],
            ['default_currency', 'IDR', 'general', 'Mata Uang Default'],
            ['default_timezone', 'Asia/Jakarta', 'general', 'Zona Waktu Default'],
            ['support_email', 'support@nexora.local', 'general', 'Email Dukungan Bantuan'],
            ['maintenance_mode', '0', 'maintenance', 'Status Maintenance Global Platform'],
            ['auto_backup_enabled', '1', 'backup', 'Otomatisasi Backup Harian Database Client'],
            ['backup_retention_days', '30', 'backup', 'Retensi Penyimpanan File Backup (Hari)'],
        ];

        foreach ($settings as $s) {
            SystemSetting::setVal($s[0], $s[1], $s[2], $s[3]);
        }

        $proPlan = Plan::where('plan_code', 'PRO')->first();

        // 4. Provision & Seed 2 Initial Clients dengan ULID
        $demoClients = [
            [
                'client_name' => 'PT Kopi Senja Indonesia',
                'client_code' => 'KOPISENJA',
                'business_name' => 'Kopi Senja',
                'owner_name' => 'Aditya Pratama (Owner)',
                'owner_email' => 'owner@kopisenja.com',
                'owner_phone' => '081234567001',
                'owner_password' => 'password123',
                'cashier_name' => 'Siti Rahma (Kasir)',
                'cashier_email' => 'kasir@kopisenja.com',
                'cashier_password' => 'password123',
                'address' => 'Jl. Senja Raya No. 12, Jakarta Selatan',
            ],
            [
                'client_name' => 'PT Geprek Gambos Indonesia',
                'client_code' => 'GEPREKGAMBOS',
                'business_name' => 'Geprek Gambos',
                'owner_name' => 'Budi Santoso (Owner)',
                'owner_email' => 'owner@geprekgambos.com',
                'owner_phone' => '081234567002',
                'owner_password' => 'password123',
                'cashier_name' => 'Ahmad Fauzi (Kasir)',
                'cashier_email' => 'kasir@geprekgambos.com',
                'cashier_password' => 'password123',
                'address' => 'Jl. Merdeka No. 10, Jakarta Pusat',
            ],
        ];

        foreach ($demoClients as $demo) {
            $slug = Str::slug($demo['client_name']);
            $dbName = ClientProvisioningService::generateDatabaseName($demo['client_code'], 'dev');

            $existingClient = Client::where('client_code', $demo['client_code'])->orWhere('client_slug', $slug)->first();
            $clientId = $existingClient ? $existingClient->client_id : (string) Str::ulid();

            // 4.1 Buat Record Client di Central DB
            $client = Client::updateOrCreate(
                ['client_code' => $demo['client_code']],
                [
                    'client_id' => $clientId,
                    'client_slug' => $slug,
                    'client_name' => $demo['client_name'],
                    'business_name' => $demo['business_name'],
                    'owner_name' => $demo['owner_name'],
                    'owner_email' => $demo['owner_email'],
                    'owner_phone' => $demo['owner_phone'],
                    'address' => $demo['address'],
                    'database_name' => $dbName,
                    'db_host' => env('DB_HOST', '127.0.0.1'),
                    'db_port' => env('DB_PORT', 3306),
                    'db_username' => env('DB_USERNAME', 'root'),
                    'db_password' => env('DB_PASSWORD', ''),
                    'status' => 'active',
                    'provisioned_at' => now(),
                    'last_active_at' => now(),
                    'created_by' => 'CentralSeeder',
                ]
            );

            // 4.2 Buat Database Fisik di MySQL jika belum ada
            ClientDatabaseManager::createDatabase($dbName);

            // 4.3 Jalankan Migrasi Fresh Client & Seed Data Operasional Khusus Klien
            ClientDatabaseManager::connectToClient($dbName);
            Artisan::call('migrate:fresh', [
                '--database' => 'client',
                '--path' => 'database/migrations/client',
                '--force' => true,
            ]);

            if ($demo['client_code'] === 'KOPISENJA') {
                $seeder = new \Database\Seeders\Client\KopiSenjaSeeder();
                $seeder->run($clientId);
            } elseif ($demo['client_code'] === 'GEPREKGAMBOS') {
                $seeder = new \Database\Seeders\Client\GeprekGambosSeeder();
                $seeder->run($clientId);
            }

            // 4.4 Kembali ke Central DB & Catat Metadata Langganan + Koneksi
            ClientDatabaseManager::connectToCentral();

            Subscription::create([
                'subscription_id' => 'SUB-' . $clientId . '-' . date('YmdHis'),
                'client_id' => $clientId,
                'plan_id' => $proPlan->id,
                'start_date' => now()->toDateString(),
                'expired_date' => now()->addMonths(12)->toDateString(),
                'status' => 'active',
                'billing_reference' => 'DEMO-SEED-' . strtoupper(Str::random(6)),
                'amount_paid' => $proPlan->price_yearly,
                'auto_renew' => 1,
                'notes' => 'Langganan Pro 1 Tahun Demo Client',
                'created_by' => 'CentralSeeder',
            ]);

            $health = ClientDatabaseManager::testConnection($dbName);
            DatabaseConnection::updateOrCreate(
                ['client_id' => $clientId],
                [
                    'database_name' => $dbName,
                    'server_host' => env('DB_HOST', '127.0.0.1'),
                    'server_port' => env('DB_PORT', 3306),
                    'connection_status' => $health['success'] ? 'connected' : 'warning',
                    'latency_ms' => $health['latency_ms'] ?? 0,
                    'tables_count' => $health['tables_count'] ?? 0,
                    'migration_version' => '2026_08_24_000001_create_setting_outlets_table',
                    'last_health_check_at' => now(),
                    'status_message' => 'Terkoneksi sempurna dengan seluruh sampel data operasional.',
                ]
            );

            AuditLog::record(
                action: 'client_provisioned',
                clientId: $clientId,
                targetType: 'Client',
                targetId: (string) $client->id,
                result: 'success',
                metadata: [
                    'client_code' => $demo['client_code'],
                    'database_name' => $dbName,
                    'plan' => $proPlan->plan_name,
                ]
            );
        }

        ClientDatabaseManager::connectToCentral();
    }
}
