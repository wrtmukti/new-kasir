# Blueprint POS SaaS F&B — Scope, Arsitektur, Pajak, Upselling & Roadmap MVP

> **Dokumen Referensi**: Blueprint Desain Sistem, Arsitektur Database, Formula Akuntansi, dan Prioritas MVP POS SaaS F&B.

---

## 1. Evaluasi & Koreksi Konsep Accounting

Sebelum masuk ke struktur modul dan database, berikut adalah penyesuaian konsep dasar akuntansi F&B:

### A. Net Sales vs. Net Profit (Istilah di Kasir)
* **Net Sales (Penjualan Bersih)** $= \text{Gross Sales} - \text{Discounts \& Promotions}$.
* **Net Profit (Laba Bersih)** $= \text{Net Sales} - \text{COGS/HPP} - \text{Operational Expenses (Gaji, Sewa, Listrik, Depresiasi)}$.
* **Aturan di Layar Kasir**: Di level POS Kasir, **dilarang menggunakan istilah "Laba Bersih"**. Gunakan istilah **"Net Sales"** atau **"Net Revenue"**. Laba bersih baru dihitung di modul Finance.

### B. Pajak Restoran (PB1 / PBJT) = Kewajiban (Liability)
* Pajak Restoran (10%) **bukanlah pendapatan (revenue)** bisnis. Kasir hanya bertindak sebagai pemungut pajak untuk disetor ke pemerintah daerah.
* Pajak disimpan dalam akun **Liability (Tax Payable)** dan tidak masuk ke perhitungan Net Sales maupun Profit.

### C. Service Charge (Biaya Layanan)
* Service charge dialokasikan untuk karyawan (**Service Charge Payable**) dan/atau pendapatan operasional (*Other Income*).
* **Urutan Pengenaan Pajak & Service Charge di F&B Indonesia**:
  1. $\text{Subtotal} = \sum (\text{Harga Item} \times \text{Qty})$
  2. $\text{Discounted Subtotal} = \text{Subtotal} - \text{Diskon}$
  3. $\text{Service Charge} = \text{Discounted Subtotal} \times \text{Service Charge \%}$
  4. $\text{Dasar Pengenaan Pajak (DPP)} = \text{Discounted Subtotal} + \text{Service Charge}$
  5. $\text{Tax (PB1)} = \text{DPP} \times \text{Tax \%}$
  6. $\text{Grand Total} = \text{DPP} + \text{Tax}$

### D. COGS vs. HPP
* **COGS (Cost of Goods Sold)**: Modal bahan baku murni per porsi resep (Biji Kopi + Susu + Cup). Dikelola oleh modul **POS & Inventory**.
* **HPP (Harga Pokok Penjualan)**: $\text{COGS} + \text{Gaji Dapur} + \text{Listrik Dapur} + \text{Waste}$. Dikelola oleh modul **Finance & Accounting (Add-on)**.

---

## 2. Modul Architecture (Decoupled Modular Architecture)

Sistem dirancang secara independen antar-layer agar tier Basic dapat berjalan cepat tanpa dibebani modul Finance yang berat:

```text
               ┌─────────────────────────────────────────┐
               │              POS CORE ENGINE            │
               │  (Cashier, Order, Payment, Multi-Tax)   │
               └────────────────────┬────────────────────┘
                                    │
     ┌──────────────────┬───────────┼───────────┬──────────────────┐
     ▼                  ▼           ▼           ▼                  ▼
┌─────────┐       ┌───────────┐ ┌───────┐ ┌───────────┐    ┌──────────────┐
│ PRODUCT │       │ INVENTORY │ │REPORT │ │ UPSELLING │    │   FINANCE    │
│ & REPE  │       │ & STOCKS  │ │ENGINE │ │ ENGINE    │    │ (ADD-ON B2B) │
└─────────┘       └───────────┘ └───────┘ └───────────┘    └──────────────┘
 (Basic/Pro)       (Pro/Prem)    (All)     (Pro/Prem)         (Premium)
```

---

## 3. Feature Matrix (Subscription Tiering)

| Fitur / Kapabilitas | BASIC (UMKM/Warung) | PRO (Cafe/Resto Growing) | PREMIUM (Multi-Outlet) | FINANCE (Add-on) |
| :--- | :---: | :---: | :---: | :---: |
| **Pencatatan Kasir & Meja** | ✅ Standard | ✅ Advanced (Split Bill/Move Table) | ✅ Multi-device Realtime | ❌ |
| **Metode Pembayaran** | ✅ Cash, QRIS, Transfer | ✅ Multi-payment / Split | ✅ Integrated Payment EDC | ❌ |
| **Tax Configuration** | ✅ Inclusive / Exclusive Global | ✅ Custom per Category | ✅ Multi-Jurisdiction | ❌ |
| **Service Charge** | ✅ Global % | ✅ Flexible | ✅ Flexible | ❌ |
| **Shift Closing Kasir** | ✅ Simple Cash Recap | ✅ Blind Closing + Petty Cash | ✅ Multi-Drawer Audit | ❌ |
| **Stok Barang Jadi** | ✅ Simple Stock (Item Count) | ✅ Advanced | ✅ Multi-Warehouse | ❌ |
| **BOM / Resep & COGS Bahan**| ❌ | ✅ (Automatic Deduct) | ✅ | ❌ |
| **Purchase Order & Supplier** | ❌ | ✅ | ✅ | ❌ |
| **Daily Upselling Campaign** | ❌ | ✅ (Metrics & Conversion) | ✅ | ❌ |
| **Laporan Penjualan (Sales)**| ✅ Daily/Product Summary | ✅ Advanced Export (Excel/PDF) | ✅ Multi-Outlet Analytics | ❌ |
| **Laba Rugi (P&L) & Overhead**| ❌ | ❌ | ❌ | ✅ |
| **Accounting / General Ledger**| ❌ | ❌ | ❌ | ✅ |

---

## 4. Alur Transaksi POS & Closing (POS Transaction Flow)

```text
[CUSTOMER ORDER]
       │
       ▼
[SUBTOTAL (Sum of Items)]
       │
       ▼
[APPLY DISCOUNT & PROMOTION] ──► (Pengurang Gross Sales)
       │
       ▼
[DISCOUNTED SUBTOTAL (Taxable Base / DPP awal)]
       │
       ▼
[CALCULATE SERVICE CHARGE] ──► (Dihitung dari Discounted Subtotal)
       │
       ▼
[CALCULATE TAX (PB1/VAT)] ──► (Dihitung dari: Discounted Subtotal + Service Charge)
       │
       ▼
[GRAND TOTAL (Total Payment)]
       │
       ▼
[PAYMENT COMPLETED] ──► Deduct Inventory Stock (Stok Bahan Mentah Terpotong)
       │
       ▼
[DAILY CLOSING KASIR] ──► Rekonsiliasi Uang Kas (Cash in Hand vs System)
       │
       ▼
[SALES REPORT] ──► Omzet Net Sales Terbentuk (Data POS Selesai)
       │
       ▼ (Async Sync / Periodical)
[FINANCE & ACCOUNTING MODULE] ──► Hitung HPP + Sewa + Gaji ──► PRODUCE NET PROFIT (P&L)
```

---

## 5. Rekomendasi Skema Database (Flexible & Snapshot Audit Proof)

> ⚠️ **Aturan Penting**: Nilai rate pajak, service charge, dan diskon wajib disimpan sebagai **snapshot** pada tabel `orders` agar data transaksi historis tidak berubah jika konfigurasi sistem diubah di masa depan.

```sql
-- 1. Outlets / Cabang
CREATE TABLE outlets (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 2. Taxes Configuration
CREATE TABLE taxes (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    outlet_id BIGINT NOT NULL,
    name VARCHAR(100) NOT NULL,
    rate_percent DECIMAL(5,2) NOT NULL DEFAULT 10.00,
    type ENUM('inclusive', 'exclusive') DEFAULT 'exclusive',
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE
);

-- 3. Service Charges Configuration
CREATE TABLE service_charges (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    outlet_id BIGINT NOT NULL,
    name VARCHAR(100) NOT NULL,
    rate_percent DECIMAL(5,2) NOT NULL DEFAULT 5.00,
    is_taxable BOOLEAN DEFAULT TRUE,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE
);

-- 4. Orders (Header Transaksi)
CREATE TABLE orders (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    outlet_id BIGINT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    cashier_id BIGINT NOT NULL,
    gross_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    net_sales_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    service_charge_percent DECIMAL(5,2) DEFAULT 0.00,
    service_charge_amount DECIMAL(15,2) DEFAULT 0.00,
    tax_percent DECIMAL(5,2) DEFAULT 0.00,
    tax_amount DECIMAL(15,2) DEFAULT 0.00,
    tax_type ENUM('inclusive', 'exclusive') DEFAULT 'exclusive',
    grand_total DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    payment_status ENUM('pending', 'paid', 'cancelled', 'refunded') DEFAULT 'paid',
    shift_id BIGINT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id)
);

-- 5. Order Items (Detail Pesanan)
CREATE TABLE order_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(15,2) NOT NULL,
    unit_cogs DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    subtotal DECIMAL(15,2) NOT NULL,
    discount_amount DECIMAL(15,2) DEFAULT 0.00,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- 6. Daily Shift Closings
CREATE TABLE daily_closings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    outlet_id BIGINT NOT NULL,
    cashier_id BIGINT NOT NULL,
    opened_at TIMESTAMP NOT NULL,
    closed_at TIMESTAMP NULL,
    starting_cash DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    expected_cash DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    actual_cash DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    cash_difference DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    total_non_cash DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    status ENUM('open', 'closed') DEFAULT 'open'
);

-- 7. Upselling Campaigns & Logs
CREATE TABLE upselling_campaigns (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    outlet_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE upselling_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    status ENUM('offered', 'accepted', 'rejected') NOT NULL,
    created_at TIMESTAMP NULL
);
```

---

## 6. Matrix Formula Matematika & Akuntansi

| Komponen | Formula Perhitungan | Tanggung Jawab Modul |
| :--- | :--- | :--- |
| **Subtotal (Gross)** | $\sum (\text{Harga Item} \times \text{Qty})$ | POS Kasir |
| **Discount** | $\sum (\text{Nilai Diskon Item}) + \text{Diskon Transaksi}$ | POS Kasir |
| **Net Sales (Net Revenue)** | $\text{Subtotal (Gross)} - \text{Discount}$ | POS Kasir |
| **Service Charge** | $\text{Net Sales} \times \text{Service Charge \%}$ | POS Kasir |
| **Base Tax (DPP)** | $\text{Net Sales} + \text{Service Charge}$ | POS Kasir |
| **Tax (Exclusive)** | $\text{Base Tax} \times \text{Tax \%}$ | POS Kasir |
| **Tax (Inclusive)** | $\text{Net Sales} - \left( \frac{\text{Net Sales}}{1 + \text{Tax \%}} \right)$ | POS Kasir |
| **Grand Total** | $\text{Net Sales} + \text{Service Charge} + \text{Tax (Exclusive)}$ | POS Kasir |
| **COGS (Modal Bahan)** | $\sum (\text{Unit COGS Resep} \times \text{Qty Terjual})$ | Inventory / POS |
| **Gross Profit** | $\text{Net Sales} - \text{COGS}$ | POS / Report |
| **Net Profit (Laba Bersih)**| $\text{Gross Profit} - (\text{Gaji} + \text{Sewa} + \text{Listrik} + \text{Waste})$ | **FINANCE ONLY (ADD-ON)** |

---

## 7. Prioritas Roadmap MVP (P0 - P3)

### 🔥 P0 — WAJIB (Must Have for Launch - Tier Basic)
1. **Katalog Produk & Kategori** (Harga, Foto, Varian).
2. **Layar Kasir (Touch Order & Quick Search)**.
3. **Multi Payment Type** (Cash, QRIS, Transfer).
4. **Configurable Tax & Service Charge** (Global Inclusive/Exclusive).
5. **Daily Shift Closing Kasir** (Saldo awal, total tunai/non-tunai, selisih kas).
6. **Laporan Penjualan Harian & Per Produk (Daily Sales & Item Sales)**.
7. **Stok Produk Sederhana** (Pengurangan stok barang jadi).

### ⚡ P1 — PENTING (High Value for Tier Pro)
1. **Resep / BOM (Bill of Materials) & Multi Raw Materials**.
2. **Potong Stok Bahan Mentah Otomatis saat Transaksi**.
3. **Feature Upselling Campaign & Metric Log Kasir** (Fitur Unik / Sales Booster!).
4. **Purchase Order (PO) & Receiving Stok dari Supplier**.
5. **Laporan Waste Log (Barang Rusak / Busuk)**.
6. **Export Excel / PDF Laporan Penjualan**.

### 🗓️ P2 — NANTI (Tier Premium)
1. **Multi-Outlet & Multi-Gudang Central Management**.
2. **Customer Loyalty, CRM, & Point System**.
3. **Advanced Table Management & QR Ordering Sync**.
4. **Role & Permission Granular** (Hak akses kasir vs supervisor).

### ⛔ P3 — JANGAN DIBUAT DULU (Avoid Bloat)
1. ❌ **Full Accounting (General Ledger / Jurnal Umum / Balance Sheet)**.
2. ❌ **Laporan Pajak Masa Resmi Akuntansi (Tax Audit Form)**.
3. ❌ **Perhitungan Depresiasi Aset Restoran**.
