# Analisis Database: Produk & Diskon Historical Tracking

## Tujuan
- Produk — harga bisa berubah seiring waktu (Nasi Goreng naik 25rb → 30rb)
- Diskon — promo produk bisa datang dan pergi (hari ini diskon 20%, besok normal)
- Order lama tetap akurat — order bulan lalu harus nyimpen harga & diskon saat itu
- Snapshot / tracing data historikal

---

## Masalah: Slowly Changing Dimension (SCD Type 2)

Produk berubah (harga, diskon) sedangkan order lama perlu tetap akurat. Ada 3 approach:

---

## Approach 1: Snapshot di Order/Transaction (Simple)

**Cara:**
- `products` cuman 1 baris, harga & diskon di-update langsung
- `order_product` nyimpen **copy/snapshot** dari harga & diskon pas transaksi

**Contoh:**
```
products:
  product_id: 1
  product_name: "Nasi Goreng"
  product_price: 30.000       ← data terbaru

order_product:
  order_id: 1
  product_id: 1
  price: 25.000               ← snapshot harga waktu itu
  discount_type: percentage
  discount_value: 10
  price_after_discount: 22.500
```

**Untung:** Sangat simpel, order historis tetap akurat
**Rugi:** Tidak ada track record perubahan harga — ga bisa jawab "Nasi Goreng bulan lalu harganya berapa?" kalo ga pernah dipesan
**Cocok:** Resto kecil / MVP, produk jarang berubah harga

---

## Approach 2: History Table Terpisah ✅ (Paling Umum di SaaS)

**Cara:**
- `products` tetap 1 baris — hanya menyimpan **data terbaru / current**
- Tabel `product_histories` — **setiap perubahan disimpan sebagai baris baru**

### Struktur Tabel

**`products`** (harga & diskon terbaru):
```
product_id            PK
company_id            FK → companies
product_name          Nama produk
product_price         Current price
product_discount_type Current discount (null, percentage, nominal)
product_discount_value Current discount value
... (field lainnya)
```

**`product_histories`** (riwayat lengkap):
```
history_id            PK
company_id            FK → companies
product_id            FK → products

price                 Harga saat itu
discount_type         Diskon saat itu (null, percentage, nominal)
discount_value        Nilai diskon saat itu

start_date            Mulai berlaku
end_date              Berakhir (null = masih aktif)

reason                Alasan perubahan: "kenaikan harga", "promo ramadhan"
action_type           'created', 'price_changed', 'discount_applied', 'discount_removed'
changed_by            Siapa yang ngubah

timestamps
```

**Contoh Riwayat:**
| history_id | price | disc_type | disc_value | start_date | end_date | reason |
|---|---|---|---|---|---|---|
| 4 | 30.000 | null | null | 2026-06-10 | null | Diskon habis |
| 3 | 30.000 | percentage | 10 | 2026-06-01 | 2026-06-09 | Diskon lebaran 10% |
| 2 | 30.000 | percentage | 20 | 2026-05-15 | 2026-05-31 | Promo HUT 20% |
| 1 | 25.000 | null | null | 2026-01-01 | 2026-05-14 | Harga awal |

**Mapping ke order_product** (2 opsi):

Opsi A — Snapshot aja (simple):
```
order_product:
  product_id: 1
  price: 30.000               ← snapshot harga
  discount_type: percentage
  discount_value: 20
  price_after_discount: 24.000
  qty: 2
  subtotal: 48.000
```

Opsi B — + history_id (proper, tracing ke versi spesifik):
```
order_product:
  product_id: 1
  product_history_id: 3      ← FK ke product_histories
  price: 30.000
  discount_type: percentage
  discount_value: 20
  price_after_discount: 24.000
  qty: 2
  subtotal: 48.000
```

### Query Penting

**Cari harga yg berlaku di tanggal tertentu:**
```sql
SELECT * FROM product_histories
WHERE product_id = 1
  AND start_date <= '2026-05-15'
  AND (end_date IS NULL OR end_date > '2026-05-15')
```

**Riwayat perubahan harga:**
```sql
SELECT * FROM product_histories
WHERE product_id = 1
ORDER BY start_date DESC
```

**Untung:**
- ✅ Full track record — tau kapan harga berubah, dari berapa, siapa, kenapa
- ✅ Query historis gampang — pake WHERE start_date / end_date
- ✅ Audit trail lengkap — cocok buat SaaS compliance
- ✅ Order tiap order bisa di-trace ke versi spesifik

**Rugi:**
- ⚡ Lebih kompleks (2 tabel instead of 1)
- ⚡ Bisa membengkak kalo sering update

---

## Approach 3: Temporal Table (Paling Proper)

**Cara:** Produk sendiri yang jadi versioned, setiap perubahan bikin **baris baru di products**.

```
products:
  product_id: 2
  product_name: "Nasi Goreng"
  product_price: 30.000
  start_date: 2026-06-01
  end_date: null
  version: 2

  product_id: 1
  product_name: "Nasi Goreng"
  product_price: 25.000
  start_date: 2026-01-01
  end_date: 2026-05-31
  version: 1
```

**Untung:** 1 tabel aja, order langsung nunjuk `product_id` spesifik
**Rugi:** Kompleks (FK bermasalah), tabel membesar, logic "cari terbaru" ribet

---

## Perbandingan Lengkap

| Aspek | Approach 1 (Snapshot) | Approach 2 (History) ✅ | Approach 3 (Temporal) |
|---|---|---|---|
| Kompleksitas | ✅ Rendah | ⚡ Sedang | ❌ Tinggi |
| Ukuran DB | ✅ Kecil | ⚡ Sedang | ❌ Besar |
| Tracing harga historis | ❌ Susah | ✅ Mudah | ✅ Mudah |
| Order historis akurat | ✅ Ya | ✅ Ya | ✅ Ya |
| Query "harga tgl X" | ❌ Ga bisa | ✅ start_date <= X | ✅ start_date <= X |
| Audit trail | ❌ No | ✅ Yes | ✅ Yes |
| Laporan perubahan harga | ❌ Ga ada | ✅ Bisa | ✅ Bisa |
| SaaS scale | ❌ Kurang | ✅ Cocok | ✅ Cocok |

---

## Rekomendasi: Approach 2 (History Table)

```
┌─────────────────────────────────────────────┐
│   products (1 baris per produk)              │
│   - product_id, company_id                   │
│   - product_price (current)                  │
│   - product_discount_type (current)          │
│   - product_discount_value (current)         │
│   - ... (nama, deskripsi, dll)               │
├─────────────────────────────────────────────┤
│        ↓ setiap ada perubahan harga/diskon   │
├─────────────────────────────────────────────┤
│   product_histories (berbaris-baris)         │
│   - history_id, product_id (FK)              │
│   - price, discount_type, discount_value     │
│   - start_date, end_date                    │
│   - reason, changed_by, action_type         │
├─────────────────────────────────────────────┤
│        ↓ pas transaksi, copy snapshot        │
├─────────────────────────────────────────────┤
│   order_product                              │
│   - product_id                               │
│   - product_history_id (FK opsional)         │
│   - price (snapshot)                         │
│   - discount_type, discount_value            │
│   - price_after_discount, qty, subtotal     │
└─────────────────────────────────────────────┘
```

### Alur Diskon Produk (1 produk 1 diskon)

- `products.product_discount_type` + `products.product_discount_value` = diskon **sedang aktif**
- Diskon berubah → **insert baris baru di product_histories** dengan snapshot harga + diskon baru
- Diskon dihapus → insert baris baru dengan `discount = null`

### Kesimpulan

| Kriteria | Value |
|---|---|
| Operasional simpel | ✅ 1 tabel untuk sehari-hari |
| Audit trail | ✅ Setiap perubahan tercatat |
| Order akurat | ✅ Snapshot harga |
| Tracing historis | ✅ Harga di tgl tertentu |
| Discount tracking | ✅ Naik/turun ke-track |
| SaaS ready | ✅ Multi-tenant + audit |

---

## Catatan Khusus untuk Voucher

Voucher **tidak masuk** ke product_histories. Voucher:
- Target: **grand total transaksi** (bukan per produk)
- Opsional: pake kode, customer bisa milih
- Disimpan di tabel `vouchers` + `transaction_vouchers` (pivot)

> **Diskon → ke produk (otomatis)**
> **Voucher → ke grand total (opsional, pake kode)**
