# 🎯 MASTER MILESTONE V4: SINKRONISASI SEEDER MULTI-CABANG, KATALOG MASTER TERPUSAT & OPERASIONAL HARIAN KASIR

> **Dokumen**: `milestone.md` (Milestone V4)  
> **Lokasi**: `basic-knowledge/deva-branch/2026-09-07/milestone.md`  
> **Tanggal Rilis**: 2026-09-07 (07 September 2026)  
> **Branch**: `deva-branch`  
> **Status**: 📝 **Planned & Ready for Execution (Awaiting User Go-Ahead)**  
> **Target Pengguna**: Owner Multi-Cabang, Supervisor Outlet, & Kasir Frontline  
> **Dokumen Terkait**:
> - Rencana Perbaikan Detail: [`rencana_perbaikan.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-09-07/rencana_perbaikan.md)
> - Task Tracker Utama: [`../todo.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/todo.md)
> - Briefing Arsitektur Multi-Cabang: [`../../owner_suite_multi_branch_briefing.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/owner_suite_multi_branch_briefing.md)

---

## 🗺️ 1. Peta Jalan 6 Pilar Eksekusi Milestone V4

```text
+-------------------------------------------------------------------------------------------------------------------+
| MASTER MILESTONE V4: MULTI-BRANCH SEEDER SYNCHRONIZATION & CENTRALIZED CATALOG SUITE                              |
+-------------------------------------------------------------------------------------------------------------------+
| 📌 PILAR 1: Katalog Master Terpusat Lintas Cabang (Centralized Menu Catalog)                                      |
|    ├── 1.1 Category Master: Set `outlet_id => null` pada seluruh kategori menu (Ayam Spesial, Minuman, dsb.)     |
|    ├── 1.2 Product Master: Set `outlet_id => null` pada seluruh 10 produk agar otomatis aktif di 4 cabang        |
|    └── 1.3 Slug & SKU Integrity: Menjamin kode produk (GG-001 s/d GG-010) unik dan tidak bentrok                 |
|                                                                                                                   |
| 📌 PILAR 2: Relasi Pivot Bahan Baku Produk (Product-Stock Mapping)                                                |
|    ├── 2.1 Attach Pivot `product_stock`: Menghubungkan setiap produk ke stok fisik porsi masing-masing cabang    |
|    ├── 2.2 Tampilan Master Produk: Memastikan kolom "Bahan Baku" di `/admin/kasir/product` tidak lagi strip (-)  |
|    └── 2.3 Auto-Decrement Stock: Memastikan pemotongan stok otomatis saat kasir checkout berjalan normal         |
|                                                                                                                   |
| 📌 PILAR 3: Sesi Buka/Tutup Kasir Multi-Cabang (Cashier Drawer Governance)                                        |
|    ├── 3.1 Sesi Kemarin (Closed): Dibuat untuk 4 cabang (Jakarta, Bogor, Jogja, Surabaya) lengkap Z-Report       |
|    ├── 3.2 Sesi Hari Ini (Open): Dibuat aktif untuk 4 cabang dengan modal laci Rp 250.000                        |
|    ├── 3.3 Penugasan Kasir Riil: Rina (JKT), Ahmad (BGR), Bayu (YOG), Eko (SBY) ditugaskan di sesi cabang       |
|    └── 3.4 Log Kas Laci (`cash_drawer_logs`): Catatan modal awal dan pengeluaran darurat operasional             |
|                                                                                                                   |
| 📌 PILAR 4: Relasi Transaksi ke Sesi Kasir Harian (Transaction Closing Binding)                                   |
|    ├── 4.1 Binding Transaksi Kemarin: Menautkan transaksi H-1 ke `daily_closing_id` kemarin masing-masing cabang |
|    ├── 4.2 Binding Transaksi Hari Ini: Menautkan transaksi berjalan ke `daily_closing_id` aktif masing-masing     |
|    └── 4.3 Eliminasi `daily_closing_id = null`: Tidak ada lagi transaksi non-Jakarta yang "menggantung"          |
|                                                                                                                   |
| 📌 PILAR 5: Finansial Holding Konsolidasi (COGS, Waste Log, & Laporan HPP)                                        |
|    ├── 5.1 Resep COGS & Master Bahan: Dapat diakses lintas cabang untuk penghitungan theoretical food cost       |
|    ├── 5.2 Catatan Kerugian Dapur (`CogsWasteLog`): 2-3 data simulasi bahan terbuang/basi per cabang             |
|    ├── 5.3 Laporan HPP Bulanan (`HppFinancialReport`): Disimpan untuk 4 cabang lengkap beban gaji & overhead      |
|    └── 5.4 Pusat Tagihan PO (`PurchaseOrder`): PO Supplier tempo dan tunai terdistribusi rapi                    |
|                                                                                                                   |
| 📌 PILAR 6: Promosi & Penjualan Tambahan (Bundles & Discounts)                                                    |
|    ├── 6.1 Paket Hemat Komplit: Seed minimal 1 Paket Bundle di tabel `bundles` & `bundle_items`                   |
|    └── 6.2 Diskon Promo Aktif: Seed minimal 1 Diskon di tabel `discounts` & pivot `discount_product`              |
+-------------------------------------------------------------------------------------------------------------------+
```

---

## 🎯 2. Deliverables & Kriteria Keberhasilan (Definition of Done)

| No | Komponen / Modul | Kondisi Sebelum (Broken) | Kondisi Sesudah (Target Milestone) |
|:--:|---|---|---|
| 1 | **Kasir POS Surabaya Gubeng** | `Belum ada produk. Menampilkan 0 - 0 dari 0` | 10 Menu Ayam Geprek & Minuman tampil rapi, tab kategori & tombol pesan aktif |
| 2 | **Status Laci Kasir Surabaya** | Banner peringatan kuning *"Laci Kasir Belum Dibuka"* | Status kasir aktif terbuka atas nama **Eko Prasetyo (Kasir Surabaya)** |
| 3 | **Stok Bahan Baku Produk** | Kolom Bahan Baku di master produk strip (`-`) | Menampilkan jumlah bahan baku terhubung (porsi siap jual per cabang) |
| 4 | **Buku Kas Laci Surabaya** | Tidak ada histori sesi kasir sama sekali | Histori sesi kemarin (closed) dan hari ini (open) tercatat di Z-Report |
| 5 | **Portal Owner - Leaderboard** | Status kasir cabang Bogor/Jogja/Surabaya nonaktif | Seluruh 4 cabang menampilkan status kasir aktif dan omzet riil |
| 6 | **Portal Owner - Audit Waste** | Tabel kerugian bahan dapur kosong | Tampil 2–3 catatan kerugian bahan makanan terbuang/basi per cabang |
| 7 | **Tab Bundel POS Kasir** | Tab Bundel kosong melompong | Tampil paket bundle hemat komplit yang siap dipesan |

---

## 📅 3. Tahapan Pengerjaan & Protokol Keamanan

1. **Tahap 1: Persetujuan Rencana**: User meninjau dokumen ini dan memberikan konfirmasi lanjut.
2. **Tahap 2: Pembaruan File Seeder**: Mengedit `database/seeders/client/GeprekGambosSeeder.php` dan `KopiSenjaSeeder.php` tanpa merusak tabel database yang sedang berjalan.
3. **Tahap 3: Verifikasi Sintaks PHP**: Menguji file seeder dengan linter PHP CLI (`php -l`).
4. **Tahap 4: Eksekusi Migrasi & Seed Baru**: Dijalankan oleh user (atau dengan izin user) via Artisan.
5. **Tahap 5: Manual Acceptance Testing**: User menguji alur kasir POS di browser sesuai panduan manual testing.
