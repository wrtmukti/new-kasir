# 🚀 Strategi Tiering SaaS, Analisis Pasar, & Panduan Pembagian Paket Nexora POS

> **Dokumen**: `saas_tiering_strategy_and_market_benchmark.md`  
> **Lokasi**: `basic-knowledge/saas_tiering_strategy_and_market_benchmark.md`  
> **Tanggal Rilis**: 2026-08-31  
> **Branch**: `deva-branch`  
> **Status**: 🌟 **Official Architectural & Business Strategy Document (100% Comprehensive)**  
> **Target Audiens**: Lead Architect, Tim Bisnis/Sales, SysAdmin & Frontend/Backend Engineers

---

## 📑 Daftar Isi
1. [Ringkasan Eksekutif & Value Proposition](#1-ringkasan-eksekutif--value-proposition)
2. [Analisis Kompetitor & Benchmark Pasar (Indonesia & Global)](#2-analisis-kompetitor--benchmark-pasar)
3. [Inventaris Lengkap Fitur Nexora POS Berdasarkan Layer](#3-inventaris-lengkap-fitur-nexora-pos)
4. [Arsitektur 4-Tier Paket Langganan Nexora SaaS](#4-arsitektur-4-tier-paket-langganan-nexora-saas)
5. [Matriks Perbandingan Fitur Komparatif (Feature Gate Matrix)](#5-matriks-perbandingan-fitur-komparatif)
6. [Pembeda Utama Tier Dasar (Starter) vs POS Pasar](#6-pembeda-utama-tier-dasar-starter-vs-pos-pasar)
7. [Pembeda Utama Tier Atas (Pro & Business) vs POS Pasar](#7-pembeda-utama-tier-atas-pro--business-vs-pos-pasar)
8. [Panduan Arsitektur Teknis Implementasi di Laravel](#8-panduan-arsitektur-teknis-implementasi-di-laravel)
9. [Playbook Sales & Panduan ROI Pitching Pelanggan](#9-playbook-sales--panduan-roi-pitching-pelanggan)

---

## 1. 🧭 Ringkasan Eksekutif & Value Proposition

Sebagian besar software POS (*Point of Sale*) di pasar Indonesia hanya bertindak sebagai **"Mesin Kasir Pencatat Transaksi"**. POS tradisional mencatat apa yang dijual dan mencetak struk, namun gagal melindungi keuntungan pemilik restoran dari kebocoran bahan mentah di dapur, manipulasi uang fisik di laci kasir, dan kekacauan laporan laba rugi akibat pencampuran modal kasir dengan pendapatan.

**Nexora POS** dirancang dari fondasi arsitektur akuntansi modern (**Plan B Finansial**) yang bertindak sebagai **"Sistem Penjaga Laba, Anti-Bocor Kasir, & Pengendali Dapur Multi-Cabang"**.

```text
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                 NEXORA POS VALUE PILLARS                                         │
├──────────────────────────────┬──────────────────────────────────┬────────────────────────────────┤
│     1. KASIR & QR NATIVE     │     2. DECOUPLED COGS KITCHEN    │    3. 👑 PORTAL OWNER SUITE    │
│  Kasir cepat, QR Meja bebas  │  Potong stok gramasi bahan riil, │  Konsolidasi holding, deteksi  │
│ komisi, promo bundle voucher,│  hitung HPP otomatis, catat waste│ anomali bocor dapur >5%, audit │
│  buku kas laci kasir rapi.   │  basi/tumpah, PO supplier tempo. │  brankas & kalender hutang PO. │
└──────────────────────────────┴──────────────────────────────────┴────────────────────────────────┘
```

---

## 2. 🔍 Analisis Kompetitor & Benchmark Pasar

Riset mendalam terhadap para pemain SaaS POS utama di Indonesia dan standar global:

### A. Tabel Perbandingan Model Bisnis & Harga Kompetitor

| Vendor / Platform | Model Lisensi | Rentang Harga (IDR / Bulan) | Biaya Tersembunyi / Batasan yang Merugikan Pengguna | Kunci Pemicu Upgrade (*Paywall Trigger*) |
|---|---|---|---|---|
| **Moka POS** (GoTo) | Per Outlet / Bulan | Rp 250.000 – Rp 299.000 / outlet | • QR Menu (Moka Order) bayar add-on tambahan.<br>• Modul akuntansi (Moka Connect) bayar terpisah.<br>• Integrasi pihak ketiga berbayar. | Butuh multi-outlet, butuh QR menu, butuh integrasi akuntansi. |
| **Majoo** | Paket Berjenjang (Tiered) | • **Starter**: Rp 129k–249k<br>• **Advance**: Rp 299k<br>• **Prime**: Rp 499k–999k | • Paket Starter tidak bisa multi-outlet.<br>• Paket Starter tidak ada resep/COGS.<br>• Terlalu banyak menu generic yang tidak spesifik F&B. | Butuh multi-cabang, butuh master resep & HPP, butuh laporan akuntansi lanjutan. |
| **Pawoon** | Freemium & Tiered | • **Free**: 1 device, limit transaksi<br>• **Basic**: Rp 149k<br>• **Pro**: Rp 250k–350k | • Versi Free membatasi kuota transaksi bulanan.<br>• Laporan detail & manajemen bahan baku dikunci di paket Pro. | Melewati batas kuota transaksi, butuh resep bahan baku & multi-outlet. |
| **Olsera** | Langganan Tahunan Flat | Mulai Rp 107.000/bln (Bayar Tahunan Rp 1.288.000 s/d Rp 2.5jt+) | • Tidak ada opsi bayar bulanan fleksibel.<br>• Fitur web ordering premium dan integrasi akuntansi dikenakan biaya tambahan. | Butuh manajemen multi-cabang dan fitur pesanan online mandiri. |
| **Toast POS** (Global Benchmark) | Pay-as-you-go & Modular | • **Starter**: $0/bln (+ MDR tinggi)<br>• **Core/Essentials**: $69–$165/bln<br>• **Enterprise**: Custom | • Hardware khusus (Toast Flex/Go) sangat mahal.<br>• Semua fitur tambahan (KDS, Loyalty, Delivery) dijual sebagai add-on bulanan. | Kebutuhan Kitchen Display System (KDS), Multi-Location Enterprise Management, & Table Management. |

---

## 3. 📦 Inventaris Lengkap Fitur Nexora POS

Codebase Nexora POS saat ini mencakup 6 layer fungsional terintegrasi:

```text
👑 PORTAL OWNER (Multi-Branch Executive Hub)
  ├── 📊 Konsolidasi Holding (KPI Glow, Multi-Outlet Switcher, Tren Omzet Multi-Line Chart)
  ├── 📈 Laba Rugi Akrual vs Arus Kas Riil Lintas Cabang (P&L, Real Inflow/Outflow, Export CSV BOM)
  ├── 🏆 Leaderboard & Benchmark Resep (Deteksi Anomali Pemborosan Dapur Deviasi HPP >5%)
  ├── 🚨 Audit Terpadu (Audit Selisih Kasir Over/Shortage & Kerugian Bahan Waste Basi/Tumpah)
  ├── 🏦 Pusat Setoran Brankas Kasir & Kalender Urgensi Jatuh Tempo Hutang Supplier PO
  └── 🏢 Master Cabang & Pengaturan Jam Cut-Off Shift Dinamis

🏛️ BACKOFFICE & KEUANGAN (Decoupled COGS Plan B)
  ├── 🥩 Master Bahan Mentah (Raw Materials), Satuan Konversi, & Stock Opname Bahan
  ├── 🥣 Komposisi Resep Menu Murni (Theoretical COGS otomatis terhitung saat menu laku di POS)
  ├── 📦 Purchase Order Lifecycle (Pending ➔ Confirm ➔ Pay/Tempo ➔ Receiving ➔ In Stock)
  ├── 🗑️ Waste Log System (Catatan bahan basi, rusak, tumpah + valuasi rupiah kerugian)
  ├── 📑 6 Dedicated Detail Laporan Keuangan + Export CSV UTF-8 BOM
  └── 🏛️ Master Pajak Resto (PB1 10%) & Service Charge (5%) Dinamis per Outlet

🏪 OPERASIONAL KASIR & CASH GOVERNANCE
  ├── 💻 POS Kasir Modern (Dine-In, Takeaway, Cart, Split Payment, Struk Thermal)
  ├── 📱 Guest QR Self-Ordering (Bawaan native di /{client_id}/{outlet_id}/{table_id})
  ├── 🪑 Manajemen Meja Resto (Table Map Status & Order Linking)
  ├── 🎁 Engine Diskon, Voucher Unik Kuota/Min Belanja, & Paket Promo Bundle
  └── 🔐 Shift Kasir (Clock-In Modal Awal, Petty Cash In/Out, Clock-Out Blind Drop Z-Report 80mm)

⚙️ SYSTEM ADMIN & MULTI-TENANCY PLATFORM
  ├── 🏢 Client Provisioning & Database Isolation per Client
  ├── 💾 Automated Database Snapshots, Backup & Restore
  ├── 🩺 System Health Monitoring & Ping Engine
  ├── 🕵️ Client Impersonation ("Login as Client" tanpa minta password)
  └── 📋 Security Audit Logs & Subscription Management
```

---

## 4. 💎 Arsitektur 4-Tier Paket Langganan Nexora SaaS

Untuk mengoptimalkan penetrasi pasar UMKM sekaligus menangkap *High-Ticket Enterprise Clients*, Nexora membagi layanan menjadi **4 Paket Langganan**:

```text
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                                 NEXORA SUBSCRIPTION TIERS                              │
├──────────────────────┬──────────────────────┬───────────────────┬──────────────────────┤
│ 1. STARTER LITE      │ 2. PRO RESTO & CAFE  │ 3. BUSINESS GROUP │ 4. ENTERPRISE        │
│ Rp 149.000 / bln     │ Rp 349.000 / bln     │ Rp 899.000 / bln  │ Rp 2.500.000+ / bln  │
│ (Rp 1.490.000/thn)   │ (Rp 3.490.000/thn)   │ (Rp 7.990.000/thn)│ (Kontrak Tahunan)    │
├──────────────────────┼──────────────────────┼───────────────────┼──────────────────────┤
│ Target: UMKM, Kios,  │ Target: Cafe, Resto, │ Target: Pemilik   │ Target: Franchise    │
│ Booth, Single Shop   │ Bakery, Dine-In      │ 3-10 Cabang       │ Besar / Skala Nas.   │
│ Kuota: 1 Cabang      │ Kuota: 1-3 Cabang    │ Kuota: 10 Cabang  │ Kuota: Unlimited     │
│ User: Maks 3 Akun    │ User: Maks 10 Akun   │ User: Maks 30 Akun│ User: Unlimited      │
└──────────────────────┴──────────────────────┴───────────────────┴──────────────────────┘
```

---

### 📦 TIER 1: STARTER LITE (UMKM & Kios Rintisan)
* **Target Segmen**: Warung makan, kedai kopi kecil, stand makanan pujasera/mall, dan booth takeaway.
* **Harga**: **Rp 149.000 / bulan** (atau Rp 1.490.000 / tahun).
* **Batas Kuota**:
  - Maksimal **1 Outlet / Cabang**.
  - Maksimal **3 Akun Pengguna** (1 Owner, 2 Kasir).
  - Penyimpanan Cloud: **1 GB**.
* **Fitur Termasuk**:
  - ✅ POS Kasir & Transaksi Cepat.
  - ✅ **Self-Ordering QR Meja Pelanggan Native** (Bebas Komisi).
  - ✅ Manajemen Denah Meja Restoran (*Table Status*).
  - ✅ Promo Bundle Menu & Sistem Voucher Diskon.
  - ✅ Stok Produk Jadi (*Ready-to-sell Inventory*).
  - ✅ Buku Kas Laci Kasir (*Cash In / Cash Out Petty Cash*).
  - ✅ Perhitungan Pajak PB1 10% & Service Charge 5%.
  - ✅ Laporan Penjualan Dasar (Omzet, Metode Pembayaran, Struk).
  - ✅ **Unlimited Transaksi & Unlimited Menu Produk**.
* **Payload `features_json` di Database**:
  ```json
  {
    "pos_cashier": true,
    "guest_qr": true,
    "table_management": true,
    "bundle_promo": true,
    "voucher_engine": true,
    "inventory_basic": true,
    "drawer_petty_cash": true,
    "reports_sales": true,
    "cogs_recipe": false,
    "purchase_order": false,
    "waste_log": false,
    "shift_audit_zreport": false,
    "reports_financial_detail": false,
    "owner_portal_suite": false,
    "benchmark_anomaly": false,
    "api_access": false
  }
  ```

---

### 🚀 TIER 2: PRO RESTO & CAFE (Sweet Spot / Paling Populer)
* **Target Segmen**: Cafe ramai, Restoran Dine-In, Rumah Makan, Bakery, dan Bisnis F&B yang mulai bertumbuh.
* **Harga**: **Rp 349.000 / bulan** (atau Rp 3.490.000 / tahun).
  - *Add-on Cabang*: +Rp 100.000 / cabang tambahan (maksimal hingga 3 cabang).
* **Batas Kuota**:
  - Mendukung hingga **3 Outlet / Cabang**.
  - Mendukung hingga **10 Akun Pengguna** (Owner, Supervisor, Kasir, Koki).
  - Penyimpanan Cloud: **5 GB**.
* **Fitur Termasuk (Semua Fitur Starter +)**:
  - ✅ **Modul Resep Murni & Theoretical COGS**: Auto-potong stok bahan baku saat pesanan kasir/QR diproses.
  - ✅ **Purchase Order (PO) Supplier Lifecycle**: Penerimaan bahan mentah (*Receiving*) & pencatatan hutang tempo.
  - ✅ **Waste Log System**: Pencatatan bahan basi, rusak, atau tumpah beserta konversi rupiah kerugiannya.
  - ✅ **Stock Opname Bahan Mentah**.
  - ✅ **Shift Kasir Z-Report**: SOP *Blind Cash Drop*, cetak struk rekap thermal 80mm, dan audit selisih (*overage / shortage*).
  - ✅ **6 Dedicated Detail Laporan Keuangan** (Sales, Product PMIX, Cash Flow, Tax-Service, Inventory-Waste, Shift Audit).
  - ✅ **Fitur Export CSV / Excel UTF-8 BOM** di seluruh laporan.
  - ✅ **Laporan Laba Rugi Akrual & Arus Kas Riil (Plan B)** per cabang.
* **Payload `features_json` di Database**:
  ```json
  {
    "pos_cashier": true,
    "guest_qr": true,
    "table_management": true,
    "bundle_promo": true,
    "voucher_engine": true,
    "inventory_basic": true,
    "drawer_petty_cash": true,
    "reports_sales": true,
    "cogs_recipe": true,
    "purchase_order": true,
    "waste_log": true,
    "shift_audit_zreport": true,
    "reports_financial_detail": true,
    "owner_portal_suite": false,
    "benchmark_anomaly": false,
    "api_access": false
  }
  ```

---

### 👑 TIER 3: BUSINESS HOLDING (Multi-Outlet & Group Resto)
* **Target Segmen**: Pemilik bisnis multi-cabang (3 hingga 10 outlet), manajemen holding, grup restoran, dan *franchisee*.
* **Harga**: **Rp 899.000 / bulan** (atau Rp 7.990.000 / tahun).
* **Batas Kuota**:
  - Mendukung hingga **10 Outlet / Cabang**.
  - Mendukung hingga **30 Akun Pengguna** (Role: Executive Owner, Area Manager, Branch Manager, Kasir, Dapur).
  - Penyimpanan Cloud: **15 GB**.
* **Fitur Termasuk (Semua Fitur Pro +)**:
  - ✅ **👑 Seluruh Akses PORTAL OWNER (Executive Multi-Branch Suite)**:
    - **Dashboard Konsolidasi Holding**: Total Omzet Holding, Laba Bersih Konsolidasi, Kas Masuk Riil, dan Rekap Setoran Brankas.
    - **Laba Rugi Akrual vs Arus Kas Riil Konsolidasi**: Perbandingan kinerja finansial seluruh cabang dalam 1 layar.
    - **Leaderboard & Benchmark Resep**: Peringkat cabang terbaik dan **Deteksi Otomatis Anomali Pemborosan Dapur (Deviasi HPP >5% / Porsi Bocor)**.
    - **Audit Selisih Kasir & Kerugian Bahan Terpadu**: Deteksi kasir yang sering minus uang fisik dan dapur yang boros bahan.
    - **Pusat Setoran Brankas Kasir**: Monitor uang fisik kasir yang sudah atau belum disetor ke brankas pusat (*Safe Deposit*).
    - **Kalender Urgensi Jatuh Tempo Hutang Supplier**: Peringatan tagihan PO tempo (*Overdue, Critical, Warning, Safe*).
    - **Manajemen Cabang Terpusat & Custom Jam Cut-Off Shift per Cabang**.
* **Payload `features_json` di Database**:
  ```json
  {
    "pos_cashier": true,
    "guest_qr": true,
    "table_management": true,
    "bundle_promo": true,
    "voucher_engine": true,
    "inventory_basic": true,
    "drawer_petty_cash": true,
    "reports_sales": true,
    "cogs_recipe": true,
    "purchase_order": true,
    "waste_log": true,
    "shift_audit_zreport": true,
    "reports_financial_detail": true,
    "owner_portal_suite": true,
    "benchmark_anomaly": true,
    "api_access": false
  }
  ```

---

### 🏢 TIER 4: ENTERPRISE FRANCHISE (Skala Besar & Waralaba Nasional)
* **Target Segmen**: Jaringan waralaba (*franchise*), rantai restoran skala nasional (10+ hingga ratusan cabang), korporasi F&B.
* **Harga**: **Custom Pricing (Mulai dari Rp 2.500.000+ / bulan)** atau kontrak tahunan korporasi.
* **Batas Kuota**:
  - **Unlimited Outlet / Cabang**.
  - **Unlimited Akun Pengguna**.
  - **Dedicated Database Storage (100+ GB)**.
* **Fitur Termasuk (Semua Fitur Business +)**:
  - ✅ **Dedicated Database per Client** (Isolasi data 100% aman, cepat, independen).
  - ✅ **Custom Subdomain / White-label Domain** (misal: `pos.namabrand.com`).
  - ✅ **API Access & Webhook Engine** (Integrasi ke SAP, Accurate, Jurnal.id, atau ERP internal).
  - ✅ **Automated Daily Cloud Backup & Snapshot Download**.
  - ✅ **Dedicated Account Manager & Priority Support SLA (99.9% Uptime Guarantee)**.
  - ✅ **Custom Role & Advanced Permission Matrix**.
* **Payload `features_json` di Database**:
  ```json
  {
    "pos_cashier": true,
    "guest_qr": true,
    "table_management": true,
    "bundle_promo": true,
    "voucher_engine": true,
    "inventory_basic": true,
    "drawer_petty_cash": true,
    "reports_sales": true,
    "cogs_recipe": true,
    "purchase_order": true,
    "waste_log": true,
    "shift_audit_zreport": true,
    "reports_financial_detail": true,
    "owner_portal_suite": true,
    "benchmark_anomaly": true,
    "dedicated_database": true,
    "custom_domain": true,
    "api_access": true,
    "priority_sla": true
  }
  ```

---

## 5. 📊 Matriks Perbandingan Fitur Komparatif

| Fitur / Modul | STARTER LITE (Rp 149k) | PRO RESTO (Rp 349k) | BUSINESS HOLDING (Rp 899k) | ENTERPRISE (Custom) |
|---|:---:|:---:|:---:|:---:|
| **Batas Cabang / Outlet** | 1 Outlet | Maks 3 Outlet | Maks 10 Outlet | Unlimited |
| **Batas Pengguna (User)** | 3 User | 10 User | 30 User | Unlimited |
| **POS Kasir & Meja** | ✅ | ✅ | ✅ | ✅ |
| **Guest Self-Ordering QR Meja** | ✅ | ✅ | ✅ | ✅ |
| **Bundle Menu & Voucher Promo** | ✅ | ✅ | ✅ | ✅ |
| **Stok Barang Jadi (Simple)** | ✅ | ✅ | ✅ | ✅ |
| **Petty Cash Laci Kasir (In/Out)** | ✅ | ✅ | ✅ | ✅ |
| **Pajak Resto PB1 & Service Charge**| ✅ | ✅ | ✅ | ✅ |
| **Master Bahan Baku & Resep Gramasi**| ❌ | ✅ | ✅ | ✅ |
| **Theoretical COGS HPP Otomatis** | ❌ | ✅ | ✅ | ✅ |
| **PO Supplier & Receiving Barang** | ❌ | ✅ | ✅ | ✅ |
| **Waste Log (Bahan Rusak/Basi)** | ❌ | ✅ | ✅ | ✅ |
| **Stock Opname Bahan Mentah** | ❌ | ✅ | ✅ | ✅ |
| **Clock-In/Out Shift & Blind Drop** | ❌ | ✅ | ✅ | ✅ |
| **Cetak Struk Z-Report 80mm** | ❌ | ✅ | ✅ | ✅ |
| **Audit Selisih Kasir (Over/Short)**| ❌ | ✅ | ✅ | ✅ |
| **6 Laporan Keuangan Dedicated** | ❌ | ✅ | ✅ | ✅ |
| **Export CSV / Excel UTF-8 BOM** | ❌ | ✅ | ✅ | ✅ |
| **Laba Rugi Akrual vs Cash Flow Riil**| ❌ | ✅ (Per Outlet) | ✅ (Konsolidasi) | ✅ (Konsolidasi) |
| **👑 Dashboard Konsolidasi Holding**| ❌ | ❌ | ✅ | ✅ |
| **👑 Benchmark Resep Antar Cabang** | ❌ | ❌ | ✅ | ✅ |
| **👑 Alarm Pemborosan Dapur (>5%)** | ❌ | ❌ | ✅ | ✅ |
| **👑 Pusat Setoran Brankas Kasir** | ❌ | ❌ | ✅ | ✅ |
| **👑 Kalender Hutang Supplier Tempo**| ❌ | ❌ | ✅ | ✅ |
| **👑 Manajemen Cut-Off Shift Cabang**| ❌ | ❌ | ✅ | ✅ |
| **Dedicated Database per Client** | ❌ | ❌ | ❌ | ✅ |
| **API Access & Webhook Integrasi** | ❌ | ❌ | ❌ | ✅ |
| **Custom Domain & Priority SLA** | ❌ | ❌ | ❌ | ✅ |

---

## 6. 🥊 Pembeda Utama Tier Dasar (Starter) vs POS Pasar

Calon pelanggan sering bertanya: *"Kenapa harus pilih Paket Starter Nexora dibanding POS murah lain?"*

```text
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│                             NEXORA STARTER VS KOMPETITOR PASAR                                   │
├────────────────────────────────┬────────────────────────────────┬────────────────────────────────┤
│         FITUR DASAR            │      POS KOMPETITOR LAIN        │      NEXORA STARTER LITE       │
├────────────────────────────────┼────────────────────────────────┼────────────────────────────────┤
│ 1. Self-Ordering QR Meja       │ Add-on berbayar (Rp 150k/bln)   │ ✅ Gratis & Native Bawaan       │
│                                │ atau komisi 1%-2% per pesanan. │   (0% Komisi Tambahan)         │
│ 2. Batas Transaksi & Menu      │ Dibatasi (Maks 100 transaksi)  │ ✅ Unlimited Transaksi & Menu   │
│ 3. Paket Bundle & Voucher      │ Dikunci untuk paket Pro/Bisnis │ ✅ Lengkap di Paket Starter    │
│ 4. Denah & Manajemen Meja      │ Sering hanya untuk mode retail │ ✅ Manajemen Meja Dine-In Aktif │
│ 5. Kas Masuk/Keluar Kasir      │ Tidak bisa catat kas kecil     │ ✅ Petty Cash Laci Kasir Rapi  │
│ 6. Pajak PB1 & Service Charge  │ Perhitungan sering keliru/kaku │ ✅ Formula Standar Resmi F&B   │
│ 7. Kecepatan & UI Kasir        │ Web jadul, reload layar penuh  │ ✅ Full AJAX, Shimmer, DarkMode│
└────────────────────────────────┴────────────────────────────────┴────────────────────────────────┘
```

---

## 7. 👑 Pembeda Utama Tier Atas (Pro & Business) vs POS Pasar

Untuk level menengah ke atas, Nexora menawarkan kemampuan intelijen finansial yang tidak dimiliki kompetitor:

1. **Decoupled COGS Resep Murni (Plan B)**:
   - POS biasa memperlakukan belanja PO sebagai beban langsung (laba rugi terdistorsi).
   - Nexora mencatat PO sebagai aset persediaan, dan memotong HPP murni berdasarkan gramasi resep yang terjual di POS.
2. **Deteksi Otomatis Anomali Pemborosan Dapur (*Kitchen Leakage >5%*)**:
   - POS biasa hanya memberi grafik omzet per cabang.
   - Nexora membandingkan konsumsi bahan menu yang sama di Cabang Jakarta vs Bandung. Jika cabang tertentu memakai bahan di luar batas toleransi >5%, sistem langsung membunyikan peringatan merah.
3. **Blind Cash Balancing & Audit Selisih Kasir**:
   - Kasir wajib input uang fisik tanpa melihat angka sistem (*anti-manipulasi*).
   - Rekap overage/shortage tercatat permanen dalam audit trail, dan setoran brankas dapat dipantau langsung oleh owner.
4. **Pemisahan 3 Buku Finansial**:
   - Uang modal laci kasir tidak akan pernah tercampur menjadi omzet penjualan atau mencemari laba rugi.

---

## 8. 🛠️ Panduan Arsitektur Teknis Implementasi di Laravel

### A. Model Data & Schema Migrasi Central
Tabel `plans` pada koneksi `central` (lihat [`app/Models/SysAdmin/Plan.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Models/SysAdmin/Plan.php)) menjadi sumber kebenaran otorisasi:

```php
// app/Models/SysAdmin/Plan.php
public function hasFeature(string $featureKey): bool
{
    $features = $this->features_json ?? [];
    return !empty($features[$featureKey]);
}
```

### B. Middleware Paywall: `CheckPlanFeature.php`
Buat middleware baru `app/Http/Middleware/CheckPlanFeature.php` untuk memproteksi route group:

```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPlanFeature
{
    public function handle(Request $request, Closure $next, string $featureKey)
    {
        $currentPlan = session('client_plan'); // atau diambil dari client active subscription
        
        if (!$currentPlan || !$currentPlan->hasFeature($featureKey)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Fitur ini tidak tersedia di paket langganan Anda. Silakan upgrade ke paket yang lebih tinggi.',
                    'upgrade_url' => route('admin.setting.index', ['tab' => 'subscription'])
                ], 403);
            }

            return redirect()->route('admin.setting.index', ['tab' => 'subscription'])
                ->with('error_paywall', "Fitur '{$featureKey}' terkunci. Upgrade paket Anda untuk menikmati fitur ini.");
        }

        return $next($request);
    }
}
```

### C. Pemetaan Route Proteksi di `routes/web.php`
```php
// Proteksi Modul COGS & Resep (Tier PRO ke atas)
Route::middleware(['auth:web', 'plan.feature:cogs_recipe'])->prefix('keuangan')->group(function () {
    Route::resource('cogs-raw-material', CogsRawMaterialController::class);
    Route::resource('cogs-recipe', CogsRecipeController::class);
    Route::resource('cogs-waste', CogsWasteLogController::class);
    Route::resource('purchase-order', PurchaseOrderController::class);
});

// Proteksi Portal Owner (Tier BUSINESS ke atas)
Route::middleware(['auth:web', 'plan.feature:owner_portal_suite'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('financial', [OwnerFinancialController::class, 'index'])->name('financial');
    Route::get('benchmark', [OwnerBenchmarkController::class, 'index'])->name('benchmark');
    Route::get('audit', [OwnerAuditController::class, 'index'])->name('audit');
    Route::get('cash-debt', [OwnerCashDebtController::class, 'index'])->name('cash-debt');
});
```

### D. Strategi Uji Coba Gratis 14 Hari (*Full Access Pro Trial*)
* Setiap pendaftaran akun baru otomatis mendapatkan **Trial Paket PRO (Full Access)** selama 14 hari.
* Selama 14 hari, user merasakan betapa mudahnya resep bahan mentah terpotong otomatis dan laporan laba rugi terhitung rapi.
* Ketika masa uji coba berakhir, pengguna yang sudah memasukkan data resep akan terdorong kuat untuk berlangganan **Paket PRO atau BUSINESS** daripada turun ke paket Starter.

---

## 9. 📈 Playbook Sales & Panduan ROI Pitching Pelanggan

Saat tim sales menghadapi pertanyaan harga dari calon pelanggan:

### 💬 Skrip Menjawab Pertanyaan: *"Kenapa harga Nexora lebih mahal dari POS lain?"*

> **Sales Representative**:  
> *"Bapak/Ibu, software kasir seharga Rp 100 ribuan di luar sana hanya mencatat nota transaksi. Tapi aplikasi tersebut tidak bisa memberitahu Bapak/Ibu jika ada bahan makanan terbuang Rp 2 juta di dapur atau kasir salah hitung uang kembalian Rp 50 ribu setiap shift.*
>
> *Dengan berinvestasi di **Nexora POS Pro / Business** (seharga Rp 349.000 – Rp 899.000/bulan):*
> 1. *Anda **menghemat Rp 2.000.000 – Rp 5.000.000/bulan** karena sistem otomatis mendeteksi kebocoran gramasi porsi di dapur.*
> 2. *Anda **menghemat Rp 1.000.000/bulan** karena sistem blind-drop kami mengunci selisih uang fisik kasir.*
> 3. *Anda **menghemat Rp 3.000.000/bulan** biaya jasa akuntan karena laporan Laba Rugi Akrual dan Arus Kas Riil sudah tersaji otomatis tiap malam.*
>
> *Total uang bisnis yang diselamatkan Nexora mencapai **Rp 6.000.000+ per bulan**. Dengan biaya langganan di bawah Rp 900 ribu, Nexora bukan biaya pengeluaran, melainkan investasi yang memberikan keuntungan berkali-kali lipat."*

---

> 📝 **Catatan**: Dokumen ini telah diselaraskan dengan seluruh migration, model, controller, dan view yang ada di branch `deva-branch`.
