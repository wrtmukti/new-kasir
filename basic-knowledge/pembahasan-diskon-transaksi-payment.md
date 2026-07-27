# Pembahasan: Diskon → Transaksi → Payment

> Dibuat: 2026-07-25
> Tujuan: Mapping alur diskon dari master sampai ke payment, identifikasi celah, tentukan prioritas.

---

## 📊 Kondisi Saat Ini

### Flow Order → Transaction → Payment (sekarang)

```
ORDER (in_progress)
  ├── order_product (pivot: product_id, qty, note)
  │     ⚠️ TIDAK ADA kolom diskon — harga ambil dari products.product_price
  │     ⚠️ TIDAK ADA snapshot harga
  │
  └── Order complete
        │
        ▼
  TRANSACTION (success)
    ├── transaction_subtotal      = price × qty (TANPA diskon)
    ├── transaction_discount_*    = ❌ KOSONG (tidak diisi)
    ├── transaction_grand_total   = subtotal (TANPA diskon)
    │
    ├── transaction_items
    │     ├── price               = product_price
    │     ├── discount_*          = ❌ KOSONG (tidak diisi)
    │     └── subtotal            = price × qty (TANPA diskon)
    │
    └── PAYMENT (belum ada implementasi)
          └── payment_grand_total = subtotal + tax + service_charge - discount
                                  ⚠️ BELUM ADA CONTROLLER/VIEW PAYMENT
```

### Yang UDAH Ada (infra siap)

| Komponen | Status |
|----------|--------|
| Master `discounts` + CRUD | ✅ |
| Relasi Product → Discount (`product_discount_id`) | ✅ |
| Dropdown diskon di form Produk | ❌ |
| `transaction_items.discount_type/value/amount` | ✅ kolom siap |
| `transactions.discount_id/name/type/value/amount` | ✅ kolom siap |
| `order_product` — snapshot harga + diskon | ❌ **kolom belum ada** |
| **OrderController@complete — hitung diskon** | ❌ **belum diimplement** |
| **TransactionController — display diskon** | ❌ **belum ditampilkan** |
| **Payment** — Model/Controller/View | ❌ **belum dibuat sama sekali** |

---

## 🔍 Arsitektur dari File .md (Sudah Dirancang)

Dari `arsitektur-diskon-voucher-snapshot.md`, flow checkout seharusnya:

### Step 1: Kasir Pilih Produk
- Ambil data produk (harga + diskon terkini dari `product_discount_*`)

### Step 2: Hitung Per Item → `transaction_items`
```
price             = products.product_price (SNAPSHOT)
discount_type     = products.product_discount_type (SNAPSHOT)
discount_value    = products.product_discount_value (SNAPSHOT)
discount_amount   = KALKULASI
                    - percentage: min(price × value/100, discount_max_amount)
                    - nominal:    min(value, price)
subtotal          = (price - discount_amount) × qty
```

### Step 3: Diskon Transaksi Manual (opsional)
- Admin pilih dari master `discounts`
- Snapshot ke `transactions.discount_*`
```
discount_amount   = KALKULASI
                    - percentage: min(subtotal × value/100, discount_max_amount)
                    - nominal:    min(value, subtotal)
```

### Step 4: Voucher (opsional — nanti)
- Via kode → pivot `transaction_voucher`

### Step 5: Grand Total
```
grand_total = subtotal + tax + service_charge
              - discount_amount      (dari diskon transaksi manual)
              - voucher_amount       (dari voucher — nanti)
```

### Step 6: Payment
```
payment_amount        = grand_total
payment_grand_total   = grand_total
payment_status        = completed
```

---

## 🧮 Contoh Hitungan

### Skenario: Nasi Goreng (Rp25.000) × 2, diskon produk 10%

#### Step 2 — Per Item:
| Field | Nilai |
|-------|-------|
| price | Rp25.000 |
| discount_type | percentage |
| discount_value | 10 |
| discount_amount | Rp2.500 (10% × 25.000) |
| qty | 2 |
| subtotal | (25.000 - 2.500) × 2 = **Rp45.000** |

#### Step 3 — Diskon Transaksi (opsional):
Misal admin pilih "Diskon Akhir Pekan" (15%, max Rp50.000):
| Field | Nilai |
|-------|-------|
| discount_name | Diskon Akhir Pekan |
| discount_type | percentage |
| discount_value | 15 |
| discount_amount | min(45.000 × 15%, 50.000) = **Rp6.750** |

#### Step 5 — Grand Total:
| Field | Nilai |
|-------|-------|
| subtotal | Rp45.000 |
| tax (0%) | Rp0 |
| service_charge (0%) | Rp0 |
| discount_amount | -Rp6.750 |
| **grand_total** | **Rp38.250** |

---

## 🔴 Celah / Masalah yang Ditemukan

### 1. `order_product` (pivot) TIDAK punya kolom snapshot
- Sekarang cuma: `product_id, order_id, note, quantity`
- Pas order store, data produk bisa berubah nanti → laporan order history bakal salah
- **Perlu tambah kolom:** `price, discount_type, discount_value, discount_amount, subtotal`

### 2. `OrderController@complete` TIDAK hitung diskon sama sekali
- `transaction_items.discount_*` dikosongin
- `transactions.discount_*` dikosongin
- Grand total = subtotal doang

### 3. `TransactionController@show` TIDAK nampilin diskon
- Kolom diskon di transactions & transaction_items udah ada tapi gak dipake di view

### 4. Payment belum dibuat
- Tabel `payments` udah ada migrationnya
- Tapi Model, Controller, View, Routes — SEMUA belum ada

### 5. `order_product` (pivot) vs `transaction_items`
- Ada gap: pas order → order_product (belum ada snapshot)
- Pas complete → copy ke transaction_items (ada snapshot)
- Kalo order belum di-complete, data di order_product bisa berubah

### 6. Produk bisa punya diskon dari master (`product_discount_id`)
- Tapi form create/edit produk belum ada dropdown pilih diskon
- Admin harus buka menu Diskon → show → attach produk

---

## 🎯 Opsi & Prioritas

### Opsi A: Fix Order → Transaction → Payment (Full Flow)

1. **Tambah kolom snapshot di `order_product` migration**
   - `price`, `discount_type`, `discount_value`, `discount_amount`, `subtotal`
2. **Update OrderController@store**
   - Pas simpan order, hitung diskon produk (dari `product_discount_*`)
   - Simpan snapshot di `order_product`
3. **Update OrderController@complete**
   - Copy dari `order_product` ke `transaction_items`
   - Ambil diskon transaksi (dari input admin)
   - Hitung grand total
4. **Update TransactionController@show**
   - Tampilkan diskon di detail transaksi
5. **Bikin Payment CRUD**
   - Model, minimal store+show

### Opsi B: Fix OrderController@complete Doang (Quick Win)
Skip `order_product`, langsung hitung di complete:
1. **Update OrderController@complete**
   - Hitung diskon produk per item
   - Simpan ke `transaction_items.discount_*`
   - Simpan diskon transaksi ke `transactions.discount_*`
   - Grand total = subtotal - diskon
2. **Update TransactionController@show** — tampilkan diskon
3. **Tambahkan dropdown pilih diskon transaksi di halaman complete**
4. Bikin payment belakangan

### Opsi C: Incremental (Yang Paling Rasional)

| Urut | Item | Prioritas |
|------|------|-----------|
| 1 | **Dropdown diskon di form Produk** | 🔴 High |
| 2 | **OrderController@complete — hitung diskon produk ke transaction_items** | 🔴 High |
| 3 | **Tambah input diskon transaksi di halaman complete** | 🔴 High |
| 4 | **Transaction show — tampilkan diskon** | 🟡 Medium |
| 5 | **Tambah kolom snapshot di order_product** | 🟡 Medium |
| 6 | **Order store — hitung diskon produk ke order_product** | 🟡 Medium |
| 7 | **Payment CRUD** | 🟢 Low (nanti) |

---

## 🤔 Pertanyaan Buat Didiskusiin

1. **Diskon Produk vs Diskon Transaksi — mana dulu?**
   - Diskon produk (otomatis dari `product_discount_*`) langsung masuk hitungan pas order/complete?
   - Atau manual pilih juga?

2. **Order_product perlu snapshot atau enggak?**
   - Kalo order belum complete dan harga produk berubah → pas complete pake harga lama atau baru?
   - Kalo perlu snapshot → perlu migration tambah kolom

3. **Diskon Transaksi — kapan milihnya?**
   - Pas complete order (sekarang) — tambah dropdown di halaman complete?
   - Atau di halaman terpisah (payment page)?

4. **Payment — bareng sama complete atau dipisah?**
   - Complete == langsung success payment?
   - Atau complete → pending payment → payment terpisah?

5. **Prioritas lo:**

---

## ✅ Keputusan Sementara (2026-07-25) — Diskusi

### Putusan
1. **Diskon transaksi (manual) → masuk ke VOUCHER** — jangan campur aduk sama `discounts`. `discounts` murni buat diskon produk.
2. **Fokus utama kita: Diskon Produk → Transaction → Payment (atau skip payment langsung struk)**
3. **Payment diskip dulu** — complete langsung cetak struk (kayak alur sekarang).
4. **Produk udah dihubungin sama diskon** → tampilkan harga sebelum & sesudah diskon.

### Alur Final (sementara)
```
PRODUK:
  Nasi Goreng Rp25.000, diskon 20%
  → Tampilkan: Harga Rp25.000, Diskon 20% (Rp5.000), Harga disc Rp20.000

ORDER → COMPLETE:
  ├── transaction_items:
  │     price = 25.000 (snapshot)
  │     discount_type/value = dari product_discount_* (snapshot)
  │     discount_amount = 5.000
  │     subtotal = (25.000 - 5.000) × qty
  ├── transaction.grand_total = Σ subtotal (udah include diskon)
  └── → langsung cetak struk (alur lama gak berubah)

TAMPILAN:
  ├── Order show → harga sebelum & sesudah diskon
  ├── Transaction show → kolom diskon
  └── Struk → harga, diskon, subtotal per item
```

### Yang perlu diputusin berikutnya
- ~~**Snapshot `order_product`?**~~ → **OKT 2026-07-27: Pilih Opsi B (Single Snapshot di `transaction_items`).** Order in_progress pake live price, completed pake snapshot dari transaction_items. Gak perlu migration `order_product`.
- **Diskon transaksi (voucher)** — nanti

---

## Referensi

- `arsitektur-diskon-voucher-snapshot.md` — arsitektur lengkap diskon+voucher+snapshot
- `diskon.md` — analisis SCD Type 2 untuk produk & diskon
- `OrderController@complete` — baris 101-171 (kunci implementasi)
- `TransactionController` — index + show doang, edit/delete sengaja gak ada
- Migrasi: `2026_06_27_000011_add_discount_snapshot_to_transactions_table.php`
