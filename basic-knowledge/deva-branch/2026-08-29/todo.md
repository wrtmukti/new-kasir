# Active Task Tracker & Architecture Blueprint — Branch `deva-branch` (2026-08-29)

> **Tanggal**: 2026-08-29 (29 Agustus 2026)  
> **Branch**: `deva-branch`  
> **Fokus Utama**: **Owner Executive Portal & Multi-Outlet Consolidated Financial Hub (Plan B Multi-Branch Suite)**  
> **Status**: 📋 DRAFTING & BRAINSTORMING (NO CODE EXECUTION YET)

---

## 🎯 1. Latar Belakang & Problem Statement Owner Multi-Cabang

Seorang pemilik bisnis restoran/F&B yang memiliki banyak cabang (3 - 50 outlet) memiliki kebutuhan yang sangat berbeda dengan manajer outlet tunggal:

| Masalah Utama Owner Saat Ini | Solusi yang Dibutuhkan di Nexora POS |
|---|---|
| **1. Kebutaan Konsolidasi** | Owner tidak mau login bolak-balik ke 5 akun cabang berbeda lalu menjumlahkan omzet secara manual. Butuh **1 Dashboard Konsolidasi Total (All-Branch Aggregation)**. |
| **2. Lambat Mendeteksi Cabang Bermasalah** | Butuh **Leaderboard & Anomaly Alert**: langsung tahu cabang mana yang paling untung, cabang mana yang defisit kas, dan cabang mana yang HPP-nya membengkak karena porsi bocor. |
| **3. Kepastian Uang Setoran Malam Ini** | Butuh melihat **Total Uang Setoran Brankas (*Safe Deposit*)** dari seluruh kasir cabang secara real-time sebelum toko tutup. |
| **4. Kontrol Hutang Supplier Lintas Cabang** | Butuh melihat **Total Komitmen PO Supplier Tempo** yang harus dibayar minggu ini di semua cabang. |
| **5. Efisiensi Bahan & Transfer Antar Cabang** | Butuh memantau stok bahan mentah di setiap cabang dan mutasi transfer bahan antar cabang. |

---

## 🏛️ 2. Arsitektur Menu & Fitur Khusus Owner (Multi-Outlet Hub)

Berikut rancangan menu dan komponen yang disiapkan untuk Owner Executive Portal:

```text
OWNER EXECUTIVE SUITE
  ├── 📊 1. Konsolidasi Eksekutif (All-Branch Overview)
  │     ├── Consolidated KPI Cards (Total Omzet, Laba Bersih, Kas Masuk, Setoran Brankas)
  │     ├── Outlet Performance Ranking (Leaderboard Terbaik vs Terbawah)
  │     └── Quick Outlet Switcher & Multi-Select Filter
  │
  ├── ⚖️ 2. Laba Rugi & Cash Flow Gabungan (Consolidated Financials & Export)
  │     ├── P&L Multi-Cabang Side-by-Side
  │     └── Cash Flow Inflow vs Outflow Holding
  │
  ├── 🏆 3. Leaderboard & Benchmark Cabang (Cross-Outlet Analytics)
  │     ├── Perbandingan Margin HPP Resep Antar Cabang (Deteksi Pemborosan Porsi)
  │     └── Ranking Outlet Terlaris vs Defisit
  │
  ├── 🚨 4. Audit & Deteksi Kebocoran (Fraud, Selisih Kas & Waste)
  │     ├── Audit Kedisiplinan Kasir & Selisih Kas Laci (Shortage/Over Tracker)
  │     ├── Audit Bahan Terbuang (Waste Comparison)
  │     └── Audit Void & Diskon Manual
  │
  ├── 🏦 5. Pusat Setoran Kas & Hutang Supplier (Cash & Debt Central)
  │     ├── Live Safe Deposit Tracker (Setoran Brankas Malam Ini)
  │     └── Kalender Jatuh Tempo PO Supplier Semua Cabang
  │
  └── 🏢 6. Manajemen Cabang & Outlet (Branch Control)
        ├── Master Outlet CRUD
        └── Jam Cut-Off & Manager Penugasan
```

---

## 📋 3. Checklist Task Roadmap & Mandatory QA Protocols

### [x] FASE 1: Backend Aggregation, Scoping & Role Guard
- [x] Buat Service `ConsolidatedFinancialService.php` (Agregasi Multi-Cabang: Omzet, P&L, Cash Flow, PO Tempo).
- [x] Buat Scope Query Multi-Outlet Filter (`outlet_ids[]`, All Outlets vs Single Outlet).
- [x] Implementasi Role Guard & Middleware `EnsureOwnerAccess` (Kasir/Staff biasa diblokir 403).
- [x] Pendaftaran Routes `admin.owner.*` di `routes/web.php`.
- [x] **[QA Check]** Test HTTP route status & pastikan role security bekerja sempurna.

### [x] FASE 2: UI Sidebar & Modul Konsolidasi Semua Cabang (Menu 1 & 2)
- [x] Pemasangan Nav Section `👑 PORTAL OWNER` di `resources/views/admin/layouts/app.blade.php`.
- [x] Buat Controller `OwnerDashboardController.php` & View `resources/views/admin/owner/dashboard.blade.php`:
  - 4 Kartu KPI Glow Konsolidasi (Total Omzet Gabungan, Laba Bersih, Kas Masuk, Setoran Brankas).
  - Multi-Outlet Filter Switcher & Date Range Preset.
  - Multi-Line Chart Tren Omzet & Transaksi Antar Cabang (Auto Dark/Light Mode Adapt).
- [x] Buat Controller `OwnerFinancialController.php` & View `resources/views/admin/owner/financial.blade.php` (Laba Rugi & Cash Flow Gabungan + Export Excel CSV UTF-8 BOM).
- [x] **[QA Check]** Verifikasi form action redirect (Zero Raw JSON Screen) & verifikasi kontras Dark Mode.

### [x] FASE 3: Leaderboard, Benchmark Resep & Deteksi Kebocoran (Menu 3 & 4)
- [x] Buat Controller `OwnerBenchmarkController.php` & View `resources/views/admin/owner/benchmark.blade.php`:
  - Ranking Cabang (Omzet, Margin Tertebal, Cabang Defisit Warning).
  - Benchmark HPP Resep Antar Cabang (Deteksi Anomali Pemborosan Dapur & Porsi Bocor).
- [x] Buat Controller `OwnerAuditController.php` & View `resources/views/admin/owner/audit.blade.php`:
  - Audit Selisih Kasir (Tracking Shortage/Over Kasir per Cabang saat Tutup Shift).
  - Audit Bahan Terbuang (Waste Log Comparison per Dapur Cabang).
  - Audit Diskon Manual & Void Struk Transaksi.
- [x] **[QA Check]** Verifikasi kalkulasi deviasi HPP dan tabel audit di Dark/Light mode.

### [x] FASE 4: Pusat Setoran Kas, Hutang Supplier & Manajemen Cabang (Menu 5 & 6)
- [x] Buat Controller `OwnerCashDebtController.php` & View `resources/views/admin/owner/cash-debt.blade.php`:
  - Rekap Setoran Brankas Malam Ini (Real-Time Safe Deposit Monitor dari Seluruh Kasir Cabang).
  - Kalender Jatuh Tempo Hutang PO Supplier Gabungan (Alert Tagihan Supplier 7 Hari ke Depan).
- [x] Buat Controller `OwnerBranchController.php` & View `resources/views/admin/owner/branches/index.blade.php` (Master CRUD Outlet & Jam Cut-Off).
- [x] **[QA Check]** Verifikasi form save cabang redirect back dengan flash message (Zero Raw JSON).

### [x] FASE 5: Seeder Enriched Multi-Branch & Verifikasi Menyeluruh
- [x] Update Seeder Multi-Tenant (`KopiSenjaSeeder` dengan 3 Cabang Aktif: Jakarta, Bandung, Yogyakarta).
- [x] Seeding Transaksi, Shift Kasir, PO Supplier & Waste di masing-masing cabang.
- [x] **[QA Check Final]** Uji seluruh 6 sub-menu, ekspor Excel, dark mode switcher, dan cek `laravel.log` pastikan 0 error.

---

## 4. Hasil Verifikasi & QA 6 Sub-Menu Owner Portal

| No | Sub-Menu Owner | Route Name | File View | Status Uji |
|:--:|---|---|---|:--:|
| 1 | **Konsolidasi Semua Cabang** | `admin.owner.dashboard` | `owner/dashboard.blade.php` | ✅ **PASS (200 OK)** |
| 2 | **Laba Rugi & Arus Kas** | `admin.owner.financial` | `owner/financial.blade.php` | ✅ **PASS (200 OK)** |
| 2b | **Ekspor CSV Finansial** | `admin.owner.financial.export` | UTF-8 BOM Stream CSV | ✅ **PASS (200 OK)** |
| 3 | **Leaderboard & Benchmark** | `admin.owner.benchmark` | `owner/benchmark.blade.php` | ✅ **PASS (200 OK)** |
| 4 | **Audit Selisih & Waste** | `admin.owner.audit` | `owner/audit.blade.php` | ✅ **PASS (200 OK)** |
| 5 | **Pusat Setoran & Hutang PO** | `admin.owner.cash-debt` | `owner/cash-debt.blade.php` | ✅ **PASS (200 OK)** |
| 6 | **Manajemen Cabang** | `admin.owner.branches.index` | `owner/branches/index.blade.php` | ✅ **PASS (200 OK)** |

> 📝 *Dokumen ini disahkan pada 2026-08-29 sebagai panduan konseptual & checklist pengujian ketat Phase 3.*
