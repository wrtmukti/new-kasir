# Blueprint & Milestone Rinci Modul Shift Closing & Daily Cut-Off (`cut_off_plan.md`)

> **Dokumen Referensi**: Desain Konfigurasi Toko, Struktur Database Shift & Cut-off, Alur Kerja Kasir (Buka/Tutup Shift), 3 Proteksi Keamanan, serta Penanganan Kasus 24 Jam & Dini Hari.

---

## 1. Tiga Kondisi Operasional Cut-Off F&B di Lapangan

Sistem dirancang untuk menangani 3 kondisi operasional bisnis F&B di dunia nyata:

1. **Restoran Normal (2 Shift: Pagi & Malam)**:
   - Shift 1 (Pagi-Sore): Kasir Arief (07.00 - 15.00).
   - Shift 2 (Sore-Malam): Kasir Budi (15.00 - 23.00).
   - Pertanggungjawaban kasir dilakukan setiap kali terjadi pergantian staf.

2. **Restoran Operasional 24 Jam Non-Stop**:
   - Toko tidak pernah tutup pintu. Kasir berganti 3x sehari (Shift 1, Shift 2, Shift 3).
   - Closing tidak bergantung pada jam buka/tutup toko, melainkan pada **Event Pergantian Kasir**.

3. **Cafe / Bar Tutup Dini Hari (Shift Melewati Tengah Malam)**:
   - Operasional cafe jam 17.00 sore s.d. jam 02.00 dini hari.
   - Jam 01.00 dini hari secara kalender sudah ganti tanggal (misal tanggal 20), tetapi secara omzet bisnis **tetap dihitung omzet tanggal 19 (`business_date`)**.

---

## 2. Struktur Database Configurable & Binding Transaksi

### A. Tabel Master Konfigurasi Toko (`company_settings`)
User/Owner dapat mengatur aturan main operasional tokonya di UI Admin:

```sql
CREATE TABLE company_settings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNIQUE NOT NULL,
    closing_mode ENUM('shift_based', 'daily_based') DEFAULT 'shift_based',
    business_start_hour TIME DEFAULT '06:00:00',
    enable_blind_closing BOOLEAN DEFAULT TRUE,
    default_starting_cash DECIMAL(15,2) DEFAULT 300000.00,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### B. Tabel Eksekusi Shift & Cut-Off (`daily_closings`)
Memuat seluruh riwayat pertanggungjawaban kasir dan audit selisih uang kas:

```sql
CREATE TABLE daily_closings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT NOT NULL,
    cashier_id BIGINT NOT NULL,              -- Kasir yang bertugas
    shift_number INT DEFAULT 1,              -- Shift 1, Shift 2, atau Shift 3
    shift_name VARCHAR(50) DEFAULT 'Shift 1',-- "Shift Pagi", "Shift Malam", "Shift Dini Hari"
    business_date DATE NOT NULL,             -- Tanggal Operasional Bisnis (misal 2026-08-19)
    opened_at TIMESTAMP NOT NULL,            -- Jam persis Buka Shift (misal 19 Aug 17:00)
    closed_at TIMESTAMP NULL,                -- Jam persis Tutup Shift (misal 20 Aug 02:15 Dini Hari)
    
    starting_cash DECIMAL(15,2) NOT NULL DEFAULT 0.00, -- Modal Awal Kasir di Laci (Float Cash)
    
    -- Hitungan Sistem Otomatis selama Shift ini:
    system_cash_sales DECIMAL(15,2) DEFAULT 0.00,     -- Omzet Tunai dari Sistem
    system_non_cash_sales DECIMAL(15,2) DEFAULT 0.00, -- Omzet QRIS/EDC/Transfer dari Sistem
    cash_in_amount DECIMAL(15,2) DEFAULT 0.00,         -- Pemasukan kas kecil tambahan
    cash_out_amount DECIMAL(15,2) DEFAULT 0.00,        -- Pengeluaran kas kecil (misal beli es batu)
    system_expected_cash DECIMAL(15,2) DEFAULT 0.00,   -- (starting + cash_sales + cash_in - cash_out)
    
    -- Hitungan Fisik Kasir saat Closing (Cut-off):
    actual_cash_counted DECIMAL(15,2) DEFAULT 0.00,   -- Uang kertas/koin fisik hasil hitung kasir
    cash_difference DECIMAL(15,2) DEFAULT 0.00,       -- Selisih: actual - expected (+ Over / - Short)
    
    notes TEXT NULL,                                   -- Catatan kasir (misal: "Selisih Rp 2.000 koin habis")
    status ENUM('open', 'closed') DEFAULT 'open',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### C. Relasi FK di `transactions` & `orders`
* **`transactions.daily_closing_id`** $\rightarrow$ Wajib ada untuk audit uang laci kasir (Menentukan `system_cash_sales` vs `system_non_cash_sales`).
* **`orders.daily_closing_id`** $\rightarrow$ Wajib ada untuk audit pesanan dapur & melacak pesanan batal (void) per shift.

---

## 3. Tiga Proteksi Keamanan Sistem (Security Guards)

1. **Proteksi Layar Kasir POS (`POS Order Guard`)**:
   - Jika belum ada Shift Aktif (`status = 'open'`), tombol Checkout di POS dikunci dan menampilkan peringatan `NexoraToast`: *"Shift Kasir belum dibuka! Silakan Buka Shift terlebih dahulu."*
2. **Proteksi Self-Order Pelanggan (`Guest QR Guard`)**:
   - Jika resto sudah Tutup Shift di akhir hari, pemesanan dari QR Meja pelanggan otomatis menampilkan banner: *"Toko telah Tutup / Sesi Kasir Berakhir. Silakan kembali besok."*
3. **Proteksi Sebelum Closing (`Unpaid Order Block`)**:
   - Kasir dilarang Tutup Shift jika masih ada pesanan berstatus *Pending / Unpaid* di meja. Sistem akan menolak closing hingga seluruh pesanan di-checkout atau di-void.

---

## 4. Milestone Rinci Pengerjaan Modul Cut-Off (Step-by-Step)

### Step 1: Database Migration & Model
* **`[NEW]` Migration `create_daily_closings_table.php`**: Membuat tabel `daily_closings`.
* **`[MODIFY]` Migration `create_orders_table.php` & `create_transactions_table.php`**: Tambahkan FK `daily_closing_id`.
* **`[NEW]` Model `App\Models\Admin\DailyClosing.php`**: Relasi ke `User`, `Order`, `Transaction`.
* **`[MODIFY]` Model `Order.php` & `Transaction.php`**: Tambahkan `$fillable` `daily_closing_id`.

### Step 2: Controller & Guard Logic
* **`[NEW]` Controller `App\Http\Controllers\Admin\Keuangan\ShiftClosingController.php`**:
  - `openShift()`: Input modal awal kasir.
  - `getCurrentShift()`: Pengecekan shift aktif.
  - `closeShift()`: Input uang fisik, hitung selisih, & kunci status `closed`.
  - `printZReport()`: Format cetak struk rekap shift kasir.
  - `index() & data()`: Audit trail histori shift kasir.
* **`[MODIFY]` Controller `OrderController.php`**:
  - Validasi shift aktif sebelum checkout & simpan `daily_closing_id` ke order & transaksi.

### Step 3: Views UI & Interface POS
* **`[NEW]` Modal Buka Shift (`resources/views/admin/pos/modal-open-shift.blade.php`)**: Form input modal awal.
* **`[NEW]` Modal Tutup Shift (`resources/views/admin/pos/modal-close-shift.blade.php`)**: Form input uang fisik laci & catatan.
* **`[NEW]` View Audit Shift (`resources/views/admin/keuangan/reports/shifts.blade.php`)**: Tabel histori shift closing kasir + export Excel.

### Step 4: Seeder & Data Refresh
* **`[NEW]` Seeder `DailyClosingSeeder.php`**: Seed histori shift 26 hari.
* **`[MODIFY]` Seeder `OrderSeeder.php`, `TransactionSeeder.php`, & `DatabaseSeeder.php`**: Bind 275 order ke shift ID.
