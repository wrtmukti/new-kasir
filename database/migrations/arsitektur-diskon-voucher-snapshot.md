# Arsitektur Diskon, Voucher & Snapshot

> **Tujuan:** Memastikan data diskon & voucher di laporan transaksi tetap akurat meskipun master data berubah (SCD Type 2 — Slowly Changing Dimension).

---

## Daftar Isi

1. [Konsep Dasar](#konsep-dasar)
2. [Arsitektur Final](#arsitektur-final)
3. [Struktur Tabel Lengkap](#struktur-tabel-lengkap)
   - [3.1 Products & Histories](#31-products--histories)
   - [3.2 Discount Transaksi](#32-discount-transaksi)
   - [3.3 Vouchers & Histories](#33-vouchers--histories)
   - [3.4 Pivot: Transaction Items](#34-pivot-transaction-items)
   - [3.5 Pivot: Transaction Voucher](#35-pivot-transaction-voucher)
   - [3.6 Transactions (Update)](#36-transactions-update)
4. [Flow Checkout](#flow-checkout)
5. [Laporan Transaksi](#laporan-transaksi)
6. [Diagram Relasi](#diagram-relasi)

---

## Konsep Dasar

### Diskon vs Voucher

| | **Diskon** | **Voucher** |
|---|---|---|
| **Level** | Produk (per item) & Transaksi | Transaksi (grand total) |
| **Bersifat** | Otomatis / langsung | Opsional (pake kode) |
| **Target** | Potong harga barang / subtotal | Potong grand total |
| **Contoh** | "Nasi Goreng disc 10rb" / "Disc owner 20rb" | Kode `DISC10` pas bayar |

### Snapshot — Kenapa Penting

Kalo diskon diubah atau voucher dihapus SETELAH transaksi terjadi, laporan lama harus tetap benar:

```
❌ Tanpa Snapshot:
  Transaksi tgl 1 Juni pake voucher DISC10 (10rb)
  10 Juni → voucher DISC10 diubah jadi 5rb
  Laporan transaksi tgl 1 Juni otomatis ikut berubah → SALAH!

✅ Dengan Snapshot:
  Transaksi tgl 1 Juni nyimpen data voucher pas dipake
  10 Juni → voucher DISC10 diubah jadi 5rb
  Laporan transaksi tgl 1 Juni tetap 10rb → BENAR!
```

### 3 Pendekatan Snapshot

| Pendekatan | Cara | Cocok buat |
|---|---|---|
| **A: Kolom langsung di transactions** | Simpen snapshot di kolom (`voucher_code`, `voucher_amount`, dll) | Simpel, MVP |
| **B: Pivot table + snapshot** | `transaction_voucher` punya kolom snapshot sendiri | Scale ✅ |
| **C: History table** | `voucher_histories` tracking setiap perubahan + FK ke pivot | Enterprise |

**Keputusan project ini: Pendekatan B (Pivot + Snapshot)**

---

## Arsitektur Final

```
                                    DISKON PRODUK (otomatis, 1 produk 1 diskon)
┌───────────────────────────────────────────────────────────────────────────┐
│  products                                       product_histories         │
│  ├── product_price                               ├── price                │
│  ├── product_discount_type (percentage/nominal)   ├── discount_type       │
│  ├── product_discount_value (10 / 10000)          ├── discount_value      │
│  └── ...                                          ├── start_date          │
│                                                   ├── end_date            │
│                                                   └── reason              │
└───────────────────────────────────────────────────────────────────────────┘
                                              ↓ snapshot setiap checkout
┌───────────────────────────────────────────────────────────────────────────┐
│  transaction_items (pivot order_product) — SNAPSHOT MANDATORY             │
│  ├── product_id  (FK)                                                     │
│  ├── product_name (snapshot)                                              │
│  ├── price (snapshot)                                                     │
│  ├── discount_type (snapshot)                                             │
│  ├── discount_value (snapshot)                                            │
│  ├── discount_amount (snapshot)  ← hasil potongan yg udah diitung         │
│  ├── qty                                                                  │
│  └── subtotal (snapshot)                                                  │
└───────────────────────────────────────────────────────────────────────────┘

                                    DISKON TRANSAKSI (manual, dari admin)
┌───────────────────────────────────────────────────────────────────────────┐
│  discounts                                       discount_histories       │
│  ├── discount_name                                ├── discount_name       │
│  ├── discount_type (percentage/nominal)           ├── discount_type       │
│  ├── discount_value                               ├── discount_value      │
│  └── ...                                          ├── start_date          │
│                                                   └── end_date            │
└───────────────────────────────────────────────────────────────────────────┘
                                              ↓ snapshot di transactions
┌───────────────────────────────────────────────────────────────────────────┐
│  transactions — FLAT FIELD SNAPSHOT                                       │
│  ├── discount_id (FK, nullable)                                           │
│  ├── discount_name (snapshot)                                             │
│  ├── discount_type (snapshot)                                             │
│  ├── discount_value (snapshot)                                            │
│  └── discount_amount (snapshot)  ← hasil potongan yg udah diitung         │
└───────────────────────────────────────────────────────────────────────────┘

                                    VOUCHER (customer pake kode)
┌───────────────────────────────────────────────────────────────────────────┐
│  vouchers                                       voucher_histories         │
│  ├── voucher_code                                 ├── voucher_code       │
│  ├── voucher_type (nominal/percentage/free_item)  ├── voucher_type       │
│  ├── voucher_value                                 ├── voucher_value      │
│  ├── voucher_max_discount                          ├── voucher_max_discount│
│  ├── voucher_min_purchase                          ├── voucher_min_purchase│
│  ├── voucher_usage_limit                           ├── ...                │
│  ├── voucher_usage_per_customer                    ├── start_date         │
│  ├── voucher_start_date                            └── end_date           │
│  ├── voucher_end_date                                                     │
│  └── voucher_status                                                      │
└───────────────────────────────────────────────────────────────────────────┘
                                              ↓ snapshot di pivot
┌───────────────────────────────────────────────────────────────────────────┐
│  transaction_voucher (pivot) — SNAPSHOT MANDATORY                        │
│  ├── transaction_id (FK)                                                  │
│  ├── voucher_id (FK, nullable) — null kalo voucher dihapus                │
│  ├── voucher_code (snapshot)                                              │
│  ├── voucher_type (snapshot)                                              │
│  ├── voucher_value (snapshot)                                             │
│  ├── voucher_max_discount (snapshot)                                      │
│  └── voucher_amount (snapshot)  ← hasil potongan yg udah diitung          │
└───────────────────────────────────────────────────────────────────────────┘
```

---

## Struktur Tabel Lengkap

### 1. Products & Histories

```php
// products — TAMBAHKAN FIELD DISKON
Schema::table('products', function (Blueprint $table) {
    $table->string('product_discount_type')->nullable()     // percentage, nominal
          ->comment('percentage = diskon %, nominal = diskon rupiah');
    $table->decimal('product_discount_value', 15, 2)->nullable() // 10 (10%) atau 10000 (Rp10.000)
          ->comment('nilai diskon: 10 untuk 10%, 10000 untuk Rp10.000');
});

// product_histories — BARU (track perubahan harga & diskon)
Schema::create('product_histories', function (Blueprint $table) {
    $table->id('history_id');
    $table->foreignId('company_id')->nullable()->constrained('companies', 'company_id');
    $table->foreignId('product_id')->constrained('products', 'product_id');

    $table->decimal('price', 15, 2);
    $table->string('discount_type')->nullable();       // null, percentage, nominal
    $table->decimal('discount_value', 15, 2)->nullable();

    $table->datetime('start_date');
    $table->datetime('end_date')->nullable();          // null = masih aktif

    $table->string('reason')->nullable();               // "kenaikan harga", "promo ramadhan"
    $table->string('action_type');                      // created, price_changed, discount_applied, discount_removed
    $table->string('changed_by', 50)->nullable();

    $table->timestamps();
});
```

### 2. Discount Transaksi

```php
// discounts — BARU (diskon transaksi manual dari admin)
Schema::create('discounts', function (Blueprint $table) {
    $table->id('discount_id');
    $table->foreignId('company_id')->nullable()->constrained('companies', 'company_id');

    $table->string('discount_name');
    $table->string('discount_type');                   // percentage, nominal
    $table->decimal('discount_value', 15, 2);           // 10 (10%) atau 20000 (Rp20.000)
    $table->decimal('discount_max_amount', 15, 2)->nullable(); // cap maksimal
    $table->text('discount_description')->nullable();
    $table->tinyInteger('discount_status')->default(1);  // 0 inactive, 1 active

    $table->string('created_by', 50)->nullable();
    $table->string('updated_by', 50)->nullable();
    $table->tinyInteger('delete_status')->default(0);
    $table->timestamps();
});

// discount_histories — BARU (opsional, kalo perlu audit perubahan)
Schema::create('discount_histories', function (Blueprint $table) {
    $table->id('history_id');
    $table->foreignId('discount_id')->constrained('discounts', 'discount_id');

    $table->string('discount_name');
    $table->string('discount_type');
    $table->decimal('discount_value', 15, 2);
    $table->decimal('discount_max_amount', 15, 2)->nullable();

    $table->datetime('start_date');
    $table->datetime('end_date')->nullable();
    $table->string('reason')->nullable();
    $table->string('changed_by', 50)->nullable();

    $table->timestamps();
});
```

### 3. Vouchers & Histories

```php
// vouchers — SUDAH ADA (harga nambah field kalo perlu)
// File: 2026_06_13_175534_create_vouchers_table.php ✅

// voucher_histories — BARU (opsional, kalo perlu audit perubahan)
Schema::create('voucher_histories', function (Blueprint $table) {
    $table->id('history_id');
    $table->foreignId('voucher_id')->constrained('vouchers', 'voucher_id');

    $table->string('voucher_code');
    $table->string('voucher_name');
    $table->string('voucher_type');                    // nominal, percentage, free_item
    $table->decimal('voucher_value', 15, 2);
    $table->decimal('voucher_max_discount', 15, 2)->nullable();
    $table->decimal('voucher_min_purchase', 15, 2)->nullable();

    $table->datetime('start_date');
    $table->datetime('end_date')->nullable();
    $table->string('reason')->nullable();
    $table->string('changed_by', 50)->nullable();

    $table->timestamps();
});
```

### 4. Pivot: Transaction Items (order_product)

> **Catatan:** File migration sekarang namanya `order_product` tapi isinya `ingredients`.  
> **Saran:** Buat migration BARU atau rename jadi `transaction_items`.

```php
// transaction_items — BARU / GANTI order_product
Schema::create('transaction_items', function (Blueprint $table) {
    $table->id('item_id');
    $table->foreignId('company_id')->nullable()->constrained('companies', 'company_id');
    $table->foreignId('transaction_id')->constrained('transactions', 'transaction_id');
    $table->foreignId('product_id')->nullable()->constrained('products', 'product_id');

    // SNAPSHOT — data produk & diskon PAS TRANSAKSI (wajib!)
    $table->string('product_name');                     // snapshot
    $table->decimal('price', 15, 2);                    // snapshot harga produk
    $table->string('discount_type')->nullable();        // snapshot: percentage / nominal
    $table->decimal('discount_value', 15, 2)->nullable(); // snapshot: 10 atau 10000
    $table->decimal('discount_amount', 15, 2)->nullable(); // HASIL potongan (udah dihitung)
    $table->integer('qty');
    $table->decimal('subtotal', 15, 2);                 // snapshot: (price - discount_amount) × qty

    $table->text('note')->nullable();

    $table->string('created_by', 50)->nullable();
    $table->timestamps();
});
```

### 5. Pivot: Transaction Voucher

```php
// transaction_voucher — BARU (pivot + snapshot)
Schema::create('transaction_voucher', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->nullable()->constrained('companies', 'company_id');
    $table->foreignId('transaction_id')->constrained('transactions', 'transaction_id');
    $table->foreignId('voucher_id')->nullable()->constrained('vouchers', 'voucher_id')
          ->nullOnDelete(); // kalo voucher dihapus, data transaksi tetap ada

    // SNAPSHOT — data voucher PAS TRANSAKSI (wajib!)
    $table->string('voucher_code');                     // snapshot
    $table->string('voucher_type');                     // snapshot: nominal, percentage, free_item
    $table->decimal('voucher_value', 15, 2);            // snapshot: 10000 atau 10
    $table->decimal('voucher_max_discount', 15, 2)->nullable(); // snapshot: cap diskon
    $table->decimal('voucher_amount', 15, 2);           // HASIL potongan yg udah dihitung

    $table->string('created_by', 50)->nullable();
    $table->timestamps();
});
```

### 6. Transactions (Update Field)

```php
// transactions — UPDATE (ganti/relayout field diskon & voucher)
Schema::table('transactions', function (Blueprint $table) {
    // HAPUS (kalo ada):
    // $table->dropColumn('discount_id'); — pindahin logic

    // SNAPSHOT DISKON TRANSAKSI (dari admin — flat field aja, ga perlu pivot)
    $table->foreignId('discount_id')->nullable()->constrained('discounts', 'discount_id')
          ->nullOnDelete()->change();                   // ubah jadi FK (kalo sebelumnya integer)
    $table->string('discount_name')->nullable();        // snapshot
    $table->string('discount_type')->nullable();        // snapshot: percentage, nominal
    $table->decimal('discount_value', 15, 2)->nullable(); // snapshot: 10 atau 20000
    $table->decimal('discount_amount', 15, 2)->nullable(); // HASIL potongan yg udah dihitung

    // HAPUS field voucher lama (pindah ke pivot transaction_voucher):
    $table->dropColumn('transaction_voucher_id');       // kalo ada
});
```

---

## Flow Checkout

### Step-by-step pas transaksi baru:

```
1. KASIR PILIH PRODUK
   ├── Ambil data produk dari tabel products (harga + diskon terkini)
   └── Tampilkan ke customer

2. HITUNG PER ITEM (transaction_items)
   ├── price             = product_price (SNAPSHOT dari products)
   ├── discount_type     = product_discount_type (SNAPSHOT)
   ├── discount_value    = product_discount_value (SNAPSHOT)
   ├── discount_amount   = KALKULASI (harga > diskon ? diskon : harga)
   ├── subtotal          = (price - discount_amount) × qty
   └── SIMPAN ke transaction_items

3. KASIR INPUT DISKON TRANSAKSI (manual, opsional)
   ├── Pilih dari tabel discounts
   ├── Hitung potongan
   ├── discount_name     = SNAPSHOT dari discounts
   ├── discount_type     = SNAPSHOT
   ├── discount_value    = SNAPSHOT
   ├── discount_amount   = KALKULASI
   └── SIMPAN di transactions

4. CUSTOMER INPUT KODE VOUCHER (opsional)
   ├── Validasi dari tabel vouchers
   ├── Hitung potongan
   ├── voucher_code      = SNAPSHOT dari vouchers
   ├── voucher_type      = SNAPSHOT
   ├── voucher_value     = SNAPSHOT
   ├── voucher_amount    = KALKULASI (min(cap, hasil_percentage))
   ├── Kurangi usage_limit vouchers
   └── SIMPAN ke transaction_voucher

5. HITUNG GRAND TOTAL
   subtotal = Σ(transaction_items.subtotal)
   grand_total = subtotal + tax + service_charge
                 - discount_amount    (dari diskon transaksi)
                 - voucher_amount     (dari voucher)

6. TUTUP TRANSAKSI
   └── Status = completed
```

---

## Laporan Transaksi

Setelah semua data pakai snapshot, laporan tinggal SELECT — **100% akurat**, ga peduli data master berubah:

### Report Harian

```sql
SELECT
    t.transaction_code,
    t.transaction_date,
    t.transaction_subtotal,
    t.transaction_tax,
    t.transaction_service_charge,

    -- Diskon transaksi (manual admin)
    COALESCE(t.discount_name, '-') AS discount_name,
    t.discount_amount AS discount_amount,

    -- Voucher (pivot)
    tv.voucher_code,
    tv.voucher_amount AS voucher_amount,

    -- Grand total
    t.transaction_grand_total

FROM transactions t
LEFT JOIN transaction_voucher tv ON tv.transaction_id = t.transaction_id
WHERE DATE(t.transaction_date) = '2026-06-27'
ORDER BY t.transaction_date;
```

### Report Detail Per Item

```sql
SELECT
    t.transaction_code,
    ti.product_name,
    ti.price,
    ti.discount_type,
    ti.discount_value,
    ti.discount_amount,
    ti.qty,
    ti.subtotal
FROM transaction_items ti
JOIN transactions t ON t.transaction_id = ti.transaction_id
WHERE t.transaction_id = 1
ORDER BY ti.item_id;
```

### Ringkasan Penggunaan Voucher

```sql
SELECT
    tv.voucher_code,
    COUNT(*) AS total_pemakaian,
    SUM(tv.voucher_amount) AS total_potongan
FROM transaction_voucher tv
WHERE tv.created_at BETWEEN '2026-01-01' AND '2026-06-27'
GROUP BY tv.voucher_code
ORDER BY total_pemakaian DESC;
```

---

## Diagram Relasi

```
┌───────────────┐     ┌───────────────────┐     ┌──────────────────┐
│   companies   │     │   products        │     │ product_histories│
│───────────────│     │───────────────────│     │──────────────────│
│ company_id PK │──┬──│ product_id PK     │──┬──│ history_id PK    │
└───────────────┘  │  │ product_price     │  │  │ product_id FK    │
                   │  │ product_discount_ │  │  │ price            │
                   │  │  type             │  │  │ discount_type    │
                   │  │ product_discount_ │  │  │ discount_value   │
                   │  │  value            │  │  │ start_date       │
                   │  └───────────────────┘  │  │ end_date         │
                   │                         │  └──────────────────┘
                   │  ┌───────────────────┐  │
                   │  │ transaction_items │  │
                   │  │───────────────────│  │
                   │  │ item_id PK        │  │
                   ├──│ transaction_id FK │  │
                   ├──│ product_id FK     │  │
                   │  │ product_name (snap)│  │ ← SNAPSHOT
                   │  │ price (snap)      │  │
                   │  │ discount_type(snap)│  │
                   │  │ discount_value(sn)│  │
                   │  │ discount_amount   │  │
                   │  │ qty               │  │
                   │  │ subtotal          │  │
                   │  └───────────────────┘  │
                   │                         │
                   │  ┌───────────────────┐  │
                   │  │   transactions    │  │
                   │  │───────────────────│  │
                   ├──│ transaction_id PK │  │
                   │  │ discount_id FK    │──┼───┐
                   │  │ discount_name(snap)│  │   │
                   │  │ discount_type(snap)│  │   │
                   │  │ discount_value(sn)│  │   │
                   │  │ discount_amount   │  │   │
                   │  │ subtotal          │  │   │
                   │  │ tax               │  │   │
                   │  │ service_charge    │  │   │
                   │  │ grand_total       │  │   │
                   │  └───────────────────┘  │   │
                   │                         │   │
                   │  ┌───────────────────┐  │   │
                   │  │transaction_voucher│  │   │
                   │  │───────────────────│  │   │
                   ├──│ transaction_id FK │  │   │
                   ├──│ voucher_id FK     │──┼───┼──┐
                   │  │ voucher_code(snap)│  │   │  │
                   │  │ voucher_type(snap)│  │   │  │
                   │  │ voucher_value(sn) │  │   │  │
                   │  │ voucher_max_disc  │  │   │  │
                   │  │ voucher_amount    │  │   │  │
                   │  └───────────────────┘  │   │  │
                   │                         │   │  │
┌───────────────┐  │  ┌───────────────────┐  │   │  │
│   discounts   │  │  │   vouchers        │  │   │  │
│───────────────│  │  │───────────────────│  │   │  │
│ discount_id PK│──┘  │ voucher_id PK     │──┘   │  │
│ discount_name │     │ voucher_code      │      │  │
│ discount_type │     │ voucher_type      │      │  │
│ discount_value│     │ voucher_value     │      │  │
│ discount_max  │     │ voucher_max_disc  │      │  │
└───────────────┘     │ voucher_min_purch │      │  │
                      └───────────────────┘      │  │
                                                 │  │
┌───────────────────┐  ┌───────────────────┐     │  │
│ discount_histories│  │ voucher_histories  │     │  │
│───────────────────│  │───────────────────│     │  │
│ history_id PK     │  │ history_id PK     │     │  │
│ discount_id FK    │  │ voucher_id FK     │─────┘  │
│ discount_name     │  │ voucher_code      │        │
│ discount_type     │  │ voucher_type      │        │
│ discount_value    │  │ voucher_value     │        │
│ start_date        │  │ start_date        │        │
│ end_date          │  │ end_date          │        │
└───────────────────┘  └───────────────────┘        │
                                                    │
┌───────────────────────────────────────────────────┘
│  LEGENDA:
│  (snap)  = Snapshot — dicopy pas transaksi, ga berubah lagi
│  FK      = Foreign key — nullable, kalo data master dihapus transaksi tetap utuh
│  ──┬──   = one-to-many
│  ──┼──   = many-to-many (via pivot)
```

---

## Kesimpulan

| Prinsip | Cara |
|---|---|
| **Produk diskon otomatis** | Kolom di `products` (1 produk 1 diskon) |
| **Snapshot per item pesanan** | `transaction_items` nyimpen salinan harga + diskon pas transaksi |
| **Diskon transaksi manual** | Flat field di `transactions` + master `discounts` |
| **Voucher transaksi** | Pivot `transaction_voucher` nyimpen salinan data voucher |
| **History / audit trail** | `product_histories`, `discount_histories`, `voucher_histories` (opsional) |
| **Laporan akurat** | Tinggal SELECT — data snapshot, ga berubah lagi |

> **Prinsip Utama:**
> **Data transaksi adalah dokumen legal — snapshotnya tidak boleh berubah setelah transaksi selesai.**

---

*File ini adalah evolution dari `diskon.md` — lebih lengkap dengan arsitektur diskon, voucher, dan snapshot secara terintegrasi.*