# PRD — System Admin
**Version:** 1.0  
**Product:** Multi-Client Cafe/POS Platform  
**Architecture:** 1 repository, Central Database + 1 database per client

## 1. Tujuan
Membangun System Admin sebagai control panel untuk mengelola seluruh client yang menggunakan Aplikasi ini. System Admin berbeda dari Client Admin: System Admin mengelola tenant, akses, subscription, database, backup, keamanan, dan kesehatan sistem; Client Admin mengelola operasional outlet.

## 2. Konsep Utama
- `client_id` = identitas tenant/customer yang menggunakan Aplikasi ini.
- Satu client dapat memiliki banyak outlet.
- `company_id` = identitas outlet/cabang di dalam client.
- Satu repository/application digunakan seluruh client.
- Setiap client memiliki database sendiri untuk isolasi data.
- Central DB menyimpan data platform/global.
- Client DB menyimpan data operasional client.
- System Admin tidak mengelola transaksi operasional secara langsung.

Contoh:
Client CLI001 — Bagaskara Food
- company_id 1 — Cafe Pusat
- company_id 2 — Cafe Bandung
- company_id 3 — Cafe Jakarta

## 3. Arsitektur Database
### Central Database
Minimal menyimpan:
- clients
- client_users / system_users
- plans
- subscriptions
- database_connections
- licenses
- system_settings
- audit_logs
- notifications
- backup metadata
- system health/status

### Client Database
Setiap client memiliki database terpisah dengan struktur aplikasi yang sama, misalnya:
- users
- companies/outlets
- roles & permissions
- products
- categories
- recipes
- stocks
- stock_movements
- orders
- order_details
- payments
- receipts
- reports
- client-specific settings

## 4. Role System Admin
### Super Admin
Akses penuh:
- Client management
- User management
- Subscription/plan
- Database
- Backup
- System tools
- Settings
- Audit logs
- Impersonation

### System Admin
Mengelola client, user, subscription, database, backup, monitoring, dan audit sesuai permission.

### Support
Fokus pada troubleshooting client. Tidak boleh melakukan tindakan destructive atau mengubah konfigurasi global tanpa permission.

## 5. Dashboard
Dashboard menampilkan:
- Total client
- Active client
- Suspended client
- Trial client
- Total outlet
- Total users
- Orders hari ini
- Transaksi hari ini
- Subscription akan expired
- Failed backup
- Failed jobs
- Database/API/system health
- Recent activity

Dashboard harus menyediakan filter periode dan status bila relevan.

## 6. Client Management
### List Client
Kolom:
- Client ID
- Nama client
- Business name
- Owner
- Email
- Phone
- Jumlah outlet
- Plan
- Status
- Subscription
- Database status
- Created date
- Expired date

### Detail Client
Tab yang disediakan:
1. Overview
2. Outlets
3. Users
4. Subscription
5. Database
6. Backup
7. Activity/Audit
8. Settings

### Action
- Create client
- Edit client
- Activate
- Suspend
- Reactivate
- View detail
- Login as Client
- Reset/repair configuration sesuai permission

Saat membuat client, sistem dapat:
1. Membuat `client_id`.
2. Membuat record client di Central DB.
3. Provision database client.
4. Menjalankan migration.
5. Membuat client owner/admin.
6. Membuat outlet pertama jika diperlukan.
7. Menyimpan database mapping.
8. Mencatat seluruh proses pada audit log.

## 7. Outlet Management
System Admin dapat melihat seluruh outlet berdasarkan client:
- Client
- Company ID
- Outlet name
- Address
- Status
- User count
- Order activity
- Created date

System Admin hanya melakukan administrasi tingkat platform. Pengaturan operasional outlet tetap dilakukan Client Admin.

## 8. User Management
Menampilkan:
- Name
- Email/username
- Client
- Outlet
- Role
- Status
- Last login
- Created date

Action:
- Activate/deactivate
- Reset password
- Force logout
- Review access
- View activity

Password tidak pernah dapat dilihat oleh System Admin.

## 9. Plans
Plan menentukan batas dan fitur client, misalnya:
- Jumlah outlet
- Jumlah user
- Storage
- Fitur inventory
- Fitur reports
- API access
- Retention backup

Plan harus configurable sehingga perubahan paket tidak membutuhkan perubahan kode inti.

## 10. Subscription
Data:
- Client
- Plan
- Start date
- Expiration date
- Status
- Billing/reference number
- Trial period
- Renewal information

Status:
- Trial
- Active
- Expiring Soon
- Expired
- Suspended
- Cancelled

Sistem harus dapat memberikan notifikasi subscription yang mendekati expired.

## 11. Database Management
Menampilkan:
- Client ID
- Database name
- Server/connection identifier
- Connection status
- Database size
- Schema/migration version
- Last successful backup
- Last activity

Action non-destructive:
- Test connection
- Health check
- View migration version
- View database statistics

Tindakan destructive harus memiliki permission khusus, confirmation berlapis, dan audit log.

## 12. Backup Management
Fitur:
- Backup history
- Backup status
- Scheduled backup
- Retention policy
- Backup size
- Last successful backup
- Failed backup alert

Setiap client minimal memiliki informasi:
- Last backup
- Backup status
- Backup age
- Backup location/reference

Restore tidak boleh dilakukan tanpa konfirmasi kuat dan audit trail.

## 13. System Health
Monitoring:
- Application
- Central DB
- Client DB connections
- Storage
- Queue
- Scheduler
- Backup
- Error count
- Failed jobs

Status:
- Healthy
- Warning
- Critical
- Unknown

System Admin dapat melihat client mana yang mengalami masalah tanpa harus membuka database secara manual.

## 14. Audit Logs
Semua aktivitas penting dicatat:
- actor
- role
- client_id
- company_id bila ada
- action
- target type
- target ID
- timestamp
- IP
- user agent
- result
- metadata

Contoh:
System Admin Mukti → Suspend Client CLI002 → Success.

Audit log tidak boleh mudah dihapus melalui UI.

## 15. Notifications
Jenis notifikasi:
- New client
- Subscription expiring
- Subscription expired
- Database disconnected
- Backup failed
- Backup overdue
- Failed jobs
- Critical system error
- Security event

Notification memiliki:
- type
- severity
- title
- message
- client_id bila relevan
- status read/unread
- created_at

## 16. System Settings
Global settings:
- Application name
- Timezone
- Currency
- Email configuration
- Notification configuration
- Backup policy
- Storage configuration
- Security settings
- Session timeout
- Maintenance mode

Perubahan global wajib dicatat dalam audit log.

## 17. System Tools
Hanya role berpermission tinggi:
- Clear application cache
- Health check
- Queue monitoring
- Failed job management
- Migration status
- System maintenance
- Rebuild/index task jika diperlukan

Tool destructive tidak boleh memiliki eksekusi sekali klik tanpa confirmation.

## 18. Impersonation / Login as Client
System Admin dapat masuk ke environment client untuk troubleshooting tanpa mengetahui password user client.

Flow:
System Admin → Client → Login as Client → pilih outlet bila diperlukan.

Setiap session impersonation harus mencatat:
- System Admin
- Client
- User/role yang diwakili
- Start time
- End time
- IP
- Semua action selama impersonation

UI harus selalu menunjukkan bahwa session sedang dalam mode impersonation.

## 19. Security
- Central authentication untuk System Admin.
- Role-based access control.
- Permission per menu/action.
- Password hashing.
- Session timeout.
- Force logout.
- Audit log.
- Rate limiting pada login.
- CSRF protection.
- Validasi input.
- Database credentials tidak ditampilkan di UI.
- Secret/credential disimpan menggunakan environment/secret management.
- System Admin dan Client Admin memiliki authentication boundary yang jelas.

## 20. Tenant Isolation
Aturan wajib:
- `client_id` menentukan tenant.
- `company_id` menentukan outlet di tenant.
- Client tidak boleh mengakses database client lain.
- Query operasional harus menggunakan konteks client/outlet.
- Database connection harus dipilih berdasarkan trusted `client_id`, bukan input bebas dari browser.
- Jangan pernah menerima nama database langsung dari request user.
- Impersonation tetap harus melalui authorization server-side.

## 21. Provisioning Client
Saat client dibuat:
1. Generate unique client ID.
2. Validate client data.
3. Create Central DB record.
4. Provision client database.
5. Apply migrations.
6. Create initial admin.
7. Create default outlet bila dipilih.
8. Apply default configuration.
9. Run health check.
10. Mark client Active hanya jika provisioning berhasil.
11. Audit seluruh proses.

Jika provisioning gagal, sistem harus menunjukkan tahap kegagalan dan tidak meninggalkan client dalam status Active.

## 22. Client Lifecycle
```text
Draft
  ↓
Provisioning
  ↓
Active
  ↓
Suspended
  ↓
Active / Cancelled
```

Subscription dapat memiliki lifecycle berbeda dari status client.

## 23. Navigation
```text
Dashboard

TENANT MANAGEMENT
- Clients
- Outlets
- Users
- Plans
- Subscriptions

INFRASTRUCTURE
- Databases
- Backups
- System Health

SECURITY
- Audit Logs
- Notifications

SYSTEM
- Settings
- System Tools

Profile
Logout
```

## 24. Client Admin Boundary
Client Admin menangani:
- Outlet
- Users
- Roles
- Products
- Menu
- Categories
- Recipe
- Inventory
- Stock
- Orders
- Payments
- Receipts
- Reports
- Operational settings

System Admin menangani:
- Client
- Platform access
- Subscription
- Database
- Backup
- Security
- System health
- Platform configuration

## 25. MVP
Prioritas tahap pertama:
- Authentication
- Dashboard
- Client CRUD
- Client provisioning
- Client status
- Outlet overview
- User overview
- Plans
- Subscription
- Database health
- Backup status
- Audit logs
- Basic system settings
- Basic impersonation

## 26. Phase 2
- Advanced monitoring
- Automated backup scheduling
- Notification center
- Advanced subscription automation
- Usage metrics
- Storage metrics
- Advanced permissions
- Support tools
- Client usage analytics

## 27. Non-Functional Requirements
- Responsive desktop-first UI.
- Semua action server-side authorized.
- Database-per-client harus dapat berkembang tanpa perubahan besar pada aplikasi.
- Migration client harus konsisten antar database.
- Error harus aman dan tidak membocorkan credentials.
- Audit log harus reliable.
- Provisioning harus idempotent sejauh memungkinkan.
- System Admin harus dapat menangani banyak client tanpa membuka database secara manual.

## 28. Prinsip Arsitektur
```text
                    Aplikasi ini
                      │
             ┌────────┴────────┐
             │                 │
        CENTRAL DB          APPLICATION
             │                 │
       client registry         │
       subscriptions          │
       system users            │
       DB mapping              │
             │                 │
       ┌─────┼─────┬───────────┤
       ↓     ↓     ↓           ↓
     DB001  DB002  DB003      DB00N
       │     │     │           │
      CLI1  CLI2  CLI3        CLIN
       │
   company_id
       ├── Outlet 1
       ├── Outlet 2
       └── Outlet N
```

## 29. Success Criteria
System Admin dianggap siap jika:
- Client baru dapat dibuat tanpa manual setup database.
- Setiap client mendapatkan database terisolasi.
- System Admin dapat melihat status seluruh client.
- Client tidak dapat mengakses tenant lain.
- Outlet tetap terisolasi menggunakan `company_id`.
- Backup client dapat dipantau.
- Aktivitas sensitif tercatat di audit log.
- System Admin dapat melakukan troubleshooting melalui impersonation dengan aman.
- Penambahan client baru tidak membutuhkan repository baru.
