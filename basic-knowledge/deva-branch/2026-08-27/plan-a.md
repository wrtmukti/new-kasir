# Dokumen Rencana & Arsitektur Finansial: Refaktor Kalkulasi Standar USAR (Plan A)

> **Dokumen**: `plan-a.md`  
> **Tanggal**: 2026-08-27 (27 Agustus 2026)  
> **Branch**: `deva-branch`  
> **Status**: DRAFT FOR DISCUSSION & REVIEW  
> **Target Standar**: **USAR (*Uniform System of Accounts for Restaurants*)**

---

## 1. Executive Summary & Latar Belakang

Sistem POS & ERP F&B saat ini telah memiliki modul COGS Resep, Pembelian Bahan Mentah (PO & Receiving), Kerugian Bahan Mentah (*Waste Log*), Penggajian (*Labor Cost*), dan Operasional (*Overhead/OPEX*). Namun, dalam laporan keuangan dan dashboard laba rugi, terdapat penyimpangan logika matematika dari standar baku akuntansi restoran (**USAR - Uniform System of Accounts for Restaurants**).

Dokumen ini menyajikan audit titik masalah (*flaws*), rancangan perbaikan skema database, logika backend Laravel, serta penyesuaian UI Blade dengan estetika modern tanpa ketergantungan library luar (Bootstrap 5).

---

## 2. Audit Temuan Masalah (*Current Math Flaws*)

### A. Cacat Peletakan Waste Log & Laba Kotor (*Gross Profit*)
* **Lokasi File**: [`app/Http/Controllers/Admin/Keuangan/HppReportController.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Http/Controllers/Admin/Keuangan/HppReportController.php#L199-L201) & [`L280-L282`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Http/Controllers/Admin/Keuangan/HppReportController.php#L280-L282)
* **Kondisi Kode Lama**:
  ```php
  // BARIS 199 - 200
  $grossProfit = $totalRevenue - $totalCogsEstimated;
  $netProfit = $grossProfit - $totalWasteCost - $totalLaborCost - $totalOverheadCost;
  ```
* **Masalah Akuntansi**:
  1. *Waste Log* (kerugian bahan mentah kadaluarsa/tumpah/rusak) diletakkan **setelah** *Gross Profit* sebagai pengurang laba bersih bersama biaya operasional.
  2. Menurut standar USAR, *Waste* adalah bagian tak terpisahkan dari pemakaian bahan baku riil (**Cost of Sales / Actual COGS**). Laba kotor yang dihitung tanpa memasukkan *waste* akan terlihat lebih tinggi (*overstated*) secara semu.

---

### B. Ketiadaan Metrik Kunci Industri F&B: *Prime Cost*
* **Kondisi Saat Ini**:
  - Sistem belum menghitung atau menampilkan **Prime Cost** (`Actual COGS + Labor Cost`) dan rasio persentasenya terhadap omzet.
* **Pentingnya Metrik**:
  - *Prime Cost* adalah metrik kesehatan finansial nomor 1 bagi pemilik bisnis kuliner (restoran/cafe).
  - Standar acuan industri F&B: **Prime Cost ideal berada di bawah 60% – 65% dari total omzet penjualan**.

---

### C. Kerancuan Waste Log pada Laporan Arus Kas (*Cash Flow*)
* **Lokasi File**: [`app/Http/Controllers/Admin/Keuangan/CashFlowReportController.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Http/Controllers/Admin/Keuangan/CashFlowReportController.php#L31) & [`L72`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Http/Controllers/Admin/Keuangan/CashFlowReportController.php#L72)
* **Kondisi Kode Lama**:
  ```php
  // BARIS 31
  $netCashFlow = $totalCashIn - ($totalPoOut + $totalWasteOut);
  ```
* **Masalah Akuntansi**:
  1. *Waste Log* adalah kerugian nilai persediaan non-kas (*inventory write-off/shrinkage*), **bukan uang tunai yang keluar dari laci kas saat itu**.
  2. Memasukkan *Waste* sebagai pengurang *Cash Flow* menyebabkan pengurangan ganda (*double counting*), karena uang tunai riil telah dikeluarkan ketika melakukan pembelian bahan mentah (PO belanja).

---

### D. Perlakuan Pembelian PO (*Purchase Orders / Receiving*)
* **Status Akuntansi**:
  - Pembelian bahan baku melalui PO merupakan **Aset Persediaan (Neraca / Balance Sheet)**, bukan beban laba rugi langsung (*P&L direct deduction*).
  - Beban baru diakui (*recognized as expense*) ketika bahan baku tersebut **terpakai untuk porsi menu terjual (COGS)** atau **terbuang karena rusak (Waste)**.

---

## 3. Spesifikasi Rumus Standar USAR (*Target Logic*)

Berikut struktur kalkulasi standar yang akan diimplementasikan ke seluruh sistem:

```
[1] TOTAL OMZET KASIR (REVENUE)
    = Total Penjualan Sukses (Gross Sales tanpa PB1 & Service Charge pool)

[2] HARGA POKOK PENJUALAN / COST OF SALES (ACTUAL COGS)
    = Estimasi Modal COGS Porsi Terjual + Kerugian Bahan Mentah (Waste Log)

[3] LABA KOTOR (GROSS PROFIT)
    = Total Omzet Kasir - Total Cost of Sales (Actual COGS)
    * Gross Margin (%) = (Gross Profit / Total Omzet Kasir) * 100%

[4] BIAYA TENAGA KERJA (LABOR COST)
    = Total Gaji Pokok, Upah Harian, & Komisi Karyawan

[5] PRIME COST (TOTAL BIAYA UTAMA F&B)
    = Total Cost of Sales (Actual COGS) + Biaya Tenaga Kerja (Labor Cost)
    * Prime Cost Ratio (%) = (Prime Cost / Total Omzet Kasir) * 100%
    * Benchmark Sehat: < 60% (Optimal), 60-65% (Perlu Perhatian), > 65% (Bahaya/Kritis)

[6] BIAYA OPERASIONAL / OVERHEAD (OPEX)
    = Listrik PLN, Sewa Tempat, Air PDAM, Internet WiFi, Pemeliharaan, dll

[7] LABA BERSIH OPERASIONAL (NET PROFIT)
    = Gross Profit - Labor Cost - Overhead (OPEX)
    = Omzet Kasir - Prime Cost - Overhead (OPEX)
    * Net Margin (%) = (Net Profit / Total Omzet Kasir) * 100%
```

---

## 4. Rencana Perubahan Skema Database (*Database Migrations*)

### Migration Penambahan Kolom pada Tabel `hpp_financial_reports`
File migration baru akan dibuat pada folder `database/migrations/client/`:
`2026_08_27_000001_add_usar_metrics_to_hpp_financial_reports_table.php`

```php
Schema::table('hpp_financial_reports', function (Blueprint $table) {
    $table->decimal('total_actual_cogs', 15, 2)->default(0.00)->after('total_waste_cost')
        ->comment('Total Cost of Sales = total_cogs_estimated + total_waste_cost');
    $table->decimal('prime_cost', 15, 2)->default(0.00)->after('total_labor_cost')
        ->comment('Prime Cost = total_actual_cogs + total_labor_cost');
    $table->decimal('prime_cost_percent', 5, 2)->default(0.00)->after('prime_cost');
    $table->decimal('gross_margin_percent', 5, 2)->default(0.00)->after('gross_profit');
    $table->decimal('net_margin_percent', 5, 2)->default(0.00)->after('net_profit_estimated');
});
```

### Update Model Eloquent:
File: [`app/Models/Admin/Keuangan/HppFinancialReport.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Models/Admin/Keuangan/HppFinancialReport.php)
* Menambahkan kolom-kolom baru ke array `$fillable`.
* Menambahkan casts tipe data `decimal:2` atau `float` untuk ketepatan format JSON.

---

## 5. Rencana Perubahan Logika Backend (*Controllers*)

### A. [`HppReportController.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Http/Controllers/Admin/Keuangan/HppReportController.php)
1. **Perhitungan Matriks Baru pada Method `index()`**:
   ```php
   // 1. Total Cost of Sales (Actual COGS)
   $totalActualCogs = $totalCogsEstimated + $totalWasteCost;

   // 2. Gross Profit sesuai USAR
   $grossProfit = $totalRevenue - $totalActualCogs;

   // 3. Prime Cost
   $primeCost = $totalActualCogs + $totalLaborCost;

   // 4. Net Profit
   $netProfit = $grossProfit - $totalLaborCost - $totalOverheadCost;

   // 5. Margin & Rasio Persentase
   $grossMarginPercent = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;
   $primeCostPercent   = $totalRevenue > 0 ? ($primeCost / $totalRevenue) * 100 : 0;
   $netMarginPercent   = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
   ```
2. **Penyelarasan pada Method `storeOperational()`**:
   - Menerapkan formula USAR yang sama persis saat admin menginput biaya gaji & listrik.
3. **Auto Update / Sync DB**:
   - Memastikan `updateOrCreate` pada tabel `hpp_financial_reports` menyimpan `total_actual_cogs`, `prime_cost`, `prime_cost_percent`, `gross_margin_percent`, dan `net_margin_percent`.

---

### B. [`CashFlowReportController.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Http/Controllers/Admin/Keuangan/CashFlowReportController.php)
1. **Koreksi Arus Kas Bersih**:
   - Menghapus `$totalWasteOut` dari pengurangan arus kas.
   - Rumus baru:
     ```php
     $netCashFlow = $totalCashIn - $totalPoOut;
     ```
2. **Koreksi Ekspor CSV**:
   - Mengubah baris keterangan ekspor agar tidak mencantumkan waste log sebagai arus kas keluar tunai.

---

### C. [`ReportDashboardController.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Http/Controllers/Admin/Keuangan/ReportDashboardController.php)
- Menyesuaikan ringkasan metrik agar mencerminkan Gross Sales, Net Sales, Cost of Sales, dan Prime Cost.

---

## 6. Rencana Penyesuaian UI/UX Dashboard & View Blade

*Semua komponen menggunakan class custom token proyek (`var(--bg-surface)`, `var(--text-primary)`, dll), kompatibel penuh dengan mode Light/Dark, dan tidak menggunakan Bootstrap 5.*

### A. Tampilan 4 Bento KPI Cards Baru di Header ([`hpp-report/index.blade.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/keuangan/hpp-report/index.blade.php))

```
+---------------------------+---------------------------+---------------------------+---------------------------+
| CARD 1: OMZET PENJUALAN   | CARD 2: COST OF SALES     | CARD 3: PRIME COST        | CARD 4: LABA BERSIH       |
| Rp 45.000.000             | Rp 18.250.000             | Rp 26.250.000 (58.3%)     | Rp 12.750.000 (28.3%)     |
| [Bruto POS]               | [COGS: 17.5jt | W: 750rb] | [Status: SEHAT (<60%)]    | [Net Profit Bersih]       |
+---------------------------+---------------------------+---------------------------+---------------------------+
```

### B. Tampilan Waterfall Rincian Laba Rugi Bulanan

```
+---------------------------------------------------------------------------------------+
| RINCIAN KALKULASI LABA RUGI BULANAN (USAR RESTAURANT STANDARD)                        |
+---------------------------------------------------------------------------------------+
| Total Omzet Penjualan (Kasir)                                        Rp 45.000.000,00 |
|                                                                                       |
| [HPP / Cost of Sales]                                                                 |
|   (-) Estimasi COGS Menu Terjual                                   - Rp 17.500.000,00 |
|   (-) Kerugian Bahan Terbuang (Waste Log)                            - Rp  750.000,00 |
|   (=) TOTAL COST OF SALES (ACTUAL COGS)                              Rp 18.250.000,00 |
|                                                                                       |
| (=) LABA KOTOR (GROSS PROFIT) [Margin: 59.4%]                        Rp 26.750.000,00 |
|                                                                                       |
| [Biaya Tenaga Kerja]                                                                  |
|   (-) Biaya Gaji Karyawan (Labor Cost)                              - Rp  8.000.000,00 |
|                                                                                       |
| ⭐ PRIME COST (Cost of Sales + Labor Cost)                           Rp 26.250.000,00 |
|    Rasio Prime Cost terhadap Omzet: 58.3% [Status: SEHAT]                             |
|                                                                                       |
| [Biaya Operasional / OPEX]                                                            |
|   (-) Biaya Listrik, Sewa, WiFi, & Overhead                         - Rp  6.000.000,00 |
|                                                                                       |
| (=) ESTIMASI LABA BERSIH TOKO (NET PROFIT) [Margin: 28.3%]           Rp 12.750.000,00 |
+---------------------------------------------------------------------------------------+
```

---

## 7. Rencana Eksekusi & Langkah Selanjutnya

| Langkah | Item Pengerjaan | Target File Terkait |
| :--- | :--- | :--- |
| **Tahap 1** | Buat Migration Penambahan Kolom Metrik USAR | `database/migrations/client/2026_08_27_000001_add_usar_metrics_to_hpp_financial_reports_table.php` |
| **Tahap 2** | Jalankan Migration & Update Eloquent Model | `app/Models/Admin/Keuangan/HppFinancialReport.php` |
| **Tahap 3** | Update Controller Logika Keuangan | `HppReportController.php`, `CashFlowReportController.php`, `ReportDashboardController.php` |
| **Tahap 4** | Update View Blade UI (Cards, Waterfall, Formula) | `resources/views/admin/keuangan/hpp-report/index.blade.php`, `reports/dashboard.blade.php`, `reports/cashflow.blade.php` |
| **Tahap 5** | Verifikasi & Pencatatan Log Kerja | `basic-knowledge/log_code.md` & `basic-knowledge/deva-branch/todo.md` |

---

> 📝 *Dokumen ini siap dibahas lebih detail oleh User sebelum eksekusi dimulai.*
