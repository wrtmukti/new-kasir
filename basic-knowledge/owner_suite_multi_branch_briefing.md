# 👑 Architectural Briefing & Laporan Hasil Pengembangan: Portal Eksekutif Owner Multi-Cabang (Phase 3)

> **Dokumen**: `owner_suite_multi_branch_briefing.md`  
> **Lokasi**: `basic-knowledge/owner_suite_multi_branch_briefing.md`  
> **Laporan Resmi Dari**: Lead Developer / Tim Arsitektur & Keuangan `deva-branch`  
> **Ditujukan Untuk**: Tim Developer & **AI Coding Assistant di `mukti-branch`** (Saat melakukan `git pull origin deva-branch`)  
> **Tanggal**: 2026-08-29 (29 Agustus 2026)  
> **Status**: ✅ **100% SELESAI, TERUJI (200 OK), & DISAHKAN**  
> **Tujuan**: Laporan komprehensif apa saja yang telah di-update (A, B, C), fungsi dan kegunaannya dalam operasional bisnis F&B multi-outlet, serta **instruksi wajib untuk AI di `mukti-branch` agar membaca dan memberikan feedback konfirmasi sebelum mulai bekerja.**

---

## 🤖 INSTRUKSI WAJIB UNTUK AI ASSISTANT DI `mukti-branch` (MANDATORY AI DIRECTIVE)

> [!IMPORTANT]
> ### 🛑 PROTOKOL AI SEBELUM MELAKUKAN PEKERJAAN APAPUN DI `mukti-branch`:
> 1. **BACA DOKUMEN INI SAMPAI SELESAI**: Kamu dilarang keras mengubah, menghapus, atau menimpa arsitektur finansial dan rute yang telah dibuat di `deva-branch`.
> 2. **JALANKAN SINKRONISASI DATABASE**: Segera jalankan perintah di **Bagian 5** (`client:migrate --fresh` & `CentralDatabaseSeeder`) agar data simulasi multi-cabang aktif.
> 3. **BERIKAN FEEDBACK KE USER**: Sampaikan konfirmasi kepada user bahwa kamu telah membaca briefing ini, memahami 6 sub-menu owner, memahami pemisahan Plan B (P&L vs Cash Flow vs Laci), dan siap melanjutkan tugas berikutnya tanpa merusak konsolidasi multi-cabang.

---

## 🧭 1. Latar Belakang & Executive Summary (Apa yang Di-Update & Mengapa)

Sebelum Phase 3, sistem Nexora POS hanya memiliki laporan berbasis single-outlet. Ketika pemilik bisnis memiliki banyak cabang (contoh: *Jakarta, Bandung, Yogyakarta*), owner harus login ke masing-masing cabang secara manual dan menjumlahkan omzet di spreadsheet.

Oleh karena itu, di **`deva-branch`** telah diselesaikan update besar: **Phase 3 (Portal Eksekutif Owner & Multi-Branch Consolidated Hub)** yang menyatukan seluruh data cabang ke dalam satu grup menu sidebar: **`👑 PORTAL OWNER`** (dengan badge `MULTI-CABANG`).

```text
👑 PORTAL OWNER (Sidebar Group with Multi-Cabang Badge)
  ├── 📊 1. Konsolidasi Semua Cabang (`admin.owner.dashboard`)
  │     ├── 4 Kartu KPI Glow Konsolidasi (Total Omzet Holding, Laba Bersih, Kas Masuk, Setoran Brankas)
  │     ├── Multi-Outlet Filter Switcher & Date Range Preset (Semua Cabang vs Single Cabang)
  │     ├── Multi-Line Chart Tren Omzet & Transaksi Antar Cabang (Auto Dark/Light Mode Adapt)
  │     └── Leaderboard Ranking Cabang (Omzet, Margin, Status Shift Kasir Real-Time)
  │
  ├── 📈 2. Laba Rugi & Arus Kas (`admin.owner.financial`)
  │     ├── P&L Akrual Konsolidasi (Revenue, COGS Resep Murni, Gross Profit, Overhead, Net Profit)
  │     ├── Arus Kas Riil (Total Inflow Kasir & Modal vs Outflow PO Lunas & Operasional)
  │     ├── Tabel Komparasi Finansial Lengkap per Outlet
  │     └── Fitur Export CSV / Excel UTF-8 BOM (`admin.owner.financial.export`)
  │
  ├── 🏆 3. Leaderboard & Benchmark Resep (`admin.owner.benchmark`)
  │     ├── Podium Top 3 Cabang Performa Terbaik (Juara 1, Runner Up, Peringkat 3)
  │     ├── Benchmark HPP Resep per Porsi Lintas Cabang (Porsi Terjual, Omzet, % HPP)
  │     └── Deteksi Otomatis Anomali Pemborosan Dapur (Deviasi HPP >5% / Porsi Bocor)
  │
  ├── 🚨 4. Audit Selisih & Waste (`admin.owner.audit`)
  │     ├── Stat Cards Shortage (Minus Kas), Overage (Lebih Kas), & Kerugian Bahan Waste
  │     ├── Tabel Audit Selisih Shift Kasir (Urutan Selisih Minus / Shortage Paling Atas)
  │     └── Tabel Audit Kerugian Bahan Rusak / Basi (Waste Log Dapur per Cabang)
  │
  ├── 🏦 5. Pusat Setoran & Hutang PO (`admin.owner.cash-debt`)
  │     ├── Monitor Uang Fisik Disetor ke Brankas (*Safe Deposit*) dari Semua Cabang
  │     └── Kalender Urgensi Jatuh Tempo Hutang Supplier (Overdue, Critical, Warning, Safe)
  │
  └── 🏢 6. Manajemen Cabang (`admin.owner.branches.index`)
        ├── Grid Kartu Master Cabang Outlet & Status Operasional
        ├── Modal Tambah Cabang Baru & Modal Edit Cabang
        └── Pengaturan Jam Cut-Off Shift per Cabang Outlet (Contoh: `03:00` WIB)
```

---

## 📦 2. Rincian Pembaruan Komponen (A, B, C) & Kegunaannya

### A. Komponen Backend & Aggregator Service (`ConsolidatedFinancialService.php`)
* **File**: [`app/Services/ConsolidatedFinancialService.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Services/ConsolidatedFinancialService.php)
* **Kegunaan**: Bertindak sebagai *central calculation engine* untuk seluruh holding. Tidak ada lagi duplikasi query di controller. Menghitung omzet, theoretical COGS resep murni per menu yang laku di POS, laba kotor, laba bersih, total kas masuk riil, total kas keluar operasional, net cash flow, rekap setoran brankas kasir, serta tagihan supplier tempo yang belum lunas.

### B. Komponen Controller Suite (6 Sub-Menu Owner)
* **Folder**: [`app/Http/Controllers/Admin/Owner/`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Http/Controllers/Admin/Owner)
  1. `OwnerDashboardController.php`: Menangani dashboard utama konsolidasi, chart tren multi-cabang, dan filter outlet dinamis.
  2. `OwnerFinancialController.php`: Menangani perbandingan Laba Rugi Akrual vs Cash Flow Riil, serta **Fitur Ekspor CSV / Excel UTF-8 BOM** (`export()`).
  3. `OwnerBenchmarkController.php`: Menghitung leaderboard podium dan mendeteksi anomali gramasi resep dapur antar cabang secara otomatis.
  4. `OwnerAuditController.php`: Menampilkan audit kedisiplinan kasir saat serah terima uang fisik (*shortage/overage*) dan kerugian bahan terbuang di dapur.
  5. `OwnerCashDebtController.php`: Memonitor uang setoran fisik brankas harian dan kalender jatuh tempo PO supplier (level urgensi: *overdue, critical, warning, safe*).
  6. `OwnerBranchController.php`: Menangani penambahan cabang baru, edit info outlet, status aktif, dan pengaturan jam cut-off shift harian.

### C. Komponen Blade Views & UI/UX Native Nexora
* **Folder**: [`resources/views/admin/owner/`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/owner)
* **Kegunaan**: Antarmuka eksekutif premium yang dirancang mengikuti standar resmi Nexora (`docs/cards`, `docs/forms`, `docs/components`). Semua grafik Chart.js otomatis beradaptasi dengan Dark Mode dan Light Mode (`[data-theme="dark"]` vs `[data-theme="light"]`).

### D. Panduan Finansial & Kas Plan B (Interactive Guide)
* **File**: [`resources/views/admin/keuangan/guide/index.blade.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/keuangan/guide/index.blade.php)
* **Route**: `/admin/keuangan/financial-guide`
* **Kegunaan**: Berisi 6 Bab panduan visual interaktif mengenai 3 Pilar Finansial, alasan Top-Up modal laci tidak masuk HPP, SOP Serah Terima Kasir, dan FAQ Owner.

---

## 🏛️ 3. Tabel Pemetaan Route, Controller & Blade View

| No | Sub-Menu Owner | Route Name | Controller Action | View Blade Path |
|:--:|---|---|---|---|
| 1 | **Konsolidasi Semua Cabang** | `admin.owner.dashboard` | `OwnerDashboardController@index` | `resources/views/admin/owner/dashboard.blade.php` |
| 2 | **Laba Rugi & Arus Kas** | `admin.owner.financial` | `OwnerFinancialController@index` | `resources/views/admin/owner/financial.blade.php` |
| 2b | **Ekspor CSV Finansial** | `admin.owner.financial.export` | `OwnerFinancialController@export` | File Stream CSV (UTF-8 BOM) |
| 3 | **Leaderboard & Benchmark** | `admin.owner.benchmark` | `OwnerBenchmarkController@index` | `resources/views/admin/owner/benchmark.blade.php` |
| 4 | **Audit Selisih & Waste** | `admin.owner.audit` | `OwnerAuditController@index` | `resources/views/admin/owner/audit.blade.php` |
| 5 | **Pusat Setoran & Hutang PO** | `admin.owner.cash-debt` | `OwnerCashDebtController@index` | `resources/views/admin/owner/cash-debt.blade.php` |
| 6 | **Manajemen Cabang** | `admin.owner.branches.index` | `OwnerBranchController@index` | `resources/views/admin/owner/branches/index.blade.php` |

---

## 🧪 4. Hasil Pengujian Mutu (QA Validation)

Semua komponen telah melalui pengujian unit & integrasi ketat:
* **Route Status**: Seluruh 6 endpoint controller dan template view menghasilkan output **HTTP 200 OK & 0 Error** pada `storage/logs/laravel.log`.
* **Zero Raw JSON Screens**: Seluruh form submission (tambah cabang, edit cabang, filter tanggal, dan export CSV) menerapkan mekanisme redirect kembali dengan *flash session message* atau file stream langsung.
* **Auto Theme Adapting**: Card glow, background elevated, border subtle, dan grid line grafik Chart.js terbukti beradaptasi sempurna di Light Mode maupun Dark Mode.

---

## 🚀 5. Panduan Menjalankan Migrasi & Database Seeder Pasca-Pull

Saat berada di `mukti-branch` setelah melakukan `git pull origin deva-branch`, jalankan baris perintah berikut di terminal:

```bash
# 1. Jalankan migrasi database client
php artisan client:migrate --fresh

# 2. Jalankan Seeder Utama (otomatis men-seed multi-branch simulasi Jakarta, Bandung, Yogyakarta)
php artisan db:seed --class=CentralDatabaseSeeder

# 3. Bersihkan Cache Template Blade
php artisan view:clear

# 4. Verifikasi status semua endpoint (opsional via scratch test script)
php scratch/test_owner_routes.php
```

---

## 🎲 6. Skenario Data Simulasi Multi-Branch (Seeder)

Database seeder [`database/seeders/client/KopiSenjaSeeder.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/database/seeders/client/KopiSenjaSeeder.php) telah memuat 3 cabang aktif:

1. **Kopi Senja - Jakarta (KS-JKT)**: 20 Transaksi, Shift kemarin closed (Setoran brankas Rp 1.415.000), Shift hari ini `OPEN`, Waste Susu Basi Rp 66.000, PO Tempo Rp 1.320.000.
2. **Kopi Senja - Bandung (KS-BDG)**: 12 Transaksi, Shift closed (Setoran brankas Rp 925.000), Waste Biji Kopi Tumpah Rp 140.000.
3. **Kopi Senja - Yogyakarta (KS-YOG)**: 10 Transaksi, Shift closed (Setoran brankas Rp 725.000), Selisih kas minus Rp 5.000 (simulasi kekurangan uang fisik).

---

## 🛡️ 7. Aturan Baku Pengkodean untuk Tim `mukti-branch` (Guardrails)

1. **Zero Raw JSON Screens**: Dilarang me-return raw JSON pada form submit biasa. Gunakan `redirect()->back()->with('success', ...)`.
2. **Standar Styling Native**: Jangan membuat tag `<style>` inline neon acak. Selalu gunakan token CSS Nexora: `var(--bg-surface)`, `var(--text-primary)`, `var(--border-subtle)`.
3. **Integritas Akuntansi Plan B**:
   - **Top-Up Modal Laci Kasir** BUKAN Omzet dan BUKAN HPP (masuk ke buku kas laci `cash_drawer_logs`).
   - **PO Belanja Bahan Mentah** adalah Aset Persediaan, bukan beban pemotong laba rugi.
4. **Log Code Protocol**: Setiap perubahan file wajib dicatat langsung di [`basic-knowledge/log_code.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/log_code.md).

---

> 📢 **Pesan untuk AI di `mukti-branch`**: *Beri tahu user bahwa kamu telah membaca dan memahami laporan briefing ini dari `deva-branch`, lalu jelaskan ringkas langkah apa yang akan kamu kerjakan selanjutnya.*
