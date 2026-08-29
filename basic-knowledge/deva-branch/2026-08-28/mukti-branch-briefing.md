# 📘 Handover & Architectural Briefing: Universal Enterprise F&B Suite (Plan B)

> **Dokumen**: `mukti-branch-briefing.md`  
> **Ditujukan Untuk**: Tim Developer & Reviewer `mukti-branch`  
> **Dari**: Tim Arsitektur & Keuangan `deva-branch`  
> **Tanggal**: 2026-08-28 (28 Agustus 2026)  
> **Status**: MASTER ARCHITECTURE SPECIFICATION (READY FOR CROSS-BRANCH ALIGNMENT)  
> **Tujuan**: Memberikan panduan menyeluruh mengenai cetak biru **Plan B** yang memadukan Laba Rugi Resep Murni, Dedicated Cash Flow, Laci Kasir Pintar, dan Refactor Bahan Mentah untuk skala cafe kecil hingga restoran besar.

---

## 🧭 1. Executive Summary (Latar Belakang & Filosofi)

Sebelumnya, terdapat beberapa ketidaksesuaian akuntansi dan batasan operasional di sistem lama:
1. **P&L vs Cash Flow Tercampur:** Kerugian bahan basi (*Waste Log*) sebelumnya memotong laba rugi di bawah Gross Profit dan keliru memotong arus kas keluar tunai.
2. **Belanja PO Supplier:** Sering disalahartikan sebagai pengurang laba rugi, padahal barang PO yang masuk masih berupa **Aset Persediaan Gudang**.
3. **Laci Kasir Belum Terlacak Detil:** Modal awal laci kasir, suntikan modal owner (misal Rp 200.000), dan pengeluaran kas kecil darurat (beli es batu Rp 20.000, gas) belum memiliki tabel buku kas laci per rupiah (*drawer log*).
4. **Nomenklatur Kaku:** Tabel bahan mentah bernama `cogs_raw_materials` terkesan hanya milik COGS, padahal bahan mentah didapat dari **PO Supplier & Gudang**.

**Solusi Plan B:**  
Membangun **5 Pilar Finansial & Operasional Terpadu** yang memisahkan 100% antara **Kinerja Profitabilitas Usaha (P&L)** vs **Likuiditas Kas Riil (Cash Flow)**, dilengkapi pencatatan laci kasir anti-bocor dan penamaan bahan mentah yang bersih.

---

## 🏛️ 2. Lima Pilar Arsitektur Plan B

```
                                    ARSITEKTUR UNIVERSAL PLAN B
                                                 │
     ┌───────────────────┬───────────────────────┼───────────────────────┬───────────────────┐
     │                   │                       │                       │                   │
     ▼                   ▼                       ▼                       ▼                   ▼
 [ PILAR 1 ]         [ PILAR 2 ]             [ PILAR 3 ]             [ PILAR 4 ]         [ PILAR 5 ]
Smart Cash Drawer   PO Pembelian:           Laba Rugi Resep         Dedicated Arus      Refactor Nama:
& Shift Handover    Tunai vs Tempo AP       Murni (P&L Engine)      Kas & Drill-Down    raw_stock_materials
```

---

### 💵 PILAR 1: Smart Cash Drawer & Shift Handover Kasir
Mengakomodasi operasional kasir harian, pergantian shift (estafet kas), dan pencatatan kas kecil:
1. **Clock-In Cerdas:**  
   Kasir membuka shift dengan input **Modal Awal Laci (*Starting Cash Float*)** yang bebas/fleksibel. Sistem otomatis memberikan info cerdas berapa sisa uang fisik di laci dari shift penutupan kemarin.
2. **Buku Kas Laci Kasir (`cash_drawer_logs`):**  
   Mencatat setiap rupiah kas masuk (*Paid-In: Suntikan modal owner*) dan kas keluar (*Paid-Out: Beli es batu, galon, gas*).
3. **Clock-Out Cerdas (*Blind Count & Handover*):**  
   Kasir menghitung fisik uang di laci tanpa melihat angka tebakan sistem. Kasir membagi uang fisik menjadi dua:
   - **Disetor ke Brankas / Owner (*Cash Deposit to Safe*)**
   - **Disisakan di Laci untuk Shift Depan (*Retained Cash Float*)**
4. **Struk Z-Report Thermal 80mm:**  
   Mencetak rekapitulasi modal awal, penjualan cash vs non-cash, kas kecil, uang setoran, dan selisih kasir (*Over/Short*).

---

### 🥩 PILAR 2: Fleksibilitas Pembelian PO Supplier (Tunai vs Tempo Hutang AP)
Mengakomodasi cafe kecil yang beli bahan langsung bayar lunas, DAN resto besar yang beli tempo 14–30 hari:
* Pada tabel `purchase_orders`, ditambahkan: `payment_status` (`unpaid`, `partial`, `paid`), `payment_date`, dan `payment_method`.
* **Beli Tunai:** Status langsung `PAID` &rarr; Arus Kas Keluar langsung tercatat hari itu di Cash Flow.
* **Beli Tempo (AP):** Status `UNPAID` &rarr; Kas toko **belum keluar**. Saat tim Finance melunasi faktur supplier di kemudian hari, status diubah `PAID` &rarr; barulah memotong Arus Kas.
* **Hukum Laba Rugi:** Keduanya **HARAM MEMOTONG LABA RUGI**, karena barang yang masuk masih berstatus Aset Persediaan Gudang.

---

### 📊 PILAR 3: Laporan HPP & Laba Rugi Resep Murni (P&L Engine)
Berdiri di route [`/admin/keuangan/hpp-report`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/keuangan/hpp-report/index.blade.php):
1. **Net Revenue (Omzet Murni):**  
   $$\text{Net Sales} = \text{Total Subtotal Transaksi Selesai} - \text{Diskon}$$
   *(Pajak PB1 10% dan Service Charge 5% dipisahkan karena merupakan titipan pemda & pool karyawan).*
2. **Laba Kotor (Gross Profit):**  
   $$\text{Gross Profit} = \text{Net Sales} - \text{Estimasi Modal COGS Resep Terjual}$$
3. **Laba Bersih Toko (Net Profit):**  
   $$\text{Net Profit} = \text{Gross Profit} - \text{Gaji Karyawan (Labor)} - \text{Biaya Listrik, Sewa, WiFi (OPEX)}$$
4. **Dua Radar Box Pengawasan:**
   - 🚨 **Radar Kerugian Dapur (Waste Log Alert):** Menampilkan rincian bahan basi/tumpah bulan itu beserta nominal kerugiannya, **tanpa memotong rumus laba**.
   - 📦 **Radar Belanja Gudang (Memo PO):** Menampilkan total bahan mentah baru yang masuk dari supplier bulan itu sebagai status aset persediaan.

---

### 🌊 PILAR 4: Dedicated Laporan Arus Kas (Cash Flow) & "Click to Trace"
Berdiri mandiri di route [`/admin/reports/cashflow`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/keuangan/reports/cashflow.blade.php):
1. **Arus Kas Masuk (Inflow):**  
   Penjualan POS Tunai + Penjualan POS Digital (QRIS/EDC) + Modal Awal Laci + Suntikan Modal Owner (*Paid-In*).
2. **Arus Kas Keluar (Outflow):**  
   Pembayaran PO Supplier yang berstatus `PAID` + Gaji Karyawan Dibayar + Biaya Listrik/OPEX Dibayar + Kas Kecil Laci (*Paid-Out*).
3. **Posisi Kas Bersih (Net Cash Flow):**  
   $$\text{Net Cash Flow} = (\text{Total Inflow} - \text{Total Outflow}) + \text{Selisih Kasir Tutup Shift (Over/Short)}$$
4. **Fitur "Click to Trace" (Audit Trail Popup):**  
   Setiap angka di tabel Cash Flow dan P&L dapat diklik untuk membuka modal popup bukti rincian transaksi (daftar PO, struk kas kecil, audit shift).

---

### 📦 PILAR 5: Refactor & Renaming `raw_stock_materials`
* Mengubah nama tabel:
  - `cogs_raw_materials` &rarr; **`raw_stock_materials`** (PK: `raw_stock_material_id`)
  - `cogs_raw_material_histories` &rarr; **`raw_stock_material_histories`**
* Menyelaraskan seluruh Foreign Key relasi di `purchase_order_items`, `purchase_receiving_items`, `cogs_recipe_items`, dan `cogs_waste_logs`.
* Nama model menjadi: `RawStockMaterial.php` & `RawStockMaterialHistory.php`.

---

## 🗄️ 3. Perubahan Skema Database (5 Migration Baru)

Semua migration bersifat **non-destructive / additive** sehingga tidak merusak data lama:

1. `2026_08_27_000001_create_cash_drawer_logs_table.php`  
   Membuat tabel `cash_drawer_logs` (`id`, `outlet_id`, `daily_closing_id`, `cashier_id`, `type` [in/out], `category`, `amount`, `reason`, `timestamps`).
2. `2026_08_27_000002_add_handover_fields_to_daily_closings_table.php`  
   Menambahkan `retained_cash_float` (`decimal(15,2)` default 0) dan `cash_deposit_to_safe` (`decimal(15,2)` default 0).
3. `2026_08_27_000003_add_payment_status_to_purchase_orders_table.php`  
   Menambahkan `payment_status` (`enum: unpaid, partial, paid` default 'paid'), `payment_date`, dan `payment_method`.
4. `2026_08_27_000004_add_margins_to_hpp_financial_reports_table.php`  
   Menambahkan kolom `gross_margin_percent` dan `net_margin_percent`.
5. `2026_08_27_000005_rename_cogs_raw_materials_to_raw_stock_materials_table.php`  
   Me-rename tabel `cogs_raw_materials` & `cogs_raw_material_histories` serta foreign key terkait.

---

## 🔄 4. Hubungan dengan Tabel Transaksi (`transactions`)

Tabel `transactions` **TIDAK PERLU DIUBAH SKEMANYA**, karena sudah memiliki kolom lengkap:
* `daily_closing_id`: Mengikat transaksi ke sesi shift kasir aktif (menghitung uang kas fisik laci vs uang non-tunai bank).
* `transaction_subtotal`: Menjadi basis omzet murni (*Net Sales*) di Laporan Laba Rugi.
* `transaction_items`: Menghitung konsumsi modal resep terjual (*COGS*).
* `payment_id`: Menentukan aliran kas masuk tunai (*Cash Inflow*) vs digital (*Bank Inflow*).

---

## 🚀 5. Roadmap 5 Fase Eksekusi

```
+-----------------------------------------------------------------------------------------------+
| FASE EKSEKUSI PLAN B                                                                          |
+-----------------------------------------------------------------------------------------------+
| Fase 1: Database Migrations (5 Migration Baru)                                                |
| Fase 2: Eloquent Models & Logic Refactor (RawStockMaterial, CashDrawerLog, DailyClosing, PO)  |
| Fase 3: Smart Cash Drawer & Shift Kasir (Clock-In, [+ Kas Masuk], [- Kas Keluar], Z-Report)   |
| Fase 4: Laba Rugi Murni (P&L) & Dedicated Cash Flow + Popup "Click to Trace"                  |
| Fase 5: Seeder Enriched Data & Multi-Tenant Verification (KopiSenja, GeprekGambos Seeder)    |
+-----------------------------------------------------------------------------------------------+
```

---

## 🤝 Catatan Khusus untuk Tim `mukti-branch`
* Seluruh pengerjaan fitur ini dilakukan di branch `deva-branch`.
* Setiap pembuatan/perubahan file langsung dicatat di [`basic-knowledge/log_code.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/log_code.md) dan [`basic-knowledge/deva-branch/2026-08-28/todo.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-08-28/todo.md).
* Tampilan UI sepenuhnya mengadopsi standar modern: TailwindCSS, `[data-theme="light"]` / `[data-theme="dark"]`, `NexoraToast()`, `input-skeleton`, dan `btn-loading`.

---

> 📌 *Dokumen ini disahkan sebagai acuan teknis resmi bersama antara deva-branch dan mukti-branch.*
