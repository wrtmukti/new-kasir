# Rangkuman Kebutuhan POS — Kasir, HPP, Pembayaran, Laporan & Supplier

## 1. Tujuan

Sistem tidak hanya berfungsi sebagai kasir, tetapi menghubungkan proses:

**Customer Order → Kasir → Pembayaran → Stok/HPP → Laporan Finance/Owner → Purchasing/Supplier**

Fokus utama saat ini adalah memastikan kebutuhan real user sebelum fitur dikembangkan terlalu jauh.

---

## 2. Detail Transaksi

Setiap transaksi dapat dibuka melalui menu **Detail Transaksi**.

Informasi yang ditampilkan menyesuaikan role.

### Kasir/Karyawan

Dapat melihat:

- Nomor transaksi
- Waktu transaksi
- Item yang dibeli
- Quantity
- Harga jual
- Subtotal
- Total transaksi
- Status pembayaran
- Metode pembayaran

Tidak dapat melihat:

- HPP
- Laba kotor
- Margin/laba internal

### Finance & Owner

Dapat melihat seluruh informasi transaksi, termasuk:

- Total penjualan
- Total HPP
- Laba kotor
- Margin
- Detail pembayaran
- Metode pembayaran
- Informasi kasir
- Waktu transaksi

### Contoh

```text
Total Penjualan : Rp80.000
HPP             : Rp30.000
Laba Kotor      : Rp50.000

Pembayaran:
QRIS            : Rp50.000
Cash            : Rp30.000
Total Dibayar   : Rp80.000
```

---

## 3. HPP (Harga Pokok Penjualan)

HPP merupakan total biaya yang diperlukan untuk menghasilkan/menyediakan produk sampai siap dijual.

Contoh:

```text
Produk          : Ayam
Total biaya     : Rp20.000
Harga jual      : Rp30.000
Laba kotor      : Rp10.000
```

Untuk produk yang terdiri dari beberapa bahan, HPP dapat berasal dari komponen bahan/ingredient.

Contoh:

```text
Ayam            Rp10.000
Bumbu           Rp3.000
Minyak          Rp2.000
Bahan lainnya   Rp5.000
-----------------------
Total HPP       Rp20.000
```

### Catatan penting

HPP sebaiknya tidak dihitung manual setiap kali transaksi.

Lebih baik sistem memiliki:

**Produk → Recipe/BOM → Bahan → Harga Modal → HPP**

Sehingga ketika produk terjual, sistem dapat menghitung HPP secara otomatis.

---

## 4. Split Payment

Satu transaksi harus dapat menggunakan lebih dari satu metode pembayaran.

Contoh:

```text
Total transaksi : Rp80.000

QRIS            : Rp50.000
Cash            : Rp30.000
-------------------------
Total            Rp80.000
```

Jangan membatasi transaksi hanya memiliki satu `payment_method`.

Lebih baik struktur pembayaran memungkinkan:

```text
Transaction
    └── Payments
          ├── QRIS
          ├── Cash
          └── metode lainnya
```

Dengan begitu satu transaksi dapat memiliki banyak pembayaran.

---

## 5. Kasir

Kasir harus didesain untuk kondisi ramai dan membutuhkan input cepat.

### Fitur yang dibutuhkan

- Search produk
- Search pesanan
- Filter pesanan
- Melihat pesanan yang masuk
- Menambahkan item tambahan
- Memproses pembayaran
- Split payment
- Melihat status transaksi
- Menghindari scrolling panjang

### Alur

```text
Customer melakukan order
        ↓
Order masuk ke sistem
        ↓
Kasir melihat order
        ↓
Kasir melakukan pengecekan
        ↓
Jika ada tambahan → edit/tambah item
        ↓
Pembayaran
        ↓
Transaksi selesai
```

---

## 6. Customer Order / QR Order

Customer dapat melakukan order sendiri melalui QR.

Order tersebut kemudian otomatis masuk ke sistem kasir.

```text
Customer
   ↓
Scan QR
   ↓
Pilih produk
   ↓
Buat pesanan
   ↓
Order masuk ke kasir
   ↓
Kasir memproses
   ↓
Pembayaran
```

Tampilan customer sebaiknya dipisahkan dari tampilan internal karyawan.

---

## 7. Laporan Finance & Owner

Bagian laporan perlu menjadi fokus utama karena kebutuhan real user masih perlu digali lebih lanjut.

### Laporan Penjualan

Minimal pertimbangkan:

- Penjualan harian
- Penjualan mingguan
- Penjualan bulanan
- Total transaksi
- Total omzet
- Produk terlaris
- Penjualan per produk
- Penjualan per kategori
- Penjualan per kasir

### Laporan Pembayaran

- Total Cash
- Total QRIS
- Total pembayaran per metode
- Split payment
- Transaksi belum lunas
- Refund
- Void/batal

### Laporan HPP & Profit

Khusus Finance & Owner:

- Total HPP
- HPP per produk
- HPP per transaksi
- Omzet
- Laba kotor
- Margin
- Profit berdasarkan periode
- Profit berdasarkan produk

Contoh:

```text
Omzet       : Rp100.000.000
Total HPP   : Rp60.000.000
Laba Kotor  : Rp40.000.000
Margin      : 40%
```

### Laporan Kasir

- Total transaksi per kasir
- Total penjualan per kasir
- Cash yang diterima
- QRIS yang diterima
- Transaksi void
- Refund
- Selisih kas
- Shift kasir

---

## 8. Role & Permission

### Customer

- Melihat produk
- Membuat order
- Melihat status order

### Kasir/Karyawan

- Mengelola transaksi
- Melihat order
- Memproses pembayaran
- Menambahkan item
- Melihat informasi penjualan yang diperlukan

Tidak boleh melihat:

- HPP
- Laba
- Margin
- Informasi keuangan internal

### Finance

Dapat melihat:

- Transaksi
- Pembayaran
- HPP
- Laba
- Margin
- Laporan keuangan
- Data purchasing

### Owner

Memiliki akses monitoring paling luas:

- Dashboard bisnis
- Penjualan
- HPP
- Laba
- Margin
- Pembayaran
- Purchasing
- Supplier
- Stok
- Laporan

---

## 9. Supplier & Purchasing

Sistem dapat memberikan rekomendasi supplier berdasarkan harga.

Contoh:

| Bahan  |  Supplier A |  Supplier B | Rekomendasi |
| ------ | ----------: | ----------: | ----------- |
| Ayam   | Rp36.000/kg | Rp20.000/kg | Supplier B  |
| Minyak |  Rp18.000/L |  Rp20.000/L | Supplier A  |

Sistem dapat menampilkan:

> Supplier termurah untuk Ayam: Supplier B — Rp20.000/kg

### Pengembangan lanjutan

Pertimbangkan:

- Histori harga supplier
- Harga terbaru
- Harga rata-rata
- MOQ
- Lead time
- Supplier aktif/nonaktif
- Riwayat pembelian
- Purchase Order
- Rekomendasi supplier

**Catatan:** Jangan hanya menggunakan harga termurah sebagai satu-satunya indikator jika nantinya kualitas, minimum order, atau waktu pengiriman juga penting.

---

# 10. Hal yang Perlu Dikonsultasikan ke User

Sebelum development dilanjutkan, tanyakan kebutuhan berdasarkan proses kerja nyata.

## Kasir

- Apa yang paling sering dicari kasir?
- Apakah search produk wajib?
- Apakah search order wajib?
- Apakah kasir boleh mengubah order customer?
- Apakah perlu hold transaksi?
- Apakah perlu void?
- Apakah perlu refund?
- Apakah menggunakan shift kasir?

## Pembayaran

- Apakah Cash + QRIS dapat digunakan dalam satu transaksi?
- Apakah ada transfer bank?
- Apakah ada debit/kartu?
- Bagaimana jika pembayaran kurang?
- Bagaimana jika pembayaran lebih?
- Bagaimana proses refund?

## Finance

- Laporan apa yang digunakan setiap hari?
- Apakah membutuhkan HPP per transaksi?
- Apakah membutuhkan laba per transaksi?
- Apakah membutuhkan laba per produk?
- Apakah membutuhkan laporan per kasir?
- Apakah membutuhkan laporan per metode pembayaran?
- Apakah export Excel benar-benar diperlukan?
- Laporan apa yang biasanya masih dibuat manual di Excel?

## Purchasing

- Bagaimana menentukan supplier saat ini?
- Apakah harga supplier sering berubah?
- Apakah ada supplier utama?
- Apakah harga termurah selalu menjadi pilihan?
- Apakah ada minimum pembelian?
- Apakah kualitas supplier perlu dipertimbangkan?

---

# 11. Saran Pengembangan

## Prioritas 1 — Core POS

Selesaikan terlebih dahulu:

1. Product
2. Cart
3. Order
4. Transaction
5. Payment
6. Split Payment
7. Receipt
8. Stock deduction
9. Role & Permission

## Prioritas 2 — Reporting

Setelah transaksi stabil:

1. Sales Report
2. Payment Report
3. Cashier Report
4. HPP
5. Gross Profit
6. Margin
7. Dashboard Finance/Owner

## Prioritas 3 — Inventory & HPP

Kemudian:

1. Ingredient/Material
2. Recipe/BOM
3. Stock
4. Stock Movement
5. Purchase
6. Supplier
7. Supplier Price History
8. Automatic HPP

## Prioritas 4 — Smart Purchasing

Terakhir:

1. Supplier comparison
2. Cheapest supplier recommendation
3. Purchase recommendation
4. Price history
5. Reorder recommendation

---

# 12. Rekomendasi Arsitektur Data

Minimal pisahkan konsep berikut:

```text
products
    ↓
product_recipes / product_bom
    ↓
materials / ingredients
    ↓
stock_movements

transactions
    ↓
transaction_items
    ↓
payments
```

Untuk purchasing:

```text
suppliers
    ↓
supplier_prices
    ↓
purchases
    ↓
purchase_items
```

Dengan struktur tersebut, sistem lebih mudah dikembangkan untuk HPP, stok, laporan, dan supplier recommendation.

---

# 13. Kesimpulan

Sistem sebaiknya diposisikan sebagai:

**POS + Order Management + Inventory + Purchasing + Financial Reporting**

Bukan hanya aplikasi kasir.

Fokus terdekat adalah memastikan tiga hal benar terlebih dahulu:

1. **Alur transaksi benar**
2. **HPP dan laba dapat dihitung dengan benar**
3. **Hak akses informasi keuangan benar**

Setelah tiga hal tersebut stabil, baru fitur supplier recommendation dan laporan lanjutan dikembangkan.

---

# 14. Pertanyaan Utama untuk Validasi

Sebelum coding fitur baru, pastikan sudah mendapatkan jawaban untuk:

> **"Kalau sistem ini dipakai dari buka sampai tutup operasional, apa saja yang benar-benar dilakukan oleh Customer, Kasir, Finance, Purchasing, dan Owner?"**

Jawaban dari pertanyaan tersebut sebaiknya menjadi dasar finalisasi flow dan fitur, bukan hanya berdasarkan asumsi developer.
