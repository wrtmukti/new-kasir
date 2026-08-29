# 🎯 MASTER MILESTONE EKSEKUSI: PORTAL EKSEKUTIF OWNER & MULTI-BRANCH CONSOLIDATED HUB (PHASE 3)

> **Dokumen**: `milestone.md`  
> **Tanggal**: 2026-08-29 (29 Agustus 2026)  
> **Branch**: `deva-branch`  
> **Status**: ✅ 100% COMPLETE & VERIFIED (SEMUA 6 SUB-MENU SELESAI & LULUS PENGUJIAN)  
> **Target Pengguna**: Pemilik Bisnis (Owner / Direktur / Franchise Executive) Multi-Cabang  
> **Fokus Utama**: Implementasi Grup Menu `👑 PORTAL OWNER` di Sidebar, Agregasi Finansial Konsolidasi Lintas Cabang, Leaderboard Performa, Audit Kebocoran (Selisih Kas & Waste), Pusat Setoran Kas & Hutang PO Supplier, dan Manajemen Cabang.

---

## 🗺️ Peta Jalan 5 Fase Eksekusi Master Milestone

```text
+-------------------------------------------------------------------------------------------------------------------+
| MASTER ROADMAP PHASE 3 — OWNER EXECUTIVE SUITE & MULTI-OUTLET CONSOLIDATION                                       |
+-------------------------------------------------------------------------------------------------------------------+
| 📌 FASE 1: Backend Aggregator Service, Scoping & Role Permission                                                 |
|    ├── 1.1 Service `ConsolidatedFinancialService.php` (Agregasi Multi-Cabang: Omzet, P&L, Cash Flow, PO Tempo)  |
|    ├── 1.2 Helper/Scope Query Multi-Outlet Filter (`outlet_ids[]`, All Outlets vs Single Outlet)                 |
|    ├── 1.3 Role Permission & Middleware `EnsureOwnerAccess` (Proteksi agar Kasir/Staff tidak bisa akses)         |
|    └── 1.4 Pendaftaran Routes Grup `admin.owner.*` di `routes/web.php`                                            |
|                                                                                                                   |
| 📌 FASE 2: UI Sidebar & Modul Konsolidasi Semua Cabang (Menu 1 & 2)                                              |
|    ├── 2.1 Pemasangan Nav Section `👑 PORTAL OWNER` di `resources/views/admin/layouts/app.blade.php`             |
|    ├── 2.2 Controller `OwnerDashboardController.php` & View `resources/views/admin/owner/dashboard.blade.php`    |
|    │   ├── 4 Kartu KPI Konsolidasi (Total Omzet Gabungan, Laba Bersih, Kas Masuk, Setoran Brankas)               |
|    │   ├── Multi-Outlet Filter Switcher & Date Range Preset                                                       |
|    │   └── Multi-Line Chart Tren Omzet & Transaksi Antar Cabang (Chart.js / ApexCharts Theme Adapted)             |
|    ├── 2.3 Controller `OwnerFinancialController.php` (Laba Rugi & Cash Flow Konsolidasi)                         |
|    └── 2.4 View `resources/views/admin/owner/financial.blade.php` + Fitur Export Excel Konsolidasi               |
|                                                                                                                   |
| 📌 FASE 3: Leaderboard, Benchmark Resep & Deteksi Kebocoran (Menu 3 & 4)                                         |
|    ├── 3.1 Controller `OwnerBenchmarkController.php` & View `resources/views/admin/owner/benchmark.blade.php`    |
|    │   ├── Leaderboard Cabang (Ranking Omzet, Margin Tertebal, Cabang Defisit Warning)                           |
|    │   └── Benchmark HPP Resep Antar Cabang (Deteksi Anomali Pemborosan Dapur & Porsi Bocor)                     |
|    ├── 3.2 Controller `OwnerAuditController.php` & View `resources/views/admin/owner/audit.blade.php`            |
|    │   ├── Audit Selisih Kasir (Tracking Shortage/Over Kasir per Cabang saat Tutup Shift)                         |
|    │   ├── Audit Bahan Terbuang (Waste Log Comparison per Dapur Cabang)                                           |
|    │   └── Audit Diskon Manual & Void Struk Transaksi                                                            |
|                                                                                                                   |
| 📌 FASE 4: Pusat Setoran Kas, Hutang Supplier & Manajemen Cabang (Menu 5 & 6)                                    |
|    ├── 4.1 Controller `OwnerCashDebtController.php` & View `resources/views/admin/owner/cash-debt.blade.php`     |
|    │   ├── Rekap Setoran Brankas Malam Ini (Real-Time Safe Deposit Monitor dari Seluruh Kasir Cabang)             |
|    │   └── Kalender Jatuh Tempo Hutang PO Supplier Gabungan (Alert Tagihan Supplier 7 Hari ke Depan)              |
|    ├── 4.2 Controller `OwnerBranchController.php` & View `resources/views/admin/owner/branches/index.blade.php`  |
|    │   ├── Master CRUD Cabang (Tambah Cabang Baru, Alamat, Kontak, Status Aktif)                                  |
|    │   └── Pengaturan Jam Cut-Off Shift per Cabang & Penugasan Manager Cabang                                     |
|                                                                                                                   |
| 📌 FASE 5: Seeder Enriched Multi-Branch & Verifikasi Menyeluruh                                                   |
|    ├── 5.1 Update Seeder Multi-Tenant (`KopiSenjaSeeder` & `GeprekGambosSeeder` dengan 3 Cabang Aktif)           |
|    ├── 5.2 Seeding Transaksi, Shift Handover, PO Supplier & Waste di Masing-Masing Cabang                        |
|    ├── 5.3 Pengujian Role Guard: Kasir Tidak Bisa Akses Portal Owner (403 Forbidden)                              |
|    └── 5.4 Verifikasi UI/UX Dark/Light Mode, Responsiveness Tablet/Mobile, dan Akurasi Kalkulasi Angka Konsolidasi|
+-------------------------------------------------------------------------------------------------------------------+
```

---

## 📋 RINCIAN DETAIL 6 SUB-MENU KHUSUS OWNER

---

### 👑 1. Konsolidasi Cabang (*All-Branch Overview*)
* **Route**: `admin.owner.dashboard` (`/admin/owner/dashboard`)
* **Tujuan**: Memberikan tampilan *Helicopter View* dalam 1 layar kepada pemilik restoran atas kondisi seluruh cabang.
* **Komponen Utama**:
  1. **Consolidated KPI Stat Cards (4 Kartu Nexora Glow)**:
     - 💰 **Total Omzet Gabungan**: Rp 45.800.000 *(+12.4% vs Kemarin)*
     - 💵 **Total Laba Bersih Konsolidasi**: Rp 14.200.000 *(Net Margin 31.0%)*
     - 🏦 **Total Kas Masuk Setoran Brankas**: Rp 28.500.000 *(Kasir siap setor malam ini)*
     - ⏳ **Total Hutang PO Supplier Tempo**: Rp 8.400.000 *(Jatuh tempo minggu ini)*
  2. **Multi-Outlet Dynamic Filter**:
     - Dropdown: `[Semua Cabang (Konsolidasi)]`, `[Cabang Jakarta Pusat]`, `[Cabang Bandung]`, `[Cabang Surabaya]`.
  3. **Multi-Line Performance Chart**:
     - Grafik komparasi jam sibuk dan omzet harian antar cabang secara berdampingan.

---

### 👑 2. Laba Rugi & Cash Flow Gabungan (*Consolidated Financials*)
* **Route**: `admin.owner.financial` (`/admin/owner/financial`)
* **Tujuan**: Menghitung kinerja keuntungan nyata dari seluruh unit bisnis restoran.
* **Komponen Utama**:
  1. **Tabel Konsolidasi P&L Multi-Cabang**:
     - Menampilkan kolom: `[Komponen] | [Cabang A] | [Cabang B] | [Cabang C] | [TOTAL KONSOLIDASI]`.
     - Rincian: Gross Sales, Potongan Diskon, Modal Resep COGS, Laba Kotor, Biaya Waste, Gaji Seluruh Cabang, Listrik/Air/Sewa, Laba Bersih Holding.
  2. **Konsolidasi Cash Flow Inflow vs Outflow**:
     - Total uang fisik nyata yang berputar di seluruh cabang.
  3. **Export Excel / CSV Konsolidasi**:
     - Menghasilkan file laporan formal untuk keperluan rapat direksi / investor.

---

### 👑 3. Leaderboard & Benchmark Cabang (*Perbandingan Antar Cabang*)
* **Route**: `admin.owner.benchmark` (`/admin/owner/benchmark`)
* **Tujuan**: Mengetahui cabang mana yang berprestasi dan mendeteksi pemborosan takaran resep.
* **Komponen Utama**:
  1. **Ranking Performa Cabang (Leaderboard)**:
     - 🥇 Cabang Terlaris (*Top Revenue*)
     - 💎 Cabang Paling Efisien (*Lowest COGS % / Highest Net Margin*)
     - ⚠️ Cabang Paling Rawan (*Defisit Kas / Waste Tertinggi*)
  2. **Benchmark Resep Menu Unggulan**:
     - Membandingkan HPP menu yang sama di berbagai cabang.
     - *Anomaly Detector*: Jika di Cabang Jakarta HPP Ayam Geprek 32%, tapi di Cabang Surabaya tercatat 45%, sistem otomatis memberi tanda peringatan kuning (*Porsi Takaran Boros*).

---

### 👑 4. Audit & Deteksi Kebocoran (*Fraud, Selisih Kas & Waste*)
* **Route**: `admin.owner.audit` (`/admin/owner/audit`)
* **Tujuan**: Alat pengawasan owner terhadap kedisiplinan dan integritas staf kasir/dapur di setiap cabang.
* **Komponen Utama**:
  1. **Audit Selisih Kasir (Cash Variance Tracker)**:
     - Riwayat kasir per cabang: Kasir siapa dan cabang mana yang sering mengalami selisih minus (*shortage*) saat tutup shift.
  2. **Audit Kerugian Dapur (Waste Comparison)**:
     - Dapur cabang mana yang paling sering membuang bahan baku busuk/rusak.
  3. **Audit Diskon Manual & Void Transaksi**:
     - Memantau frekuensi pembatalan nota atau pemberian diskon manual oleh kasir untuk mencegah penipuan di laci.

---

### 👑 5. Pusat Setoran Kas & Hutang Supplier (*Cash & Debt Central*)
* **Route**: `admin.owner.cash-debt` (`/admin/owner/cash-debt`)
* **Tujuan**: Manajemen likuiditas uang tunai dan pengawasan jatuh tempo supplier.
* **Komponen Utama**:
  1. **Live Safe Deposit Tracker (Setoran Brankas)**:
     - Menampilkan rekap uang tunai yang malam ini telah diserahkan dari laci kasir ke brankas/rekening owner per cabang.
  2. **Kalender Jatuh Tempo PO Supplier**:
     - Rekap seluruh tagihan PO Tempo supplier dari seluruh cabang dengan badge warna:
       - 🔴 *Jatuh Tempo Hari Ini*
       - 🟡 *Jatuh Tempo 3 Hari ke Depan*
       - 🟢 *Jatuh Tempo > 7 Hari*

---

### 👑 6. Manajemen Cabang & Outlet (*Branch Control*)
* **Route**: `admin.owner.branches` (`/admin/owner/branches`)
* **Tujuan**: Manajemen master operasional jaringan cabang.
* **Komponen Utama**:
  1. **CRUD Master Outlet / Cabang**:
     - Tambah cabang baru, nama outlet, alamat lengkap, nomor telepon, logo cabang.
  2. **Pengaturan Operasional per Cabang**:
     - Jam cut-off harian, target omzet bulanan, dan penugasan Store Manager.

---

## 🗂️ RENCANA FILE & STRUKTUR KODE

### 1. Controllers Baru (`app/Http/Controllers/Admin/Owner/`):
- `OwnerDashboardController.php` (Menu 1: Konsolidasi Semua Cabang)
- `OwnerFinancialController.php` (Menu 2: Laba Rugi & Cash Flow Konsolidasi)
- `OwnerBenchmarkController.php` (Menu 3: Leaderboard & Perbandingan HPP)
- `OwnerAuditController.php` (Menu 4: Audit Selisih Kas & Waste)
- `OwnerCashDebtController.php` (Menu 5: Setoran Brankas & Hutang PO)
- `OwnerBranchController.php` (Menu 6: Manajemen Cabang & Outlet)

### 2. Services Backend (`app/Services/`):
- `ConsolidatedFinancialService.php` (Mesin penghitung agregasi multi-outlet)

### 3. Views Baru (`resources/views/admin/owner/`):
- `layouts/owner.blade.php` (atau integrasi langsung ke `admin.layouts.app`)
- `dashboard.blade.php`
- `financial.blade.php`
- `benchmark.blade.php`
- `audit.blade.php`
- `cash-debt.blade.php`
- `branches/index.blade.php`, `branches/create.blade.php`, `branches/edit.blade.php`

---

## 🔒 PRINSIP KEAMANAN & UI/UX NATIVE NEXORA (ANTI AI-SLOP MANDATORY)

1. **Role Access Restriction**: Kasir dan staf biasa **TIDAK BISA** melihat menu `👑 PORTAL OWNER`. Hanya role `owner` / `superadmin` / `director` yang memiliki otorisasi.
2. **Kiblat File Referensi Resmi (Wajib Mengikuti Halaman yang Sudah Ada)**:
   - 📑 **Dokumentasi Komponen Native:**
     - `resources/views/docs/cards.blade.php` &rarr; Standar kartu metrik statistik `.stat-card` dan `.card-glow`.
     - `resources/views/docs/forms.blade.php` &rarr; Standar form input, group prepend/append, dan modal pop-up.
     - `resources/views/docs/components.blade.php` &rarr; Standar badge pill, tombol aksi, dan tabel data native `.table-custom`.
   - 📑 **Modul Finansial yang Sudah Sempurna:**
     - `resources/views/admin/keuangan/reports/cashflow.blade.php` &rarr; Standar kontras tinggi dark mode & kartu KPI glowing.
     - `resources/views/admin/keuangan/shift-operational/index.blade.php` &rarr; Standar 4 kartu statistik berjejer rapi, active live bar, dan layout 2 kolom.
3. **Anti AI-Slop / Zero Ad-Hoc Styling**:
   - ❌ Dilarang membuat tag `<style>` inline acak atau warna neon aneh yang tidak ada di tema.
   - ❌ Dilarang membuat border tebal kaku atau drop-shadow pekat yang merusak kerapian UI.
   - ✅ Seluruh warna dan kontras **WAJIB** menggunakan CSS Variables Nexora: `var(--bg-surface)`, `var(--bg-elevated)`, `var(--text-primary)`, `var(--text-secondary)`, `var(--border-subtle)`, `var(--accent-1)`, `var(--success)`, `var(--danger)`, `var(--warning)`.
4. **Dark / Light Theme Auto-Adaptation**: Seluruh kartu glow, chart warna, dan badge status likuiditas wajib beradaptasi otomatis dan diuji coba pada mode terang (`[data-theme="light"]`) dan gelap (`[data-theme="dark"]`).

---

## 🧪 PROTOKOL PENGUJIAN & QUALITY ASSURANCE KETAT (MANDATORY)

Setiap tahapan eksekusi fitur dan route baru **WAJIB lolos 3 protokol pengujian berikut**:

### 1. 🛡️ Protokol Anti Raw JSON Screen (Zero Raw JSON Dumps)
* **Aturan Keras**: Tidak boleh ada form submission (POST / PUT / DELETE) yang menghasilkan layar putih polos berisi raw JSON `{"status": "success", ...}` di web interface.
* **Standar Controller**:
  ```php
  if ($request->expectsJson() || $request->ajax()) {
      return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan']);
  }
  return redirect()->back()->with('success', 'Data berhasil disimpan.');
  ```
* **Verifikasi**: Uji coba submit form secara normal di browser dan pastikan ada alert banner / NexoraToast dengan transisi mulus.

### 2. 🎨 Protokol Kontras & Legibility Mode Gelap (Dark & Light Mode)
* **Aturan Keras**: Teks tidak boleh mati/tenggelam di background gelap (contoh: teks hitam di atas background hitam atau teks abu-abu pudar di atas background abu-abu gelap).
* **Standar Styling**:
  - Selalu gunakan `--text-primary`, `--text-secondary`, dan `--bg-surface`.
  - Chart.js / ApexCharts wajib mendeteksi theme `[data-theme="dark"]` dan `[data-theme="light"]` secara dinamis (gridlines redup, tooltip kontras, font putih/terang di mode gelap).
* **Verifikasi**: Beralih tema Dark & Light mode pada setiap halaman dan periksa keterbacaan setiap baris teks, badge, dan tabel.

### 3. 🔬 Protokol Uji Setiap Baris, Setiap Route & Zero Error Logs
* **Aturan Keras**: Dilarang menganggap fitur selesai tanpa menguji route yang bersangkutan.
* **Prosedur Pengujian**:
  - Test HTTP GET status `200 OK` untuk seluruh 6 sub-menu owner.
  - Test skenario filter date range dan filter dropdown multi-outlet.
  - Test export Excel/CSV untuk memastikan format UTF-8 BOM valid dan angka konsolidasi akurat.
  - Periksa file log server (`storage/logs/laravel.log`) untuk memastikan `0 error` dan `0 warning`.

---

> 📝 *Dokumen ini disahkan pada 2026-08-29 sebagai blueprint resmi Phase 3. Seluruh langkah eksekusi berikutnya akan mengacu secara ketat pada milestone ini.*
