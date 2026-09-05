# 🎯 MASTER MILESTONE V1: ARUS KAS (CASH FLOW), PEMBAYARAN TUNAI & TATA KELOLA LACI KASIR (CASHIER GOVERNANCE)

> **Dokumen**: `milestone.md` (Milestone V1)  
> **Tanggal Rilis**: 2026-09-04 (04 September 2026)  
> **Branch**: `deva-branch`  
> **Status**: 🌟 **Official Baseline Architecture & Master Milestone V1 (100% Implemented & Verified)**  
> **Target Pengguna**: Kasir POS, Supervisor Outlet, Manajer Operasional & Owner Multi-Cabang  
> **Dokumen Terkait**:
> - Arsitektur JSON Alur Kas & Shift: [`cash_flow_and_clock_in_out_architecture.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/cash_flow_and_clock_in_out_architecture.md)
> - Cetak Biru Arsitektur Plan B: [`2026-08-27/plan-b.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-08-27/plan-b.md)
> - Panduan Visual Sistem: [`resources/views/admin/keuangan/guide/index.blade.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/keuangan/guide/index.blade.php)

---

## 🗺️ 1. Peta Jalan 5 Pilar Eksekusi Milestone V1

```text
+-------------------------------------------------------------------------------------------------------------------+
| MASTER MILESTONE V1: CASH FLOW, CASH PAYMENT, & CASHIER GOVERNANCE SUITE                                          |
+-------------------------------------------------------------------------------------------------------------------+
| 📌 PILAR 1: Mesin Pembayaran Tunai & Live Kalkulator Kembalian (POS Cash Checkout)                                 |
|    ├── 1.1 Layar Pembayaran Kasir (`/admin/order/{id}/payment` via `OrderController@payment`)                     |
|    ├── 1.2 Live Calculator Kembalian (JavaScript realtime, deteksi Uang Pas / Kembalian Ada / Kurang)             |
|    ├── 1.3 Tombol Cepat Pecahan (Quick Cash: Uang Pas, Rp 20k, Rp 50k, Rp 100k, Rp 200k)                          |
|    ├── 1.4 Proteksi Anti-Tekor Kasir (Tombol Konfirmasi terkunci jika uang diterima < total tagihan)               |
|    ├── 1.5 Cetak Struk Struk Kasir Thermal 80mm (Mencetak Total, Bayar Cash, dan Kembali Rp X)                    |
|    └── 1.6 Audit Snapshot Pembayaran (Tabel `payments`: `payment_amount`, `payment_grand_total`, `payment_remark`)  |
|                                                                                                                   |
| 📌 PILAR 2: Smart Cash Drawer & Buku Kas Laci (Petty Cash & Float Top-Up)                                         |
|    ├── 2.1 Skema Database `cash_drawer_logs` (Tipe `in`/`out`, Kategori, Alasan, Kasir, Outlet)                   |
|    ├── 2.2 Tombol Quick Action `[+ Kas Masuk (Top-up)]` (Suntikan modal pecahan 2rb & 5rb / Top-up Owner)         |
|    ├── 2.3 Tombol Quick Action `[- Kas Keluar (Petty Cash)]` (Pengeluaran darurat: es batu, gas LPG, galon, dll)  |
|    └── 2.4 Live Mutasi Buku Kas Laci Kasir (Audit trail jam, nominal, kategori & alasan per shift berjalan)       |
|                                                                                                                   |
| 📌 PILAR 3: Siklus Shift Kasir, Pengaturan Shift, Handover & Brankas (Cashier Shift Lifecycle)       |
|    ├── 3.1 Sinkronisasi Pengaturan Shift (`shift_mode`: Terjadwal/Auto Master, Manual, Single Daily) |
|    ├── 3.2 Kalkulasi Otomatis Tanggal Bisnis Berdasarkan Jam Cut-Off Resto (`daily_cutoff_time`)    |
|    ├── 3.3 Proteksi Anti-Shift Ganda (Validasi ketat status `open`, shift baru terkunci jika blm close) |
|    ├── 3.4 Buka Shift (Clock-In): Pilihan Master Shift otomatis vs Manual & Modal Awal Kas Laci    |
|    ├── 3.5 4 Kartu Stat Kas Realtime: Modal Awal, Penjualan Tunai, Kas Laci In/Out, Ekspektasi Laci |
|    ├── 3.6 Tutup Shift (Clock-Out): Input uang fisik hasil hitungan laci (`actual_cash_counted`)    |
|    ├── 3.7 Handover Uang Kembalian (`retained_cash_float` ditinggal di laci untuk modal shift depan)|
|    ├── 3.8 Setoran Fisik ke Brankas (`cash_deposit_to_safe` = Fisik Laci - Modal Ditinggalkan)     |
|    ├── 3.9 Rekonsiliasi Selisih Kasir (`cash_difference`: Pas / Overage Lebih / Shortage Tekor)     |
|    └── 3.10 Cetak Struk Z-Report 80mm (`z-report.blade.php`) & Auto-Handover ke Shift Berikutnya   |
|                                                                                                                   |
| 📌 PILAR 4: Dedicated Laporan Arus Kas Outlet (Plan B Financial Reporting)                                        |
|    ├── 4.1 Controller `CashFlowReportController.php` & View `resources/views/admin/keuangan/reports/cashflow.blade.php` |
|    ├── 4.2 Pemisahan Tegas Laba Rugi Akrual vs Arus Kas Riil (Net Cash Flow = Total Inflow Riil - Total Outflow Riil)|
|    ├── 4.3 Eliminasi Cacat Lama: Pembersihan 100% Waste Log & Theoretical COGS dari Kas Keluar                    |
|    ├── 4.4 Pengakuan Belanja PO Berbasis Kas Lunas (`payment_status = 'paid'`)                                     |
|    ├── 4.5 Radar Box Komitmen Hutang PO Supplier Tempo (Belum memotong kas selama masih `unpaid`)                 |
|    └── 4.6 Ekspor Laporan Arus Kas ke CSV UTF-8 BOM                                                               |
|                                                                                                                   |
| 📌 PILAR 5: Konsolidasi Arus Kas Multi-Cabang & Monitoring Eksekutif Owner                                        |
|    ├── 5.1 Agregasi Cash Flow Multi-Cabang di `ConsolidatedFinancialService.php`                                  |
|    ├── 5.2 Tampilan Side-by-Side P&L vs Cash Flow di Portal Owner (`/admin/owner/financial`)                      |
|    ├── 5.3 Live Safe Deposit Monitor (Pusat Setoran Brankas Malam Ini Semua Cabang di `/admin/owner/cash-debt`)   |
|    └── 5.4 Kalender Urgensi Tagihan Supplier Tempo Lintas Outlet                                                  |
+-------------------------------------------------------------------------------------------------------------------+
```

---

## 📋 2. Rincian Teknis & Arsitektur Fitur V1

### 💵 PILAR 1: Mesin Pembayaran Tunai & Live Kalkulator Kembalian
* **Route**: `admin.order.payment` (`/admin/order/{id}/payment`) & `admin.order.processPayment` (POST)
* **Controller**: [`App\Http\Controllers\Admin\OrderController.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Http/Controllers/Admin/OrderController.php)
* **View**: [`resources/views/admin/order/payment.blade.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/order/payment.blade.php)
* **Struk View**: [`resources/views/admin/order/receipt.blade.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/order/receipt.blade.php)

#### Fitur & Mekanisme:
1. **Live Kembalian Display (Realtime JS)**:
   - Menghitung `Kembalian = Uang Diterima - Grand Total`.
   - Menampilkan angka kembalian format Rupiah tebal dan jelas.
   - Badge visual interaktif:
     - `[Uang Pas]` &rarr; Hijau (selisih Rp 0).
     - `[Kembalian Ada]` &rarr; Hijau terang + nominal kembalian.
     - `[Kurang]` &rarr; Merah terang (`-Rp X (Kurang)`) + tombol submit dikunci (*disabled*).
2. **Pilihan Cepat Pecahan Uang (Quick Cash Presets)**:
   - Tombol 1-klik untuk pecahan lazim di kasir: `Uang Pas`, `Rp 20.000`, `Rp 50.000`, `Rp 100.000`, `Rp 200.000`.
3. **Format Struk Thermal 80mm**:
   - Mencetak baris `BAYAR (CASH): Rp X` dan `KEMBALI: Rp Y` di bawah Grand Total.
4. **Penyimpanan Snapshot Pembayaran**:
   - Disimpan di tabel `payments` dengan `payment_metode = 'cash'`, `payment_amount`, `payment_grand_total`, dan `payment_remark = "Tunai: Rp X (Kembalian: Rp Y)"`.

---

### 🗄️ PILAR 2: Smart Cash Drawer & Buku Kas Laci (Petty Cash & Float)
* **Route**: `admin.keuangan.shift-operational.cashIn` & `admin.keuangan.shift-operational.cashOut`
* **Controller**: [`App\Http\Controllers\Admin\Keuangan\ShiftOperationalController.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Http/Controllers/Admin/Keuangan/ShiftOperationalController.php)
* **Model**: [`App\Models\Admin\CashDrawerLog.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Models/Admin/CashDrawerLog.php)
* **Tabel Database**: `cash_drawer_logs`

#### Struktur Database:
```sql
CREATE TABLE cash_drawer_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    outlet_id VARCHAR(255) NULL,
    daily_closing_id BIGINT UNSIGNED NULL,
    cashier_id BIGINT UNSIGNED NULL,
    type ENUM('in', 'out') NOT NULL,
    category VARCHAR(50) DEFAULT 'general',
    amount DECIMAL(15,2) NOT NULL,
    reason VARCHAR(255) NOT NULL,
    created_by VARCHAR(50) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### Alur Operasional:
1. **Kas Masuk Laci (`type = 'in'`)**:
   - Digunakan saat kasir kehabisan uang pecahan kembalian (*small denominations*) atau ada suntikan modal tambahan dari owner.
   - Kategori: `Top-up Owner / Tambah Pecahan Kembalian`.
   - Mengupdate saldo ekspektasi kas laci secara otomatis.
2. **Kas Keluar Laci (`type = 'out'`)**:
   - Digunakan untuk pengeluaran mendadak/kas kecil (*Petty Cash Paid-Out*): beli es batu kristal, gas LPG cadangan, galon air, atau uang parkir supplier.
   - Menghindarkan kasir dari vonis "uang tekor" saat closing shift.

---

### 🔄 PILAR 3: Siklus Shift Kasir, Pengaturan Shift, Handover & Setoran Brankas
* **Route Operasional**: `admin.keuangan.shift-operational.*` (`/admin/keuangan/shift-operational`)
* **Route Pengaturan Shift**: `admin.setting-shift.*` & tab `Setting > Shift & Cut-off` (`/admin/setting#shift`)
* **Controllers**:
  - [`App\Http\Controllers\Admin\Keuangan\ShiftOperationalController.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Http/Controllers/Admin/Keuangan/ShiftOperationalController.php)
  - [`App\Http\Controllers\Admin\Keuangan\ShiftSettingController.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Http/Controllers/Admin/Keuangan/ShiftSettingController.php)
* **Views**:
  - Operasional Kasir: [`resources/views/admin/keuangan/shift-operational/index.blade.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/keuangan/shift-operational/index.blade.php)
  - Pengaturan Shift: [`resources/views/admin/setting/index.blade.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/setting/index.blade.php)
* **Models Terikat**:
  - [`App\Models\Admin\DailyClosing.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Models/Admin/DailyClosing.php)
  - [`App\Models\Admin\ShiftSetting.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Models/Admin/ShiftSetting.php)
  - [`App\Models\Admin\Shift.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Models/Admin/Shift.php)

#### A. Keterhubungan Dinamis dengan Pengaturan Shift (`ShiftSetting`):
1. **Adaptasi Mode Shift (`shift_mode`) saat Clock-In**:
   - **Mode Terjadwal (`auto_master`)**: Form Clock-In otomatis menampilkan dropdown pilihan Master Shift resmi. Memilih shift akan otomatis mengisi jam kerja, nomor shift, dan modal awal kas laci (`default_starting_cash`) tanpa kasir perlu mengetik manual.
   - **Mode Manual / Dinamis (`manual`)**: Dropdown master disembunyikan. Kasir bebas mengetik nama shift (misal *"Shift Tambahan"* atau *"Shift Malam"*) dan modal awal laci secara fleksibel.
   - **Mode Single Daily Shift (`single_daily`)**: Kasir terkunci hanya membuka 1 shift penuh per hari (cocok untuk cafe/outlet kecil yang buka pagi dan tutup malam).
2. **Penentuan Tanggal Bisnis Otomatis Berdasarkan Jam Cut-Off (`daily_cutoff_time`)**:
   - Sistem membaca jam cut-off operasional outlet (default: `03:00` subuh).
   - Method `calculateBusinessDate()` memastikan bahwa transaksi atau shift yang dibuka lewat tengah malam (misal jam `01:30` AM) tetap diakui sebagai **Tanggal Bisnis Kemarin**, sehingga omzet malam hari tidak terpecah.
3. **Proteksi Anti-Shift Ganda (`status = 'open'`)**:
   - Sistem memblokir pembukaan shift baru jika masih ada sesi shift yang berstatus `open` di outlet tersebut. Kasir wajib melakukan Clock-Out terlebih dahulu sebelum shift berikutnya dapat dibuka.

#### B. Formula Baku Rekonsiliasi Kasir:
$$\text{Expected Cash} = \text{Starting Cash} + \text{Cash Sales} + \text{Cash In} - \text{Cash Out}$$
$$\text{Cash Difference (Variance)} = \text{Actual Cash Counted} - \text{Expected Cash}$$
$$\text{Cash Deposit to Safe} = \max(0, \text{Actual Cash Counted} - \text{Retained Cash Float})$$

#### C. Komponen UI & Alur Operasional:
1. **4 Stat Cards Dashboard Shift**:
   - Card 1: *Modal Awal Laci (Starting Cash Float)*
   - Card 2: *Penjualan Tunai Kasir (Cash Sales)*
   - Card 3: *Kas Laci In/Out (+Drawer In / -Drawer Out)*
   - Card 4: *Ekspektasi Uang di Laci (Expected Cash)*
2. **Form Tutup Shift Terpadu**:
   - Input `actual_cash_counted`: Total uang kertas dan koin di laci kasir.
   - Input `retained_cash_float`: Uang modal yang ditinggalkan di laci untuk shift berikutnya.
   - Dynamic Display `displayDepositSafe`: Jumlah uang yang dimasukkan ke amplop setoran brankas.
   - Dynamic Display `varianceContainer`: Status selisih kas (*Kas PAS*, *Lebih*, atau *Tekor*).
3. **Z-Report Printing**:
   - Struk thermal 80mm merangkum seluruh penjualan, mutasi laci kas, selisih, dan nominal setoran brankas.
4. **Auto-Handover Shift**:
   - Kasir shift selanjutnya otomatis membaca `retained_cash_float` shift sebelumnya sebagai `starting_cash` shift baru.

---

### 📑 PILAR 4: Dedicated Laporan Arus Kas Outlet (Plan B)
* **Route**: `admin.reports.cashflow` (`/admin/reports/cashflow`)
* **Controller**: [`App\Http\Controllers\Admin\Keuangan\CashFlowReportController.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Http/Controllers/Admin/Keuangan/CashFlowReportController.php)
* **View**: [`resources/views/admin/keuangan/reports/cashflow.blade.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/keuangan/reports/cashflow.blade.php)

#### Formula Matematis Arus Kas:
```text
[ARUS KAS MASUK (INFLOW)]
= Penjualan Kasir Tunai (Cash Sales)
+ Penjualan Kasir Non-Tunai (QRIS / EDC / Transfer Bank)
+ Kas Masuk Tambahan Laci (Owner Top-Up / Petty In)

[ARUS KAS KELUAR (OUTFLOW)]
= Pembayaran PO Bahan Mentah Lunas (PO Paid / Disbursement)
+ Pengeluaran Kas Kecil Laci (Petty Cash Out)
+ Biaya Gaji Karyawan Dibayar (Labor Cost)
+ Biaya Operasional Listrik / Sewa Dibayar (Overhead/OPEX)

[NET CASH FLOW]
= TOTAL INFLOW - TOTAL OUTFLOW
```

#### Aturan Anti-Distorsi:
- **Waste Log**: 100% dikeluarkan dari Cash Flow (bukan kas keluar).
- **Theoretical COGS**: 100% dikeluarkan dari Cash Flow (hanya ada di Laba Rugi).
- **PO Tempo (Unpaid)**: Belum memotong kas keluar; ditampilkan pada card radar tersendiri.

---

### 👑 PILAR 5: Konsolidasi Arus Kas Multi-Cabang & Portal Owner
* **Routes**:
  - `admin.owner.financial` (`/admin/owner/financial`)
  - `admin.owner.cash-debt` (`/admin/owner/cash-debt`)
* **Service**: [`App\Services\ConsolidatedFinancialService.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Services/ConsolidatedFinancialService.php)
* **Controllers**:
  - [`App\Http\Controllers\Admin\Owner\OwnerFinancialController.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Http/Controllers/Admin/Owner/OwnerFinancialController.php)
  - [`App\Http\Controllers\Admin\Owner\OwnerCashDebtController.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/app/Http/Controllers/Admin/Owner/OwnerCashDebtController.php)

#### Fitur Eksekutif:
1. **Side-by-Side P&L vs Cash Flow**:
   - Kolom Kiri: Laba Rugi Akrual (Omzet − COGS Resep − Biaya OPEX − Waste).
   - Kolom Kanan: Arus Kas Riil (Total Uang Masuk − Total Pengeluaran Kas Riil).
2. **Pusat Setoran Brankas (*Safe Deposit Tracker*)**:
   - Memantau total uang fisik setoran brankas malam ini dari seluruh kasir cabang secara real-time.
3. **Kalender Urgensi Hutang PO Supplier Tempo**:
   - Radar tagihan supplier tempo yang akan jatuh tempo dalam 7 hari ke depan di semua cabang.
4. **Ekspor CSV UTF-8 BOM**:
   - Data laporan dapat diunduh langsung untuk keperluan audit holding/akuntan.

---

## 📊 3. Matriks Alokasi Transaksi Finansial V1

| Jenis Transaksi | Laba Rugi (P&L) | Arus Kas (Cash Flow) | Buku Laci Kasir | Audit Trail / Tabel |
|---|:---:|:---:|:---:|---|
| **Penjualan Tunai Kasir** | YA (Omzet) | YA (Inflow) | YA (Cash Sales) | `transactions`, `payments` |
| **Penjualan QRIS / EDC** | YA (Omzet) | YA (Inflow) | TIDAK (Non-Laci) | `transactions`, `payments` |
| **Modal Awal Laci Kasir** | TIDAK | YA (Opening Float) | YA (Starting Cash) | `daily_closings` |
| **Top-up Uang Kembalian** | TIDAK | YA (Drawer Inflow) | YA (Cash In) | `cash_drawer_logs` |
| **Petty Cash (Beli Es/Gas)**| YA (Overhead OPEX) | YA (Outflow) | YA (Cash Out) | `cash_drawer_logs` |
| **Belanja PO Status LUNAS** | TIDAK (Aset Gudang)| YA (Outflow) | TIDAK | `purchase_orders` |
| **Belanja PO Status TEMPO** | TIDAK (Aset Gudang)| TIDAK (Hutang AP) | TIDAK | `purchase_orders` |
| **Kerugian Bahan Waste** | YA (Pengurang Laba)| TIDAK (Non-Kas) | TIDAK | `cogs_waste_logs` |
| **Modal Ditinggal di Laci** | TIDAK | YA (Retained Float)| YA (Next Float) | `daily_closings` |
| **Setoran Fisik Brankas** | TIDAK | YA (Safe Transfer) | YA (Cash Drop) | `daily_closings` |

---

## 🧪 4. Protokol Pengujian & Status Verifikasi (QA Gate V1)

| No | Modul / Skenario Uji | Target Verifikasi | Hasil Uji |
|:--:|---|---|:--:|
| 1 | **Checkout Cash Uang Pas** | Kembalian Rp 0, badge Uang Pas hijau, struk thermal tercetak pas. | ✅ **PASS (100%)** |
| 2 | **Checkout Cash Lebih** | Kembalian terhitung presisi, badge Kembalian Ada, struk cetak kembali Rp X. | ✅ **PASS (100%)** |
| 3 | **Checkout Cash Kurang** | Alert merah `Kurang`, tombol submit terkunci (anti-human error kasir). | ✅ **PASS (100%)** |
| 4 | **Top-Up Pecahan Kembalian** | Modal laci bertambah, mutasi tercatat di `cash_drawer_logs`. | ✅ **PASS (100%)** |
| 5 | **Petty Cash Keluar (Es Batu)** | Ekspektasi kas laci berkurang, mutasi tercatat lengkap di buku kas. | ✅ **PASS (100%)** |
| 6 | **Tutup Shift & Handover** | Selisih variance presisi, modal ditinggal tersimpan, setoran brankas terhitung. | ✅ **PASS (100%)** |
| 7 | **Z-Report Printing** | Struk rekap 80mm mencetak data shift, kasir, tunai, non-tunai, dan selisih. | ✅ **PASS (100%)** |
| 8 | **Laporan Cash Flow Outlet** | Inflow vs Outflow akurat, Waste & COGS tidak memotong kas, PO tempo jadi alert. | ✅ **PASS (100%)** |
| 9 | **Side-by-Side Owner Portal** | P&L akrual vs Cash Flow riil tampil berdampingan, ekspor CSV BOM sukses. | ✅ **PASS (100%)** |

---

## 🔮 5. Ide Penyempurnaan Berikutnya (Next Step / V1.1 Roadmap)

Untuk pengembangan lanjutan di masa depan:
1. **Widget Mini-Saldo Laci di Topbar Kasir**:
   - Menampilkan pill kecil di navbar kasir (misal `💵 Laci: Rp 1.450.000`) agar kasir bisa mengintip saldo laci langsung dari halaman POS tanpa harus membuka halaman Clock-In.
2. **Mode Blind Count Murni (Strict Cash Count)**:
   - Mengosongkan nilai default input `actual_cash_counted` pada form tutup shift (placeholder `0`), mewajibkan kasir menghitung lembaran uang fisik secara manual tanpa bisa melihat angka tebakan sistem terlebih dahulu.
