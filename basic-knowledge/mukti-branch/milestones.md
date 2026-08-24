# Milestones & Implementation Task Checklist — System Admin (Multi-Client Platform)

**Dokumen Acuan:** [`basic-knowledge/mukti-branch/System_Admin_PRD.md`](file:///c:/Users/mukti/OneDrive/Desktop/project/new-kasir/basic-knowledge/mukti-branch/System_Admin_PRD.md)  
**Versi:** 1.0  
**Arsitektur:** 1 Repository, Central Database (Platform Registry) + Database-per-Client (Isolated Operational DB)

---

## 🗺️ Ringkasan Roadmap Milestone

```mermaid
gantt
    title Roadmap Implementasi System Admin Multi-Client
    dateFormat  YYYY-MM-DD
    section Fase 1: Foundation
    M1: Central DB & Tenant Connection :2026-08-25, 3d
    M2: Auth & Role Boundary           :after M1, 2d
    section Fase 2: Core Engine
    M3: Automated Provisioning Engine  :after M2, 4d
    M4: Tenant & Subscription Mgmt     :after M3, 4d
    section Fase 3: Observability
    M5: System Admin Dashboard & Stats :after M4, 3d
    M6: Infra, Backup & System Health  :after M5, 3d
    section Fase 4: Security & Delivery
    M7: Impersonation & Audit Log      :after M6, 2d
    M8: E2E Testing & Hardening        :after M7, 3d
```

---

## 📌 MILESTONE 1: Central Database Schema & Dynamic Connection Infrastructure
> **Tujuan:** Membangun fondasi arsitektur multi-database di Laravel, konfigurasi koneksi `central` vs `tenant`, serta skema tabel dasar di Central Database.

- [x] **1.1 Konfigurasi Multi-Database Connection di Laravel**
  - [x] Daftarkan koneksi `central` (default platform) dan `tenant` (dinamis per client) di `config/database.php`.
  - [x] Buat custom dynamic connection switcher helper / service (`TenantDatabaseManager`).
  - [x] Implementasikan fungsi `TenantDatabaseManager::connectToClient($client)` (`Config::set` + `DB::purge('tenant')` + `DB::reconnect('tenant')`).

- [x] **1.2 Migrasi Skema Central Database**
  - [x] `create_central_system_users_table` (Super Admin, System Admin, Support).
  - [x] `create_central_clients_table` (`client_id`, `client_name`, `business_name`, `email`, `phone`, `status`, `database_name`, `db_host`, `db_port`).
  - [x] `create_central_plans_table` (`plan_code`, `plan_name`, `max_outlets`, `max_users`, `max_storage_mb`, `features_json`, `price_monthly`, `price_yearly`).
  - [x] `create_central_subscriptions_table` (`subscription_id`, `client_id`, `plan_id`, `start_date`, `expired_date`, `status`, `billing_reference`).
  - [x] `create_central_database_connections_table` (`client_id`, `db_name`, `db_size_mb`, `migration_version`, `status`, `last_health_check`).
  - [x] `create_central_audit_logs_table` (`actor_id`, `actor_role`, `client_id`, `company_id`, `action`, `target_type`, `target_id`, `ip_address`, `user_agent`, `payload_json`).
  - [x] `create_central_system_settings_table` (Platform global configs).

- [x] **1.3 Pembuatan Central Models & Repositories**
  - [x] Model `SystemUser`, `Client`, `Plan`, `Subscription`, `DatabaseConnection`, `AuditLog`, `SystemSetting` dengan connection explicit `$connection = 'central'`.
  - [x] Seeder `CentralDatabaseSeeder` (Super Admin, Default Plans [Trial, Starter, Pro, Enterprise], Platform Settings).

---

## 📌 MILESTONE 2: System Admin Authentication, Roles & Separation Boundary
> **Tujuan:** Memisahkan secara tegas otentikasi System Admin (Platform) dari Client Admin / Kasir POS (Tenant).

- [x] **2.1 Guard & Provider Otentikasi Khusus System Admin**
  - [x] Daftarkan guard `system_admin` dan provider `system_users` di `config/auth.php`.
  - [x] Buat middleware `AuthenticateSystemAdmin` dan `RedirectIfSystemAdminAuthenticated`.
  - [x] Implementasikan route group `/sys_admin/login`, `/sys_admin/logout`.
  - [x] Form Request `LoginRequest.php` dengan pesan validasi Bahasa Indonesia.

- [x] **2.2 Role-Based Access Control (RBAC) System Admin**
  - [x] Role 1: **Super Admin** (Full access, bypass permissions, sensitive settings).
  - [x] Role 2: **System Admin** (Client management, subscription, backups, monitoring).
  - [x] Role 3: **Support** (Read-only diagnostics, safe troubleshooting, limited impersonation).
  - [x] Buat middleware `CheckSystemAdminRole:super_admin,system_admin`.

- [x] **2.3 Layout & UI Shell System Admin (Nexora Platform Theme)**
  - [x] Buat master layout `resources/views/sys_admin/layouts/app.blade.php`.
  - [x] Buat auth layout `resources/views/sys_admin/layouts/auth.blade.php` & login view `sys_admin/auth/login.blade.php`.
  - [x] Buat executive dashboard `resources/views/sys_admin/dashboard/index.blade.php` & `DashboardController.php`.
  - [x] Integrasikan sidebar navigasi lengkap sesuai PRD Section 23:
    - *Dashboard*
    - *Tenant Management* (Clients, Outlets, Users, Plans, Subscriptions)
    - *Infrastructure* (Databases, Backups, System Health)
    - *Security* (Audit Logs, Notifications)
    - *System* (Settings, System Tools)

---

## 📌 MILESTONE 3: Automated Client Provisioning Engine & Database Management
> **Tujuan:** Mengotomatiskan pembuatan database baru, migrasi skema operasional POS, dan seed default outlet + owner akun saat pendaftaran client baru.

- [x] **3.1 Service `ClientProvisioningService`**
  - [x] Step 1: Validasi data unik `client_id`, slug, dan email owner.
  - [x] Step 2: Insert record di `central.clients` dengan status `provisioning`.
  - [x] Step 3: Eksekusi statement SQL `CREATE DATABASE {client_db_name} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci`.
  - [x] Step 4: Jalankan Artisan Migration terarah ke target Client DB via `TenantDatabaseManager::runTenantMigrations`.
  - [x] Step 5: Buat user Owner/Client Admin pertama di Client DB (role: admin).
  - [x] Step 6: Buat default outlet pertama (`company_id: ULID`) & default settings (`setting_outlets`).
  - [x] Step 7: Buat initial subscription record sesuai paket/trial yang dipilih.
  - [x] Step 8: Update status client menjadi `active` dan catat riwayat di `central.audit_logs`.
  - [x] Step 9 (Error Handling & Rollback): Jika terjadi error di salah satu step, jalankan rollback otomatis dan tandai status client `failed_provisioning` tanpa merusak Central DB.

- [x] **3.2 Modul Infrastruktur: Database Management UI**
  - [x] Tabel list seluruh Client Database dengan status koneksi, ukuran database (MB), versi skema migrasi, dan tanggal backup terakhir (`resources/views/sys_admin/databases/index.blade.php`).
  - [x] Controller `DatabaseManagementController.php` dengan AJAX endpoint *Test Connection Ping* & *Run Migration*.
  - [x] Artisan Command `tenant:migrate` (`app/Console/Commands/TenantMigrateCommand.php`) untuk menjalankan migrasi massal ke seluruh database client yang aktif.

---

## 📌 MILESTONE 4: Tenant Management (Clients, Outlets, Users, Plans & Subscriptions)
> **Tujuan:** Control panel lengkap untuk mengelola lifecycle client, pembatasan paket (plans), dan masa berlaku langganan (subscriptions).

- [x] **4.1 Client Management (`/sys_admin/clients`)**
  - [x] Halaman Index: Tabel interaktif client (Filter status: Active, Trial, Suspended, Expired; Search & Pagination AJAX shimmer).
  - [x] Modal / Wizard Form *Create New Client* (`resources/views/sys_admin/clients/index.blade.php`) terintegrasi dengan Provisioning Engine.
  - [x] Halaman Detail Client dengan 8 Tab Informasi (`resources/views/sys_admin/clients/show.blade.php`):
    - Tab 1: *Overview* (Identitas, Profil, PIC, Status)
    - Tab 2: *Outlets* (Daftar cabang/company_id milik client)
    - Tab 3: *Users* (Daftar staff & kasir yang terdaftar di client)
    - Tab 4: *Subscription* (Paket aktif, masa berlaku, riwayat billing)
    - Tab 5: *Database* (Spesifikasi DB, ukuran, status koneksi)
    - Tab 6: *Backup* (Riwayat backup snapshot DB client)
    - Tab 7: *Activity / Audit* (Log aksi yang melibatkan tenant ini)
    - Tab 8: *Settings & Action* (Suspend, Reactivate, Repair Config)
  - [x] Fitur Aksi: *Suspend Client* (Kunci akses masuk POS client), *Reactivate Client*.

- [x] **4.2 Outlet & User Overview (Platform Scope)**
  - [x] Halaman Outlet Overview: Daftar seluruh outlet cabang di semua tenant (`resources/views/sys_admin/outlets/index.blade.php`).
  - [x] Halaman User Overview: Monitoring akun user di seluruh client & system users (`resources/views/sys_admin/users/index.blade.php`).

- [x] **4.3 Plans & Subscriptions Management**
  - [x] CRUD Plans (Free Trial, Starter, Pro, Enterprise) dengan kuota outlet, user, storage, dan toggle fitur modular (`PlanController.php` & `resources/views/sys_admin/plans/index.blade.php`).
  - [x] Manajemen Subscription: Perpanjangan masa aktif, upgrade/downgrade paket, peringatan otomatis H-7 dan H-1 *Expiring Soon* (`SubscriptionController.php` & `resources/views/sys_admin/subscriptions/index.blade.php`).

---

## 📌 MILESTONE 5: System Admin Dashboard & Multi-Tenant Analytics
> **Tujuan:** Dashboard metrik eksekutif terpusat untuk memantau kesehatan bisnis dan platform SaaS.

- [x] **5.1 Widget KPI & Status Metrik Utama**
  - [x] Total Client, Active Clients, Trial Clients, Suspended/Expired Clients (`DashboardController.php`).
  - [x] Monthly Recurring Revenue (MRR) & Annual Run Rate (ARR) SaaS projections.
  - [x] Infrastructure Health & Database Ping latency monitor.
  - [x] Alert Banner & Counter: Subscription akan expired (< 7 hari).

- [x] **5.2 Visualisasi Grafik & Chart Analytics (Chart.js & Nexora Design System)**
  - [x] Grafik Pertumbuhan Akuisisi Klien Baru (6 Bulan Terakhir) dengan Chart.js line area.
  - [x] Distribusi Paket Langganan SaaS (Doughnut Chart).
  - [x] Feed Real-Time Live Audit Log Stream & Recent Client Onboarding Table.
  - [x] Filter Rentang Waktu Dinamis (Hari Ini, 7 Hari, 30 Hari, Bulan Ini, Semua Data) via AJAX loading shimmer.

---

## 📌 MILESTONE 6: Infrastructure Health, Automated Backups & System Tools
> **Tujuan:** Fitur pemeliharaan sistem, monitoring ketersediaan server/database, dan proteksi backup data.

- [x] **6.1 System Health Monitoring (`/sys_admin/health`)**
  - [x] Monitor Central Database connection status & latency (`SystemHealthController.php`).
  - [x] Multi-Client DB connectivity health checker & AJAX batch ping endpoint (`/sys_admin/health/ping-all`).
  - [x] Memory usage, Peak memory, PHP version, OS, dan Disk Storage Progress Monitor.

- [x] **6.2 Backup Management (`/sys_admin/backups`)**
  - [x] Service `BackupService.php` (otomasi dump SQL skema + data per tenant database ke storage disk).
  - [x] Metadata riwayat backup snapshot (Client ID, Nama File, Ukuran MB, Timestamp, Aksi Download & Delete).
  - [x] Modal & AJAX action *Trigger Snapshot Backup* on-demand (`BackupController.php` & `resources/views/sys_admin/backups/index.blade.php`).

- [x] **6.3 System Tools & Maintenance (`/sys_admin/tools`)**
  - [x] 6 Bento Maintenance Tools: Clear App Cache (`cache:clear`), Clear Config (`config:clear`), Clear Routes (`route:clear`), Clear Compiled Views (`view:clear`), Full Optimization Reset (`optimize:clear`), and Queue Worker Restart (`queue:restart`).
  - [x] Feedback AJAX instan dengan `NexoraToast()` dan pencatatan audit log otomatis (`SystemToolsController.php`).

---

## 📌 MILESTONE 7: Impersonation ("Login as Client"), Security & Audit Logs
> **Tujuan:** Kemampuan troubleshooting live masuk ke akun client tanpa password dengan pengawasan audit trail ketat.

- [x] **7.1 Fitur Impersonation Engine**
  - [x] Action *Login as Client* pada halaman detail client (`ImpersonationController.php` & `resources/views/sys_admin/clients/show.blade.php`).
  - [x] Generate secure session context (`is_impersonating`, `impersonator_name`, `impersonated_client_id`).
  - [x] Switch connection ke client DB dan login otomatis sebagai admin client.
  - [x] Floating Banner di UI Client Admin: *"Mode Impersonation oleh System Admin [Nama] — [Kembali ke System Admin]"* (`resources/views/admin/layouts/app.blade.php`).
  - [x] Catat event `impersonation_start` dan `impersonation_stop` di Audit Log.

- [x] **7.2 Central Audit Logging System**
  - [x] Hubungan relasi `client()` di model `AuditLog.php`.
  - [x] Halaman Central Audit Logs (`AuditLogController.php` & `resources/views/sys_admin/audit_logs/index.blade.php`) dengan filter Action, Client ID, Result, Search, dan AJAX pagination shimmer.
  - [x] Immutable Policy: Log audit tidak dapat diubah/dihapus via antarmuka UI.

---

## 📌 MILESTONE 8: Verification, End-to-End Testing & Staging Readiness
> **Tujuan:** Pengujian menyeluruh skenario multi-tenant untuk memastikan tidak ada kebocoran data dan sistem siap diproduksi.

- [x] **8.1 Automated Integration & Unit Tests**
  - [x] Test dynamic tenant database switching (`TenantDatabaseManager.php`).
  - [x] Test automated client provisioning & database creation (`ClientProvisioningService.php`).
  - [x] Test isolation & zero data leakage: Data Client A tidak bocor ke Client B dan sebaliknya (`scratch/test_milestone_8_final_e2e_and_security.php`).
  - [x] Test subscription expiry & suspend/reactivate lifecycle (`ClientController.php` & `SubscriptionController.php`).
  - [x] Test impersonation flow & audit log record validity (`ImpersonationController.php`).

- [x] **8.2 Security & Performance Hardening**
  - [x] Rate limiting pada endpoint login System Admin (`AuthController.php` & `LoginRequest.php`).
  - [x] Sanitasi input dan proteksi credential database (`$hidden = ['db_password']` di model `Client.php`).
  - [x] Dokumentasi pemeliharaan platform di `basic-knowledge/log_code.md` dan checklist roadmap.

---

### 📋 Status Progres Task Tracker
- **Total Milestone:** 8 Fase
- **Status Saat Ini:** SELURUH 8 MILESTONE SELESAI 100% (STAGING & PRODUCTION READY) 🎉
- **Progress:** `[████████████████████] 100.0%`
