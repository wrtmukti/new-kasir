# 🛠️ RENCANA PERBAIKAN KOMPREHENSIF: SEEDER MULTI-CABANG & SINKRONISASI KATALOG POS KASIR

> **Dokumen**: `rencana_perbaikan.md`  
> **Lokasi**: `basic-knowledge/deva-branch/2026-09-07/rencana_perbaikan.md`  
> **Tanggal**: 2026-09-07 (07 September 2026)  
> **Branch**: `deva-branch`  
> **Status**: 📝 **Siap Dikerjakan Begitu User Memberi Izin**  
> **Tujuan**: Panduan teknis lengkap perbaikan file seeder klien (`GeprekGambosSeeder` & `KopiSenjaSeeder`) agar katalog produk, stok fisik, sesi kasir, dan laporan finansial terdistribusi merata ke seluruh cabang.

---

## 🔍 1. Analisis Akar Masalah (Root Causes)

Berdasarkan inspeksi langsung pada kode program dan database tenant `new_kasir_geprekgambos_dev`:

1. **Hardcoded `outlet_id` pada Produk & Kategori**:
   * Di `GeprekGambosSeeder.php` baris 239–268:
     * Kategori dibuat dengan `'outlet_id' => $jkt->outlet_id`
     * Produk dibuat dengan `'outlet_id' => $jkt->outlet_id`
   * Akibat: Saat berpindah ke cabang **Surabaya Gubeng**, query `OrderController@index` yang memfilter `where('outlet_id', $activeOutletId)->orWhereNull('outlet_id')` menghasilkan 0 baris produk.
2. **Ketiadaan Binding Pivot `product_stock`**:
   * Data `Stock` dibuat untuk 4 cabang, tetapi relasi pivot `$prod->stocks()->attach(...)` tidak pernah dipanggil. Kolom Bahan Baku di tabel produk tampil strip (`-`), dan fitur auto-potong stok kasir tidak berfungsi.
3. **Ketiadaan Sesi Kasir di Luar Jakarta**:
   * `DailyClosing` hanya dibuat untuk Jakarta (1 closed kemarin, 1 open hari ini).
   * Cabang Bogor, Jogja, dan Surabaya tidak memiliki sesi kasir sama sekali, memicu peringatan *"Laci Kasir Belum Dibuka"* di layar POS.
4. **Transaksi Cabang Non-Jakarta Menggantung**:
   * Transaksi di Bogor, Jogja, dan Surabaya memiliki `daily_closing_id = null`.
5. **Ketiadaan Data Promosi (Bundle & Diskon)**:
   * Tabel `bundles`, `bundle_items`, `discounts`, dan `discount_product` belum disentuh di seeder klien.
6. **Ketiadaan Simulasi Kerugian Dapur (`CogsWasteLog`)**:
   * Portal Owner modul Audit Selisih & Waste menampilkan tabel waste dapur yang kosong.

---

## 📐 2. Desain Solusi Teknis (Action Plan)

### A. Solusi Master Katalog Terpusat (Best Practice Multi-Branch Brand)
Ubah pembuatan kategori dan produk menjadi katalog terpusat holding:
```php
// Kategori Master (Berlaku untuk semua cabang)
$catAyam  = Category::create(['outlet_id' => null, 'category_name' => 'Ayam Geprek Spesial', ...]);
$catPaket = Category::create(['outlet_id' => null, 'category_name' => 'Paket Hemat Lengkap', ...]);
$catSide  = Category::create(['outlet_id' => null, 'category_name' => 'Side Dishes & Ekstra', ...]);
$catDrink = Category::create(['outlet_id' => null, 'category_name' => 'Minuman Segar', ...]);

// Produk Master (Berlaku untuk semua cabang)
foreach ($productsData as $p) {
    $prod = Product::create([
        'outlet_id' => null, // Master Catalog
        'category_id' => $p['cat']->category_id,
        'product_name' => $p['name'],
        'product_code' => $p['sku'],
        'product_price' => $p['price'],
        ...
    ]);
}
```

### B. Solusi Binding Pivot Stok Bahan Baku (`product_stock`)
Saat stok porsi dibuat per cabang, sambungkan ke pivot produk:
```php
foreach ([$jkt, $bgr, $yog, $sby] as $ot) {
    $stk = Stock::create([
        'outlet_id' => $ot->outlet_id,
        'stock_code' => 'STK-' . $p['sku'] . '-' . $ot->outlet_code,
        'stock_name' => $prod->product_name . ' (' . $ot->outlet_branch . ')',
        'stock_unit' => 'porsi',
        'stock_amount' => rand(80, 250),
        'stock_price' => $prod->product_price,
        'stock_status' => 1,
    ]);

    // Hubungkan relasi pivot
    $prod->stocks()->attach($stk->stock_id, [
        'quantity' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
```

### C. Solusi Sesi Buka Kasir 4 Cabang (`DailyClosing` & `CashDrawerLog`)
Gunakan perulangan untuk membuat sesi kasir di seluruh 4 cabang:
```php
$cashiersByOutlet = [
    $jkt->outlet_id => DB::connection('client')->table('users')->where('email', 'kasir@geprekgambos.com')->first(),
    $bgr->outlet_id => DB::connection('client')->table('users')->where('email', 'kasir.bogor@geprekgambos.com')->first(),
    $yog->outlet_id => DB::connection('client')->table('users')->where('email', 'kasir.jogja@geprekgambos.com')->first(),
    $sby->outlet_id => DB::connection('client')->table('users')->where('email', 'kasir.surabaya@geprekgambos.com')->first(),
];

$closingsYesterday = [];
$closingsToday = [];

foreach ([$jkt, $bgr, $yog, $sby] as $ot) {
    $usr = $cashiersByOutlet[$ot->outlet_id];

    // 1. Sesi Kemarin (Tutup / Z-Report Selesai)
    $closingsYesterday[$ot->outlet_id] = DailyClosing::create([
        'outlet_id' => $ot->outlet_id,
        'cashier_id' => $usr?->id ?? 1,
        'shift_name' => 'Sesi Kasir ' . $ot->outlet_branch,
        'business_date' => now()->subDay()->toDateString(),
        'opened_at' => now()->subDay()->setTime(9, 0),
        'closed_at' => now()->subDay()->setTime(21, 30),
        'starting_cash' => 250000,
        'status' => 'closed',
        ...
    ]);

    // 2. Sesi Hari Ini (Sedang Buka / Open)
    $closingsToday[$ot->outlet_id] = DailyClosing::create([
        'outlet_id' => $ot->outlet_id,
        'cashier_id' => $usr?->id ?? 1,
        'shift_name' => 'Sesi Kasir ' . $ot->outlet_branch,
        'business_date' => now()->toDateString(),
        'opened_at' => now()->setTime(8, 30),
        'closed_at' => null,
        'starting_cash' => 250000,
        'system_expected_cash' => 250000,
        'status' => 'open',
        ...
    ]);
}
```

### D. Solusi Binding Transaksi ke Closing ID
Saat men-generate pesanan, tautkan ke ID closing cabang yang bersangkutan:
```php
$closingId = $isYesterday 
    ? $closingsYesterday[$currentOutlet->outlet_id]->id 
    : $closingsToday[$currentOutlet->outlet_id]->id;
```

### E. Solusi Paket Bundle & Diskon Promo
Tambahkan data bundle hemat dan promo:
```php
// Paket Bundle
$bundle = Bundle::create([
    'outlet_id' => null, // Berlaku semua cabang
    'bundle_code' => 'BND-KOMPLIT',
    'bundle_name' => 'Paket Geprek Kenyang Komplit',
    'bundle_price' => 25000,
    'bundle_status' => 1,
]);
BundleItem::create(['bundle_id' => $bundle->bundle_id, 'product_id' => $products[0]->product_id, 'quantity' => 1]);
BundleItem::create(['bundle_id' => $bundle->bundle_id, 'product_id' => $products[8]->product_id, 'quantity' => 1]); // Es teh

// Diskon Promo
$disc = Discount::create([
    'discount_name' => 'Promo Diskon Sambal Mantap 10%',
    'discount_type' => 'percentage',
    'discount_value' => 10,
    'discount_status' => 1,
]);
$products[0]->discounts()->attach($disc->discount_id, [
    'start_date' => now()->subDays(5),
    'end_date' => now()->addDays(25),
]);
```

### F. Solusi Log Kerugian Dapur (`CogsWasteLog`)
Tambahkan 2–3 log bahan basi per cabang agar Portal Owner Audit Waste terisi:
```php
foreach ([$jkt, $bgr, $yog, $sby] as $ot) {
    CogsWasteLog::create([
        'outlet_id' => $ot->outlet_id,
        'raw_stock_material_id' => $rawStockMap['RAW-AYAM-BROILER']->raw_stock_material_id,
        'loss_date' => now()->subDays(rand(1, 4))->toDateString(),
        'waste_qty' => 1.5, // 1.5 kg ayam rusak freezer mati
        'waste_cost' => 1.5 * 38000,
        'waste_reason' => 'Bahan rusak suhu chiller turun saat malam',
        'reported_by' => 'Chef Dapur ' . $ot->outlet_branch,
    ]);
}
```

---

## 🧪 3. Skenario Pengujian Manual (Untuk User)

| Langkah | Aksi Pengguna | Ekspektasi Tampilan Sistem |
|:---:|---|---|
| **1** | Buka browser, pilih cabang **Surabaya Gubeng** di topbar | Header menampilkan: `Geprek Gambos - Surabaya (Surabaya Gubeng)` |
| **2** | Masuk menu **Transaksi Toko > Kasir POS** | 10 Menu Ayam Geprek tampil lengkap dengan harga, foto, & tombol `+ Pesan`. Banner kuning laci belum buka **tidak muncul**. |
| **3** | Klik tab **Bundel** di Kasir POS | Muncul kartu *Paket Geprek Kenyang Komplit* siap dipesan. |
| **4** | Klik `+ Pesan` 2 menu, buka **Keranjang**, pilih Meja 1, bayar Tunai | Pesanan sukses diproses, struk tercetak rapi, kas masuk ke laci. |
| **5** | Buka menu **Main Toko > Buka Kasir** | Status kasir BUKA, kasir bertugas **Eko Prasetyo**, total kas fisik laci ter-update bertambah. |
| **6** | Buka **Portal Owner > Leaderboard & Konsolidasi** | Ke-4 cabang (*Jakarta, Bogor, Jogja, Surabaya*) tampil lengkap dengan omzet dan status kasir hijau aktif. |
| **7** | Buka **Portal Owner > Audit Selisih & Waste** | Muncul tabel selisih kas tutup kasir dan tabel kerugian bahan waste dapur. |

---

## 🚀 4. Langkah Eksekusi (Ketika User Sudah Memberi Izin)

1. AI memperbarui file [`database/seeders/client/GeprekGambosSeeder.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/database/seeders/client/GeprekGambosSeeder.php) dan [`KopiSenjaSeeder.php`](file:///c:/xampp812/htdocs/newpost/new-kasir/database/seeders/client/KopiSenjaSeeder.php).
2. Melakukan linter check untuk memastikan tidak ada kesalahan sintaks PHP.
3. User menjalankan perintah:
   ```bash
   c:\xampp812\php\php.exe artisan db:seed --class=CentralDatabaseSeeder
   ```
   *(Atau mengizinkan AI menjalankannya secara otomatis).*
4. User melakukan pengujian manual di browser sesuai tabel di atas.
