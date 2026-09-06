> **Branch Context**: `deva-branch`
> **Project Scope**: POS SaaS F&B MVP (Decoupled COGS + HPP Report + Master Tax & Service Charge + Shift Closing Kasir + Dedicated Cash Flow Plan B + Multi-Outlet Owner Executive Hub).
> **Dokumen Milestone V4 (Seeder Multi-Cabang & Master Katalog)**: [`2026-09-07/milestone.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-09-07/milestone.md)
> **Dokumen Rencana Perbaikan Detail (V4)**: [`2026-09-07/rencana_perbaikan.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-09-07/rencana_perbaikan.md)
> **Dokumen Milestone V1 (Cash Flow & Kasir)**: [`2026-09-04/milestone.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-09-04/milestone.md)
> **Dokumen Milestone V2 (Frontline POS Shift HUD & Enterprise Closing)**: [`2026-09-04/milestone_v2.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-09-04/milestone_v2.md)
> **Dokumen Arsitektur Alur Kas & Shift Closing (JSON)**: [`cash_flow_and_clock_in_out_architecture.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/cash_flow_and_clock_in_out_architecture.md)
> **Dokumen Strategi Tiering SaaS & Benchmark Pasar**: [`../saas_tiering_strategy_and_market_benchmark.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/saas_tiering_strategy_and_market_benchmark.md)
> **Dokumen Milestone Phase 3 (Owner Portal)**: [`2026-08-29/milestone.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-08-29/milestone.md)
> **Dokumen Task Tracker Phase 3**: [`2026-08-29/todo.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-08-29/todo.md)
> **Dokumen Milestone Plan B**: [`2026-08-28/milestone.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-08-28/milestone.md)
> **Status**: **Phase 1, 2, 3 COMPLETED 100% | Phase 4 (Penyempurnaan Seeder Multi-Cabang) PLANNED**

---

## 📋 Checklist Task & Progress `deva-branch`:

### ✅ PHASE 1 — Decoupled COGS, HPP Report & Seeder (COMPLETED 100%)
- [x] **1.1** Verification active branch `deva-branch` & structure namespace
- [x] **1.2** Database Migrations (Tabel `cogs_raw_materials`, `cogs_recipes`, `cogs_waste_logs`, `hpp_financial_reports`)
- [x] **1.3** Decouple Purchase Order dari stocks & hubungkan ke `cogs_raw_materials`
- [x] **1.4** Modul Bahan Mentah, PO Receiving, COGS Resep, Waste Log, & Stock Opname
- [x] **1.5** Laporan HPP, Laba Rugi, & Modal Resep per Menu (`HppReportController`)
- [x] **1.6** Grafik Analitik Penjualan & Jam Sibuk (`MenuAnalyticsController`)
- [x] **1.7** Seeder 1 Bulan (26 Hari, 294 Pesanan) & Running `php artisan migrate:fresh --seed` (SUKSES 100%)

---

### 🚀 PHASE 2 — Master Pajak (PB1), Service Charge, Shift Closing, & Report Dashboard + Excel Export (COMPLETED 100%)

#### 📌 M1 — Master Pajak (PB1) & Service Charge (Tahap 1) (COMPLETED 100%)
- [x] **2.1.1** Migration tabel `taxes` & `service_charges` (master setting per company/outlet)
- [x] **2.1.2** Snapshot kolom di `orders`: `tax_percent`, `tax_amount`, `tax_type`, `service_charge_percent`, `service_charge_amount`
- [x] **2.1.3** Model `Tax` & `ServiceCharge` + Seeder `TaxSeeder.php` & `ServiceChargeSeeder.php` (PBJT 10% & Service 5%)
- [x] **2.1.4** FormRequest `TaxRequest` & `ServiceChargeRequest` dengan validasi Bahasa Indonesia
- [x] **2.1.5** Controller `App\Http\Controllers\Admin\Keuangan\TaxController` (CRUD Master Setting & AJAX Update)
- [x] **2.1.6** UI View Setting Pajak & Service Charge (`resources/views/admin/keuangan/setting-tax/index.blade.php`)
- [x] **2.1.7** Update kalkulasi Checkout/Order (Subtotal -> Diskon -> Service Charge -> Tax PB1 -> Grand Total) & Testing Matriks Success (100% Presisi)


#### 🔐 M2 — Modul Shift Closing, Cut-Off & Cash Balancing Kasir (Tahap 2) (COMPLETED 100%)
- [x] **2.2.1** Migration tabel `daily_closings` (Buka/tutup shift kasir, cash expected vs actual, variance over/short)
- [x] **2.2.2** Foreign Key Binding `daily_closing_id` (`nullable`) pada tabel `transactions` & `orders`
- [x] **2.2.3** Model `DailyClosing` + Relasi Eloquent di `Order` & `Transaction`
- [x] **2.2.4 (M2.1)** Migration `create_shift_settings_table.php` & `create_shifts_table.php` (Pengaturan Cut-Off & Master Shift)
- [x] **2.2.5 (M2.1)** Model `ShiftSetting` & `Shift` + Seeder `ShiftSeeder.php` (Cut-Off 03:00 AM & Shift 1/2)
- [x] **2.2.6 (M2.1)** Controller `App\Http\Controllers\Admin\Keuangan\ShiftSettingController.php` (CRUD Cutoff & Master Shift)
- [x] **2.2.7 (M2.1)** UI View Setting Shift & Cut-off (`resources/views/admin/keuangan/setting-shift/index.blade.php`) + Menu Sidebar `Master Shift & Cut-off`
- [x] **2.2.8** Controller `App\Http\Controllers\Admin\Keuangan\ShiftOperationalController.php` (Clock-In, Clock-Out, Z-Report, Audit trail)
- [x] **2.2.9 (M2.2)** Dedicated UI View Buka / Tutup Shift Kasir (`resources/views/admin/keuangan/shift-operational/index.blade.php`) + Menu Sidebar `Buka / Tutup Shift (Clock-In)`
- [x] **2.2.10 (M2.2)** Struk Rekap Z-Report Thermal 80mm Print View (`resources/views/admin/keuangan/shift-operational/z-report.blade.php`)
- [x] **2.2.11** Perbaikan Alur Simpan & Redirect Order: Mengarahkan redirect `store()` ke `/admin/order/list` + Memperbaiki aturan validasi ID (`product_id`, `table_id`, `customer_id`, `bundle_id`) untuk menerima integer
- [x] **2.2.12** Perbaikan Presisi Pengurutan Order & Transaksi: Mengubah pengurutan dari `latest()` (`created_at DESC`) menjadi `orderBy('order_id', 'desc')` & `orderBy('transaction_id', 'desc')` agar pesanan & transaksi terbaru SELALU berada paling atas No. 1
- [x] **2.2.13** Penambahan Pop-Up Modal Error & Banner Alert Notifikasi Transparan saat simpan order gagal
- [x] **2.2.14** Seeder `DailyClosingSeeder.php` & Update `OrderSeeder.php` & `TransactionSeeder.php` (Seed 26 hari histori shift & bind 273 order/trx)
- [x] **2.2.15** POS Cashier Clock-In Gatekeeper (Hard Lock Laci Kasir Saat Shift Tutup): Backend validation & 422 JSON reject untuk pembayaran cash tanpa shift aktif, banner proteksi interaktif di katalog POS & order create, alert proteksi di layar payment, dan client-side guard dengan NexoraToast
- [x] **2.2.16** Refactoring Terminologi F&B ("Clock-In / Clock-Out" → "Buka Kasir / Tutup Kasir"): Menyelaraskan seluruh UI kasir, sidebar, notifikasi, dan struk Z/X-Report agar menggunakan istilah Buka Kasir & Tutup Kasir
- [x] **2.2.17** Penyederhanaan Siklus Kasir (Eliminasi Pilihan Shift ➔ Murni Buka & Tutup Kasir): Menghapus dropdown dan keharusan memilih "Shift 1 / Shift 2 / Shift 3" di layar Buka Kasir, menyederhanakan form jadi satu input Modal Awal Laci dengan preset cepat, dan otomatisasi nama kasir bertugas


#### 💳 M3 — Report Dashboard Hub & 6 Detail Dedicated Laporan + Export Excel (Tahap 3) (COMPLETED 100%)
- [x] **2.3.1** `ReportDashboardController` & View Pusat Dashboard Laporan (`resources/views/admin/keuangan/reports/dashboard.blade.php`) dengan 6 Kartu Navigasi Clickable
- [x] **2.3.2** `SalesReportController` & View Detail Laporan Penjualan + Payment Breakdown + Fitur Export Excel (`.csv`) (`/admin/reports/sales`)
- [x] **2.3.3** `ProductReportController` & View Detail Laporan Menu Terlaris (PMIX & Qty Terjual) + Fitur Export Excel (`.csv`) (`/admin/reports/products`)
- [x] **2.3.4** `CashFlowReportController` & View Detail Laporan Pemasukan vs Pengeluaran (Arus Kas) + Fitur Export Excel (`.csv`) (`/admin/reports/cashflow`)
- [x] **2.3.5** `TaxServiceReportController` & View Detail Laporan Rekap Pajak PB1 & Service Charge + Fitur Export Excel (`.csv`) (`/admin/reports/tax-service`)
- [x] **2.3.6** `InventoryReportController` & View Detail Laporan Stok Bahan Mentah, PO & Waste Log + Fitur Export Excel (`.csv`) (`/admin/reports/inventory`)
- [x] **2.3.7** `ShiftClosingReportController` & View Detail Audit Shift Closing Kasir + Fitur Export Excel (`.csv`) (`/admin/reports/shifts`)
- [x] **2.3.8** Pendaftaran 13 Route Laporan pada `routes/web.php` & Verifikasi Caching Blade Template (100% Success)



---

### 🛠️ PHASE 4 — Penyempurnaan Seeder Multi-Cabang & Sinkronisasi Katalog Master (PENDING EXECUTION)

#### 📦 M4.1 — Perbaikan Seeder Klien (`GeprekGambosSeeder.php` & `KopiSenjaSeeder.php`)
- [ ] **4.1.1** **Katalog Menu Terpusat (Kategori & Produk)**: Set `outlet_id => null` pada `Category::create()` dan `Product::create()` agar berlaku otomatis untuk semua cabang (Jakarta, Bogor, Jogja, Surabaya).
- [ ] **4.1.2** **Relasi Pivot `product_stock`**: Hubungkan produk dengan stok fisik cabang via `$prod->stocks()->attach($stock->stock_id, ['quantity' => 1])` agar kolom Bahan Baku tidak kosong dan auto-decrement transaksi berjalan.
- [ ] **4.1.3** **Sesi Kasir (`DailyClosing` & `CashDrawerLog`) Multi-Cabang**: Buat loop sesi kasir (1 sesi kemarin `closed`, 1 sesi hari ini `open` dengan modal laci Rp 250.000) untuk seluruh 4 cabang, ditugaskan ke masing-masing kasir cabang.
- [ ] **4.1.4** **Binding `daily_closing_id` Transaksi Non-Jakarta**: Hubungkan transaksi & order Bogor, Jogja, dan Surabaya ke ID closing cabang masing-masing (tidak lagi `null`).
- [ ] **4.1.5** **Master Supplier & Bahan Mentah (`RawStockMaterial`)**: Jadikan bahan mentah dan PO dapat diakses atau di-seed per cabang.
- [ ] **4.1.6** **Simulasi Kerugian Dapur (`CogsWasteLog`)**: Seed 2-3 log bahan makanan rusak/basi di dapur agar tabel Audit Waste di Portal Owner terisi realistis.
- [ ] **4.1.7** **Laporan Laba Rugi Bulanan (`HppFinancialReport`)**: Buat data laporan bulanan untuk semua cabang agar performa laba rugi holding lengkap.
- [ ] **4.1.8** **Paket Bundle & Diskon Promo**: Tambahkan seeder untuk paket bundle dan diskon aktif agar tab "Bundel" di POS Kasir tidak kosong.

---

## 🧪 Panduan Manual Testing di Browser (Untuk Dijalankan User)

### 1. Uji Coba Pindah Cabang (Multi-Branch Switcher)
- [ ] Masuk ke header atas, klik dropdown cabang aktif: **Surabaya Gubeng**.
- [ ] Pastikan badge cabang berubah menjadi `Geprek Gambos - Surabaya (Surabaya Gubeng)`.

### 2. Uji Coba Kasir POS (`/admin/kasir/order`)
- [ ] Buka menu **Transaksi Toko > Kasir POS**.
- [ ] **Cek Produk**: Pastikan ke-10 produk ayam geprek muncul lengkap dengan foto placeholder/ikon, harga, dan tombol `+ Pesan`.
- [ ] **Cek Tab Kategori**: Klik tab *Semua*, *Ayam Geprek Spesial*, *Paket Hemat Lengkap*, *Side Dishes & Ekstra*, dan *Minuman Segar*. Pastikan filter kategori bekerja instan.
- [ ] **Cek Tab Bundel**: Klik tab *Bundel*, pastikan paket hemat komplit muncul.
- [ ] **Cek Status Laci**: Pastikan banner kuning peringatan *"Laci Kasir Belum Dibuka"* hilang karena kasir Surabaya sudah memiliki sesi aktif.

### 3. Uji Coba Order & Transaksi Pembayaran
- [ ] Klik `+ Pesan` pada 2-3 menu (misal: *Ayam Geprek Mozzarella* + *Es Teh Manis Jumbo*).
- [ ] Klik tombol **Keranjang** di kanan atas.
- [ ] Pilih nomor meja (misal: *Meja 1 Surabaya*).
- [ ] Klik simpan pesanan dan lanjutkan ke pembayaran.
- [ ] Pilih metode pembayaran (*Tunai / QRIS*), selesaikan pembayaran.
- [ ] Pastikan redirect berhasil dan struk transaksi tercatat rapi.

### 4. Uji Coba Verifikasi Pemotongan Stok (`/admin/kasir/stock`)
- [ ] Masuk ke menu **Master Data Cabang > Stok Bahan**.
- [ ] Pastikan jumlah porsi produk yang baru saja dibeli berkurang sesuai kuantitas transaksi.

### 5. Uji Coba Buka / Tutup Kasir (`/admin/kasir/shift`)
- [ ] Buka menu **Main Toko > Buka Kasir**.
- [ ] Cek status kasir: Kasir aktif atas nama **Eko Prasetyo (Kasir Surabaya)**.
- [ ] Cek Modal Laci Kasir: Tercatat Rp 250.000.
- [ ] Cek Buku Kas Laci Hari Ini: Pemasukan dari transaksi baru masuk ke total uang laci.

### 6. Uji Coba Portal Owner Multi-Cabang (`/admin/owner/dashboard`)
- [ ] Klik tombol **👑 Kembali ke Portal Owner** di pojok kiri atas sidebar.
- [ ] **Dashboard Konsolidasi**: Cek leaderboard 4 cabang (*Jakarta, Bogor, Jogja, Surabaya*). Pastikan omzet dan transaksi masing-masing kota tampil.
- [ ] **Laba Rugi & Arus Kas** (`/admin/owner/financial`): Cek komparasi omzet, COGS HPP resep, laba kotor, dan laba bersih 4 cabang.
- [ ] **Audit Selisih & Waste** (`/admin/owner/audit`): Cek tabel selisih kas kasir dan log bahan terbuang di dapur.
- [ ] **Pusat Setoran & Hutang PO** (`/admin/owner/cash-debt`): Cek setoran brankas kasir dan tagihan supplier.

---

## 🎯 Rencana Kerja Hari Ini (What To Do Today)
1. **Review TODO**: Periksa daftar perbaikan M4.1 di atas.
2. **Persetujuan Rencana**: Berikan instruksi apakah Anda ingin AI mulai mengupdate kode seeder `GeprekGambosSeeder.php` dan `KopiSenjaSeeder.php`.
3. **Eksekusi Seeder (Oleh User)**: Setelah seeder diperbaiki, user dapat menjalankan perintah seeder (atau biarkan AI yang menjalankan setelah izin).
4. **Verifikasi Manual**: Jalankan checklist 6 poin manual testing di atas di browser.

---

## ⚠️ Aturan Kerja Khusus Branch `deva-branch`:
1. **Branch Lock**: Jangan pernah commit atau ubah langsung di branch `main`/`master` — seluruh pengerjaan wajib di `deva-branch`.
2. **Log Code Mandatory**: Setiap selesai buat/update file, langsung catat di `basic-knowledge/log_code.md`.
3. **Konfirmasi TODO**: Sebelum mulai pengerjaan sesi, selalu cek file ini (`basic-knowledge/deva-branch/todo.md`) dan berikan kabar branch aktif ke user.

