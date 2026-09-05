# 🎯 MASTER MILESTONE V2: FRONTLINE POS SHIFT HUD, ERGONOMIC DRAWER OPERATIONS, MID-SHIFT X-REPORT & ENTERPRISE BLIND CLOSING

> **Dokumen**: `milestone_v2.md` (Milestone V2)  
> **Tanggal Rilis**: 2026-09-04 (04 September 2026)  
> **Branch**: `deva-branch`  
> **Status**: 🚀 **Approved Architectural Blueprint & Master Milestone V2**  
> **Basis Pondasi**: Memperluas pondasi [Milestone V1 (`milestone.md`)](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-09-04/milestone.md)  
> **Target Pengguna**: Kasir Garis Depan POS, Head Bar/Lead Cashier, Store Manager, & Auditor Finansial  
> **Dokumen Terkait**:
> - Milestone V1 (Pondasi Finansial & Database): [`milestone.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/2026-09-04/milestone.md)
> - Arsitektur Alur Kas & Shift Closing (JSON): [`cash_flow_and_clock_in_out_architecture.md`](file:///c:/xampp812/htdocs/newpost/new-kasir/basic-knowledge/deva-branch/cash_flow_and_clock_in_out_architecture.md)
> - Komponen UI Standar Nexora: [`resources/views/docs/ui-components.blade.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/docs/ui-components.blade.php)

---

## 🗺️ 1. Peta Jalan 5 Pilar Eksekusi Milestone V2

```text
+-------------------------------------------------------------------------------------------------------------------+
| MASTER MILESTONE V2: FRONTLINE POS SHIFT HUD & ENTERPRISE GOVERNANCE SUITE                                        |
+-------------------------------------------------------------------------------------------------------------------+
| 📌 PILAR 1: Persistent Navbar Shift & Cash Drawer HUD (Real-time Status Indicator)                                 |
|    ├── 1.1 Widget Topbar Dinamis: `[ 🟢 Shift #1 Pagi | 💵 Laci: Rp 1.450.000 ]` atau `[ ⚠️ Buka Shift ]`       |
|    ├── 1.2 Global Keyboard Shortcut (Hotkey `F4` atau `Shift + D`) untuk memanggil Drawer HUD                     |
|    ├── 1.3 Sinkronisasi Real-time AJAX/Event Listener setiap transaksi tunai selesai / drawer mutasi            |
|    └── 1.4 Auto-Theme Adaptation (`[data-theme="light"]` & `[data-theme="dark"]`) sesuai standar Nexora         |
|                                                                                                                   |
| 📌 PILAR 2: Seamless Slide-Over Offcanvas Drawer Utility (Zero Cart Disruption)                                   |
|    ├── 2.1 Panel Geser Kanan (Offcanvas Component pattern dari `docs/ui-components.blade.php`)                   |
|    ├── 2.2 Transaksi Anti-Reset (Kasir tetap melihat keranjang antrean POS tanpa perlu berpindah rute/halaman)    |
|    ├── 2.3 4 Live Stat Cards Laci: Modal Awal, Penjualan Kasir (Tunai vs QRIS), Mutasi Laci, Sisa Kas Laci     |
|    ├── 2.4 Tombol Cepat Modal: `[+ Top-Up Modal (Cash-In)]` (Suntikan uang pecahan kembalian dari supervisor)     |
|    ├── 2.5 Tombol Cepat Biaya: `[- Catat Petty Cash (Cash-Out)]` (Beli es batu/gas langsung potong laci kasir)   |
|    ├── 2.6 Log Ringkas Mutasi: Menampilkan 5 mutasi kas laci kasir terakhir secara langsung di dalam panel        |
|    └── 2.7 Umpan Balik Responsif: Loading skeleton min 400ms (`btn-loading`, `input-skeleton`) & `NexoraToast()`  |
|                                                                                                                   |
| 📌 PILAR 3: Mid-Shift Interim X-Report (Audit Kas & Handover Tanpa Tutup Shift)                                   |
|    ├── 3.1 Tombol `[🖨️ Cetak X-Report]` pada Slide-Over Drawer HUD                                                |
|    ├── 3.2 Modal Quick Preview Ringkasan Operasional Berjalan (Penjualan, Tunai, Non-Tunai, Mutasi Kas)          |
|    ├── 3.3 Format Cetak Struk Thermal 80mm Khusus X-Report (`x-report.blade.php`)                                 |
|    └── 3.4 Non-Destructive Audit: Mencetak tanpa menutup shift aktif, tanpa reset counter nomor invoice kasir   |
|                                                                                                                   |
| 📌 PILAR 4: Enterprise Blind Drop Closing (Anti-Skimming Cashier Governance)                                      |
|    ├── 4.1 Form Tutup Shift dengan Perlindungan "Blind Count" (Ekspektasi sistem disembunyikan di awal)          |
|    ├── 4.2 Kalkulator Denominasi Pecahan Fisik (Lembar 100k, 50k, 20k, 10k, 5k, 2k, dan Koin)                   |
|    ├── 4.3 Rekonsiliasi Terkunci Backend: Hitung fisik `actual_cash_counted` dikirim dahulu via POST             |
|    ├── 4.4 Evaluasi Variance Realtime Pasca-Submit: Deteksi otomatis Pas, Selisih Lebih, atau Tekor             |
|    ├── 4.5 Setoran Fisik Brankas (`cash_deposit_to_safe`) & Modal Ditinggalkan (`retained_cash_float`)           |
|    └── 4.6 Cetak Z-Report Final 80mm & Auto-Flagging ke Audit Owner jika terjadi selisih melebihi batas toleransi|
|                                                                                                                   |
| 📌 PILAR 5: Kompatibilitas Multi-Layer & Integritas Cash Flow Tanpa Distorsi                                       |
|    ├── 5.1 Kompatibel 100% Pengaturan Shift (`shift_mode`: auto_master, manual, single_daily)                   |
|    ├── 5.2 Kompatibel 100% Jam Cut-Off Operasional Resto (`daily_cutoff_time`: 03:00 Subuh)                      |
|    ├── 5.3 Kompatibel 100% Arus Kas Plan B: Top-up Inflow tidak dihitung omzet P&L, Petty Cash Outflow = OPEX   |
|    └── 5.4 Konsolidasi Multi-Cabang: Setoran brankas langsung masuk ke radar eksekutif owner di `/admin/owner`   |
+-------------------------------------------------------------------------------------------------------------------+
```

---

## 📋 2. Rincian Teknis & Arsitektur Fitur V2

### 🧭 PILAR 1: Persistent Navbar Shift & Cash Drawer HUD
* **Target File**:
  - Layout Utama Admin: [`resources/views/admin/layouts/app.blade.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/layouts/app.blade.php)
  - Topbar Partial: [`resources/views/admin/layouts/partials/_header.blade.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/layouts/partials/_header.blade.php)
* **Status Widget**:
  1. **Jika Shift Aktif (`DailyClosing::where('status', 'open')->first()`)**:
     - Menampilkan pill hijau elegan:
       ```html
       <button type="button" class="btn btn-sm btn-outline-success d-flex align-items-center gap-2 rounded-pill px-3" data-bs-toggle="offcanvas" data-bs-target="#cashierDrawerHud">
           <span class="spinner-grow spinner-grow-sm text-success" role="status" style="width: 8px; height: 8px;"></span>
           <span class="fw-semibold">Shift #1 Pagi</span>
           <span class="text-muted">|</span>
           <span class="fw-bold text-success">💵 Rp 1.450.000</span>
       </button>
       ```
  2. **Jika Shift Belum Dibuka (`status != 'open'`)**:
     - Menampilkan pill kuning/oranye peringatan:
       ```html
       <button type="button" class="btn btn-sm btn-warning d-flex align-items-center gap-2 rounded-pill px-3" data-bs-toggle="offcanvas" data-bs-target="#cashierDrawerHud">
           <i class="bi bi-exclamation-circle-fill"></i>
           <span class="fw-bold">Buka Shift Kasir</span>
       </button>
       ```
* **Hotkey Listener**:
  - Script JavaScript global mendengarkan `keydown`: saat tombol `F4` atau `Shift + D` ditekan, memicu offcanvas `#cashierDrawerHud` terbuka otomatis.

---

### 🗄️ PILAR 2: Seamless Slide-Over Offcanvas Drawer Utility
* **Target File**:
  - Blade Partial: `resources/views/admin/layouts/partials/_shift_hud_offcanvas.blade.php`
  - Controller AJAX Endpoint: `ShiftOperationalController@getLiveDrawerStatus` & `ShiftOperationalController@quickDrawerMutation`
* **Keuntungan Operasional Terbesar**:
  - **Zero Cart Disruption**: Kasir yang sedang melayani antrean di POS tidak perlu berpindah halaman saat uang kembalian habis atau perlu mencatat pembelian es batu darurat.
* **Komponen di Dalam Offcanvas**:
  1. **Header Panel**: Nama Kasir bertugas, badge shift, jam clock-in, dan tombol reload data realtime.
  2. **4 Live Metric Cards**:
     - *Modal Awal (Starting Float)*
     - *Penjualan Kasir (Tunai vs QRIS)*
     - *Mutasi Laci (+Top-up / -Petty Cash)*
     - *Total Kas di Laci Saat Ini (Live Drawer Balance)*
  3. **Aksi Cepat (Quick Drawer Form via AJAX)**:
     - Form input nominal, kategori, dan keterangan singkat.
     - Tombol submit dengan spinner minimum 400ms (`btn-loading`).
     - Notifikasi sukses menggunakan `NexoraToast()`.
  4. **Riwayat 5 Mutasi Laci Terakhir**:
     - Audit trail instan yang menampilkan jam, jenis (`in`/`out`), jumlah uang, dan alasan pengeluaran/pemasukan.

---

### 🖨️ PILAR 3: Mid-Shift Interim X-Report
* **Tujuan**:
  - Memberikan kemampuan kasir dan supervisor untuk memverifikasi kondisi kas dan omzet sementara di tengah jam operasional tanpa merusak atau menutup shift yang sedang berjalan.
* **Target View**:
  - Template Struk Thermal 80mm: `resources/views/admin/keuangan/shift-operational/x-report.blade.php`
* **Karakteristik X-Report vs Z-Report**:
  - **X-Report (Interim)**: Boleh dicetak berkali-kali, tidak mereset transaksi, tidak mengubah status shift, digunakan untuk serah terima istirahat makan siang kasir (*lunch turnover*) atau audit inspeksi manager.
  - **Z-Report (Final)**: Hanya dicetak 1 kali saat kasir resmi Clock-Out / Tutup Shift, mencatat setoran brankas final dan mengunci buku kas shift.

---

### 🔒 PILAR 4: Enterprise Blind Drop Closing
* **Masalah pada V1**:
  - Di V1, form tutup shift menampilkan angka "Perkiraan Kas Sistem" di awal form, sehingga kasir berpotensi mencocokkan fisik uang dengan angka sistem atau mengambil selisih lebih (*skimming*).
* **Standar Enterprise V2 (Blind Count)**:
  1. Kolom "Uang Fisik Kasir" (`actual_cash_counted`) diinisialisasi kosong (placeholder `0`).
  2. Kasir wajib menghitung uang fisik nyata di laci menggunakan **Kalkulator Denominasi Lembaran & Koin**:
     - 100.000 × ...
     - 50.000 × ...
     - 20.000 × ...
     - 10.000 × ...
     - 5.000 × ...
     - 2.000 × ...
     - Koin × ...
  3. Kasir menekan tombol `[Verifikasi & Tutup Shift]`.
  4. Backend mencocokkan hitungan kasir dengan catatan transaksi sistem.
  5. Barulah struk Z-Report dicetak dan selisih (*variance*) ditampilkan secara resmi.
  6. Jika selisih melebihi batas wajar (misal: tekor > Rp 20.000), sistem otomatis memberikan alert merah pada audit owner [`resources/views/admin/owner/audit.blade.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/resources/views/admin/owner/audit.blade.php).

---

### 🔗 PILAR 5: Kompatibilitas Multi-Layer
1. **Pengaturan Shift (`ShiftSetting`)**:
   - Menghormati pengaturan `shift_mode` (`auto_master`, `manual`, `single_daily`).
   - Menghormati jam pergantian tanggal bisnis `daily_cutoff_time` (default 03:00 AM subuh).
2. **Arus Kas Plan B (`CashFlowReportController`)**:
   - Kas masuk laci (`in`) diakui sebagai *Drawer Cash Inflow*, bukan omzet penjualan (tidak mencemari P&L).
   - Kas keluar laci (`out`) diakui sebagai *Petty Cash Outflow* operasional.
   - Waste log bahan rusak 100% diisolasi dari laporan kas keluar.

---

## ⚖️ 3. Tabel Matriks Perbandingan Mendalam: Milestone V1 vs Milestone V2

| Dimensi Arsitektur | 🥉 Milestone V1 (Baseline Foundation) | 🥇 Milestone V2 (Enterprise Frontline HUD) |
| :--- | :--- | :--- |
| **Lokasi Akses Kasir** | Halaman Web Terpisah (`/admin/keuangan/shift-operational`). | **Persistent di Navbar** (Tampil di semua halaman POS via Slide-Over Panel). |
| **Pengalaman Transaksi Kasir** | ❌ **Terganggu**: Kasir harus pindah rute, keranjang antrean POS berisiko hilang. | ✅ **Zero Disruption**: Laci dibuka via Offcanvas samping kanan, keranjang kasir tetap utuh. |
| **Pintasan Cepat (Shortcut)** | Tidak ada shortcut keyboard. | **Hotkey Global `F4` atau `Shift + D`** untuk instan memanggil drawer utility. |
| **Mutasi Uang (Top-Up / Petty)** | Form standar reload page di modul shift operational. | **AJAX Live Quick Form** dengan loading skeleton & toast notification (`NexoraToast`). |
| **Laporan Sementara (Mid-Shift)** | ❌ **Tidak Ada**: Hanya bisa cetak struk setelah shift ditutup. | ✅ **Ada (X-Report 80mm)**: Cetak audit kas berjalan kapan saja tanpa tutup shift. |
| **Metode Tutup Shift (Closing)** | **Open Display**: Kasir bisa melihat angka ekspektasi sistem sebelum menghitung uang fisik. | **Strict Blind Count**: Ekspektasi sistem disembunyikan sampai uang fisik selesai dihitung. |
| **Kalkulator Uang Fisik** | Kasir menghitung manual dan mengetik total akhir. | **Kalkulator Denominasi Lembar & Koin** (100k, 50k, 20k, dll.) terintegrasi di modal. |
| **Visibilitas Status Shift** | Kasir tidak tahu shift aktif kecuali membuka menu shift. | **Live Pill di Topbar** dengan warna dinamis (Hijau Aktif / Oranye Tutup). |
| **Keamanan Finansial (Anti-Fraud)**| Standar reguler (Audit trail tersimpan di DB). | **Tingkat Tinggi (Enterprise Anti-Skimming & Auto-Flagging ke Owner Portal)**. |
| **Kompabilitas Database** | Membangun tabel `cash_drawer_logs` & `daily_closings`. | **100% Kompatibel**: Memakai struktur tabel yang sama tanpa merusak data V1. |

---

## 🎯 4. Keuntungan Nyata Milestone V2 bagi Operasional Bisnis

1. **Bagi Kasir Toko**:
   - Tidak cemas antrean kasir buyar saat uang kembalian habis di jam sibuk.
   - Mudah melihat sisa modal laci secara sekilas di pojok layar.
   - Perhitungan uang fisik tutup shift lebih mudah dengan bantuan kalkulator pecahan.
2. **Bagi Supervisor & Store Manager**:
   - Bisa melakukan inspeksi kas tengah hari (*spot check*) dengan mencetak X-Report tanpa perlu menyuruh kasir tutup shift.
   - Top-up uang pecahan kembalian dari brankas tercatat rapi dalam 5 detik.
3. **Bagi Owner Multi-Cabang**:
   - Data setoran brankas dan arus kas di portal owner dijamin bebas dari kecurangan atau manipulasi angka (*anti-skimming* berkat Blind Drop).
   - Laporan Laba Rugi (P&L) dan Laporan Arus Kas (Cash Flow) tetap murni tanpa double-counting.
